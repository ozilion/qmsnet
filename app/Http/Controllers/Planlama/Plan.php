<?php

namespace App\Http\Controllers\Planlama;

use App\Helpers\Helpers;
use App\Http\Controllers\Controller;
use App\Http\Controllers\denetci_atama\AuditorsController;
use App\Models\Denetciler;
use DateTime;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use IntlDateFormatter;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use Zip;
use ZipArchive;

class Plan extends Controller
{
  public static $ttips = [];

  private static $sonuc9001;
  private static $sonuc14001;
  private static $sonuc45001;
  private static $sonuc50001;
  private static $sonuc27001;
  private static $sonuc22000;
  private static $sonucSmiic;
  private $doc = '';
  private $zip;
  private $canzip = false;

  public function index()
  {
    $kid = Auth::user()->kurulusid;
    $basvuru = DB::select('SELECT * FROM basvuru where kid=' . $kid . ' ORDER BY planno DESC');

    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.kid', '=', $kid)
      ->get();

    foreach ($plan as $r => $ret) {
      $cevrim = $ret->belgecevrimi;
      $cevrim = $cevrim == '' ? '1' : $cevrim;

      $ea = $ret->eakodu;
      $nace = $ret->nacekodu;
      $kat = str_replace('@', '', $ret->kategori22);
      $oickat = str_replace('ß', '', $ret->kategorioic);
      $enysteknikalan = str_replace('Æ', '', $ret->teknikalanenys);
      $bgkat = str_replace('€', '', $ret->kategoribgys);
      $eanacekat = '';

      if ($nace != '') {
        $nace = '|' . $nace;
      }
      if ($kat != '') {
        $kat = '@' . str_replace('@', '', $kat);
      }
      $oickat = $oickat != '' ? 'ß' . str_replace('ß', '', $oickat) : str_replace('ß', '', $oickat);
      $enysteknikalan =
        $enysteknikalan != '' ? 'Æ' . str_replace('Æ', '', $enysteknikalan) : str_replace('Æ', '', $enysteknikalan);
      if ($bgkat != '') {
        $bgkat = '€' . $bgkat;
      }

      $eanacekat = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

      $dentarihi = '';
      $dtipi = '';
      if ($ret->asama2 != '') {
        $dentarihi = $ret->asama2;
        $dtipi = 'İlk';
      }
      if ($ret->gozetim1 != '') {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if ($ret->gozetim2 != '') {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if ($ret->ybtar != '') {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Yb';
      }
      if ($ret->ozeltar != '' && strtotime($ret->ozeltar) > strtotime($dentarihi)) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }

      if (intval($cevrim) >= 2 && ($ret->asama == 'g1' || $ret->asama == 'g1karar')) {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if (intval($cevrim) >= 2 && ($ret->asama == 'g2' || $ret->asama == 'g2karar')) {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if (intval($cevrim) >= 2 && ($ret->asama == 'yb' || $ret->asama == 'ybkarar')) {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Yb';
      }
      if (
        intval($cevrim) >= 2 &&
        ($ret->asama == 'ozel' || $ret->asama == 'ozelkarar') &&
        strtotime($ret->ozeltar) > strtotime($dentarihi)
      ) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }
      $ret->dentarihi = wordwrap($dentarihi, 15, '<br>');
      $ret->dtipi = $dtipi;

      $durum = $ret->belgedurum;
      $durum = str_replace('İ', 'i', $durum);
      $durum = str_replace('A', 'a', $durum);
      $durum = str_replace('D', 'd', $durum);

      $ret->belgedurum = $durum;
      $ret->eanacekat = $eanacekat;

      $plan[$r] = $ret;
    }

    return view('content.planlama.dashboards-plan', ['basvuru' => $basvuru, 'plan' => $plan]);
  }

  public static function getPlanlar()
  {
    // Kuruluş ID'sini al
    $kid = Auth::user()->kurulusid;

    // planlar ve basvuru tablolarını JOIN ile birleştir,
    // sadece lazım olan alanları çek, en son planno DESC ile sırala.
    $planlar = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.kid', '=', $kid)
      ->where('planlar.kid', '=', $kid)
      ->orderBy('planlar.planno', 'desc')
      ->get();

    // Bugünün tarihinin 00:00:00 (sadece tarih bazında kıyaslama için) timestamp'ini al
    // (isteğe göre now()->timestamp da kullanılabilir,
    //  ancak "sadece günü" kıyaslamak istiyorsanız 00:00:00 mantıklı olabilir)
    $today = new \DateTime('today');
    $bgnTimestamp = $today->getTimestamp(); // Bugünün timestamp'i (gün başlangıcı)

    // planlar koleksiyonunu dönüştürerek veriyi işliyoruz
    $planlar->transform(function ($ret) use ($bgnTimestamp) {
      /**
       * 1) Cevirim ve belgelendirileceksistemler hesaplaması
       */
      $cevrim = empty($ret->belgecevrimi) ? '1' : $ret->belgecevrimi;

      $kyssistemler = Helpers::getSistemler($ret);
      $oicsistemler = Helpers::getOicSistemler($ret);

      if (!empty($kyssistemler) && !empty($oicsistemler)) {
        $ret->belgelendirileceksistemler = $kyssistemler . ', ' . $oicsistemler;
      } elseif (empty($kyssistemler) && !empty($oicsistemler)) {
        $ret->belgelendirileceksistemler = $oicsistemler;
      } elseif (!empty($kyssistemler) && empty($oicsistemler)) {
        $ret->belgelendirileceksistemler = $kyssistemler;
      } else {
        $ret->belgelendirileceksistemler = '';
      }

      /**
       * 2) Kategori / EA / NACE Kodları vb.
       */
      $ea = $ret->eakodu;
      $nace = $ret->nacekodu;
      $kat = str_replace('@', '', $ret->kategori22);
      $oickat = str_replace('ß', '', $ret->kategorioic);
      $enysteknikalan = str_replace('Æ', '', $ret->teknikalanenys);
      $bgkat = str_replace('€', '', $ret->kategoribgys);

      // NACE önüne | ekliyoruz (mevcutsa)
      if (!empty($nace)) {
        $nace = '|' . $nace;
      }
      // Kategori önüne @ ekliyoruz (mevcutsa)
      if (!empty($kat)) {
        $kat = '@' . $kat;
      }
      // OIC kategori önüne ß ekliyoruz (mevcutsa)
      if (!empty($oickat)) {
        $oickat = 'ß' . $oickat;
      }
      // ENYS teknik alan önüne Æ ekliyoruz (mevcutsa)
      if (!empty($enysteknikalan)) {
        $enysteknikalan = 'Æ' . $enysteknikalan;
      }
      // BG kategori önüne € ekliyoruz (mevcutsa)
      if (!empty($bgkat)) {
        $bgkat = '€' . $bgkat;
      }

      // EA + NACE + Kategori vs. birleştiriyoruz
      $ret->eanacekat = self::teknikAlan($ret);//$ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

      /**
       * 3) Denetim Tarihi ve Denetim Tipi Hesaplaması
       */
      $dentarihi = '';
      $dtipi = '';

      // 3.1 Hangi aşamaların dolu olduğuna göre son denetim tarihini seç
      if (!empty($ret->asama2)) {
        $dentarihi = $ret->asama2;
        $dtipi = 'İlk';
      }
      if (!empty($ret->gozetim1)) {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if (!empty($ret->gozetim2)) {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if (!empty($ret->ybtar)) {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Y.b.';
      }
      // Özel tarihin bitişi, mevcut tarihinkinden büyükse özel tar
      if (
        !empty($ret->ozeltar) &&
        strtotime(self::getDenetimBitisTarihi($ret->ozeltar)) > strtotime(self::getDenetimBitisTarihi($dentarihi))
      ) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }

      // 3.2 Cevirim >=2 ise asama durumuna göre tekrar güncelle
      if (intval($cevrim) >= 2) {
        if ($ret->asama == 'g1' || $ret->asama == 'g1karar') {
          $dentarihi = $ret->gozetim1;
          $dtipi = 'G1';
        }
        if ($ret->asama == 'g2' || $ret->asama == 'g2karar') {
          $dentarihi = $ret->gozetim2;
          $dtipi = 'G2';
        }
        if ($ret->asama == 'yb' || $ret->asama == 'ybkarar') {
          $dentarihi = $ret->ybtar;
          $dtipi = 'Yb';
        }
        if (
          ($ret->asama == 'ozel' || $ret->asama == 'ozelkarar') &&
          !empty($ret->ozeltar) &&
          strtotime(self::getDenetimBitisTarihi($ret->ozeltar)) > strtotime(self::getDenetimBitisTarihi($dentarihi))
        ) {
          $dentarihi = $ret->ozeltar;
          $dtipi = 'Özel';
        }
      }

      // Görsel amaçlı uzun tarihleri satır atlamak için wordwrap
      $ret->dentarihi = wordwrap($dentarihi, 15, '<br>', true);
      $ret->dtipi = $dtipi;

      /**
       * 4) Belge Durumunun Küçük Harfe Dönüşümü vs.
       */
      $durum = (string) $ret->belgedurum;
      // Örnek basit harf dönüştürmeler (İ, A, D).
      // Gerekirse strtr veya mb_strtolower gibi çoklu karakter dönüşümlerini de düşünebilirsiniz.
      $durum = str_replace('İ', 'i', $durum);
      $durum = str_replace('A', 'a', $durum);
      $durum = str_replace('D', 'd', $durum);
      $ret->belgedurum = $durum;

      /**
       * 5) Denetim Başlangıç Tarihi Parse ve Karşılaştırma
       *    $dentarihi virgüllü ise ilk parçayı alıyoruz vs.
       */
      $denbastarihi = date('d.m.Y'); // Varsayılan olarak bugünün tarihi
      if (strpos($dentarihi, ',') !== false) {
        // çoklu tarih varsa, ilkini alıyoruz
        $dentars = explode(',', str_replace(' ', '', $dentarihi));
        $denbastarihi = $dentars[0] ?? date('d.m.Y');
      } elseif (!empty($dentarihi)) {
        $denbastarihi = str_replace(' ', '', $dentarihi);
      }

      // Denetim tarihini parse et (gün.ay.yıl)
      $dateFormat = 'd.m.Y';
      $dbt = DateTime::createFromFormat($dateFormat, $denbastarihi);
      $yayintarihi = DateTime::createFromFormat($dateFormat, $ret->ilkyayintarihi);
      $ret->yayintarihi = $yayintarihi;

      // Parse başarılı mı?
      if ($dbt instanceof DateTime) {
        $dbtTimestamp = $dbt->getTimestamp();
        // Bugüne göre gelecek bir tarihse (bugünden büyükse)
        // isterseniz bir event veya başka işlem çalıştırabilirsiniz
        if ($dbtTimestamp > $bgnTimestamp) {
          // event(new PlanEvent(...));
          // Örneğin: $ret->future_event = "Will Trigger Event";
        }
      }

      // dönüş olarak bu $ret'i güncellenmiş haliyle planlar koleksiyonuna kazandırıyoruz
      return $ret;
    });

    // Artık tüm veriler işlendi, JSON olarak döndürüyoruz
    return response()->json([
      'data' => $planlar,
    ]);
  }

  public static function getEaNacekodlari()
  {
    $eanacetablo = DB::select('SELECT * FROM eanacekodlari ORDER BY id ASC');

    return json_encode(['data' => $eanacetablo]);
  }

  public static function get22Cats()
  {
    $cattablo = DB::select('SELECT * FROM cls22000 ORDER BY id ASC');

    return json_encode(['data' => $cattablo]);
  }

  public static function get27001Cats()
  {
    $cattablo = DB::select('SELECT * FROM cls27001 ORDER BY id ASC');

    return json_encode(['data' => $cattablo]);
  }

  public static function get50001Cats()
  {
    $cattablo = DB::select('SELECT * FROM cls50001 ORDER BY id ASC');

    return json_encode(['data' => $cattablo]);
  }

  public static function getSmiicCats()
  {
    $smiictablo = DB::select('SELECT * FROM clssmiic ORDER BY id ASC');

    return json_encode(['data' => $smiictablo]);
  }

  public function checkCevirim(Request $request)
  {
    $cevrim = $request->cevrim;
    $pno = $request->pno;
    $asama = $request->asama;

    $kid = Auth::user()->kurulusid;
    if (intval($kid) < 0) {
      return view('content.planlama.dashboards-plan', ['kiderror' => 'Seçili kuruluşa ait bilgiler alınamadı.']);
    }

    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.planno', '=', $pno, 'and')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.planno', '=', $pno, 'and')
      ->where('planlar.kid', '=', $kid, 'and')
      ->where('planlar.belgecevrimi', $cevrim)
      ->first();

    //    $basvuruekbg = DB::select('SELECT * FROM basvuru_27001_ek where planno=' . $pno . ' ORDER BY planno DESC');
    $basvuruekbg = DB::table('basvuru_27001_ek')
      ->where('planno', '=', $pno)
      ->orderBy('planno', 'desc')
      ->first();

    $basvurubgys = $basvuruekbg ? $basvuruekbg : null;

    if ($plan) {
      return view('content.planlama.planlama', [
        'exists' => true,
        'plan' => $plan,
        'basvurubgys' => $basvurubgys,
        'asama' => $plan->asama,
        'pno' => $pno,
        'cvrm' => $cevrim,
      ]);

      //      // Eğer eşleşen plan varsa, content'i geri döndür
      //      $content = view('planlama.planlama', compact('plan'))->render();
      //      return response()->json(['exists' => true, 'content' => $content]);
    } else {
      // Eşleşen plan yoksa
      $plan = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('basvuru.planno', '=', $pno, 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.planno', '=', $pno, 'and')
        ->where('planlar.kid', '=', $kid)
        ->first();

      return view('content.planlama.planlama', [
        'exists' => false,
        'plan' => $plan,
        'basvurubgys' => $basvurubgys,
        'asama' => $asama,
        'pno' => $pno,
        'cvrm' => $cevrim,
      ]);
    }
  }

  public function planlama(Request $request)
  {
    $kid = Auth::user()->kurulusid;
    if (intval($kid) < 0) {
      return view('content.planlama.dashboards-plan', ['kiderror' => 'Seçili kuruluşa ait bilgiler alınamadı.']);
    }

    $basvuru = DB::select(
      'SELECT * FROM basvuru where planno=' . $request->pno . ' and kid=' . $kid . ' ORDER BY planno DESC'
    );

    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.planno', '=', $request->pno, 'and')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.planno', '=', $request->pno, 'and')
      ->where('planlar.kid', '=', $kid)
      ->first();

    $smiictablo = DB::select('SELECT * FROM clssmiic ORDER BY id ASC');

    if ($request->asama === 'basvuru') {
      $basvuruek = DB::select('SELECT * FROM basvuru_50001_ek where planno=' . $request->pno . ' ORDER BY planno DESC');
      $basvuruenys = count($basvuruek) > 0 ? $basvuruek[0] : null;

      $basvuruekbg = DB::select(
        'SELECT * FROM basvuru_27001_ek where planno=' . $request->pno . ' ORDER BY planno DESC'
      );
      $basvurubgys = count($basvuruekbg) > 0 ? $basvuruekbg[0] : null;

      return view('content.planlama.basvuru', [
        'basvuru' => $basvuru,
        'basvuruenys' => $basvuruenys,
        'basvurubgys' => $basvurubgys,
        'plan' => $plan,
        'asama' => $request->asama,
        'pno' => $request->pno,
      ]);
    } elseif ($request->asama === 'basvuruyeni') {
      return view('content.planlama.basvuruyeni');
    } elseif (
      $request->asama === 'ilkplan' ||
      $request->asama === 'g1' ||
      $request->asama === 'g2' ||
      $request->asama === 'yb' ||
      $request->asama === 'ozel'
    ) {
      $basvuruekenys = DB::select(
        'SELECT * FROM basvuru_50001_ek where planno=' . $request->pno . ' ORDER BY planno DESC'
      );
      $basvuruenys = count($basvuruekenys) > 0 ? $basvuruekenys[0] : null;

      $basvuruekbg = DB::select(
        'SELECT * FROM basvuru_27001_ek where planno=' . $request->pno . ' ORDER BY planno DESC'
      );
      $basvurubgys = count($basvuruekbg) > 0 ? $basvuruekbg[0] : null;

      return view('content.planlama.planlama', [
        'exists' => true,
        'plan' => $plan,
        'basvurubgys' => $basvurubgys,
        'basvuruenys' => $basvuruenys,
        'asama' => $request->asama,
        'pno' => $request->pno,
      ]);
    } elseif (
      $request->asama === 'ilkkarar' ||
      $request->asama === 'g1karar' ||
      $request->asama === 'g2karar' ||
      $request->asama === 'ybkarar' ||
      $request->asama === 'ozelkarar'
    ) {
      $karar = DB::select('SELECT * FROM plan_karar where planno=' . $request->pno . ' ORDER BY planno DESC')[0];
      $karargg = DB::select('SELECT * FROM plan_karar_gg where planno=' . $request->pno . ' ORDER BY planno DESC');
      $karargg = count($karargg) > 0 ? $karargg[0] : [];
      $kararbd = DB::select('SELECT * FROM plan_karar_bd where planno=' . $request->pno . ' ORDER BY planno DESC');
      $kararbd = count($kararbd) > 0 ? $kararbd[0] : [];
      $kararo = DB::select('SELECT * FROM plan_karar_onay where planno=' . $request->pno . ' ORDER BY planno DESC');
      $kararo = count($kararo) > 0 ? $kararo[0] : [];
      return view('content.planlama.karar', [
        'plan' => $plan,
        'karar' => $karar,
        'karargg' => $karargg,
        'kararbd' => $kararbd,
        'kararo' => $kararo,
        'asama' => $request->asama,
        'pno' => $request->pno,
      ]);
    } elseif ($request->asama === 'sertifika') {
      $cert = DB::select(
        'SELECT * FROM plan_sertifika where planno=' .
          $request->pno .
          ' and akreditasyon="Akreditasyonlu" ORDER BY bitistarihi DESC'
      );
      return view('content.planlama.sertifika', [
        'plan' => $plan,
        'cert' => $cert,
        'asama' => $request->asama,
        'pno' => $request->pno,
      ]);
    } else {
      return view('content.planlama.dashboards-plan', ['basvuru' => $basvuru, 'plan' => $plan]);
    }
  }

  /**
   * Process grouped data to update rows array
   *
   * @param array &$rows
   * @param array $groupedData
   * @return void
   */
  private function processGroupedData(&$rows, $groupedData)
  {
    // First, identify existing numeric rows
    $numericRows = [];
    foreach ($rows as $key => $row) {
      if (is_numeric($key) && $key >= 2) {
        $numericRows[$key] = $row;
      }
    }

    // Clear numeric rows from the main rows array
    foreach (array_keys($numericRows) as $key) {
      unset($rows[$key]);
    }

    // Re-number rows starting from 2
    $nextIndex = 2;

    // Now process grouped data
    foreach ($groupedData as $groupKey => $group) {
      if (empty($group['department']) && empty($group['start']) && empty($group['end'])) {
        continue; // Skip empty rows
      }

      if (empty($group['standards']) || !is_array($group['standards'])) {
        continue; // Skip groups with no standards
      }

      // Get the first standard from the group
      $firstStandard = array_shift($group['standards']);

      // Create the main row with first standard
      $rows[$nextIndex] = [
        'department' => $group['department'],
        'start' => $group['start'],
        'end' => $group['end'],
        'team' => $group['team'],
        'standard' => $firstStandard['standard'] ?? '',
        'madde_no' => $firstStandard['maddeNo'] ?? '',
        'additional_standards' => [] // Will be populated below
      ];

      // Add remaining standards as additional_standards
      foreach ($group['standards'] as $standard) {
        $rows[$nextIndex]['additional_standards'][] = [
          'standard' => $standard['standard'] ?? '',
          'madde_no' => $standard['maddeNo'] ?? ''
        ];
      }

      $nextIndex++;
    }
  }

  public function basvuruYeni()
  {
    //    $basvuru = DB::select('SELECT planno, firmaadi FROM basvuru ORDER BY planno DESC');
    //    $basvuru = DB::table('basvuru')
    //      ->orderBy("planno","desc")
    //      ->select('planno', 'firmaadi')
    //      ->get();
    //    $sonbasvuru = DB::select('select planno from basvuru order by planno desc limit 1');
    return view('content.planlama.basvuruyeni'); //, ['basvuru' => $basvuru, 'sonbasvuru' => $sonbasvuru]);
  }

  public function basvuruKaydet(Request $request)
  {
    $kid = Auth::user()->kurulusid;
    $plnarr = [];
    $enysek = [];
    $bgysek = [];
    $input = $request->all();
    $pno = $input['planno'];
    unset($input['_token']);

    $inceleneceksahasayisi = 0;
    if (!is_null($input['subevardaa']) && intval($input['subevardaa']) > 0) {
      $inceleneceksahasayisi++;
    }
    if (!is_null($input['subevardba']) && intval($input['subevardba']) > 0) {
      $inceleneceksahasayisi++;
    }
    if (!is_null($input['subevardca']) && intval($input['subevardca']) > 0) {
      $inceleneceksahasayisi++;
    }

    $input['iso900115varyok'] = isset($input['iso900115varyok']) ? $input['iso900115varyok'] : 0;
    $input['iso1400115varyok'] = isset($input['iso1400115varyok']) ? $input['iso1400115varyok'] : 0;
    $input['iso4500118varyok'] = isset($input['iso4500118varyok']) ? $input['iso4500118varyok'] : 0;
    $input['iso5000118varyok'] = isset($input['iso5000118varyok']) ? $input['iso5000118varyok'] : 0;
    $input['iso27001varyok'] = isset($input['iso27001varyok']) ? $input['iso27001varyok'] : 0;
    $input['iso2200018varyok'] = isset($input['iso2200018varyok']) ? $input['iso2200018varyok'] : 0;
    $input['helalvaryok'] = isset($input['helalvaryok']) ? $input['helalvaryok'] : 0;
    $input['oicsmiik6varyok'] = isset($input['oicsmiik6varyok']) ? $input['oicsmiik6varyok'] : 0;
    $input['oicsmiik9varyok'] = isset($input['oicsmiik9varyok']) ? $input['oicsmiik9varyok'] : 0;
    $input['oicsmiik171varyok'] = isset($input['oicsmiik171varyok']) ? $input['oicsmiik171varyok'] : 0;
    $input['oicsmiik23varyok'] = isset($input['oicsmiik23varyok']) ? $input['oicsmiik23varyok'] : 0;
    $input['oicsmiik24varyok'] = isset($input['oicsmiik24varyok']) ? $input['oicsmiik24varyok'] : 0;

    $basvuruekmesaj = 'Güncellenecek ISO 50001 Gözden geçirme bilgileri bulunamadı';
    if (isset($input['iso5000118varyok'])) {
      $enysek['planno'] = $pno;
      $enysek['enyseffectiveemployee'] = isset($input['enyseffectiveemployee']) ? $input['enyseffectiveemployee'] : 0;
      $enysek['enyscalisanust'] = isset($input['enyscalisanust']) ? $input['enyscalisanust'] : 0;
      $enysek['enyscalisanekip'] = isset($input['enyscalisanekip']) ? $input['enyscalisanekip'] : 0;
      $enysek['enyscalisanperf'] = isset($input['enyscalisanperf']) ? $input['enyscalisanperf'] : 0;
      $enysek['enyscalisanetkin'] = isset($input['enyscalisanetkin']) ? $input['enyscalisanust'] : 0;
      $enysek['enyscalisanarge'] = isset($input['enyscalisanarge']) ? $input['enyscalisanarge'] : 0;
      $enysek['enyscalisanoek'] = isset($input['enyscalisanoek']) ? $input['enyscalisanoek'] : 0;

      if (isset($input['iso5000118varyok'])) {
        $basvuruek = DB::table('basvuru_50001_ek')->updateOrInsert(['planno' => $pno], $enysek);
        if ($basvuruek) {
          $basvuruekmesaj = '<br>ISO 50001 Gözden geçirme bilgileri kayıt edildi.';
        }
        if (!$basvuruek) {
          $basvuruekmesaj = '<br>ISO 50001 Gözden geçirme bilgileri kayıt edilemedi';
        }
      }
    }
    unset($input['enyseffectiveemployee']);
    unset($input['subetepaa']);
    unset($input['subetepba']);
    unset($input['subetepca']);
    unset($input['subeetsayab']);
    unset($input['subeetsaybb']);
    unset($input['subeetsaycb']);
    unset($input['subeoekac']);
    unset($input['subeoekbc']);
    unset($input['subeoekcc']);
    unset($input['enyscalisanust']);
    unset($input['enyscalisanekip']);
    unset($input['enyscalisanperf']);
    unset($input['enyscalisanetkin']);
    unset($input['enyscalisanarge']);
    unset($input['enyscalisanoek']);

    $basvuruekbgmesaj = 'Güncellenecek ISO 27001 Gözden geçirme bilgileri bulunamadı';
    if (isset($input['iso27001varyok'])) {
      $bgysek['planno'] = $pno;
      $bgysek['bgyseffectiveemployee'] = isset($input['bgyseffectiveemployee']) ? $input['bgyseffectiveemployee'] : 0;
      $bgysek['isturu'] = isset($input['isturu']) ? $input['isturu'] : 0;
      $bgysek['prosesler'] = isset($input['prosesler']) ? $input['prosesler'] : 0;
      $bgysek['ysolusmaseviyesi'] = isset($input['ysolusmaseviyesi']) ? $input['ysolusmaseviyesi'] : 0;
      $bgysek['btaltyapi'] = isset($input['btaltyapi']) ? $input['btaltyapi'] : 0;
      $bgysek['diskaynak'] = isset($input['diskaynak']) ? $input['diskaynak'] : 0;
      $bgysek['bilgisistemgelisimi'] = isset($input['bilgisistemgelisimi']) ? $input['bilgisistemgelisimi'] : 0;

      if (isset($input['iso27001varyok'])) {
        $basvuruekbg = DB::table('basvuru_27001_ek')->updateOrInsert(['planno' => $pno], $bgysek);
        if ($basvuruekbg) {
          $basvuruekbgmesaj = '<br>ISO 27001 Gözden geçirme bilgileri kayıt edildi.';
        }
        if (!$basvuruekbg) {
          $basvuruekbgmesaj = '<br>ISO 27001 Gözden geçirme bilgileri kayıt edilemedi';
        }
      }
    }
    unset($input['bgyseffectiveemployee']);
    unset($input['isturu']);
    unset($input['prosesler']);
    unset($input['ysolusmaseviyesi']);
    unset($input['btaltyapi']);
    unset($input['diskaynak']);
    unset($input['bilgisistemgelisimi']);

    $kyssistemler = Helpers::getSistemler($input);
    $oicsistemler = Helpers::getOicSistemler($input);
    $belgelendirileceksistemler = '';

    if ($kyssistemler !== '' && $oicsistemler !== '') {
      $belgelendirileceksistemler = $kyssistemler . ', ' . $oicsistemler;
    }
    if ($kyssistemler === '' && $oicsistemler !== '') {
      $belgelendirileceksistemler = $oicsistemler;
    }
    if ($kyssistemler !== '' && $oicsistemler === '') {
      $belgelendirileceksistemler = $kyssistemler;
    }
    //    if (isset($input["subeadresi4"]) && (!is_null($input["subeadresi4"]) || intval($input["subeadresi4"]) > 0)) {
    //      $inceleneceksahasayisi++;
    //      echo $input["subeadresi4"];
    //    }

    unset($input['mevsimselUretim']);
    unset($input['akreditasyonKapsam']);
    unset($input['naceKodPersonel']);
    unset($input['basvuruKabul']);

    $input['inceleneceksahasayisi'] = $inceleneceksahasayisi; //ceil(sqrt($inceleneceksahasayisi));

    if ($input['iso2200018varyok']) {
      $input['inceleneceksahasayisi'] = intval($input['sahasayisi22']);
    }
    if (
      $input['helalvaryok'] ||
      $input['oicsmiik6varyok'] ||
      $input['oicsmiik9varyok'] ||
      $input['oicsmiik171varyok'] ||
      $input['oicsmiik24varyok']
    ) {
      $input['inceleneceksahasayisi'] = intval($input['sahasayisi22']);
    }

    $plnarr['planno'] = $pno;
    //    $plnarr["basvuru_id"] = 0;
    $plnarr['belgelendirileceksistemler'] = $belgelendirileceksistemler;

    $bakb = DB::select('select * from basvuru where planno=' . $pno);

    $dugme =
      '<a href="' .
      route('crm-planlama', ['asama' => 'ilkplan', 'pno' => $pno]) .
      '" class="btn btn-success btn-sm">DENETİM/GÜN HESAPLAMA</a>';
    if ($bakb) {
      $kayitb = DB::table('basvuru')
        ->where('planno', $pno)
        ->update($input);
      if (!$kayitb) {
        $data = [
          'hata' => 1,
          'mesaj' =>
            'Belgelendirme Başvuru bilgileri başarıyla güncellenemedi, lütfen bilgilerinizi kontrol ediniz....<br>ya da planlama sayfasına geçiş için ==>' .
            $dugme .
            $basvuruekmesaj .
            $basvuruekbgmesaj,
        ];

        return json_encode($data);
      }
      //      $plnarr["basvuru_id"] = $bakb[0]->id;
      DB::table('planlar')
        ->where('planno', $pno)
        ->update($plnarr);

      //      event(new MyEvent('Başvuru bilgileri başarıyla güncellenmiştir.<br>' . $dugme));

      $data = [
        'hata' => 0,
        'mesaj' => 'Başvuru bilgileri başarıyla güncellenmiştir.<br>' . $dugme . $basvuruekmesaj . $basvuruekbgmesaj,
      ];
      return json_encode($data);
    } else {
      $input['kid'] = $kid;
      $kayitb = DB::table('basvuru')->insert($input);
      //      $plnarr["basvuru_id"] = DB::table('basvuru')->get()->last()->id;
      $plnarr['asama'] = 'ilkplan';
      $plnarr['kid'] = $kid;
      $plnarr['belgelendirileceksistemler'] = $belgelendirileceksistemler;
      DB::table('planlar')->insert($plnarr);

      if (!$kayitb) {
        $data = [
          'hata' => 1,
          'mesaj' =>
            'Belgelendirme Başvuru bilgileri başarıyla eklenemedi, lütfen bilgilerinizi kontrol ediniz...<br>ya da planlama sayfasına geçiş için ==>' .
            $dugme .
            $basvuruekmesaj .
            $basvuruekbgmesaj,
        ];
        //        event(new MyEvent('Belgelendirme Başvuru bilgileri başarıyla eklenemedi, lütfen bilgilerinizi kontrol ediniz...<br>ya da planlama sayfasına geçiş için ==>' . $dugme));
        return json_encode($data);
      }
      $data = [
        'hata' => 0,
        'mesaj' => 'Başvuru bilgileri başarıyla eklenmiştir.<br>' . $dugme . $basvuruekmesaj . $basvuruekbgmesaj,
      ];
      //      event(new MyEvent('Başvuru bilgileri başarıyla eklenmiştir.<br>' . $dugme));
      return json_encode($data);
    }
    $data = ['hata' => 1, 'mesaj' => 'İşlenecek bilgi bulunamadı...'];
    return json_encode($data);
  }

  public function planKaydet(Request $request)
  {
    $kid = Auth::user()->kurulusid;
    $pot = [];
    $input = [];
    $req = $request->all();
    $pno = $req['planno'];
    $basvuru = DB::select(
      'SELECT * FROM basvuru where planno=' . $pno . ' and kid=' . $kid . ' ORDER BY planno DESC'
    )[0];

    foreach ($basvuru as $key => $val) {
      $input[$key] = $val;
    }

    foreach ($req as $key => $val) {
      $input[$key] = $val;

      if (str_starts_with($key, 'chb_indart9001')) {
        $pot['chb_indart9001']['planno'] = $input['planno'];
        $pot['chb_indart9001']['standart'] = '9001';
        $pot['chb_indart9001']['alan'] = $key;
        $pot['chb_indart9001']['oran'] = $val;
      }
      if (str_starts_with($key, 'chb_indart14001')) {
        $pot['chb_indart14001']['planno'] = $pno;
        $pot['chb_indart14001']['standart'] = '14001';
        $pot['chb_indart14001']['alan'] = $key;
        $pot['chb_indart14001']['oran'] = $val;
      }

      /* indirim/arttırım seçeneklerini kaydet */

      if (str_starts_with($key, 'chb_indart45001')) {
        $pot['chb_indart45001']['planno'] = $pno;
        $pot['chb_indart45001']['standart'] = '45001';
        $pot['chb_indart45001']['alan'] = $key;
        $pot['chb_indart45001']['oran'] = $val;
      }
    }
    //    $input = array_merge($basvuru, $requests);

    $asama = $input['asama'];
    $mesaj9001 = '';
    $mesaj14001 = '';
    $mesaj27001 = '';
    $mesaj45001 = '';
    $mesaj50001 = '';
    $mesaj22000 = '';
    $mesajSmiic = '';
    $mesaj9001indartsebepler = '';
    $mesaj14001indartsebepler = '';
    $mesaj45001indartsebepler = '';
    unset($input['_token']);

    $dizin = public_path();
    $pati = $dizin . '/setler/' . str_pad($pno, 4, '0', STR_PAD_LEFT);

    $iso9001 = isset($input['iso900115varyok']) ? $input['iso900115varyok'] : 0;
    $iso14001 = isset($input['iso1400115varyok']) ? $input['iso1400115varyok'] : 0;
    $iso45001 = isset($input['iso4500118varyok']) ? $input['iso4500118varyok'] : 0;
    $iso50001 = isset($input['iso5000118varyok']) ? $input['iso5000118varyok'] : 0;
    $iso27001 = isset($input['iso27001varyok']) ? $input['iso27001varyok'] : 0;
    $iso22000 = isset($input['iso2200018varyok']) ? $input['iso2200018varyok'] : 0;
    $oicsmiic = isset($input['helalvaryok']) ? $input['helalvaryok'] : 0;
    $oicsmiic6 = isset($input['oicsmiic6varyok']) ? $input['oicsmiic6varyok'] : 0;
    $oicsmiic9 = isset($input['oicsmiic9varyok']) ? $input['oicsmiic9varyok'] : 0;
    $oicsmiic171 = isset($input['oicsmiic171varyok']) ? $input['oicsmiic171varyok'] : 0;
    $oicsmiic23 = isset($input['oicsmiic23varyok']) ? $input['oicsmiic23varyok'] : 0;
    $oicsmiic24 = isset($input['oicsmiic24varyok']) ? $input['oicsmiic24varyok'] : 0;
    $belgelendirileceksistemler = $input['belgelendirileceksistemler'];
    $kapsamgenisletme = isset($input['kapsamgenisletme']) && $input['kapsamgenisletme'] == '1' ? 'var' : 'yok';
    $input['teknikalan'] = $iso50001 ? $input['enysteknikalan'] : '';
    $input['teknolojikalan'] = $iso27001 ? $input['bgcategories'] : '';

    $indartneden = str_replace(' (%-5)', '', $input['indartneden']);
    $indartneden = str_replace(' (%-20)', '', $indartneden);
    $indartneden = str_replace(' (%+5)', '', $indartneden);
    $indartneden = str_replace('(-10)', '', $indartneden);
    $indartneden = str_replace('(-30)', '', $indartneden);
    $indartneden = str_replace('(+10)', '', $indartneden);
    $indartneden = str_replace('10', '', $indartneden);
    $indartneden = str_replace('-10', '', $indartneden);
    $indartneden = str_replace('(-)', '', $indartneden);
    $input['indartneden'] = $indartneden;
    $input["subeadresid"] = "";

    $pot['plan']['asama'] = $asama;
    $pot['plan']['belgelendirileceksistemler'] = $belgelendirileceksistemler;
    $pot['plan']['eakodu'] = $input['eakodu'];
    $pot['plan']['nacekodu'] = $input['firmanacekodu'];
    $pot['plan']['kategori22'] = $input['categories'];
    $pot['plan']['kategorioic'] = $input['oiccategories'];
    $pot['plan']['kategoribgys'] = $input['bgcategories'];
    $pot['plan']['teknikalanenys'] = $input['enysteknikalan'];
    $pot['plan']['gozdengecirmetarihi'] = $input['gozdengecirmetarihi'];
    if ($asama === 'ilkplan') {
      $pot['plan']['asama1'] = $input['asama1'];
      $pot['plan']['tarihrevasama1'] = $input['tarihrevasama1'];
      $pot['plan']['bd1'] = $input['bd1'];
      $pot['plan']['d1'] = $input['d1'];
      $pot['plan']['tu1'] = $input['tu1'];
      $pot['plan']['g1'] = $input['g1'];
      $pot['plan']['iku1'] = $input['iku1'];
      $pot['plan']['ad1'] = $input['ad1'];
      $pot['plan']['sid1'] = $input['sid1'];
      $pot['plan']['asama2'] = $input['asama2'];
      $pot['plan']['tarihrevasama2'] = $input['tarihrevasama2'];
      $pot['plan']['bd2'] = $input['bd2'];
      $pot['plan']['d2'] = $input['d2'];
      $pot['plan']['tu2'] = $input['tu2'];
      $pot['plan']['g2'] = $input['g2'];
      $pot['plan']['iku2'] = $input['iku2'];
      $pot['plan']['ad2'] = $input['ad2'];
      $pot['plan']['sid2'] = $input['sid2'];
      $pot['plan']['belgecevrimi'] = 1;
    }
    if ($asama === 'g1') {
      $pot['plan']['gozetim1'] = $input['gozetim1'];
      $pot['plan']['tarihrevgozetim1'] = $input['tarihrevgozetim1'];
      $pot['plan']['gbd1'] = $input['gbd1'];
      $pot['plan']['gd1'] = $input['gd1'];
      $pot['plan']['gtu1'] = $input['gtu1'];
      $pot['plan']['gg1'] = $input['gg1'];
      $pot['plan']['ikug1'] = $input['ikug1'];
      $pot['plan']['adg1'] = $input['adg1'];
      $pot['plan']['sidg1'] = $input['sidg1'];
      $pot['plan']['belgecevrimi'] = $input['belgecevrimi'] ?? 1;
    }
    if ($asama === 'g2') {
      $pot['plan']['gozetim2'] = $input['gozetim2'];
      $pot['plan']['tarihrevgozetim2'] = $input['tarihrevgozetim2'];
      $pot['plan']['gbd2'] = $input['gbd2'];
      $pot['plan']['gd2'] = $input['gd2'];
      $pot['plan']['gtu2'] = $input['gtu2'];
      $pot['plan']['gg2'] = $input['gg2'];
      $pot['plan']['ikug2'] = $input['ikug2'];
      $pot['plan']['adg2'] = $input['adg2'];
      $pot['plan']['sidg2'] = $input['sidg2'];
      $pot['plan']['belgecevrimi'] = $input['belgecevrimi'] ?? 1;
    }
    if ($asama === 'yb') {
      $pot['plan']['ybtar'] = $input['ybtar'];
      $pot['plan']['tarihrevyb'] = $input['tarihrevyb'];
      $pot['plan']['ybbd'] = $input['ybbd'];
      $pot['plan']['ybd'] = $input['ybd'];
      $pot['plan']['ybtu'] = $input['ybtu'];
      $pot['plan']['ybg'] = $input['ybg'];
      $pot['plan']['ikuyb'] = $input['ikuyb'];
      $pot['plan']['adyb'] = $input['adyb'];
      $pot['plan']['sidyb'] = $input['sidyb'];
      $pot['plan']['belgecevrimi'] = $input['belgecevrimi'] ?? 1;
    }
    if ($asama === 'ozel') {
      $pot['plan']['ozeltar'] = $input['ozeltar'];
      $pot['plan']['tarihrevozel'] = $input['tarihrevozel'];
      $pot['plan']['otbd'] = $input['otbd'];
      $pot['plan']['otd'] = $input['otd'];
      $pot['plan']['ottu'] = $input['ottu'];
      $pot['plan']['otg'] = $input['otg'];
      $pot['plan']['ikuot'] = $input['ikuot'];
      $pot['plan']['adot'] = $input['adot'];
      $pot['plan']['sidot'] = $input['sidot'];
      $pot['plan']['belgecevrimi'] = $input['belgecevrimi'] ?? 1;
    }
    //    $pot["plan"]["istipi"] = $input["istipi"];
    $pot['plan']['entegreysvarmi'] = $input['indartentvarmi'];
    $pot['plan']['denetimeonerilendenetci'] = $input['denetimeonerilendenetci'];
    $pot['plan']['kararaonerilendenetci'] = $input['kararaonerilendenetci'];
    $pot['plan']['kararuonerilendenetciuye'] = $input['kararuonerilendenetciuye'];
    $pot['plan']['uyeikuadi'] = isset($input['uyeikuadi']) ? $input['uyeikuadi'] : '';
    $pot['plan']['belgelendirmedenetimucreti'] = $input['belgelendirmedenetimucreti'];
    $pot['plan']['gozetimdenetimucreti'] = $input['gozetimdenetimucreti'];
    $pot['plan']['belgelendirmedenetimucretihelal'] = isset($input['belgelendirmedenetimucretihelal']) ? $input['belgelendirmedenetimucretihelal'] : '';
    $pot['plan']['gozetimdenetimucretihelal'] = isset($input['gozetimdenetimucretihelal']) ? $input['gozetimdenetimucretihelal'] : '';
    $pot['plan']['tekliftarihi'] = $input['gozdengecirmetarihi'];
    $pot['plan']['planlamasorumlusu'] = Auth::user()->name;
    $pot['plan']['kid'] = Auth::user()->kurulusid;

    $afr01bfkontrol3 = strtotime('16.02.2024'); // öncesi afr.01 r15, sonrası afr.01 r16
    $afr01bfkontrol4 = strtotime('05.04.2024'); // öncesi afr.01 r16, sonrası afr.01 r17
    $afr02bggfkontrol1 = strtotime('10.07.2023'); // öncesi afr.02 r13, sonrası afr.02 r15
    $afr02bggfkontrol16 = strtotime('01.09.2023'); // öncesi afr.02 r15, sonrası afr.02 r16
    $afr02bggfkontrol17 = strtotime('06.11.2023'); // öncesi afr.02 r16, sonrası afr.02 r17
    $afr02bggfkontrol18 = strtotime('05.04.2024'); // öncesi afr.02 r17, sonrası afr.02 r18
    $afr02bggfkontrol19 = strtotime('20.09.2024'); // öncesi afr.02 r18, sonrası afr.02 r19
    $afr03btsfkontrol1 = strtotime('01.11.2021'); // öncesi afr.03 r8, sonrası afr.03 r9
    $afr50htsfkontrol4 = strtotime('20.09.2024'); // öncesi afr.50 r3, sonrası afr.50 r4
    $afr51nafkontrol2 = strtotime('20.09.2024'); // öncesi afr.51 r1, sonrası afr.51 r2
    $afr05dpkontrol2 = strtotime('19.01.2024'); // öncesi afr.05 r12, sonrası afr.05 r13
    $afr06debfkontrol1 = strtotime('06.11.2023'); // öncesi afr.06 r05, sonrası afr.06 r06
    $afr08a1rpkontrol2 = strtotime('20.03.2024'); // öncesi afr.08 r6, sonrası afr.08 r7
    $afr09a2rpkontrol2 = strtotime('20.03.2024'); // öncesi afr.09 r7, sonrası afr.09 r8

    $input['belgelendirileceksistemler'] = $belgelendirileceksistemler;
    $input['nacekodu'] = $input['firmanacekodu'];
    $input['kategori22'] = $input['categories'];
    $input['kategorioic'] = $input['oiccategories'];
    $input['kategoribgys'] = $input['bgcategories'];
    $input['teknikalanenys'] = $input['enysteknikalan'];
    $input['tekliftarihi'] = $input['gozdengecirmetarihi'];
    $input['teklifno'] = $pno;
    $input['denetimdili'] = 'Türkçe';
    $input['yonetimsistemsertifikasi'] = isset($input['yonetimsistemsertifikasi'])
      ? $input['yonetimsistemsertifikasi']
      : 0;

    /*#################################################################################################################*/
    //    var_dump($input);
    $input['isognormalsure'] = number_format(floatval($input['toplamgsure']), 2);
    $input['azaltmaarttirmaorani'] = number_format(
      floatval($input['denetimentegreindirim'] ?? 0) +
        floatval($input['denetimgunazaltilmasi'] ?? 0) +
        floatval($input['denetimgunarttirilmasi'] ?? 0),
      2
    );

    $indartsay = 0;
    $standartsay = 0;
    if ($input['indart9001varmi'] == '1') {
      $indartsay++;
    }
    if ($input['indart14001varmi'] == '1') {
      $indartsay++;
    }
    if ($input['indart45001varmi'] == '1') {
      $indartsay++;
    }
    if ($input['indart50001varmi'] == '1') {
      $indartsay++;
    }
    if ($input['indartentvarmi'] == '1') {
      $indartsay++;
    }
    if ($input['art22000varmi'] == '1') {
      $indartsay++;
    }
    if ($input['indartoicsmiicvarmi'] == '1') {
      $indartsay++;
    }

    if ($iso9001) {
      $standartsay++;
    }
    if ($iso14001) {
      $standartsay++;
    }
    if ($iso45001) {
      $standartsay++;
    }
    if ($iso50001) {
      $standartsay++;
    }
    if ($iso27001) {
      $standartsay++;
    }

    if ($iso22000) {
      $standartsay++;
      $input['inceleneceksahasayisi'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];
      $input['inceleneceksahasayisig'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];
      $input['inceleneceksahasayisiybr'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];

      $input['anadenetimsuresi'] = session()->get('anadenetimsuresi');
      $input['ekhaccpsuresi'] = session()->get('ekhaccpsuresi');
      //      $input["mevcutys"] = session()->get("mevcutys");
      $input["mevcutys"] = "";
      $input['ftecalisansayisi'] = session()->get('ftecalisansayisi');
      $input['tsenkisasure'] = session()->get('tsenkisasure');
      $input['extralan'] = session()->get('extralan');
      $input['sonuc22000'] = self::$sonuc22000;
    } else {
      $input['inceleneceksahasayisi'] = $input['sahasayisi22'] == '' ? '' : ceil(sqrt(intval($input['sahasayisi22'])));
      $input['inceleneceksahasayisig'] =
        $input['sahasayisi22'] == '' ? '' : ceil(0.6 * sqrt(intval($input['sahasayisi22'])));
      $input['inceleneceksahasayisiybr'] =
        $input['sahasayisi22'] == '' ? '' : ceil(0.8 * sqrt(intval($input['sahasayisi22'])));

      $input['anadenetimsuresi'] = '';
      $input['ekhaccpsuresi'] = '';
      $input["mevcutys"] = "";
      $input['ftecalisansayisi'] = '';
      $input['tsenkisasure'] = '';
      $input['extralan'] = '';
      $input['sonuc22000'] = '';
    }

    $input['cckarmasiklik'] = '';
    $input['pvdenetimgun'] = '';
    $input['sonuchelal'] = '';

    if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
      if (substr($input['oicsmiickk'], 0, 1) == 4) {
        $input['cckarmasiklik'] = '2';
      }
      if (substr($input['oicsmiickk'], 0, 1) == 3) {
        $input['cckarmasiklik'] = '1.75';
      }
      if (substr($input['oicsmiickk'], 0, 1) == 2) {
        $input['cckarmasiklik'] = '1.5';
      }
      if (substr($input['oicsmiickk'], 0, 1) == 1) {
        $input['cckarmasiklik'] = '1.25';
      }
      if (intval($input['helalurunsayisi']) >= 1 && intval($input['helalurunsayisi']) <= 3) {
        $input['pvdenetimgun'] = '0.5';
      }
      if (intval($input['helalurunsayisi']) >= 4 && intval($input['helalurunsayisi']) <= 6) {
        $input['pvdenetimgun'] = '1';
      }
      if (intval($input['helalurunsayisi']) >= 7 && intval($input['helalurunsayisi']) <= 10) {
        $input['pvdenetimgun'] = '1.5';
      }
      if (intval($input['helalurunsayisi']) >= 11 && intval($input['helalurunsayisi']) <= 20) {
        $input['pvdenetimgun'] = '2';
      }
      if (intval($input['helalurunsayisi']) > 20) {
        $input['pvdenetimgun'] = '3';
      }
      if (
        $input['helalurunsayisi'] == '' ||
        ($input['helalurunsayisi'] != '' && intval($input['helalurunsayisi']) < 1)
      ) {
        $input['pvdenetimgun'] = '0';
      }
      $standartsay++;
      $input['inceleneceksahasayisi'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];
      $input['inceleneceksahasayisig'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];
      $input['inceleneceksahasayisiybr'] = $input['sahasayisi22'] == '' ? '' : $input['sahasayisi22'];

      $input['oicanadenetimsuresi'] = session()->get('oicanadenetimsuresi');
      $input['oicekhaccpsuresi'] = session()->get('oicekhaccpsuresi');
      $input['oicftecalisansayisi'] = session()->get('oicftecalisansayisi');
      $input['oictsenkisasure'] = session()->get('oictsenkisasure');
      $input['oicextralan'] = session()->get('oicextralan');
      //            $input["cckarmasiklik"] = session()->get("oiccc");
      //            $input["pvdenetimgun"] = session()->get("oicpv");
      $input['sonucoicsmiic'] = session()->get('sonucoicsmiic');
      $input['haccpcalismasisayisi'] = $input['haccpcalismasisayisismiic'];
    } else {
      $input['inceleneceksahasayisi'] = $input['sahasayisi22'] == '' ? '' : ceil(sqrt(intval($input['sahasayisi22'])));
      $input['inceleneceksahasayisig'] =
        $input['sahasayisi22'] == '' ? '' : ceil(0.6 * sqrt(intval($input['sahasayisi22'])));
      $input['inceleneceksahasayisiybr'] =
        $input['sahasayisi22'] == '' ? '' : ceil(0.8 * sqrt(intval($input['sahasayisi22'])));
      $input['oicanadenetimsuresi'] = '';
      $input['oicekhaccpsuresi'] = '';
      $input['oicftecalisansayisi'] = '';
      $input['oictsenkisasure'] = '';
      $input['oicextralan'] = '';
      $input['cckarmasiklik'] = '';
      $input['pvdenetimgun'] = '';
      $input['sonucoicsmiic'] = '';
    }

    $input['entegreyuzdesi'] =
      floatval($input['ygg']) +
      floatval($input['icdenetim']) +
      floatval($input['politikahedefler']) +
      floatval($input['prosesentegre']) +
      floatval($input['entegredokumantasyon']) +
      floatval($input['duzelticifaaliyet']) +
      floatval($input['riskyonetimyaklasimi']) +
      floatval($input['yondessor']);

    $input['entegreazaltmaorani'] = str_replace('%', '', $input['totoranEntegre'] ?? '');
    $input['merkeza1sure'] = isset($input['iso9001hamsure']) ? $input['iso9001hamsure'] : '';
    $input['merkeza2sure'] = isset($input['iso14001hamsure']) ? $input['iso14001hamsure'] : '';
    $input['merkeza3sure'] = isset($input['iso22000hamsure']) ? $input['iso22000hamsure'] : '';
    $input['merkez45sure'] = isset($input['iso45001hamsure']) ? $input['iso45001hamsure'] : '';
    $input['merkez50sure'] = isset($input['iso50001hamsure']) ? $input['iso50001hamsure'] : '';
    $input['merkez27sure'] = isset($input['iso27001hamsure']) ? $input['iso27001hamsure'] : '';
    $input['helalsure'] = isset($input['isooicsmiichamsure']) ? $input['isooicsmiichamsure'] : '';
    $input['helalsure17'] = isset($input['isooicsmiichamsure']) ? $input['isooicsmiichamsure'] : '';
    $input['denetimgunazart9'] = !isset($input['totoran9001'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran9001']));
    $input['denetimgunazart14'] = !isset($input['totoran14001'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran14001']));
    $input['denetimgunazart45'] = !isset($input['totoran45001'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran45001']));
    $input['denetimgunazart50'] = !isset($input['totoran50001'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran50001']));
    $input['denetimgunazart27'] = !isset($input['totoran27001'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran27001']));
    $input['denetimgunazart22'] = !isset($input['totoran22000'])
      ? ''
      : floatval(str_replace('%', '', $input['totoran22000']));
    $input['denetimgunazarthelal'] = !isset($input['totoranoicsmiic'])
      ? ''
      : floatval(str_replace('%', '', $input['totoranoicsmiic']));
    $input['helalindirimorani'] = isset($input['totoranoicsmiic'])
      ? str_replace('%', '', $input['totoranoicsmiic'])
      : '';
    $input['helalysoorani'] = isset($input['chb_indartsmiic67']) ? '-30' : '';
    $input['smiic22000arttirma'] = isset($input['chb_indartsmiic73']) ? '10' : '';

    $input['sonuctoplamadamgun'] = number_format(floatval($input['toplamkalansure']), 2);
    $input['merkezyuzdelika1'] = number_format(floatval($input['toplama1sure']), 2);
    $input['merkezyuzdelika2'] = number_format(floatval($input['toplama2sure']), 2);
    $input['merkezyuzdelikg'] = number_format(floatval($input['toplamgsure']), 2);
    $input['merkezyuzdelikybr'] = number_format(floatval($input['toplamybsure']), 2);
    $input['merkezhersahasure'] = number_format(
      floatval($input['merkezyuzdelika1']) + floatval($input['merkezyuzdelika2']),
      2
    );

    $input['a1denetimzamani'] = number_format($input['merkezyuzdelika1'], 2);
    $input['a2denetimzamani'] = number_format($input['merkezyuzdelika2'], 2);
    $input['gdenetimzamani'] = number_format($input['merkezyuzdelikg'], 2);
    $input['ybrdenetimzamani'] = number_format($input['merkezyuzdelikybr'], 2);

    $input['gdenetimzamani'] = floatval($input['gdenetimzamani']) < 1 ? '1.00' : $input['gdenetimzamani'];
    $input['denetimgunsuresi'] = floatval($input['gdenetimzamani']) < 1 ? '1.00' : floatval($input['gdenetimzamani']);

    $input['sahaadresleri'] = $input['subeadresia'] . "\r\n\r";
    $input['sahaadresleri'] .= $input['subeadresib'] . "\r\n\r";
    $input['sahaadresleri'] .= $input['subeadresic'] . "\r\n\r";
    $input['sahaadresleri'] .= $input['subeadresid'] . "\r\n\r";

    /* normal değerler */
    $input['iso9001a1sure'] =
      isset($input['iso9001a1sure']) && floatval($input['iso9001a1sure']) > 0
        ? number_format($input['iso9001a1sure'], 2)
        : '';
    $input['iso9001a2sure'] =
      isset($input['iso9001a2sure']) && floatval($input['iso9001a2sure']) > 0
        ? number_format($input['iso9001a2sure'], 2)
        : '';
    $input['iso9001gsure'] =
      isset($input['iso9001gsure']) && floatval($input['iso9001gsure']) > 0
        ? number_format($input['iso9001gsure'], 2)
        : '';
    $input['iso9001ybsure'] =
      isset($input['iso9001ybsure']) && floatval($input['iso9001ybsure']) > 0
        ? number_format($input['iso9001ybsure'], 2)
        : '';
    $input['iso14001a1sure'] =
      isset($input['iso14001a1sure']) && floatval($input['iso14001a1sure']) > 0
        ? number_format($input['iso14001a1sure'], 2)
        : '';
    $input['iso14001a2sure'] =
      isset($input['iso14001a2sure']) && floatval($input['iso14001a2sure']) > 0
        ? number_format($input['iso14001a2sure'], 2)
        : '';
    $input['iso14001gsure'] =
      isset($input['iso14001gsure']) && floatval($input['iso14001gsure']) > 0
        ? number_format($input['iso14001gsure'], 2)
        : '';
    $input['iso14001ybsure'] =
      isset($input['iso14001ybsure']) && floatval($input['iso14001ybsure']) > 0
        ? number_format($input['iso14001ybsure'], 2)
        : '';
    $input['iso45001a1sure'] =
      isset($input['iso45001a1sure']) && floatval($input['iso45001a1sure']) > 0
        ? number_format($input['iso45001a1sure'], 2)
        : '';
    $input['iso45001a2sure'] =
      isset($input['iso45001a2sure']) && floatval($input['iso45001a2sure']) > 0
        ? number_format($input['iso45001a2sure'], 2)
        : '';
    $input['iso45001gsure'] =
      isset($input['iso45001gsure']) && floatval($input['iso45001gsure']) > 0
        ? number_format($input['iso45001gsure'], 2)
        : '';
    $input['iso45001ybsure'] =
      isset($input['iso45001ybsure']) && floatval($input['iso45001ybsure']) > 0
        ? number_format($input['iso45001ybsure'], 2)
        : '';
    $input['iso50001a1sure'] =
      isset($input['iso50001a1sure']) && floatval($input['iso50001a1sure']) > 0
        ? number_format($input['iso50001a1sure'], 2)
        : '';
    $input['iso50001a2sure'] =
      isset($input['iso50001a2sure']) && floatval($input['iso50001a2sure']) > 0
        ? number_format($input['iso50001a2sure'], 2)
        : '';
    $input['iso50001gsure'] =
      isset($input['iso50001gsure']) && floatval($input['iso50001gsure']) > 0
        ? number_format($input['iso50001gsure'], 2)
        : '';
    $input['iso50001ybsure'] =
      isset($input['iso50001ybsure']) && floatval($input['iso50001ybsure']) > 0
        ? number_format($input['iso50001ybsure'], 2)
        : '';
    $input['iso27001a1sure'] =
      isset($input['iso27001a1sure']) && floatval($input['iso27001a1sure']) > 0
        ? number_format($input['iso27001a1sure'], 2)
        : '';
    $input['iso27001a2sure'] =
      isset($input['iso27001a2sure']) && floatval($input['iso27001a2sure']) > 0
        ? number_format($input['iso27001a2sure'], 2)
        : '';
    $input['iso27001gsure'] =
      isset($input['iso27001gsure']) && floatval($input['iso27001gsure']) > 0
        ? number_format($input['iso27001gsure'], 2)
        : '';
    $input['iso27001ybsure'] =
      isset($input['iso27001ybsure']) && floatval($input['iso27001ybsure']) > 0
        ? number_format($input['iso27001ybsure'], 2)
        : '';
    $input['iso22000a1sure'] =
      isset($input['iso22000a1sure']) && floatval($input['iso22000a1sure']) > 0
        ? number_format($input['iso22000a1sure'], 2)
        : '';
    $input['iso22000a2sure'] =
      isset($input['iso22000a2sure']) && floatval($input['iso22000a2sure']) > 0
        ? number_format($input['iso22000a2sure'], 2)
        : '';
    $input['iso22000gsure'] =
      isset($input['iso22000gsure']) && floatval($input['iso22000gsure']) > 0
        ? number_format($input['iso22000gsure'], 2)
        : '';
    $input['iso22000ybsure'] =
      isset($input['iso22000ybsure']) && floatval($input['iso22000ybsure']) > 0
        ? number_format($input['iso22000ybsure'], 2)
        : '';
    $input['oicsmiica1sure'] =
      isset($input['oicsmiica1sure']) && floatval($input['oicsmiica1sure']) > 0
        ? number_format($input['oicsmiica1sure'], 2)
        : '';
    $input['oicsmiica2sure'] =
      isset($input['oicsmiica2sure']) && floatval($input['oicsmiica2sure']) > 0
        ? number_format($input['oicsmiica2sure'], 2)
        : '';
    $input['oicsmiicgsure'] =
      isset($input['oicsmiicgsure']) && floatval($input['oicsmiicgsure']) > 0
        ? number_format($input['oicsmiicgsure'], 2)
        : '';
    $input['oicsmiicybsure'] =
      isset($input['oicsmiicybsure']) && floatval($input['oicsmiicybsure']) > 0
        ? number_format($input['oicsmiicybsure'], 2)
        : '';

    /* yuvarlatılmış deüerler */
    $input['iso9001a1sureduz'] =
      isset($input['iso9001a1sure']) && floatval($input['iso9001a1sure']) > 0
        ? number_format($input['iso9001a1sure'], 2)
        : '';
    $input['iso9001a2sureduz'] =
      isset($input['iso9001a2sure']) && floatval($input['iso9001a2sure']) > 0
        ? number_format($input['iso9001a2sure'], 2)
        : '';
    $input['iso9001gsureduz'] =
      isset($input['iso9001gsure']) && floatval($input['iso9001gsure']) > 0
        ? number_format($input['iso9001gsure'], 2)
        : '';
    $input['iso9001ybrsureduz'] =
      isset($input['iso9001ybsure']) && floatval($input['iso9001ybsure']) > 0
        ? number_format($input['iso9001ybsure'], 2)
        : '';
    $input['iso14001a1sureduz'] =
      isset($input['iso14001a1sure']) && floatval($input['iso14001a1sure']) > 0
        ? number_format($input['iso14001a1sure'], 2)
        : '';
    $input['iso14001a2sureduz'] =
      isset($input['iso14001a2sure']) && floatval($input['iso14001a2sure']) > 0
        ? number_format($input['iso14001a2sure'], 2)
        : '';
    $input['iso14001gsureduz'] =
      isset($input['iso14001gsure']) && floatval($input['iso14001gsure']) > 0
        ? number_format($input['iso14001gsure'], 2)
        : '';
    $input['iso14001ybrsureduz'] =
      isset($input['iso14001ybsure']) && floatval($input['iso14001ybsure']) > 0
        ? number_format($input['iso14001ybsure'], 2)
        : '';
    $input['iso45001a1sureduz'] =
      isset($input['iso45001a1sure']) && floatval($input['iso45001a1sure']) > 0
        ? number_format($input['iso45001a1sure'], 2)
        : '';
    $input['iso45001a2sureduz'] =
      isset($input['iso45001a2sure']) && floatval($input['iso45001a2sure']) > 0
        ? number_format($input['iso45001a2sure'], 2)
        : '';
    $input['iso45001gsureduz'] =
      isset($input['iso45001gsure']) && floatval($input['iso45001gsure']) > 0
        ? number_format($input['iso45001gsure'], 2)
        : '';
    $input['iso45001ybrsureduz'] =
      isset($input['iso45001ybsure']) && floatval($input['iso45001ybsure']) > 0
        ? number_format($input['iso45001ybsure'], 2)
        : '';
    $input['iso50001a1sureduz'] =
      isset($input['iso50001a1sure']) && floatval($input['iso50001a1sure']) > 0
        ? number_format($input['iso50001a1sure'], 2)
        : '';
    $input['iso50001a2sureduz'] =
      isset($input['iso50001a2sure']) && floatval($input['iso50001a2sure']) > 0
        ? number_format($input['iso50001a2sure'], 2)
        : '';
    $input['iso50001gsureduz'] =
      isset($input['iso50001gsure']) && floatval($input['iso50001gsure']) > 0
        ? number_format($input['iso50001gsure'], 2)
        : '';
    $input['iso50001ybrsureduz'] =
      isset($input['iso50001ybsure']) && floatval($input['iso50001ybsure']) > 0
        ? number_format($input['iso50001ybsure'], 2)
        : '';
    $input['iso27001a1sureduz'] =
      isset($input['iso27001a1sure']) && floatval($input['iso27001a1sure']) > 0
        ? number_format($input['iso27001a1sure'], 2)
        : '';
    $input['iso27001a2sureduz'] =
      isset($input['iso27001a2sure']) && floatval($input['iso27001a2sure']) > 0
        ? number_format($input['iso27001a2sure'], 2)
        : '';
    $input['iso27001gsureduz'] =
      isset($input['iso27001gsure']) && floatval($input['iso27001gsure']) > 0
        ? number_format($input['iso27001gsure'], 2)
        : '';
    $input['iso27001ybrsureduz'] =
      isset($input['iso27001ybsure']) && floatval($input['iso27001ybsure']) > 0
        ? number_format($input['iso27001ybsure'], 2)
        : '';
    $input['iso22000a1sureduz'] =
      isset($input['iso22000a1sure']) && floatval($input['iso22000a1sure']) > 0
        ? number_format($input['iso22000a1sure'], 2)
        : '';
    $input['iso22000a2sureduz'] =
      isset($input['iso22000a2sure']) && floatval($input['iso22000a2sure']) > 0
        ? number_format($input['iso22000a2sure'], 2)
        : '';
    $input['iso22000gsureduz'] =
      isset($input['iso22000gsure']) && floatval($input['iso22000gsure']) > 0
        ? number_format($input['iso22000gsure'], 2)
        : '';
    $input['iso22000ybrsureduz'] =
      isset($input['iso22000ybsure']) && floatval($input['iso22000ybsure']) > 0
        ? number_format($input['iso22000ybsure'], 2)
        : '';
    $input['oicsmiica1sureduz'] =
      isset($input['oicsmiica1sure']) && floatval($input['oicsmiica1sure']) > 0
        ? number_format($input['oicsmiica1sure'], 2)
        : '';
    $input['oicsmiica2sureduz'] =
      isset($input['oicsmiica2sure']) && floatval($input['oicsmiica2sure']) > 0
        ? number_format($input['oicsmiica2sure'], 2)
        : '';
    $input['oicsmiicgsureduz'] =
      isset($input['oicsmiicgsure']) && floatval($input['oicsmiicgsure']) > 0
        ? number_format($input['oicsmiicgsure'], 2)
        : '';
    $input['oicsmiicybrsureduz'] =
      isset($input['oicsmiicybsure']) && floatval($input['oicsmiicybsure']) > 0
        ? number_format($input['oicsmiicybsure'], 2)
        : '';

    /*#################################################################################################################*/

    self::removePlanDir($pno);
    $input['denetimbastarihi'] = '';
    $input['denetimbitistarihi'] = '';
    $input['gozdengecirmetarihi'] = '';

    $filedizin1 = $dizin. '/sablonlar/sistem/asama1/';
    $filedizin2 = $dizin. '/sablonlar/sistem/asama2/';

    //    var_dump($input);
    if ($asama == 'ilkplan') {
      /* AŞAMA 1 */
      //            var_dump($pot);
      $input['riskvekarmasiklik'] =
        '(ISO 9001)/' .
        $input['riskgrubu9'] .
        ', (ISO 14001/ISO 45001)/' .
        $input['riskgrubu14'] .
        '/' .
        $input['riskgrubu45'];
      $dzamani = floatval($input['toplama1sure']) * 8;
      $dsuresi = $dzamani * 0.8;
      $dsuresi = number_format($dsuresi, 2);

      $input['denetimzamani'] = $dzamani . " / " .$dsuresi;
      $input['bildirimtarihi'] = $input['tarihrevasama1'];
      $input['denetimasama'] = 'İlk Belgelendirme – Aşama 1';
      $input['denetimtipi'] = 'ilk';

      $input['denetimtarihasama1'] = $input['asama1'];
      $input['denetimtarihleri'] = $input['asama1'];
      $input['basdenetciadi'] = $input['bd1'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['bd1']
      );
      $input['standartbasdenetciadi'] = self::atananSistemler($input['bd1'], $belgelendirileceksistemler);
      $input['standartkarardenetciadi'] = self::atananSistemler(
        $input['kararaonerilendenetci'],
        $belgelendirileceksistemler
      );
      $input['standartkararikuadi'] = '';
      if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
        $input['standartkararikuadi'] = 'İslami Konular Uzmanı';
      }
      $karardenetci = explode(',', $input['kararuonerilendenetciuye']);
      if (count($karardenetci) > 1) {
        $i = 1;
        foreach ($karardenetci as $denetci) {
          $input['uye' . $i . 'adi'] = trim($denetci);
          $i++;
        }
      }

      $kacdenetci = explode(',', $input['d1']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['d1' . $i] = trim($denetci);
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);
          $i++;
        }
      } else {
        $input['d11'] = $input['d1'];
        $input['denetciadi1'] = $input['d1'];
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['d1']), $belgelendirileceksistemler);
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['d1']
        );
        $input['denetciadi2'] = '';
        $input['d12'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['not22'] = '';
        $input['denetciadi3'] = '';
        $input['d13'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['not23'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }
      $input['iuzm'] = $input['iku1'];
      $input['islamiuzmanadi'] = $input['iku1'];
      $input['standartislamiuzmanadi'] =
        $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24 ? 'İslami Konular' : '';
      $input['eanacekodlariislamiuzman'] = '';

      $input['teknikuzmanadi1'] = $input['tu1'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['tu11'] = $input['tu1'];
      $input['tu12'] = '';
      $input['tu13'] = '';
      $input['adaydenetci'] = $input['ad1'];
      $input['standartadaydenetci'] = self::atananSistemler($input['ad1'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sid1'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sid1'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['g1'];
      $input['standartteknikuzmanadi1'] = self::atananSistemler($input['tu1'], $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';
      $input['standartgozlemciadi1'] = self::atananSistemler($input['g1'], $belgelendirileceksistemler);

      $input['eanacekodlariteknikuzman1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['tu1']
      );
      $input['eanacekodlariteknikuzman2'] = '';
      $input['eanacekodlariteknikuzman3'] = '';

      $input['eanacekodlarigozlemci1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['g1']
      );

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ad1']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sid1']
      );

      $input['eanacekodlarikomitebaskani'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['kararaonerilendenetci']
      );

      $input['eanacekodlariuye'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['kararuonerilendenetciuye']
      );

      $input['eanacekodlari'] = $input['firmanacekodu'];

      //      $denetimekibi = $input["bd1"] . "," . $input["d1"] . "," . $input["tu1"] . "," . $input["g1"];
      //echo "<br><br>";

      $denetcisay = count($kacdenetci) - 1 + 1;
      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $denplani = count($dentars);
        if (count($dentars) == 2) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
        if (count($dentars) == 3) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimortatarihi'] = $dentars[1];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];

      $kacdenetci = explode(',', $input['d2']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i . 'a2'] = trim($denetci);
          $input['eanacekodlaridenetci' . $i . 'a2'] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $i++;
        }
      } else {
        $input['denetciadi1a2'] = $input['d2'];
        $input['eanacekodlaridenetci1a2'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['d2']
        );
        $input['denetciadi2a2'] = '';
        $input['eanacekodlaridenetci2a2'] = '';
        $input['denetciadi3a2'] = '';
        $input['eanacekodlaridenetci3a2'] = '';
      }
      $input['basdenetciadia2'] = $input['bd2'];
      $input['eanacekodlaribasdenetcia2'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['bd2']
      );
      $input['teknikuzmanadi1a2'] = $input['tu2'];
      $input['teknikuzmanadi2a2'] = '';
      $input['teknikuzmanadi3a2'] = '';

      $input['eanacekodlariteknikuzman1a2'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['tu2']
      );
      $input['eanacekodlariteknikuzman2a2'] = '';
      $input['eanacekodlariteknikuzman3a2'] = '';

      $input['gozlemciadi1a2'] = $input['g2'];
      $input['eanacekodlarigozlemci1a2'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['g2']
      );

      /* Şablonlar/A */
      $sinir = strtotime($input['denetimbastarihi']);
      $ggtarihi = strtotime($input['gozdengecirmetarihi']);

      echo '<div class="row"><div class="col-lg-4">
                    <div class="block">
                        <div class="block-head">
                            <div class="block-title">I. AŞAMA</div>
                        </div>
                        <div class="block-content">
                        <div class="btn-group btn-group-vertical">';
      //echo "I. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/I. ASAMA';
      $asamaklasor = 'I. ASAMA';

      $filem = $filedizin1 . 'AFR.01BelgelendirmeBasvuruFormu-R17.docx';
      $newFilem = 'AFR.01 Belgelendirme Basvuru Formu-R17.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso50001) {
        $filem = $filedizin1 . 'AFR.01Ek1EnYSBelgelendirmeBasvuruKontrolFormu-R1.docx';
        $newFilem = 'AFR.01-Ek1 EnYS Belgelendirme Basvuru Kontrol Formu-R1.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }
      if ($oicsmiic9) {
        $filem = $filedizin1 . 'AFR.01Ek2HTSBelgelendirmeBasvuruKontrolFormu-R1.docx';
        $newFilem = 'AFR.01-Ek2 HTS Belgelendirme Basvuru Kontrol Formu-R1.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }
      if ($iso27001) {
        $filem = $filedizin1 . 'AFR.01Ek3BGYSBelgelendirmeBasvuruKontrolFormu-R1.docx';
        $newFilem = 'AFR.01-Ek3 BGYS Belgelendirme Basvuru Kontrol Formu-R1.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.02BasvuruGozdenGecirme-R19.docx';
      $newFilem = 'AFR.02 Basvuru Gozden Gecirme-R19.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.03BelgelendirmeTeklifSozlesmeFormu-R9.docx';
      $newFilem = 'AFR.03 Belgelendirme Teklif Sozlesme Formu-R9.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.05DenetimProgrami-R13.docx';
      $newFilem = 'AFR.05 Denetim Programi-R13.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

        $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
        $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

        $filem = $filedizin1 . 'AFR.08Asama1DenetimRaporuR7.docx';
        $newFilem = 'AFR.08 Asama1 Denetim Raporu-R7.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
        $filem = $filedizin1 . 'AFR.49HelalBelgelendirmeTeklifi-R0.docx';
        $newFilem = 'AFR.49 Helal Belgelendirme Teklifi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

        $filem = $filedizin1 . 'AFR.50HelalBelgelendirmeSozlesmesi-R4.docx';
        $newFilem = 'AFR.50 Helal Belgelendirme Sozlesmesi-R4.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

        $filem = $filedizin1 . 'AFR.51NumuneAlmaFormu-Rev.2.docx';
        $newFilem = 'AFR.51 Numune Alma Formu Rev.2.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      echo '</div></div></div></div>';

      /* ################################################  AŞAMA 2 ######################################################*/

      $dzamani = floatval($input['toplama2sure']) * 8;
      $dsuresi = self::roundUpTo5($dzamani * 0.8);
      $dsuresi = number_format($dsuresi, 2);

      $input['denetimzamani'] = $dzamani . " / " . $dsuresi;
      $input['bildirimtarihi'] = $input['tarihrevasama2'];
      $input['denetimasama'] = 'İlk Belgelendirme – Aşama 2';
      $input['denetimtipi'] = 'ilk';

      $kacdenetci = explode(',', $input['d2']);

      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);

          $i++;
        }
      } else {
        $input['denetciadi1'] = $input['d2'];
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'] ?? '',
          $input['firmanacekodu'] ?? '',
          $input['categories'] ?? '',
          $input['oiccategories'] ?? '',
          $input['enysteknikalan'] ?? '',
          $input['bgcategories'] ?? '',
          $input['d2'] ?? ''
        );
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['d2']) ?? '', $belgelendirileceksistemler);
        $input['denetciadi2'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['denetciadi3'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }

      $input['a1denetimtarihleri'] = $input['asama1'];
      $input['denetimtarihasama2'] = $input['asama2'];
      $input['denetimtarihleri'] = $input['asama2'];
      $input['basdenetciadi'] = $input['bd2'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['bd2']
      );
      $input['teknikuzmanadi1'] = $input['tu2'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['adaydenetci'] = $input['ad2'];
      $input['standartadaydenetci'] = self::atananSistemler($input['ad2'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sid2'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sid2'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['g2'];
      $input['standartteknikuzmanadi1'] = self::atananSistemler(trim($input['tu2']), $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';

      $input['standartgozlemciadi1'] = self::atananSistemler($input['g2'], $belgelendirileceksistemler);
      $input['islamiuzmanadi'] = $input['iku2'];
      $input['standartislamiuzmanadi'] =
        $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24 ? 'İslami Konular' : '';
      $input['eanacekodlariislamiuzman'] = '';

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ad2']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sid2']
      );

      $input['eanacekodlari'] = $input['firmanacekodu'];

      $denetimekibi = $input['bd2'] . ',' . $input['d2'] . ',' . $input['tu2'] . ',' . $input['g2'];
      //echo "<br><br>";

      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $input['denetimbastarihi'] = $dentars[0];
        $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
        $input['toplantiacilistarihi'] = $dentars[0];
        $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];
      $sinir = strtotime($input['denetimbastarihi']);

      //echo "<br><br>";
      echo '<div class="col-lg-4">
                    <div class="block">
                    <div class="block-head">
                        <div class="block-title">II. AŞAMA</div>
                    </div>
                    <div class="block-content"><div class="btn-group btn-group-vertical">';
      //echo "II. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/II. ASAMA';
      $asamaklasor = 'II. ASAMA';


      $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
        $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso27001) {
        $filem = $filedizin2 . 'AFR.09Ek1DenetimRaporuR0.docx';
        $newFilem = 'AFR.09DenetimRaporuR8_temp.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      } else {
        if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
          $filem = $filedizin2 . 'AFR.09Ek2OICSMIICDenetimRaporuR0.docx';
          $newFilem = 'AFR.09-Ek2 OIC SMIIC Denetim Raporu R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-1OIC-SMIICDenetimRaporuBuyukbasKucukbasKesimiR0.docx';
          $newFilem = 'AFR.09-Ek2-1 OIC-SMIIC Denetim Raporu Buyukbas Kucukbas Kesimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-4OIC-SMIICDenetimRaporuBalikveBalikUrunleriR0.docx';
          $newFilem = 'AFR.09-Ek2-4 OIC-SMIIC Denetim Raporu Balik ve Balik Urunleri R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem =
            $filedizin2 . 'AFR.09-Ek2-9OIC-SMIICDenetimRaporuDayaniksizCabukBozulabilenHayvansalveBitkiselUrunlerinIslenmesiR0.docx';
          $newFilem =
            'AFR.09-Ek2-9 OIC-SMIIC Denetim Raporu Dayaniksiz Cabuk Bozulabilen Hayvansal ve Bitkisel Urunlerin Islenmesi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-10OIC-SMIICDenetimRaporuKativeSiviYaglarR0.docx';
          $newFilem = 'AFR.09-Ek2-10 OIC-SMIIC Denetim Raporu Kati ve Sivi Yaglar R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-24OIC-SMIICDenetimRaporuBiyokimyasallarinUretimiR0.docx';
          $newFilem = 'AFR.09-Ek2-24 OIC-SMIIC Denetim Raporu Biyokimyasallarin Uretimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
        if ($iso9001 || $iso14001 || $iso45001 || $iso22000 || $iso50001 || $iso27001) {
          $filem = $filedizin2 . 'AFR.09DenetimRaporuR8.docx';
          $newFilem = 'AFR.09 Denetim Raporu-R8.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
      }

      if ($oicsmiic171) {
        $filem =
          $filedizin2 . 'AFR.09-Ek2-20OIC-SMIICDenetimRaporuDayanikliveDayaniksizUrunlerinNakliyesi-R0.docx';
        $newFilem = 'AFR.09-Ek2-20 OIC-SMIIC Denetim Raporu Dayanikli ve Dayaniksiz Urunlerin Nakliyesi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin2 . 'AFR.21DenetimEkibiDegerlendirmeFormu-MusteriR1.docx';
      $newFilem = 'AFR.21 Denetim Ekibi Degerlendirme Formu-Musteri Rev.01.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, true, $asamaklasor);

      echo '</div></div></div></div>';
    }

    if ($asama == 'g1') {
      /* AŞAMA 1 */

      //            $input["sonuctoplamadamgun"] = number_format(floatval($input["denetimgunsuresi"]), 2);
      $input['denetimzamani'] = $input['toplamgsure'];
      $input['bildirimtarihi'] = $input['tarihrevgozetim1'];
      $input['denetimasama'] = 'Gözetim 1';
      $input['denetimtipi'] = 'g1';

      $input['denetimtarihg1'] = $input['gozetim1'];
      $input['denetimtarihleri'] = $input['gozetim1'];
      $input['basdenetciadi'] = $input['gbd1'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gbd1']
      );
      $input['standartbasdenetciadi'] = self::atananSistemler($input['gbd1'], $belgelendirileceksistemler);

      $karardenetci = explode(',', $input['kararuonerilendenetciuye']);
      if (count($karardenetci) > 1) {
        $i = 1;
        foreach ($karardenetci as $denetci) {
          $input['uye' . $i . 'adi'] = trim($denetci);
          $i++;
        }
      }
      $kacdenetci = explode(',', $input['gd1']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);
          $i++;
        }
      } else {
        $input['denetciadi1'] = $input['gd1'];
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['gd1']), $belgelendirileceksistemler);
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['gd1']
        );
        $input['denetciadi2'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['denetciadi3'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }

      $input['teknikuzmanadi1'] = $input['gtu1'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['adaydenetci'] = $input['adg1'];
      $input['standartadaydenetci'] = self::atananSistemler($input['adg1'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sidg1'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sidg1'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['gg1'];

      $input['standartteknikuzmanadi1'] = self::atananSistemler($input['gtu1'], $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';

      $input['standartgozlemciadi1'] = self::atananSistemler($input['gg1'], $belgelendirileceksistemler);

      $input['eanacekodlariteknikuzman1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gtu1']
      );
      $input['eanacekodlariteknikuzman2'] = '';
      $input['eanacekodlariteknikuzman3'] = '';

      $input['eanacekodlarigozlemci1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gg1']
      );

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['adg1']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sidg1']
      );

      $input['islamiuzmanadi'] = $input['ikug1'];
      $input['standartislamiuzmanadi'] =
        $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24 ? 'İslami Konular' : '';
      $input['eanacekodlariislamiuzman'] = '';

      $input['eanacekodlari'] = $input['firmanacekodu'];

      $denetimekibi =
        $input['gbd1'] . ',' . $input['gd1'] . ',' . $input['gtu1'] . ',' . $input['gg1'] . ',' . $input['ikug1'];
      //echo "<br><br>";

      $denetcisay = count($kacdenetci) - 1 + 1;
      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $denplani = count($dentars);
        if (count($dentars) == 2) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
        if (count($dentars) == 3) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimortatarihi'] = $dentars[1];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];
      $sinir = strtotime($input['denetimbastarihi']);
      $ggtarihi = strtotime($input['gozdengecirmetarihi']);

      /* Şablonlar/A */

      //echo "<br><br>";
      echo '<div class="row"><div class="col-lg-8">
                    <div class="block">
                        <div class="block-head">
                            <div class="block-title">I. Gözetim</div>
                        </div>
                        <div class="block-content">
                        <div class="btn-group btn-group-vertical">';
      //echo "I. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/I. GOZETIM';
      $asamaklasor = 'I. GOZETIM';
      if (intval($input['belgecevrimi']) > 1) {
        $patia1 = $pati . '/' . $input['belgecevrimi'] . '. CEVRIM/I. GOZETIM';
        $asamaklasor = $input['belgecevrimi'] . '. CEVRIM/' . $asamaklasor;
      }

      if ($kapsamgenisletme == 'var') {
        $filem = $filedizin1 . 'AFR.02BasvuruGozdenGecirme-R19.docx';
        $newFilem = 'AFR.02 Basvuru Gozden Gecirme-R19.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
      $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso27001) {
        $filem = $filedizin2 . 'AFR.09Ek1DenetimRaporuR0.docx';
        $newFilem = 'AFR.09DenetimRaporuR8_temp.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      } else {
        if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
          $filem = $filedizin2 . 'AFR.09Ek2OICSMIICDenetimRaporuR0.docx';
          $newFilem = 'AFR.09-Ek2 OIC SMIIC Denetim Raporu R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-1OIC-SMIICDenetimRaporuBuyukbasKucukbasKesimiR0.docx';
          $newFilem = 'AFR.09-Ek2-1 OIC-SMIIC Denetim Raporu Buyukbas Kucukbas Kesimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-4OIC-SMIICDenetimRaporuBalikveBalikUrunleriR0.docx';
          $newFilem = 'AFR.09-Ek2-4 OIC-SMIIC Denetim Raporu Balik ve Balik Urunleri R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem =
            $filedizin2 . 'AFR.09-Ek2-9OIC-SMIICDenetimRaporuDayaniksizCabukBozulabilenHayvansalveBitkiselUrunlerinIslenmesiR0.docx';
          $newFilem =
            'AFR.09-Ek2-9 OIC-SMIIC Denetim Raporu Dayaniksiz Cabuk Bozulabilen Hayvansal ve Bitkisel Urunlerin Islenmesi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-10OIC-SMIICDenetimRaporuKativeSiviYaglarR0.docx';
          $newFilem = 'AFR.09-Ek2-10 OIC-SMIIC Denetim Raporu Kati ve Sivi Yaglar R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-24OIC-SMIICDenetimRaporuBiyokimyasallarinUretimiR0.docx';
          $newFilem = 'AFR.09-Ek2-24 OIC-SMIIC Denetim Raporu Biyokimyasallarin Uretimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
        if ($iso9001 || $iso14001 || $iso45001 || $iso22000 || $iso50001 || $iso27001) {
          $filem = $filedizin2 . 'AFR.09DenetimRaporuR8.docx';
          $newFilem = 'AFR.09 Denetim Raporu-R8.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
      }

      if ($oicsmiic171) {
        $filem =
          $filedizin2 . 'AFR.09-Ek2-20OIC-SMIICDenetimRaporuDayanikliveDayaniksizUrunlerinNakliyesi-R0.docx';
        $newFilem = 'AFR.09-Ek2-20 OIC-SMIIC Denetim Raporu Dayanikli ve Dayaniksiz Urunlerin Nakliyesi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin2 . 'AFR.21DenetimEkibiDegerlendirmeFormu-MusteriR1.docx';
      $newFilem = 'AFR.21 Denetim Ekibi Degerlendirme Formu-Musteri Rev.01.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, true, $asamaklasor);

      echo '</div></div></div></div>';
    }

    if ($asama == 'g2') {
      /* AŞAMA 1 */
      $input['denetimzamani'] = $input['toplamgsure'];
      $input['bildirimtarihi'] = $input['tarihrevgozetim2'];
      $input['denetimasama'] = 'Gözetim 2';
      $input['denetimtipi'] = 'g2';

      $input['denetimtarihg2'] = $input['gozetim2'];
      $input['denetimtarihleri'] = $input['gozetim2'];
      $input['basdenetciadi'] = $input['gbd2'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gbd2']
      );
      $input['standartbasdenetciadi'] = self::atananSistemler($input['gbd2'], $belgelendirileceksistemler);

      $karardenetci = explode(',', $input['kararuonerilendenetciuye']);
      if (count($karardenetci) > 1) {
        $i = 1;
        foreach ($karardenetci as $denetci) {
          $input['uye' . $i . 'adi'] = trim($denetci);
          $i++;
        }
      }
      $kacdenetci = explode(',', $input['gd2']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);
          $i++;
        }
      } else {
        $input['denetciadi1'] = $input['gd2'];
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['gd2']), $belgelendirileceksistemler);
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['gd2']
        );
        $input['denetciadi2'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['denetciadi3'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }

      $input['teknikuzmanadi1'] = $input['gtu2'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['adaydenetci'] = $input['adg2'];
      $input['standartadaydenetci'] = self::atananSistemler($input['adg2'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sidg2'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sidg2'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['gg2'];

      $input['standartteknikuzmanadi1'] = self::atananSistemler($input['gtu2'], $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';

      $input['standartgozlemciadi1'] = self::atananSistemler($input['gg2'], $belgelendirileceksistemler);

      $input['eanacekodlariteknikuzman1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gtu2']
      );
      $input['eanacekodlariteknikuzman2'] = '';
      $input['eanacekodlariteknikuzman3'] = '';

      $input['eanacekodlarigozlemci1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['gg2']
      );

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['adg2']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sidg2']
      );

      $input['eanacekodlari'] = $input['firmanacekodu'];

      $input['islamiuzmanadi'] = $input['ikug2'];
      $input['standartislamiuzmanadi'] =
        $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24 ? 'İslami Konular' : '';
      $input['eanacekodlariislamiuzman'] = '';

      $denetimekibi =
        $input['gbd2'] . ',' . $input['gd2'] . ',' . $input['gtu2'] . ',' . $input['gg2'] . ',' . $input['ikug2'];

      $denetc = explode(', ', $input['denetciadi1']);
      $denetcisay = count($denetc) - 1 + 1;
      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $denplani = count($dentars);
        if (count($dentars) == 2) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
        if (count($dentars) == 3) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimortatarihi'] = $dentars[1];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];
      $sinir = strtotime($input['denetimbastarihi']);
      $ggtarihi = strtotime($input['gozdengecirmetarihi']);

      /* Şablonlar/A */

      //echo "<br><br>";
      echo '<div class="row"><div class="col-lg-8">
                    <div class="block">
                        <div class="block-head">
                            <div class="block-title">II. Gözetim</div>
                        </div>
                        <div class="block-content">
                        <div class="btn-group btn-group-vertical">';
      //echo "I. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/II. GOZETIM';
      $asamaklasor = 'II. GOZETIM';
      if (intval($input['belgecevrimi']) > 1) {
        $patia1 = $pati . '/' . $input['belgecevrimi'] . '. CEVRIM/II. GOZETIM';
        $asamaklasor = $input['belgecevrimi'] . '. CEVRIM/' . $asamaklasor;
      }

      if ($kapsamgenisletme == 'var') {
        $filem = $filedizin1 . 'AFR.02BasvuruGozdenGecirme-R19.docx';
        $newFilem = 'AFR.02 Basvuru Gozden Gecirme-R19.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
      $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso27001) {
        $filem = $filedizin2 . 'AFR.09Ek1DenetimRaporuR0.docx';
        $newFilem = 'AFR.09DenetimRaporuR8_temp.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      } else {
        if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
          $filem = $filedizin2 . 'AFR.09Ek2OICSMIICDenetimRaporuR0.docx';
          $newFilem = 'AFR.09-Ek2 OIC SMIIC Denetim Raporu R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-1OIC-SMIICDenetimRaporuBuyukbasKucukbasKesimiR0.docx';
          $newFilem = 'AFR.09-Ek2-1 OIC-SMIIC Denetim Raporu Buyukbas Kucukbas Kesimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-4OIC-SMIICDenetimRaporuBalikveBalikUrunleriR0.docx';
          $newFilem = 'AFR.09-Ek2-4 OIC-SMIIC Denetim Raporu Balik ve Balik Urunleri R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem =
            $filedizin2 . 'AFR.09-Ek2-9OIC-SMIICDenetimRaporuDayaniksizCabukBozulabilenHayvansalveBitkiselUrunlerinIslenmesiR0.docx';
          $newFilem =
            'AFR.09-Ek2-9 OIC-SMIIC Denetim Raporu Dayaniksiz Cabuk Bozulabilen Hayvansal ve Bitkisel Urunlerin Islenmesi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-10OIC-SMIICDenetimRaporuKativeSiviYaglarR0.docx';
          $newFilem = 'AFR.09-Ek2-10 OIC-SMIIC Denetim Raporu Kati ve Sivi Yaglar R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-24OIC-SMIICDenetimRaporuBiyokimyasallarinUretimiR0.docx';
          $newFilem = 'AFR.09-Ek2-24 OIC-SMIIC Denetim Raporu Biyokimyasallarin Uretimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
        if ($iso9001 || $iso14001 || $iso45001 || $iso22000 || $iso50001 || $iso27001) {
          $filem = $filedizin2 . 'AFR.09DenetimRaporuR8.docx';
          $newFilem = 'AFR.09 Denetim Raporu-R8.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
      }

      if ($oicsmiic171) {
        $filem =
          $filedizin2 . 'AFR.09-Ek2-20OIC-SMIICDenetimRaporuDayanikliveDayaniksizUrunlerinNakliyesi-R0.docx';
        $newFilem = 'AFR.09-Ek2-20 OIC-SMIIC Denetim Raporu Dayanikli ve Dayaniksiz Urunlerin Nakliyesi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin2 . 'AFR.21DenetimEkibiDegerlendirmeFormu-MusteriR1.docx';
      $newFilem = 'AFR.21 Denetim Ekibi Degerlendirme Formu-Musteri Rev.01.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, true, $asamaklasor);

      echo '</div></div></div></div>';
    }

    if ($asama == 'yb') {
      /* AŞAMA 1 */
      $input['denetimzamani'] = $input['toplamybsure'];
      $input['bildirimtarihi'] = $input['tarihrevyb'];
      $input['denetimasama'] = 'Yeniden Belgelendirme';
      $input['denetimtipi'] = 'yb';

      $input['denetimtarihybr'] = $input['ybtar'];
      $input['denetimtarihleri'] = $input['ybtar'];
      $input['basdenetciadi'] = $input['ybbd'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ybbd']
      );
      $input['standartbasdenetciadi'] = self::atananSistemler($input['ybbd'], $belgelendirileceksistemler);

      $karardenetci = explode(',', $input['kararuonerilendenetciuye']);
      if (count($karardenetci) > 1) {
        $i = 1;
        foreach ($karardenetci as $denetci) {
          $input['uye' . $i . 'adi'] = trim($denetci);
          $i++;
        }
      }
      $kacdenetci = explode(',', $input['ybd']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);
          $i++;
        }
      } else {
        $input['denetciadi1'] = $input['ybd'];
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['ybd']), $belgelendirileceksistemler);
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['ybd']
        );
        $input['denetciadi2'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['denetciadi3'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }

      $input['teknikuzmanadi1'] = $input['ybtu'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['adaydenetci'] = $input['adyb'];
      $input['standartadaydenetci'] = self::atananSistemler($input['adyb'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sidyb'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sidyb'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['ybg'];

      $input['standartteknikuzmanadi1'] = self::atananSistemler($input['ybtu'], $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';

      $input['standartgozlemciadi1'] = self::atananSistemler($input['ybg'], $belgelendirileceksistemler);

      $input['eanacekodlariteknikuzman1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ybtu']
      );
      $input['eanacekodlariteknikuzman2'] = '';
      $input['eanacekodlariteknikuzman3'] = '';

      $input['eanacekodlarigozlemci1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ybg']
      );

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['adyb']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sidyb']
      );

      $input['islamiuzmanadi'] = $input['ikuyb'];
      $input['standartislamiuzmanadi'] =
        $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24 ? 'İslami Konular' : '';
      $input['eanacekodlariislamiuzman'] = '';

      $input['eanacekodlari'] = $input['firmanacekodu'];

      $denetimekibi =
        $input['ybbd'] . ',' . $input['ybd'] . ',' . $input['ybtu'] . ',' . $input['ybg'] . ',' . $input['ikuyb'];
      $denetc = explode(', ', $input['denetciadi1']);
      $denetcisay = count($denetc) - 1 + 1;
      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $denplani = count($dentars);
        if (count($dentars) == 2) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
        if (count($dentars) == 3) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimortatarihi'] = $dentars[1];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];
      $input['tekliftarihi'] = $input['tarihrevyb'];
      $sinir = strtotime($input['denetimbastarihi']);
      $ggtarihi = strtotime($input['gozdengecirmetarihi']);

      /* Şablonlar/A */

      //echo "<br><br>";
      echo '<div class="row"><div class="col-lg-8">
                    <div class="block">
                        <div class="block-head">
                            <div class="block-title">Yeniden Belgelendirme</div>
                        </div>
                        <div class="block-content">
                        <div class="btn-group btn-group-vertical">';
      //echo "I. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/YenidenBelgelendirme';
      $asamaklasor = 'YenidenBelgelendirme';
      if (intval($input['belgecevrimi']) > 1) {
        $patia1 = $pati . '/' . $input['belgecevrimi'] . '. CEVRIM/YenidenBelgelendirme';
        $asamaklasor = $input['belgecevrimi'] . '. CEVRIM/' . $asamaklasor;
      }

      $filem = $filedizin1 . 'AFR.02BasvuruGozdenGecirme-R19.docx';
      $newFilem = 'AFR.02 Basvuru Gozden Gecirme-R19.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
      $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso27001) {
        $filem = $filedizin2 . 'AFR.09Ek1DenetimRaporuR0.docx';
        $newFilem = 'AFR.09DenetimRaporuR8_temp.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      } else {
        if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
          $filem = $filedizin2 . 'AFR.09Ek2OICSMIICDenetimRaporuR0.docx';
          $newFilem = 'AFR.09-Ek2 OIC SMIIC Denetim Raporu R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-1OIC-SMIICDenetimRaporuBuyukbasKucukbasKesimiR0.docx';
          $newFilem = 'AFR.09-Ek2-1 OIC-SMIIC Denetim Raporu Buyukbas Kucukbas Kesimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-4OIC-SMIICDenetimRaporuBalikveBalikUrunleriR0.docx';
          $newFilem = 'AFR.09-Ek2-4 OIC-SMIIC Denetim Raporu Balik ve Balik Urunleri R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem =
            $filedizin2 . 'AFR.09-Ek2-9OIC-SMIICDenetimRaporuDayaniksizCabukBozulabilenHayvansalveBitkiselUrunlerinIslenmesiR0.docx';
          $newFilem =
            'AFR.09-Ek2-9 OIC-SMIIC Denetim Raporu Dayaniksiz Cabuk Bozulabilen Hayvansal ve Bitkisel Urunlerin Islenmesi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-10OIC-SMIICDenetimRaporuKativeSiviYaglarR0.docx';
          $newFilem = 'AFR.09-Ek2-10 OIC-SMIIC Denetim Raporu Kati ve Sivi Yaglar R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-24OIC-SMIICDenetimRaporuBiyokimyasallarinUretimiR0.docx';
          $newFilem = 'AFR.09-Ek2-24 OIC-SMIIC Denetim Raporu Biyokimyasallarin Uretimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
        if ($iso9001 || $iso14001 || $iso45001 || $iso22000 || $iso50001 || $iso27001) {
          $filem = $filedizin2 . 'AFR.09DenetimRaporuR8.docx';
          $newFilem = 'AFR.09 Denetim Raporu-R8.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
      }

      if ($oicsmiic171) {
        $filem =
          $filedizin2 . 'AFR.09-Ek2-20OIC-SMIICDenetimRaporuDayanikliveDayaniksizUrunlerinNakliyesi-R0.docx';
        $newFilem = 'AFR.09-Ek2-20 OIC-SMIIC Denetim Raporu Dayanikli ve Dayaniksiz Urunlerin Nakliyesi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin2 . 'AFR.21DenetimEkibiDegerlendirmeFormu-MusteriR1.docx';
      $newFilem = 'AFR.21 Denetim Ekibi Degerlendirme Formu-Musteri Rev.01.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, true, $asamaklasor);

      echo '</div></div></div></div>';
    }

    if ($asama == 'ozel') {
      /* AŞAMA 1 */
      //            $input["sonuctoplamadamgun"] = number_format(floatval($input["denetimgunsuresi"]), 2);
      $input['denetimzamani'] = $input['toplamgsure'];
      $input['bildirimtarihi'] = $input['tarihrevozel'];
      $input['denetimasama'] = 'ÖZEL DENETİM';
      $input['denetimtipi'] = 'ozel';

      $input['denetimtarihg1'] = $input['ozeltar'];
      $input['denetimtarihleri'] = $input['ozeltar'];
      $input['basdenetciadi'] = $input['otbd'];
      $input['eanacekodlaribasdenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['otbd']
      );
      $input['standartbasdenetciadi'] = self::atananSistemler($input['otbd'], $belgelendirileceksistemler);
      $input['gdenetimzamani'] = '1.00';

      $karardenetci = explode(',', $input['kararuonerilendenetciuye']);
      if (count($karardenetci) > 1) {
        $i = 1;
        foreach ($karardenetci as $denetci) {
          $input['uye' . $i . 'adi'] = trim($denetci);
          $i++;
        }
      }
      $kacdenetci = explode(',', $input['otd']);
      if (count($kacdenetci) > 1) {
        $i = 1;
        foreach ($kacdenetci as $denetci) {
          $input['denetciadi' . $i] = trim($denetci);
          $input['eanacekodlaridenetci' . $i] = self::getEaNaceKategoriPerDenetci(
            $input['eakodu'],
            $input['firmanacekodu'],
            $input['categories'],
            $input['oiccategories'],
            $input['enysteknikalan'],
            $input['bgcategories'],
            trim($denetci)
          );
          $input['standartdenetciadi' . $i] = self::atananSistemler(trim($denetci), $belgelendirileceksistemler);
          $i++;
        }
      } else {
        $input['denetciadi1'] = $input['otd'];
        $input['standartdenetciadi1'] = self::atananSistemler(trim($input['otd']), $belgelendirileceksistemler);
        $input['eanacekodlaridenetci1'] = self::getEaNaceKategoriPerDenetci(
          $input['eakodu'],
          $input['firmanacekodu'],
          $input['categories'],
          $input['oiccategories'],
          $input['enysteknikalan'],
          $input['bgcategories'],
          $input['otd']
        );
        $input['denetciadi2'] = '';
        $input['eanacekodlaridenetci2'] = '';
        $input['denetciadi3'] = '';
        $input['eanacekodlaridenetci3'] = '';
        $input['standartdenetciadi2'] = '';
        $input['standartdenetciadi3'] = '';
      }

      $input['teknikuzmanadi1'] = $input['ottu'];
      $input['teknikuzmanadi2'] = '';
      $input['teknikuzmanadi3'] = '';
      $input['adaydenetci'] = $input['adot'];
      $input['standartadaydenetci'] = self::atananSistemler($input['adot'], $belgelendirileceksistemler);
      $input['degerlendirici'] = $input['sidot'];
      $input['standartdegerlendirici'] = self::atananSistemler($input['sidot'], $belgelendirileceksistemler);
      $input['gozlemciadi1'] = $input['otg'];

      $input['standartteknikuzmanadi1'] = self::atananSistemler($input['ottu'], $belgelendirileceksistemler);
      $input['standartteknikuzmanadi2'] = '';
      $input['standartteknikuzmanadi3'] = '';

      $input['standartgozlemciadi1'] = self::atananSistemler($input['otg'], $belgelendirileceksistemler);

      $input['eanacekodlariteknikuzman1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['ottu']
      );
      $input['eanacekodlariteknikuzman2'] = '';
      $input['eanacekodlariteknikuzman3'] = '';

      $input['eanacekodlarigozlemci1'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['otg']
      );

      $input['eanacekodlariadaydenetci'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['adot']
      );

      $input['eanacekodlaridegerlendirici'] = self::getEaNaceKategoriPerDenetci(
        $input['eakodu'],
        $input['firmanacekodu'],
        $input['categories'],
        $input['oiccategories'],
        $input['enysteknikalan'],
        $input['bgcategories'],
        $input['sidot']
      );

      $input['eanacekodlari'] = $input['firmanacekodu'];

      $denetimekibi = $input['gbd1'] . ',' . $input['otd'] . ',' . $input['ottu'] . ',' . $input['otg'];
      //echo "<br><br>";

      $denetcisay = count($kacdenetci) - 1 + 1;
      if (self::InStr($input['denetimtarihleri'], ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $input['denetimtarihleri']));
        $denplani = count($dentars);
        if (count($dentars) == 2) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
        if (count($dentars) == 3) {
          $input['denetimbastarihi'] = $dentars[0];
          $input['denetimortatarihi'] = $dentars[1];
          $input['denetimbitistarihi'] = $dentars[count($dentars) - 1];
          $input['toplantiacilistarihi'] = $dentars[0];
          $input['toplantikapanistarihi'] = $dentars[count($dentars) - 1];
        }
      } else {
        $dentars = str_replace(' ', '', $input['denetimtarihleri']);
        $input['denetimbastarihi'] = $dentars;
        $input['denetimbitistarihi'] = $dentars;
        $input['toplantiacilistarihi'] = $dentars;
        $input['toplantikapanistarihi'] = $dentars;
      }
      $input['raportarihi'] = $input['denetimbitistarihi'];
      $sinir = strtotime($input['denetimbastarihi']);
      $ggtarihi = strtotime($input['gozdengecirmetarihi']);

      /* Şablonlar/A */

      //echo "<br><br>";
      echo '<div class="row"><div class="col-lg-8">
                    <div class="block">
                        <div class="block-head">
                            <div class="block-title">Özel Denetim</div>
                        </div>
                        <div class="block-content">
                        <div class="btn-group btn-group-vertical">';
      //echo "I. AŞAMA";
      //echo "<br><br>";
      $patia1 = $pati . '/OZEL TETKIK';
      $asamaklasor = 'OZEL TETKIK';
      if (intval($input['belgecevrimi']) > 1) {
        $patia1 = $pati . '/' . $input['belgecevrimi'] . '. CEVRIM/' . $asamaklasor;
        $asamaklasor = $input['belgecevrimi'] . '. CEVRIM/' . $asamaklasor;
      }

      if ($kapsamgenisletme == 'var') {
        $filem = $filedizin1 . 'AFR.02BasvuruGozdenGecirme-R19.docx';
        $newFilem = 'AFR.02 Basvuru Gozden Gecirme-R19.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.06DenetimEkibiBilgilendirmeFormu-R6.docx';
      $newFilem = 'AFR.06 Denetim Ekibi Bilgilendirme Formu-R6.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.07DenetimPlani-R8.docx';
      $newFilem = 'AFR.07DenetimPlani-R8_temp.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      if ($iso27001) {
        $filem = $filedizin2 . 'AFR.09Ek1DenetimRaporuR0.docx';
        $newFilem = 'AFR.09DenetimRaporuR8_temp.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      } else {
        if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
          $filem = $filedizin2 . 'AFR.09Ek2OICSMIICDenetimRaporuR0.docx';
          $newFilem = 'AFR.09-Ek2 OIC SMIIC Denetim Raporu R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-1OIC-SMIICDenetimRaporuBuyukbasKucukbasKesimiR0.docx';
          $newFilem = 'AFR.09-Ek2-1 OIC-SMIIC Denetim Raporu Buyukbas Kucukbas Kesimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-4OIC-SMIICDenetimRaporuBalikveBalikUrunleriR0.docx';
          $newFilem = 'AFR.09-Ek2-4 OIC-SMIIC Denetim Raporu Balik ve Balik Urunleri R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem =
            $filedizin2 . 'AFR.09-Ek2-9OIC-SMIICDenetimRaporuDayaniksizCabukBozulabilenHayvansalveBitkiselUrunlerinIslenmesiR0.docx';
          $newFilem =
            'AFR.09-Ek2-9 OIC-SMIIC Denetim Raporu Dayaniksiz Cabuk Bozulabilen Hayvansal ve Bitkisel Urunlerin Islenmesi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-10OIC-SMIICDenetimRaporuKativeSiviYaglarR0.docx';
          $newFilem = 'AFR.09-Ek2-10 OIC-SMIIC Denetim Raporu Kati ve Sivi Yaglar R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

          $filem = $filedizin2 . 'AFR.09-Ek2-24OIC-SMIICDenetimRaporuBiyokimyasallarinUretimiR0.docx';
          $newFilem = 'AFR.09-Ek2-24 OIC-SMIIC Denetim Raporu Biyokimyasallarin Uretimi R0.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
        if ($iso9001 || $iso14001 || $iso45001 || $iso22000 || $iso50001 || $iso27001) {
          $filem = $filedizin2 . 'AFR.09DenetimRaporuR8.docx';
          $newFilem = 'AFR.09 Denetim Raporu-R8.docx';
          //self::removeExtraTables($filem, $pot);
          self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
        }
      }

      if ($oicsmiic171) {
        $filem =
          $filedizin2 . 'AFR.09-Ek2-20OIC-SMIICDenetimRaporuDayanikliveDayaniksizUrunlerinNakliyesi-R0.docx';
        $newFilem = 'AFR.09-Ek2-20 OIC-SMIIC Denetim Raporu Dayanikli ve Dayaniksiz Urunlerin Nakliyesi-R0.docx';
        self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);
      }

      $filem = $filedizin1 . 'AFR.11AcilisKapanisToplantiFormu-R3.docx';
      $newFilem = 'AFR.11 Acilis Kapanis Toplanti Formu-R3.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin1 . 'AFR.13UygunsuzlukBildirim-R2.docx';
      $newFilem = 'AFR.13 Uygunsuzluk Bildirim Rev.02.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, false, $asamaklasor);

      $filem = $filedizin2 . 'AFR.21DenetimEkibiDegerlendirmeFormu-MusteriR1.docx';
      $newFilem = 'AFR.21 Denetim Ekibi Degerlendirme Formu-Musteri Rev.01.docx';
      self::setDosyasiOlustur($filem, $newFilem, $input, $patia1, true, $asamaklasor);

      echo '</div></div></div></div>';
    }

    echo '<div class="col-lg-4"><div class="block">
                                <div class="block-head">
                                    <div class="block-title">';

    if (!file_exists($pati . '/zips')) {
      if (!self::mkdirr($pati . '/zips')) {
        self::msgError($pati . '/zips dizini oluşturulamadı...', false);
      }
    }

    if ($this->canzip) {
      DB::reconnect('mysql');

      $zip_name = $pno . '.' . explode(' ', $input['firmaadi'])[0];
      $zip_directory = dirname($pati) . '/zips/';
      self::zipZip($zip_name, $zip_directory, $pati);
      //      Zip::create($zip_name . ".zip", File::files($pati))->saveTo($zip_directory);

      //        self::zipDir($zip_name, $zip_directory);
      //        self::zip_add_directory($pati);
      //        self::zip_save();

      $fname1 = $zip_name . '.zip';

      echo '<a href="' .
        route('downloadZipFile', ['dosya' => $fname1]) .
        '" class="col-sm-12 btn btn-danger btn-sm" target="_blank">' .
        $fname1 .
        '</a>';
    }

    echo '</div>
                                </div>
                                <div class="block-content"></div>
                                <div class="block-footer">
                                    <div class="pull-left">';
    $kayitp = DB::table('planlar')->updateOrInsert(
      ['planno' => $pno, 'belgecevrimi' => $input['belgecevrimi'] ?? '1'],
      $pot['plan']
    );

    if (!$kayitp) {
      echo $pno . ' nolu denetim planı kayıt başarısız.<br><br>';
    }

    if ($iso9001) {
      $pot['plan9001']['iso9001hamsure'] = $input['iso9001hamsure'];
      $pot['plan9001']['iso9001indart'] = $input['iso9001indart'];
      $pot['plan9001']['iso9001indartsure'] = $input['iso9001azartsure'];
      $pot['plan9001']['iso9001entindart'] = $input['iso9001entindart'];
      $pot['plan9001']['iso9001kalansure'] = $input['iso9001kalansure'];
      $pot['plan9001']['iso9001a1sure'] = $input['iso9001a1sure'];
      $pot['plan9001']['iso9001a2sure'] = $input['iso9001a2sure'];
      $pot['plan9001']['iso9001gsure'] = $input['iso9001gsure'];
      $pot['plan9001']['iso9001ybsure'] = $input['iso9001ybsure'];
      $kayitp9001 = DB::table('plan_9001')->updateOrInsert(['planno' => $pno], $pot['plan9001']);
      if ($kayitp9001) {
        $mesaj9001 = '<br>' . $pno . ' nolu ISO 9001 denetim hesaplaması kayıt başarılı.';
      }

      /* indirim/arttırım seçeneklerini kaydet */
      if (isset($pot['chb_indart9001'])) {
        $kayitp9001indartsebepler = DB::table('plan_azaltmaarttirmalar')->updateOrInsert(
          ['planno' => $pno],
          $pot['chb_indart9001']
        );
        if ($kayitp9001indartsebepler) {
          $mesaj9001indartsebepler =
            '<br>' . $pno . ' nolu ISO 9001 denetim azaltma arttirma nedenleri kayıt başarılı.';
        }
      }
    }

    if ($iso14001) {
      $pot['plan14001']['iso14001hamsure'] = $input['iso14001hamsure'];
      $pot['plan14001']['iso14001indart'] = $input['iso14001indart'];
      $pot['plan14001']['iso14001indartsure'] = $input['iso14001azartsure'];
      $pot['plan14001']['iso14001entindart'] = $input['iso14001entindart'];
      $pot['plan14001']['iso14001kalansure'] = $input['iso14001kalansure'];
      $pot['plan14001']['iso14001a1sure'] = $input['iso14001a1sure'];
      $pot['plan14001']['iso14001a2sure'] = $input['iso14001a2sure'];
      $pot['plan14001']['iso14001gsure'] = $input['iso14001gsure'];
      $pot['plan14001']['iso14001ybsure'] = $input['iso14001ybsure'];
      $kayitp14001 = DB::table('plan_14001')->updateOrInsert(['planno' => $pno], $pot['plan14001']);
      if ($kayitp14001) {
        $mesaj14001 = '<br>' . $pno . ' nolu ISO 14001 denetim hesaplaması kayıt başarılı.';
      }

      /* indirim/arttırım seçeneklerini kaydet */
      if (isset($pot['chb_indart14001'])) {
        $kayitp14001indartsebepler = DB::table('plan_azaltmaarttirmalar')->updateOrInsert(
          ['planno' => $pno],
          $pot['chb_indart14001']
        );
        if ($kayitp14001indartsebepler) {
          $mesaj14001indartsebepler =
            '<br>' . $pno . ' nolu ISO 14001 denetim azaltma arttirma nedenleri kayıt başarılı.';
        }
      }
    }

    if ($iso27001) {
      $pot['plan27001']['iso27001hamsure'] = $input['iso27001hamsure'];
      $pot['plan27001']['iso27001indart'] = $input['iso27001indart'];
      $pot['plan27001']['iso27001indartsure'] = $input['iso27001azartsure'];
      $pot['plan27001']['iso27001entindart'] = $input['iso27001entindart'];
      $pot['plan27001']['iso27001kalansure'] = $input['iso27001kalansure'];
      $pot['plan27001']['iso27001a1sure'] = $input['iso27001a1sure'];
      $pot['plan27001']['iso27001a2sure'] = $input['iso27001a2sure'];
      $pot['plan27001']['iso27001gsure'] = $input['iso27001gsure'];
      $pot['plan27001']['iso27001ybsure'] = $input['iso27001ybsure'];
      $kayitp27001 = DB::table('plan_27001')->updateOrInsert(['planno' => $pno], $pot['plan27001']);
      if ($kayitp27001) {
        $mesaj27001 = '<br>' . $pno . ' nolu ISO 27001 denetim hesaplaması kayıt başarılı.';
      }
    }

    if ($iso45001) {
      $pot['plan45001']['iso45001hamsure'] = $input['iso45001hamsure'];
      $pot['plan45001']['iso45001indart'] = $input['iso45001indart'];
      $pot['plan45001']['iso45001indartsure'] = $input['iso45001azartsure'];
      $pot['plan45001']['iso45001entindart'] = $input['iso45001entindart'];
      $pot['plan45001']['iso45001kalansure'] = $input['iso45001kalansure'];
      $pot['plan45001']['iso45001a1sure'] = $input['iso45001a1sure'];
      $pot['plan45001']['iso45001a2sure'] = $input['iso45001a2sure'];
      $pot['plan45001']['iso45001gsure'] = $input['iso45001gsure'];
      $pot['plan45001']['iso45001ybsure'] = $input['iso45001ybsure'];
      $kayitp45001 = DB::table('plan_45001')->updateOrInsert(['planno' => $pno], $pot['plan45001']);
      if ($kayitp45001) {
        $mesaj45001 = '<br>' . $pno . ' nolu ISO 45001 denetim hesaplaması kayıt başarılı.';
      }

      if (isset($pot['chb_indart45001'])) {
        $kayitp45001indartsebepler = DB::table('plan_azaltmaarttirmalar')->updateOrInsert(
          ['planno' => $pno],
          $pot['chb_indart45001']
        );
        if ($kayitp45001indartsebepler) {
          $mesaj45001indartsebepler =
            '<br>' . $pno . ' nolu ISO 45001 denetim azaltma arttirma nedenleri kayıt başarılı.';
        }
      }
    }

    if ($iso50001) {
      $pot['plan50001']['iso50001hamsure'] = $input['iso50001hamsure'];
      $pot['plan50001']['iso50001indart'] = $input['iso50001indart'];
      $pot['plan50001']['iso50001indartsure'] = $input['iso50001azartsure'];
      $pot['plan50001']['iso50001entindart'] = $input['iso50001entindart'];
      $pot['plan50001']['iso50001kalansure'] = $input['iso50001kalansure'];
      $pot['plan50001']['iso50001a1sure'] = $input['iso50001a1sure'];
      $pot['plan50001']['iso50001a2sure'] = $input['iso50001a2sure'];
      $pot['plan50001']['iso50001gsure'] = $input['iso50001gsure'];
      $pot['plan50001']['iso50001ybsure'] = $input['iso50001ybsure'];
      $kayitp50001 = DB::table('plan_50001')->updateOrInsert(['planno' => $pno], $pot['plan50001']);
      if ($kayitp50001) {
        $mesaj50001 = '<br>' . $pno . ' nolu ISO 50001 denetim hesaplaması kayıt başarılı.';
      }
    }

    if ($iso22000) {
      $pot['plan22000']['iso22000hamsure'] = $input['iso22000hamsure'];
      $pot['plan22000']['iso22000indart'] = $input['iso22000indart'];
      $pot['plan22000']['iso22000indartsure'] = $input['iso22000azartsure'];
      $pot['plan22000']['iso22000entindart'] = $input['iso22000entindart'];
      $pot['plan22000']['iso22000kalansure'] = $input['iso22000kalansure'];
      $pot['plan22000']['iso22000a1sure'] = $input['iso22000a1sure'];
      $pot['plan22000']['iso22000a2sure'] = $input['iso22000a2sure'];
      $pot['plan22000']['iso22000gsure'] = $input['iso22000gsure'];
      $pot['plan22000']['iso22000ybsure'] = $input['iso22000ybsure'];
      $kayitp22000 = DB::table('plan_22000')->updateOrInsert(['planno' => $pno], $pot['plan22000']);
      if ($kayitp22000) {
        $mesaj22000 = '<br>' . $pno . ' nolu ISO 22000 denetim hesaplaması kayıt başarılı.';
      }
    }

    if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24) {
      $pot['planoicsmiic']['oicsmiichamsure'] = $input['oicsmiichamsure'];
      $pot['planoicsmiic']['oicsmiicindart'] = $input['oicsmiicindart'];
      $pot['planoicsmiic']['oicsmiicindartsure'] = $input['oicsmiicazartsure'];
      $pot['planoicsmiic']['oicsmiicentindart'] = $input['oicsmiicentindart'];
      $pot['planoicsmiic']['oicsmiickalansure'] = $input['oicsmiickalansure'];
      $pot['planoicsmiic']['oicsmiica1sure'] = $input['oicsmiica1sure'];
      $pot['planoicsmiic']['oicsmiica2sure'] = $input['oicsmiica2sure'];
      $pot['planoicsmiic']['oicsmiicgsure'] = $input['oicsmiicgsure'];
      $pot['planoicsmiic']['oicsmiicybsure'] = $input['oicsmiicybsure'];
      $kayitpoicsmiic = DB::table('plan_smiic1')->updateOrInsert(['planno' => $pno], $pot['planoicsmiic']);
      if ($kayitpoicsmiic) {
        $mesajSmiic = '<br>' . $pno . ' nolu OIC/SMIIC denetim hesaplaması kayıt başarılı.';
      }
    }

    echo '<br' .
      $pno .
      ' nolu denetim plan detayları kaydı başarılı.<br>' .
      $mesaj9001 .
      $mesaj9001indartsebepler .
      $mesaj14001 .
      $mesaj14001indartsebepler .
      $mesaj22000 .
      $mesaj27001 .
      $mesaj45001 .
      $mesaj45001indartsebepler .
      $mesaj50001 .
      $mesajSmiic;

    //    event(new PlanEvents($input["planno"], $input["dentarihi"], $input["dtipi"], $input["firmaadi"]));

    echo '</div></div></div></div></div>';
    echo '<br><br>';
  }

  public function setDosyasiOlustur($filem, $newFilem, $pot, $setpath, $canzipped = false, $asama = '')
  {
    try {
      $pati = $setpath;
      $a1sahaliste9 = [
        '01',
        '02',
        '03',
        '05',
        '09',
        '11',
        '12',
        '13',
        '14',
        '15',
        '18',
        '19',
        '20',
        '21',
        '22',
        '24',
        '25',
        '26',
        '28',
        '33',
        '37',
        '38',
      ];
      $a1sahaliste14 = [
        '01',
        '02',
        '03',
        '04',
        '05',
        '07',
        '09',
        '10',
        '11',
        '12',
        '13',
        '15',
        '16',
        '17',
        '20',
        '21',
        '24',
        '25',
        '26',
        '28',
        '29',
        '35',
        '36',
        '38',
        '39',
      ];
      $a1sahaliste45 = [
        '01',
        '02',
        '03',
        '04',
        '05',
        '06',
        '07',
        '09',
        '10',
        '11',
        '12',
        '13',
        '17',
        '24',
        '25',
        '26',
        '27',
        '28',
        '29',
        '30',
        '31',
        '34',
        '35',
        '36',
        '37',
        '38',
        '39',
      ];

      $pot['iso900115varyok'] = isset($pot['iso900115varyok']) ? $pot['iso900115varyok'] : 0;
      $pot['iso1400115varyok'] = isset($pot['iso1400115varyok']) ? $pot['iso1400115varyok'] : 0;
      $pot['iso2200018varyok'] = isset($pot['iso2200018varyok']) ? $pot['iso2200018varyok'] : 0;
      $pot['iso4500118varyok'] = isset($pot['iso4500118varyok']) ? $pot['iso4500118varyok'] : 0;
      $pot['iso5000118varyok'] = isset($pot['iso5000118varyok']) ? $pot['iso5000118varyok'] : 0;
      $pot['iso27001varyok'] = isset($pot['iso27001varyok']) ? $pot['iso27001varyok'] : 0;
      $pot['helalvaryok'] = isset($pot['helalvaryok']) ? $pot['helalvaryok'] : 0;
      $pot['oicsmiik6varyok'] = isset($pot['oicsmiik6varyok']) ? $pot['oicsmiik6varyok'] : 0;
      $pot['oicsmiik9varyok'] = isset($pot['oicsmiik9varyok']) ? $pot['oicsmiik9varyok'] : 0;
      $pot['oicsmiik171varyok'] = isset($pot['oicsmiik171varyok']) ? $pot['oicsmiik171varyok'] : 0;
      $pot['oicsmiik23varyok'] = isset($pot['oicsmiik23varyok']) ? $pot['oicsmiik23varyok'] : 0;
      $pot['oicsmiik24varyok'] = isset($pot['oicsmiik24varyok']) ? $pot['oicsmiik24varyok'] : 0;
      $pot['entegreysvarmi'] = isset($pot['indartentvarmi']) ? $pot['indartentvarmi'] : 0;

      if ($this->doc == '') {
        Settings::loadConfig();
        Settings::setTempDir(public_path() . '/temp');
        Settings::setOutputEscapingEnabled(true);
        Settings::setCompatibility(true);

        $this->doc = new TemplateProcessor($filem);
        $this->doc->setMacroOpeningChars('æ');
        $this->doc->setMacroClosingChars('æ');
        $this->doc->setMacroChars('æ', 'æ');
      }

      //      echo Settings::getTempDir();
      if (isset($pot['iso900115varyok']) && $pot['iso900115varyok'] == 0) {
        $this->doc->deleteBlock('deletekys');
      }
      if (isset($pot['iso1400115varyok']) && $pot['iso1400115varyok'] == 0) {
        $this->doc->deleteBlock('deletecys');
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 0) {
        $this->doc->deleteBlock('deleteggys18');
      }
      if (isset($pot['helalvaryok']) && $pot['helalvaryok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic');
      }
      if (isset($pot['oicsmiik6varyok']) && $pot['oicsmiik6varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic6');
      }
      if (isset($pot['oicsmiik9varyok']) && $pot['oicsmiik9varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic9');
      }
      if (isset($pot['oicsmiik171varyok']) && $pot['oicsmiik171varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic171');
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic23');
      }
      if (isset($pot['oicsmiik24varyok']) && $pot['oicsmiik24varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic24');
      }
      if (isset($pot['iso4500118varyok']) && $pot['iso4500118varyok'] == 0) {
        $this->doc->deleteBlock('deleteo4500118');
      }
      if (isset($pot['iso5000118varyok']) && $pot['iso5000118varyok'] == 0) {
        $this->doc->deleteBlock('deleteo5000118');
      }
      if (isset($pot['iso27001varyok']) && $pot['iso27001varyok'] == 0) {
        $this->doc->deleteBlock('delete2700122');
      }

      if (isset($pot['iso900115varyok']) && $pot['iso900115varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="iso900115varyok"', true);
      }
      if (isset($pot['iso1400115varyok']) && $pot['iso1400115varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="iso1400115varyok"', true);
      }
      if (isset($pot['iso4500118varyok']) && $pot['iso4500118varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="iso4500118varyok"', true);
      }
      if (isset($pot['iso5000118varyok']) && $pot['iso5000118varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="iso5000118varyok"', true);
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="iso2200018varyok"', true);
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="abirsaha"', true);
      }
      if (isset($pot['iso50001varyok']) && $pot['iso50001varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="abirsaha"', true);
      }
      if (isset($pot['helalvaryok']) && $pot['helalvaryok'] == 1) {
        $this->doc->setCheckboxMS('w:name="helalvaryok"', true);
      }
      if (isset($pot['oicsmiik6varyok']) && $pot['oicsmiik6varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="oicsmiik6varyok"', true);
      }
      if (isset($pot['oicsmiik9varyok']) && $pot['oicsmiik9varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="oicsmiik9varyok"', true);
      }
      if (isset($pot['oicsmiik171varyok']) && $pot['oicsmiik171varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="oicsmiik171varyok"', true);
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="oicsmiik23varyok"', true);
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 1) {
        $this->doc->setCheckboxMS('w:name="oicsmiik24varyok"', true);
      }
      if (isset($pot['digersistemlerneler']) && $pot['digersistemlerneler'] != '') {
        $this->doc->setCheckboxMS('w:name="digervaryok"', true);
      }
      if (isset($pot['entegreysvarmi']) && $pot['entegreysvarmi'] == 1) {
        $this->doc->setCheckboxMS('w:name="entegreysvarmi"', true);
      }
      if (isset($pot['yonetimsistemsertifikasi']) && $pot['yonetimsistemsertifikasi'] == '1') {
        $this->doc->setCheckboxMS('w:name="yonetimsistemievet"', true);
      }
      if (isset($pot['yonetimsistemsertifikasi']) && $pot['yonetimsistemsertifikasi'] == '0') {
        $this->doc->setCheckboxMS('w:name="yonetimsistemihayir"', true);
      }
      if (isset($pot['tumvardayni']) && $pot['tumvardayni'] == 'EVET') {
        $this->doc->setCheckboxMS('w:name="tumvardaynievet"', true);
      }
      if (isset($pot['tumvardayni']) && $pot['tumvardayni'] == 'HAYIR') {
        $this->doc->setCheckboxMS('w:name="tumvardaynihayir"', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'iptal') {
        $this->doc->setCheckboxMS('w:name="belgeiptal"', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'aski') {
        $this->doc->setCheckboxMS('w:name="belgeaski"', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'askiindir') {
        $this->doc->setCheckboxMS('w:name="belgeaskiindir"', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckboxMS('w:name="belgeok"', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckboxMS('w:name="belgeaski"', false);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckboxMS('w:name="belgeaskiindir"', false);
      }
      if (isset($pot['subeturu1']) && $pot['subeturu1'] == '1') {
        $this->doc->setCheckboxMS('w:name="subeturu1"', true);
      }
      if (isset($pot['subeturu1']) && $pot['subeturu1'] == '2') {
        $this->doc->setCheckboxMS('w:name="subeturu1"', true);
      }
      if (isset($pot['subeturu2']) && $pot['subeturu2'] == '1') {
        $this->doc->setCheckboxMS('w:name="subeturu2"', true);
      }
      if (isset($pot['subeturu2']) && $pot['subeturu2'] == '2') {
        $this->doc->setCheckboxMS('w:name="subeturu2"', true);
      }
      if (isset($pot['subeturu3']) && $pot['subeturu3'] == '1') {
        $this->doc->setCheckboxMS('w:name="subeturu3"', true);
      }
      if (isset($pot['subeturu3']) && $pot['subeturu3'] == '2') {
        $this->doc->setCheckboxMS('w:name="subeturu3"', true);
      }
      if (isset($pot['subeturu4']) && $pot['subeturu4'] == '1') {
        $this->doc->setCheckboxMS('w:name="subeturu4"', true);
      }
      if (isset($pot['subeturu4']) && $pot['subeturu4'] == '2') {
        $this->doc->setCheckboxMS('w:name="subeturu4"', true);
      }
      //ENTEGRE SİSTEM SEÇENEKLERİ
      if (isset($pot['ygg']) && $pot['ygg'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="ygg"', true);
      }
      if (isset($pot['icdenetim']) && $pot['icdenetim'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="icdenetim"', true);
      }
      if (isset($pot['entegredokumantasyon']) && $pot['entegredokumantasyon'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="entegredokumantasyon"', true);
      }
      if (isset($pot['duzelticifaaliyet']) && $pot['duzelticifaaliyet'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="duzelticifaaliyet"', true);
      }
      if (isset($pot['yondessor']) && $pot['yondessor'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="yondessor"', true);
      }
      if (isset($pot['prosesentegre']) && $pot['prosesentegre'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="prosesentegre"', true);
      }
      if (isset($pot['politikahedefler']) && $pot['politikahedefler'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="politikahedefler"', true);
      }
      if (isset($pot['riskyonetimyaklasimi']) && $pot['riskyonetimyaklasimi'] == '12.5') {
        $this->doc->setCheckboxMS('w:name="riskyonetimyaklasimi"', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'ilk') {
        $this->doc->setCheckboxMS('w:name="denetimtipiilk"', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'g1') {
        $this->doc->setCheckboxMS('w:name="denetimtipig1"', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'g2') {
        $this->doc->setCheckboxMS('w:name="denetimtipig2"', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'yb') {
        $this->doc->setCheckboxMS('w:name="denetimtipiyb"', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'ozel') {
        $this->doc->setCheckboxMS('w:name="denetimtipiozel"', true);
      }

      // karar gözden geçirmeler
      if (isset($pot['karargga']) && $pot['karargga'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggaa"', true);
      }
      if (isset($pot['karargga']) && $pot['karargga'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggab"', true);
      }
      if (isset($pot['kararggb']) && $pot['kararggb'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggba"', true);
      }
      if (isset($pot['kararggb']) && $pot['kararggb'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggbb"', true);
      }
      if (isset($pot['kararggc']) && $pot['kararggc'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggca"', true);
      }
      if (isset($pot['kararggc']) && $pot['kararggc'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggcb"', true);
      }
      if (isset($pot['kararggd']) && $pot['kararggd'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggda"', true);
      }
      if (isset($pot['kararggd']) && $pot['kararggd'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggdb"', true);
      }
      if (isset($pot['karargge']) && $pot['karargge'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggea"', true);
      }
      if (isset($pot['karargge']) && $pot['karargge'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggeb"', true);
      }
      if (isset($pot['kararggf']) && $pot['kararggf'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggfa"', true);
      }
      if (isset($pot['kararggf']) && $pot['kararggf'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggfb"', true);
      }
      if (isset($pot['kararggg']) && $pot['kararggg'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggga"', true);
      }
      if (isset($pot['kararggg']) && $pot['kararggg'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="karargggb"', true);
      }
      if (isset($pot['kararggh']) && $pot['kararggh'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggha"', true);
      }
      if (isset($pot['kararggh']) && $pot['kararggh'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="karargghb"', true);
      }
      if (isset($pot['kararggi']) && $pot['kararggi'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggia"', true);
      }
      if (isset($pot['kararggi']) && $pot['kararggi'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggib"', true);
      }
      if (isset($pot['kararggj']) && $pot['kararggj'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggja"', true);
      }
      if (isset($pot['kararggj']) && $pot['kararggj'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggjb"', true);
      }
      if (isset($pot['kararggk']) && $pot['kararggk'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggka"', true);
      }
      if (isset($pot['kararggk']) && $pot['kararggk'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggkb"', true);
      }
      if (isset($pot['kararggl']) && $pot['kararggl'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggla"', true);
      }
      if (isset($pot['kararggl']) && $pot['kararggl'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="karargglb"', true);
      }
      if (isset($pot['kararggm']) && $pot['kararggm'] == 'u') {
        $this->doc->setCheckboxMS('w:name="kararggma"', true);
      }
      if (isset($pot['kararggm']) && $pot['kararggm'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="kararggmb"', true);
      }
      // karar gözden geçirmeler

      if (isset($pot['bskarar']) && $pot['bskarar'] == 'u') {
        $this->doc->setCheckboxMS('w:name="bskararu"', true);
      }
      if (isset($pot['bskarar']) && $pot['bskarar'] == 'ud') {
        $this->doc->setCheckboxMS('w:name="bskararud"', true);
      }

      foreach ($a1sahaliste9 as $key => $value) {
        if (
          isset($pot['iso900115varyok']) &&
          $pot['iso900115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirsaha"', true);
        }
        if (
          isset($pot['iso900115varyok']) &&
          $pot['iso900115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirofis"', false);
        }
      }
      foreach ($a1sahaliste14 as $key => $value) {
        if (
          isset($pot['iso1400115varyok']) &&
          $pot['iso1400115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirsaha"', true);
        }
        if (
          isset($pot['iso1400115varyok']) &&
          $pot['iso1400115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirofis"', false);
        }
      }
      foreach ($a1sahaliste45 as $key => $value) {
        if (
          isset($pot['iso4500118varyok']) &&
          $pot['iso4500118varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirsaha"', true);
        }
        if (
          isset($pot['iso4500118varyok']) &&
          $pot['iso4500118varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckboxMS('w:name="abirofis"', false);
        }
      }

      if (
        $pot['iso2200018varyok'] == 1 ||
        $pot['helalvaryok'] == 1 ||
        $pot['oicsmiik6varyok'] == 1 ||
        $pot['oicsmiik9varyok'] == 1 ||
        $pot['oicsmiik171varyok'] == 1 ||
        $pot['oicsmiik23varyok'] == 1 ||
        $pot['oicsmiik24varyok'] == 1
      ) {
        $this->doc->setCheckboxMS('w:name="abirsaha"', true);
        $this->doc->setCheckboxMS('w:name="abirofis"', false);
      }
      //            var_dump($pot);
      foreach ($pot as $name => $value) {
        if ($name === 'id') {
          continue;
        }
        //                echo $name." = ". $value."<br>";
        $this->doc->setValue(strtolower($name), $value);
        //                echo $name . " = " . $value . "<br>";
      }

      if (!file_exists($pati)) {
        if (!self::mkdirr($pati)) {
          self::msgError($pati . ' dizini oluşturulamadı...', false);
        }
      }

      //        echo $this->clearLocale($newFilem);
      $fname = $pati . '/' . $newFilem;
      if (file_exists($fname)) {
        unlink($fname);
      }
      $this->doc->saveAs($fname);
      $this->doc = '';

      //      rename($fname, "{$fname}");

      $fname1 = '../setler/' . $pot['planno'] . '/' . $asama . '/' . iconv('ISO-8859-9', 'UTF-8', $newFilem);
      echo '<a href="' .
        route('downloadWordFile', [
          'pno' => $pot['planno'],
          'asama' => $asama,
          'dosya' => iconv('ISO-8859-9', 'UTF-8', $newFilem),
        ]) .
        '" class="col-sm-12 btn btn-success btn-sm" target="_blank">' .
        iconv('ISO-8859-9', 'UTF-8', $newFilem) .
        '</a><br>';
      if ($canzipped) {
        $this->canzip = $canzipped;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function setDosyasiOlustur1($filem, $newFilem, $pot, $setpath, $canzipped = false, $asama = '')
  {
    try {
      $pati = $setpath;
      $a1sahaliste9 = [
        '01', '02', '03', '05', '09', '11', '12', '13', '14', '15', '18', '19',
        '20', '21', '22', '24', '25', '26', '28', '33', '37', '38',
      ];
      $a1sahaliste14 = [
        '01', '02', '03', '04', '05', '07', '09', '10', '11', '12', '13', '15',
        '16', '17', '20', '21', '24', '25', '26', '28', '29', '35', '36', '38', '39',
      ];
      $a1sahaliste45 = [
        '01', '02', '03', '04', '05', '06', '07', '09', '10', '11', '12', '13',
        '17', '24', '25', '26', '27', '28', '29', '30', '31', '34', '35', '36', '37', '38', '39',
      ];

      $pot['iso900115varyok'] = isset($pot['iso900115varyok']) ? $pot['iso900115varyok'] : 0;
      $pot['iso1400115varyok'] = isset($pot['iso1400115varyok']) ? $pot['iso1400115varyok'] : 0;
      $pot['iso2200018varyok'] = isset($pot['iso2200018varyok']) ? $pot['iso2200018varyok'] : 0;
      $pot['iso4500118varyok'] = isset($pot['iso4500118varyok']) ? $pot['iso4500118varyok'] : 0;
      $pot['iso5000118varyok'] = isset($pot['iso5000118varyok']) ? $pot['iso5000118varyok'] : 0;
      $pot['iso27001varyok'] = isset($pot['iso27001varyok']) ? $pot['iso27001varyok'] : 0;
      $pot['helalvaryok'] = isset($pot['helalvaryok']) ? $pot['helalvaryok'] : 0;
      $pot['oicsmiik6varyok'] = isset($pot['oicsmiik6varyok']) ? $pot['oicsmiik6varyok'] : 0;
      $pot['oicsmiik9varyok'] = isset($pot['oicsmiik9varyok']) ? $pot['oicsmiik9varyok'] : 0;
      $pot['oicsmiik171varyok'] = isset($pot['oicsmiik171varyok']) ? $pot['oicsmiik171varyok'] : 0;
      $pot['oicsmiik23varyok'] = isset($pot['oicsmiik23varyok']) ? $pot['oicsmiik23varyok'] : 0;
      $pot['oicsmiik24varyok'] = isset($pot['oicsmiik24varyok']) ? $pot['oicsmiik24varyok'] : 0;
      $pot['entegreysvarmi'] = isset($pot['indartentvarmi']) ? $pot['indartentvarmi'] : 0;

      if ($this->doc == '') {
        Settings::loadConfig();
        Settings::setTempDir(public_path() . '/temp');
        Settings::setOutputEscapingEnabled(true);
        Settings::setCompatibility(true);

        $this->doc = new TemplateProcessor($filem);
        $this->doc->setMacroOpeningChars('æ');
        $this->doc->setMacroClosingChars('æ');
        $this->doc->setMacroChars('æ', 'æ');
      }

      // Delete blocks if needed
      if (isset($pot['iso900115varyok']) && $pot['iso900115varyok'] == 0) {
        $this->doc->deleteBlock('deletekys');
      }
      if (isset($pot['iso1400115varyok']) && $pot['iso1400115varyok'] == 0) {
        $this->doc->deleteBlock('deletecys');
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 0) {
        $this->doc->deleteBlock('deleteggys18');
      }
      if (isset($pot['helalvaryok']) && $pot['helalvaryok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic');
      }
      if (isset($pot['oicsmiik6varyok']) && $pot['oicsmiik6varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic6');
      }
      if (isset($pot['oicsmiik9varyok']) && $pot['oicsmiik9varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic9');
      }
      if (isset($pot['oicsmiik171varyok']) && $pot['oicsmiik171varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic171');
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic23');
      }
      if (isset($pot['oicsmiik24varyok']) && $pot['oicsmiik24varyok'] == 0) {
        $this->doc->deleteBlock('deleteoicsmiic24');
      }
      if (isset($pot['iso4500118varyok']) && $pot['iso4500118varyok'] == 0) {
        $this->doc->deleteBlock('deleteo4500118');
      }
      if (isset($pot['iso5000118varyok']) && $pot['iso5000118varyok'] == 0) {
        $this->doc->deleteBlock('deleteo5000118');
      }
      if (isset($pot['iso27001varyok']) && $pot['iso27001varyok'] == 0) {
        $this->doc->deleteBlock('delete2700122');
      }

      // Set checkbox values using the official method
      if (isset($pot['iso900115varyok']) && $pot['iso900115varyok'] == 1) {
        $this->doc->setCheckbox('iso900115varyok', true);
      }
      if (isset($pot['iso1400115varyok']) && $pot['iso1400115varyok'] == 1) {
        $this->doc->setCheckbox('iso1400115varyok', true);
      }
      if (isset($pot['iso4500118varyok']) && $pot['iso4500118varyok'] == 1) {
        $this->doc->setCheckbox('iso4500118varyok', true);
      }
      if (isset($pot['iso5000118varyok']) && $pot['iso5000118varyok'] == 1) {
        $this->doc->setCheckbox('iso5000118varyok', true);
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 1) {
        $this->doc->setCheckbox('iso2200018varyok', true);
      }
      if (isset($pot['iso2200018varyok']) && $pot['iso2200018varyok'] == 1) {
        $this->doc->setCheckbox('abirsaha', true);
      }
      if (isset($pot['iso50001varyok']) && $pot['iso50001varyok'] == 1) {
        $this->doc->setCheckbox('abirsaha', true);
      }
      if (isset($pot['helalvaryok']) && $pot['helalvaryok'] == 1) {
        $this->doc->setCheckbox('helalvaryok', true);
      }
      if (isset($pot['oicsmiik6varyok']) && $pot['oicsmiik6varyok'] == 1) {
        $this->doc->setCheckbox('oicsmiik6varyok', true);
      }
      if (isset($pot['oicsmiik9varyok']) && $pot['oicsmiik9varyok'] == 1) {
        $this->doc->setCheckbox('oicsmiik9varyok', true);
      }
      if (isset($pot['oicsmiik171varyok']) && $pot['oicsmiik171varyok'] == 1) {
        $this->doc->setCheckbox('oicsmiik171varyok', true);
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 1) {
        $this->doc->setCheckbox('oicsmiik23varyok', true);
      }
      if (isset($pot['oicsmiik23varyok']) && $pot['oicsmiik23varyok'] == 1) {
        $this->doc->setCheckbox('oicsmiik24varyok', true);
      }
      if (isset($pot['digersistemlerneler']) && $pot['digersistemlerneler'] != '') {
        $this->doc->setCheckbox('digervaryok', true);
      }
      if (isset($pot['entegreysvarmi']) && $pot['entegreysvarmi'] == 1) {
        $this->doc->setCheckbox('entegreysvarmi', true);
      }
      if (isset($pot['yonetimsistemsertifikasi']) && $pot['yonetimsistemsertifikasi'] == '1') {
        $this->doc->setCheckbox('yonetimsistemievet', true);
      }
      if (isset($pot['yonetimsistemsertifikasi']) && $pot['yonetimsistemsertifikasi'] == '0') {
        $this->doc->setCheckbox('yonetimsistemihayir', true);
      }
      if (isset($pot['tumvardayni']) && $pot['tumvardayni'] == 'EVET') {
        $this->doc->setCheckbox('tumvardaynievet', true);
      }
      if (isset($pot['tumvardayni']) && $pot['tumvardayni'] == 'HAYIR') {
        $this->doc->setCheckbox('tumvardaynihayir', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'iptal') {
        $this->doc->setCheckbox('belgeiptal', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'aski') {
        $this->doc->setCheckbox('belgeaski', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'askiindir') {
        $this->doc->setCheckbox('belgeaskiindir', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckbox('belgeok', true);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckbox('belgeaski', false);
      }
      if (isset($pot['belgedurum']) && $pot['belgedurum'] == 'devam') {
        $this->doc->setCheckbox('belgeaskiindir', false);
      }
      if (isset($pot['subeturu1']) && $pot['subeturu1'] == '1') {
        $this->doc->setCheckbox('subeturu1', true);
      }
      if (isset($pot['subeturu1']) && $pot['subeturu1'] == '2') {
        $this->doc->setCheckbox('subeturu1', true);
      }
      if (isset($pot['subeturu2']) && $pot['subeturu2'] == '1') {
        $this->doc->setCheckbox('subeturu2', true);
      }
      if (isset($pot['subeturu2']) && $pot['subeturu2'] == '2') {
        $this->doc->setCheckbox('subeturu2', true);
      }
      if (isset($pot['subeturu3']) && $pot['subeturu3'] == '1') {
        $this->doc->setCheckbox('subeturu3', true);
      }
      if (isset($pot['subeturu3']) && $pot['subeturu3'] == '2') {
        $this->doc->setCheckbox('subeturu3', true);
      }
      if (isset($pot['subeturu4']) && $pot['subeturu4'] == '1') {
        $this->doc->setCheckbox('subeturu4', true);
      }
      if (isset($pot['subeturu4']) && $pot['subeturu4'] == '2') {
        $this->doc->setCheckbox('subeturu4', true);
      }

      // ENTEGRE SİSTEM SEÇENEKLERİ
      if (isset($pot['ygg']) && $pot['ygg'] == '12.5') {
        $this->doc->setCheckbox('ygg', true);
      }
      if (isset($pot['icdenetim']) && $pot['icdenetim'] == '12.5') {
        $this->doc->setCheckbox('icdenetim', true);
      }
      if (isset($pot['entegredokumantasyon']) && $pot['entegredokumantasyon'] == '12.5') {
        $this->doc->setCheckbox('entegredokumantasyon', true);
      }
      if (isset($pot['duzelticifaaliyet']) && $pot['duzelticifaaliyet'] == '12.5') {
        $this->doc->setCheckbox('duzelticifaaliyet', true);
      }
      if (isset($pot['yondessor']) && $pot['yondessor'] == '12.5') {
        $this->doc->setCheckbox('yondessor', true);
      }
      if (isset($pot['prosesentegre']) && $pot['prosesentegre'] == '12.5') {
        $this->doc->setCheckbox('prosesentegre', true);
      }
      if (isset($pot['politikahedefler']) && $pot['politikahedefler'] == '12.5') {
        $this->doc->setCheckbox('politikahedefler', true);
      }
      if (isset($pot['riskyonetimyaklasimi']) && $pot['riskyonetimyaklasimi'] == '12.5') {
        $this->doc->setCheckbox('riskyonetimyaklasimi', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'ilk') {
        $this->doc->setCheckbox('denetimtipiilk', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'g1') {
        $this->doc->setCheckbox('denetimtipig1', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'g2') {
        $this->doc->setCheckbox('denetimtipig2', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'yb') {
        $this->doc->setCheckbox('denetimtipiyb', true);
      }
      if (isset($pot['denetimtipi']) && $pot['denetimtipi'] == 'ozel') {
        $this->doc->setCheckbox('denetimtipiozel', true);
      }

      // karar gözden geçirmeler
      if (isset($pot['karargga']) && $pot['karargga'] == 'u') {
        $this->doc->setCheckbox('kararggaa', true);
      }
      if (isset($pot['karargga']) && $pot['karargga'] == 'ud') {
        $this->doc->setCheckbox('kararggab', true);
      }
      if (isset($pot['kararggb']) && $pot['kararggb'] == 'u') {
        $this->doc->setCheckbox('kararggba', true);
      }
      if (isset($pot['kararggb']) && $pot['kararggb'] == 'ud') {
        $this->doc->setCheckbox('kararggbb', true);
      }
      if (isset($pot['kararggc']) && $pot['kararggc'] == 'u') {
        $this->doc->setCheckbox('kararggca', true);
      }
      if (isset($pot['kararggc']) && $pot['kararggc'] == 'ud') {
        $this->doc->setCheckbox('kararggcb', true);
      }
      if (isset($pot['kararggd']) && $pot['kararggd'] == 'u') {
        $this->doc->setCheckbox('kararggda', true);
      }
      if (isset($pot['kararggd']) && $pot['kararggd'] == 'ud') {
        $this->doc->setCheckbox('kararggdb', true);
      }
      if (isset($pot['karargge']) && $pot['karargge'] == 'u') {
        $this->doc->setCheckbox('kararggea', true);
      }
      if (isset($pot['karargge']) && $pot['karargge'] == 'ud') {
        $this->doc->setCheckbox('kararggeb', true);
      }
      if (isset($pot['kararggf']) && $pot['kararggf'] == 'u') {
        $this->doc->setCheckbox('kararggfa', true);
      }
      if (isset($pot['kararggf']) && $pot['kararggf'] == 'ud') {
        $this->doc->setCheckbox('kararggfb', true);
      }
      if (isset($pot['kararggg']) && $pot['kararggg'] == 'u') {
        $this->doc->setCheckbox('kararggga', true);
      }
      if (isset($pot['kararggg']) && $pot['kararggg'] == 'ud') {
        $this->doc->setCheckbox('karargggb', true);
      }
      if (isset($pot['kararggh']) && $pot['kararggh'] == 'u') {
        $this->doc->setCheckbox('kararggha', true);
      }
      if (isset($pot['kararggh']) && $pot['kararggh'] == 'ud') {
        $this->doc->setCheckbox('karargghb', true);
      }
      if (isset($pot['kararggi']) && $pot['kararggi'] == 'u') {
        $this->doc->setCheckbox('kararggia', true);
      }
      if (isset($pot['kararggi']) && $pot['kararggi'] == 'ud') {
        $this->doc->setCheckbox('kararggib', true);
      }
      if (isset($pot['kararggj']) && $pot['kararggj'] == 'u') {
        $this->doc->setCheckbox('kararggja', true);
      }
      if (isset($pot['kararggj']) && $pot['kararggj'] == 'ud') {
        $this->doc->setCheckbox('kararggjb', true);
      }
      if (isset($pot['kararggk']) && $pot['kararggk'] == 'u') {
        $this->doc->setCheckbox('kararggka', true);
      }
      if (isset($pot['kararggk']) && $pot['kararggk'] == 'ud') {
        $this->doc->setCheckbox('kararggkb', true);
      }
      if (isset($pot['kararggl']) && $pot['kararggl'] == 'u') {
        $this->doc->setCheckbox('kararggla', true);
      }
      if (isset($pot['kararggl']) && $pot['kararggl'] == 'ud') {
        $this->doc->setCheckbox('karargglb', true);
      }
      if (isset($pot['kararggm']) && $pot['kararggm'] == 'u') {
        $this->doc->setCheckbox('kararggma', true);
      }
      if (isset($pot['kararggm']) && $pot['kararggm'] == 'ud') {
        $this->doc->setCheckbox('kararggmb', true);
      }
      // karar gözden geçirmeler

      if (isset($pot['bskarar']) && $pot['bskarar'] == 'u') {
        $this->doc->setCheckbox('bskararu', true);
      }
      if (isset($pot['bskarar']) && $pot['bskarar'] == 'ud') {
        $this->doc->setCheckbox('bskararud', true);
      }

      foreach ($a1sahaliste9 as $key => $value) {
        if (
          isset($pot['iso900115varyok']) &&
          $pot['iso900115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirsaha', true);
        }
        if (
          isset($pot['iso900115varyok']) &&
          $pot['iso900115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirofis', false);
        }
      }
      foreach ($a1sahaliste14 as $key => $value) {
        if (
          isset($pot['iso1400115varyok']) &&
          $pot['iso1400115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirsaha', true);
        }
        if (
          isset($pot['iso1400115varyok']) &&
          $pot['iso1400115varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirofis', false);
        }
      }
      foreach ($a1sahaliste45 as $key => $value) {
        if (
          isset($pot['iso4500118varyok']) &&
          $pot['iso4500118varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirsaha', true);
        }
        if (
          isset($pot['iso4500118varyok']) &&
          $pot['iso4500118varyok'] == 1 &&
          intval($pot['eakodu']) == intval($value)
        ) {
          $this->doc->setCheckbox('abirofis', false);
        }
      }

      if (
        $pot['iso2200018varyok'] == 1 ||
        $pot['helalvaryok'] == 1 ||
        $pot['oicsmiik6varyok'] == 1 ||
        $pot['oicsmiik9varyok'] == 1 ||
        $pot['oicsmiik171varyok'] == 1 ||
        $pot['oicsmiik23varyok'] == 1 ||
        $pot['oicsmiik24varyok'] == 1
      ) {
        $this->doc->setCheckbox('abirsaha', true);
        $this->doc->setCheckbox('abirofis', false);
      }

      // Set text values
      foreach ($pot as $name => $value) {
        if ($name === 'id') {
          continue;
        }
        $this->doc->setValue(strtolower($name), $value);
      }

      if (!file_exists($pati)) {
        if (!self::mkdirr($pati)) {
          self::msgError($pati . ' dizini oluşturulamadı...', false);
        }
      }

      $fname = $pati . '/' . $newFilem;
      if (file_exists($fname)) {
        unlink($fname);
      }
      $this->doc->saveAs($fname);
      $this->doc = '';

      $fname1 = '../setler/' . $pot['planno'] . '/' . $asama . '/' . iconv('ISO-8859-9', 'UTF-8', $newFilem);
      echo '<a href="' .
        route('downloadWordFile', [
          'pno' => $pot['planno'],
          'asama' => $asama,
          'dosya' => iconv('ISO-8859-9', 'UTF-8', $newFilem),
        ]) .
        '" class="col-sm-12 btn btn-success btn-sm" target="_blank">' .
        iconv('ISO-8859-9', 'UTF-8', $newFilem) .
        '</a><br>';
      if ($canzipped) {
        $this->canzip = $canzipped;
      }
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function createSertifika($filem, $newFilem, $pot, $setpath, $asama = '')
  {
    try {
      $pati = $setpath;

      if ($this->doc == '') {
        Settings::loadConfig();
        Settings::setTempDir(public_path() . '/temp');
        Settings::setOutputEscapingEnabled(true);
        Settings::setCompatibility(true);

        $this->doc = new TemplateProcessor($filem);
        $this->doc->setMacroOpeningChars('æ');
        $this->doc->setMacroClosingChars('æ');
        $this->doc->setMacroChars('æ', 'æ');
      }

      //            var_dump($pot);
      foreach ($pot as $name => $value) {
        if ($name === 'id') {
          continue;
        }
        //                echo $name." = ". $value."<br>";
        $this->doc->setValue(strtolower($name), $value);
        //                echo $name . " = " . $value . "<br>";
      }

      if (!file_exists($pati)) {
        if (!self::mkdirr($pati)) {
          self::msgError($pati . ' dizini oluşturulamadı...', false);
        }
      }

      //        echo $this->clearLocale($newFilem);
      $fname = $pati . '/' . $newFilem;
      if (file_exists($fname)) {
        unlink($fname);
      }
      $this->doc->saveAs($fname);
      $this->doc = '';

      //      rename($fname, "{$fname}");

      $fname1 = '../setler/' . $pot['planno'] . '/SERTIFIKA/' . iconv('ISO-8859-9', 'UTF-8', $newFilem);
      echo '<a href="' .
        route('downloadCertificateFile', [
          'pno' => $pot['planno'],
          'asama' => $asama,
          'dosya' => iconv('ISO-8859-9', 'UTF-8', $newFilem),
        ]) .
        '" class="col-sm-12 btn btn-success btn-sm" target="_blank">' .
        iconv('ISO-8859-9', 'UTF-8', $newFilem) .
        '</a><br>';
    } catch (Exception $e) {
      echo $e->getMessage();
    }
  }

  public function planKararKaydet(Request $request)
  {
    // 1) Temel form verilerini al
    $pot = $request->all();
    $pno = $pot['planno'] ?? null;
    if (!$pno) {
      // planno olmadan işlem yapamayız
      self::msgError('<br><br>Geçersiz plan numarası!');
      return;
    }
    if (intval($pot['helalvarmi']) == 1) {
      // min 2 denetçi ve 1 iku seçilmiş olmalı
      if (
        empty($pot['uye1adi']) ||
        empty($pot['uye2adi']) ||
        (empty($pot['uye3adi']) && empty($pot['uyeikuadi'])) ||
        (empty($pot['chkuye1adi']) ||
          empty($pot['chkuye2adi']) ||
          (empty($pot['chkuye3adi']) && empty($pot['chkuyeikuadi'])))
      ) {
        self::msgError('<br><br>En az 2 denetçi ve 1 İKU seçilmeli ve tüm üyeler onay vermelidir..');
        return;
      }
    }

    // 2) Dizin ve dosya yolu işlemleri
    $dizin = public_path();
    $pati = $dizin . '/setler/' . str_pad($pno, 4, '0', STR_PAD_LEFT);

    // 3) EA/NACE Kodu Mantığı
    //    firmanacekodu, eakodu, categories, oiccategories, enysteknikalan, bgcategories
    $pot['eanacekodlari'] = $pot['firmanacekodu'] ?? '';

    $pot['eanacekodlaribaskan'] = self::getEaNaceKategoriPerDenetci(
      $pot['eakodu'],
      $pot['firmanacekodu'],
      $pot['categories'],
      $pot['oiccategories'],
      $pot['enysteknikalan'],
      $pot['bgcategories'],
      $pot['kararaonerilendenetci']
    );
    $pot['eanacekodlariuye1'] = self::getEaNaceKategoriPerDenetci(
      $pot['eakodu'],
      $pot['firmanacekodu'],
      $pot['categories'],
      $pot['oiccategories'],
      $pot['enysteknikalan'],
      $pot['bgcategories'],
      $pot['uye1adi']
    );
    $pot['eanacekodlariuye2'] = self::getEaNaceKategoriPerDenetci(
      $pot['eakodu'],
      $pot['firmanacekodu'],
      $pot['categories'],
      $pot['oiccategories'],
      $pot['enysteknikalan'],
      $pot['bgcategories'],
      $pot['uye2adi']
    );
    $pot['eanacekodlariuye3'] = self::getEaNaceKategoriPerDenetci(
      $pot['eakodu'],
      $pot['firmanacekodu'],
      $pot['categories'],
      $pot['oiccategories'],
      $pot['enysteknikalan'],
      $pot['bgcategories'],
      $pot['uye3adi']
    );

    // 3a) İslami uzman ataması (varsa)
    //     örneğin "eanacekodlariuye2iku" doluysa "eanacekodlariuye2" = "İslami Konular"
    if (!empty($pot['eanacekodlariuye2iku'])) {
      $pot['eanacekodlariuye2'] = 'İslami Konular';
    } elseif (!empty($pot['eanacekodlariuye3iku'])) {
      $pot['eanacekodlariuye3'] = 'İslami Konular';
    }

    // 4) Kategori birleştirme işlemleri
    //    Not: Bu kısım proje mantığınıza ait.
    $catSimple = [];
    if (self::InStr(trim($pot['categories']), ',') > -1) {
      foreach (explode(',', $pot['categories']) as $cat) {
        $c = substr(trim($cat), 0, 1);
        if (!in_array($c, $catSimple, true)) {
          $catSimple[] = $c;
        }
      }
    } else {
      $c = substr(trim($pot['categories']), 0, 1);
      $catSimple[] = $c;
    }

    $catOicSimple = [];
    if (self::InStr(trim($pot['oiccategories']), ',') > -1) {
      foreach (explode(',', $pot['oiccategories']) as $cat) {
        $c = substr(trim($cat), 0, 1);
        if (!in_array($c, $catOicSimple, true)) {
          $catOicSimple[] = $c;
        }
      }
    } else {
      $c = substr(trim($pot['oiccategories']), 0, 1);
      $catOicSimple[] = $c;
    }

    // Birleştirme
    $sonuc = implode(',', $catSimple) . ',' . implode(',', $catOicSimple);
    $pot['kategori'] = rtrim(trim($sonuc), ',');

    // Kategorileri birleştirme
    $pot['categories'] = ($pot['categories'] ?? '') . ', ' . ($pot['oiccategories'] ?? '');
    // eakodu birleştirme
    if (!empty($pot['eakodu'])) {
      $pot['eakodu'] = $pot['eakodu'] . ', ' . ($pot['enysteknikalan'] ?? '');
    } else {
      $pot['eakodu'] = $pot['enysteknikalan'] ?? '';
    }

    // 5) Başdenetçinin değerlendirmeleri (kararbda..kararbdj)
    //    2 => "X", 1 => "X", 0 => "X"
    //    Örneğin "kararbda" => "2" -> pot["kararbdaa"] = "X"
    //    Kodunuzu koruyoruz, sadece küçük sadeleştirme:
    $mapRadio = [
      'kararbda' => 'kararbdaa,kararbdab,kararbdac',
      'kararbdb' => 'kararbdba,kararbdbb,kararbdbc',
      'kararbdc' => 'kararbdca,kararbdcb,kararbdcc',
      'kararbdd' => 'kararbdda,kararbddb,kararbddc',
      'kararbde' => 'kararbdea,kararbdeb,kararbdec',
      'kararbdf' => 'kararbdfa,kararbdfb,kararbdfc',
      'kararbdg' => 'kararbdga,kararbdgb,kararbdgc',
      'kararbdh' => 'kararbdha,kararbdhb,kararbdhc',
      'kararbdi' => 'kararbdia,kararbdib,kararbdic',
      'kararbdj' => 'kararbdja,kararbdjb,kararbdjc',
    ];

    foreach ($mapRadio as $radioKey => $fields) {
      // $fields "kararbdaa,kararbdab,kararbdac" vb.
      $splitFields = explode(',', $fields); // 3 alan
      // 2 => a, 1 => b, 0 => c
      $selected = $pot[$radioKey] ?? null;
      // Tüm alanları "" yap
      $pot[$splitFields[0]] = '';
      $pot[$splitFields[1]] = '';
      $pot[$splitFields[2]] = '';
      if ($selected === '2') {
        $pot[$splitFields[0]] = 'X';
      }
      if ($selected === '1') {
        $pot[$splitFields[1]] = 'X';
      }
      if ($selected === '0') {
        $pot[$splitFields[2]] = 'X';
      }
    }

    // 6) Karar dizin (g1karar, g2karar, ybkarar, ozel vb.)
    $karardizin = 'D';
    if (!empty($pot['asama'])) {
      switch ($pot['asama']) {
        case 'g1karar':
          $karardizin = 'E';
          break;
        case 'g2karar':
          $karardizin = 'F';
          break;
        case 'ybkarar':
          $karardizin = 'YB';
          break;
        case 'ozel':
          $karardizin = 'OK';
          break;
        default:
          $karardizin = 'D';
          break;
      }
    }
    $patia1 = $pati . '/KARAR' . $karardizin;

    // 7) Belge durumu ve tarih kontrol
    $sinir = strtotime($pot['degerlendirmekarartarih'] ?? ''); // yoksa 0 döner
    $kontrol1 = strtotime('10.05.2021');
    $kontrol2 = strtotime('01.11.2021');

    $yil = date('Y');
    $durum = '';
    if (($pot['belgedurum'] ?? '') === 'devam') {
      $pot['kararaciklama'] = '';
    } elseif (($pot['belgedurum'] ?? '') === 'aski') {
      $durum = '-ASKI';
      if (empty($pot['kararaciklama'])) {
        $pot['kararaciklama'] = 'Geçerlilik tarihi itibariyle gözetim denetimi planlanmadığından askıya alınmıştır.';
      }
      $pot['uye1adi'] = '';
      $pot['uye2adi'] = '';
      $pot['uye3adi'] = '';
      $pot['uyeikuadi'] = '';
    } elseif ($pot['belgedurum'] === 'askiindir') {
      $durum = '-ASKI INDIR';
    } elseif ($pot['belgedurum'] === 'iptal') {
      $durum = '-IPTAL';
      $pot['uye1adi'] = '';
      $pot['uye2adi'] = '';
      $pot['uye3adi'] = '';
      $pot['uyeikuadi'] = '';
    } elseif ($pot['belgedurum'] === 'ybver') {
      $durum = '-YB';
    }

    // 7a) Dosya oluşturma
    if ($sinir !== false && $sinir > $kontrol1 && $sinir < $kontrol2) {
      $filem1 = $dizin . '/sablonlar/sistem/' . $karardizin . '/AFR.16GozdenGecirmeveKararFormuR8.docx';
      $newFilem1 = "AFR.16 Gozden Gecirme ve Karar Formu Rev.08-{$yil}{$durum}.docx";
      self::setDosyasiOlustur(
        $filem1,
        iconv('UTF-8', 'ISO-8859-9', $newFilem1),
        $pot,
        $patia1,
        false,
        "KARAR{$karardizin}"
      );
    }
    if ($sinir !== false && $sinir >= $kontrol2) {
      $filem1 = $dizin . '/sablonlar/sistem/' . $karardizin . '/AFR.16GozdenGecirmeveKararFormuR9.docx';
      $newFilem1 = "AFR.16 Gozden Gecirme ve Karar Formu Rev.09-{$yil}{$durum}.docx";
      self::setDosyasiOlustur(
        $filem1,
        iconv('UTF-8', 'ISO-8859-9', $newFilem1),
        $pot,
        $patia1,
        false,
        "KARAR{$karardizin}"
      );
    }

    // 8) plan_karar updateOrInsert
    $kararData = [
      'degerlendirmekarartarih' => $pot['degerlendirmekarartarih'] ?? '',
      'uye1adi' => $pot['uye1adi'] ?? '',
      'uye2adi' => $pot['uye2adi'] ?? '',
      'uye3adi' => $pot['uye3adi'] ?? '',
      'uye4adi' => '', // Varsayılan
      'uyeikuadi' => $pot['uyeikuadi'] ?? '',
      'kararaciklama' => $pot['kararaciklama'] ?? '',
      'belgedurum' => $pot['belgedurum'] ?? '',
      'bskarar' => $pot['bskarar'] ?? '',
    ];
    $kararkayit = DB::table('plan_karar')->updateOrInsert(['planno' => $pno], $kararData);

    // 9) plan_karar_gg updateOrInsert
    //    (karargga..kararggm) => "u" ise "u", yoksa ""
    $kararggData = ['planno' => $pno];
    $kararggKeys = [
      'karargga',
      'kararggb',
      'kararggc',
      'kararggd',
      'karargge',
      'kararggf',
      'kararggg',
      'kararggh',
      'kararggi',
      'kararggj',
      'kararggk',
      'kararggl',
      'kararggm',
    ];
    foreach ($kararggKeys as $key) {
      $kararggData[$key] = isset($pot[$key]) && $pot[$key] === 'u' ? 'u' : '';
    }
    DB::table('plan_karar_gg')->updateOrInsert(['planno' => $pno], $kararggData);

    // 10) plan_karar_bd updateOrInsert (başdenetçi değerlendirmeleri + puanlar)
    $kararbdData = [
      'planno' => $pno,
      'kararbda' => $pot['kararbda'] ?? '',
      'kararbdb' => $pot['kararbdb'] ?? '',
      'kararbdc' => $pot['kararbdc'] ?? '',
      'kararbdd' => $pot['kararbdd'] ?? '',
      'kararbde' => $pot['kararbde'] ?? '',
      'kararbdf' => $pot['kararbdf'] ?? '',
      'kararbdg' => $pot['kararbdg'] ?? '',
      'kararbdh' => $pot['kararbdh'] ?? '',
      'kararbdi' => $pot['kararbdi'] ?? '',
      'kararbdj' => $pot['kararbdj'] ?? '',
      'toplampuan' => $pot['toplampuan'] ?? '0',
      'ortalamapuan' => str_replace('%', '', $pot['ortalamapuan']) ?? '0',
    ];
    DB::table('plan_karar_bd')->updateOrInsert(['planno' => $pno], $kararbdData);

    // 11) plan_karar updateOrInsert
    $kararOnayData = [
      'uye1adi' => $pot['chkuye1adi'] ?? 0,
      'uye2adi' => $pot['chkuye2adi'] ?? 0,
      'uye3adi' => $pot['chkuye3adi'] ?? 0,
      'uye4adi' => 0, // Varsayılan
      'uyeikuadi' => $pot['chkuyeikuadi'] ?? 0,
      'kararaciklama' => $pot['kararaciklama'] ?? '',
    ];
    DB::table('plan_karar_onay')->updateOrInsert(['planno' => $pno], $kararOnayData);

    // 12) Sonuç mesajı
    if (!$kararkayit) {
      self::msgError('<br><br>Karar komite bilgileri veritabanına kayıt edilemedi!');
    } else {
      self::msgSuccess('<br><br>Karar komite bilgileri veritabanına kayıt edildi.');
    }
  }

  public function planSertifika(Request $request)
  {
    $pot = $request->all();
    $pno = $pot['planno'];

    $dizin = public_path();
    $patia1 = $dizin . '/setler/' . str_pad($pno, 4, '0', STR_PAD_LEFT) . '/SERTIFIKA';

    $pot['certno'] = $pot['certkodu'] . $pot['certno'];
    $pot['soarevnodate'] = $pot['soarevnotarihi'];

    if ($pot['stdadi'] == 'ISO 9001:2015') {
      $newFilem = 'iso90012015_' . date('Y') . '.docx';
      $pot['stdtext'] = 'KALİTE YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'QUALITY MANAGEMENT SYSTEM';
      //            $pot["alimentlogo"] = $_SESSION['REALPATH']."images/certlogo/iso9001.bmp";
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrQ2015.docx';
    }
    if ($pot['stdadi'] == 'ISO 14001:2015') {
      $newFilem = 'iso140012015_' . date('Y') . '.docx';
      $pot['stdtext'] = 'ÇEVRE YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'ENVIRONMENT MANAGEMENT SYSTEM';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrE2015.docx';
    }
    if ($pot['stdadi'] == 'ISO 22000:2018') {
      $newFilem = 'iso2200018_' . date('Y') . '.docx';
      $pot['stdtext'] = 'GIDA GÜVENLİĞİ YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'FOOD SAFETY MANAGEMENT SYSTEM';
      $pot['kategori'] = $pot['categories'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrF2018.docx';
    }
    if ($pot['stdadi'] == 'ISO 45001:2018') {
      if ($pot['akredite'] == 'yes') {
        $newFilem = 'iso4500118_' . date('Y') . '.docx';
        $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatr4500118.docx';
      } else {
        $newFilem = 'iso45001_' . date('Y') . '.docx';
        $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatr45001.docx';
      }

      $pot['stdtext'] = 'İŞ SAĞLIĞI VE GÜVENLİĞİ YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'OCCUPATIONAL HEALTH AND SAFETY MANAGEMENT SYSTEM';
    }
    if ($pot['stdadi'] == 'ISO 10002:2018') {
      $newFilem = 'iso10002_' . date('Y') . '.docx';
      $pot['stdtext'] = 'MÜŞTERİ MEM. VE MÜŞTERİ ŞİKAYETLERİ Y.S.';
      $pot['stdtexting'] = 'CUSTOMER SATISFACTION AND CUSTOMER COMPLAINT M.S.';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrC.docx';
    }
    if ($pot['stdadi'] == 'HELAL GIDA') {
      $newFilem = 'helal.docx';
      $pot['stdtext'] = 'HELAL GIDA';
      $pot['stdtexting'] = 'HALAL FOOD';
      $filem = $dizin . '/sablonlar/sistem/sertifika/helalsertifika.docx';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 1:2019') {
      $newFilem = 'oicsmiic_' . date('Y') . '.docx';
      $pot['stdtext'] = 'Helal Gıda İçin Genel Gereklilikler';
      $pot['stdtexting'] = 'General Requirements for Halal Food';
      $pot['kategori'] = $pot['oiccategories'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/oicsmiic.docx';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 9:2019') {
      $newFilem = 'oicsmiic9_' . date('Y') . '.docx';
      $pot['stdtext'] = 'Helal Turizm Hizmetleri Genel Gereklilikler';
      $pot['stdtexting'] = 'General Requirements for Halal Tourism Services';
      $pot['kategori'] = $pot['oiccategories'];
      $pot['kategori'] .= '-' . $pot['smiic9tip'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/oicsmiic.docx';

      $newFilemek = 'SMIIC9Ek' . $pot['smiic9tip'] . '.docx';
      $filemek = $_SESSION['SABLONPATHSIS'] . 'sertifika/SMIIC9Ek' . $pot['smiic9tip'] . '.docx';
      self::createSertifika($filemek, iconv('UTF-8', 'ISO-8859-9', $newFilemek), $pot, $patia1, 'SERTIFIKA');
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 6:2019') {
      $newFilem = 'oicsmiic6_' . date('Y') . '.docx';
      $pot['stdtext'] =
        'İİT/SMIIC 1`in Helal Yiyecek ve İçeceklerin Hazırlandığı, Depolandığı ve Servis Edildiği Yerlere Uygulanması İçin Özel Gereksinimler';
      $pot['stdtexting'] =
        'Special Requirements for the Application of OIC/SMIIC 1 to Where Halal Food and Drinks are Prepared, Stored and Served';
      $pot['kategori'] = $pot['oiccategories'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/oicsmiic6.docx';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 24:2020') {
      $pot['stdadi'] = 'OIC/SMIIC 1:2019';
      $newFilem = 'oicsmiic24_' . date('Y') . '.docx';
      $pot['stdtext'] = 'Helal Gıda İçin Genel Gereklilikler';
      $pot['stdtexting'] = 'General Requirements for Halal Food';
      $pot['kategori'] = $pot['oiccategories'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/oicsmiic24.docx';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 17:2020') {
      $pot['stdadi'] = 'OIC/SMIIC 1:2019';
      $pot['stdtext'] = 'Helal Gıda İçin Genel Gereklilikler';
      $pot['stdtexting'] = 'General Requirements for Halal Food';
      $newFilem = 'oicsmiic17_' . date('Y') . '.docx';
      if ($pot['smiic17'] == 1) {
        $pot['smiicstd'] = 'OIC/SMIIC 17-1:2020';
      }
      if ($pot['smiic17'] == 2) {
        $pot['smiicstd'] = 'OIC/SMIIC 17-2:2020';
      }
      if ($pot['smiic17'] == 3) {
        $pot['smiicstd'] = 'OIC/SMIIC 17-1:2020, OIC/SMIIC 17-2:2020';
      }

      $pot['kategori'] = $pot['oiccategories'];
      $filem = $dizin . '/sablonlar/sistem/sertifika/oicsmiic17.docx';
    }

    if ($pot['stdadi'] == 'FSSC 22000:2011') {
      $newFilem = 'fssc.docx';
      $pot['stdtext'] = 'GIDA GÜVENLİĞİ SİSTEM SERTİFİKASYONU';
      $pot['stdtexting'] = 'FOOD SAFETY SYSTEM CERTIFICATION';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrFS.docx';
    }
    if ($pot['stdadi'] == 'HACCP') {
      $newFilem = 'haccp.docx';
      $pot['stdtext'] = 'TEHLİKE ANALİZİ KRİTİK KONTROL NOKTALARI';
      $pot['stdtexting'] = 'HAZARD ANALYSIS CRITICAL CONTROL POINTS';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrFC.docx';
    }
    if ($pot['stdadi'] == 'ISO 27001:2022') {
      $newFilem = 'iso27001_' . date('Y') . '.docx';
      $pot['stdtext'] = 'ISO 27001:2022 - BİLGİ GÜVENLİĞİ YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'ISO 27001:2022 - INFORMATION SAFETY MANAGEMENT SYSTEM';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrBGYS.docx';
    }
    if ($pot['stdadi'] == 'GDP') {
      $newFilem = 'sertifikatrGDP_' . date('Y') . '.docx';
      $pot['stdtext'] = 'İYİ DAĞITIM UYGULAMALARI';
      $pot['stdtexting'] = 'GOOD DISTRIBUTION PRACTICE';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrGDP.docx';
    }
    if ($pot['stdadi'] == 'GMP') {
      $newFilem = 'sertifikatrGMP_' . date('Y') . '.docx';
      $pot['stdtext'] = 'İYİ ÜRETİM UYGULAMASI';
      $pot['stdtexting'] = 'GOOD MANUFACTURING PRACTICE';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrGMP.docx';
    }
    if ($pot['stdadi'] == 'ISO 50001:2018') {
      if ($pot['akredite'] == 'yes') {
        $newFilem = 'iso5000118_' . date('Y') . '.docx';
        $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatr5000118.docx';
      } else {
        $newFilem = 'iso50001_' . date('Y') . '.docx';
        $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatr50001.docx';
      }
      $pot['stdtext'] = 'ENERJİ YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'ENERGY MANAGEMENT SYSTEM';
    }
    if ($pot['stdadi'] == 'IFS FOOD') {
      $newFilem = 'sertifikatrifs.docx';
      $pot['stdtext'] = 'INTERNATIONAL FOOD STANDARD';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrifs.docx';
    }
    if ($pot['stdadi'] == 'COVİD-19') {
      $newFilem = 'covid19tr.docx';
      $pot['stdtext'] = '';
      $filem = $dizin . '/sablonlar/sistem/sertifika/covid19tr.docx';
    }
    if ($pot['stdadi'] == 'SA8000:2014') {
      $newFilem = 'sertifikatrsa8000.docx';
      $pot['stdtext'] = 'SOSYAL SORUMLULUK YÖNETİM SİSTEMİ';
      $pot['stdtexting'] = 'SOCIAL ACCOUNTABILITY MANAGEMENT SYSTEM';
      $filem = $dizin . '/sablonlar/sistem/sertifika/sertifikatrsa8000.docx';
    }

    self::createSertifika($filem, iconv('UTF-8', 'ISO-8859-9', $newFilem), $pot, $patia1, 'SERTIFIKA');
  }

  public function planSertifikaKaydet(Request $request)
  {
    $pot = $request->all();
    $pno = $pot['planno'];

    $pot['belgeno'] = $pot['certkodu'] . $pot['certno']; //.$pot["certgozkodu"];

    $akreditasyon = 'Akreditasyonlu';
    if ($pot['stdadi'] == 'ISO 9001:2015') {
      $stdtext = $pot['stdadi'] . ' - KALİTE YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'ISO 14001:2015') {
      $stdtext = $pot['stdadi'] . ' - ÇEVRE YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'ISO 22000:2018') {
      $stdtext = $pot['stdadi'] . ' - GIDA GÜVENLİĞİ YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 1:2019') {
      $stdtext = $pot['stdadi'] . ' - Helal Gıda İçin Genel Gereklilikler';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 9:2019') {
      $stdtext = $pot['stdadi'] . ' - Helal Turizm Hizmetleri Genel Gereklilikler';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 6:2019') {
      $stdtext =
        $pot['stdadi'] .
        ' - İİT/SMIIC 1`in Helal Yiyecek ve İçeceklerin Hazırlandığı, Depolandığı ve Servis Edildiği Yerlere Uygulanması İçin Özel Gereksinimler';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'OIC/SMIIC 24:2020') {
      $stdtext =
        $pot['stdadi'] .
        ' - Gıda Katkı Maddeleri ve Helal Gıdaya Eklenen Diğer Kimyasallara İlişkin Genel Gereklilikler';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'ISO 45001:2018') {
      if ($pot['akredite'] == 'yes') {
        $akreditasyon = 'Akreditasyonlu';
      } else {
        $akreditasyon = 'Akreditasyonsuz';
      }
      $stdtext = $pot['stdadi'] . ' - İŞ SAĞLIĞI VE GÜVENLİĞİ YÖNETİM SİSTEMİ';
    }
    if ($pot['stdadi'] == 'ISO 10002:2018') {
      $stdtext = $pot['stdadi'] . ' - MÜŞTERİ MEM. VE MÜŞTERİ ŞİKAYETLERİ Y.S.';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'HELAL GIDA') {
      $stdtext = 'HELAL GIDA';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'FSSC 22000:2011') {
      $stdtext = 'GIDA GÜVENLİĞİ SİSTEM SERTİFİKASYONU';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'HACCP') {
      $stdtext = 'HAZARD ANALYSIS CRITICAL CONTROL POINTS';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'GDP') {
      $stdtext = 'GOOD DISTRIBUTION PRACTICE';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'GMP') {
      $stdtext = 'İYİ ÜRETİM UYGULAMASI';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'ISO 27001:2022') {
      $stdtext = $pot['stdadi'] . ' - BİLGİ GÜVENLİĞİ YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'ISO 50001:2018') {
      $stdtext = $pot['stdadi'] . ' - ENERJİ YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonlu';
    }
    if ($pot['stdadi'] == 'IFS FOOD') {
      $stdtext = 'INTERNATIONAL FOOD STANDARD';
      $akreditasyon = 'Akreditasyonsuz';
    }
    if ($pot['stdadi'] == 'SA8000:2014') {
      $stdtext = 'SOSYAL SORUMLULUK YÖNETİM SİSTEMİ';
      $akreditasyon = 'Akreditasyonsuz';
    }

    $rowcertno = date_create_from_format('d.m.Y', $pot['ilkyayin']);
    $pot['ilkyayin'] = date_format($rowcertno, 'Y-m-d');
    $rowcertno = date_create_from_format('d.m.Y', $pot['yayintarihi']);
    $pot['yayintarihi'] = date_format($rowcertno, 'Y-m-d');
    $rowcertno = date_create_from_format('d.m.Y', $pot['gecerliliktarihi']);
    $pot['gecerliliktarihi'] = date_format($rowcertno, 'Y-m-d');
    $rowcertno = date_create_from_format('d.m.Y', $pot['bitistarihi']);
    $pot['bitistarihi'] = date_format($rowcertno, 'Y-m-d');

    $durum = 'Geçerli';

    if ($pot['akredite'] == 'no') {
      $akreditasyon = 'Akreditasyonsuz';
    }

    $vals = [];

    $vals['belgeno'] = $pot['belgeno'];
    $vals['standard'] = $stdtext;
    $vals['ilkyayin'] = $pot['ilkyayin'];
    $vals['yayintarihi'] = $pot['yayintarihi'];
    $vals['gecerliliktarihi'] = $pot['gecerliliktarihi'];
    $vals['bitistarihi'] = $pot['bitistarihi'];
    $vals['akreditasyon'] = $akreditasyon;
    $vals['durum'] = $durum;
    $certkayit = DB::table('plan_sertifika')->updateOrInsert(['planno' => $pno], $vals);

    if (!$certkayit) {
      self::msgError('Sertifika bilgileri veritabanına kayıt edilemedi');
    } else {
      self::msgSuccess('Sertifika bilgileri veritabanına kayıt edildi');
    }
  }

  public function auditUpload(Request $request)
  {
    $kid = Auth::user()->kurulusid;
    if (intval($kid) < 0) {
      return view('content.planlama.auditupload', ['kiderror' => 'Seçili kuruluşa ait bilgiler alınamadı.']);
    }

    $basvuru = DB::select(
      'SELECT * FROM basvuru where planno=' . $request->pno . ' and kid=' . $kid . ' ORDER BY planno DESC'
    );

    $plan = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.planno', '=', $request->pno, 'and')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.planno', '=', $request->pno, 'and')
      ->where('planlar.kid', '=', $kid)
      ->get();

    return view('content.planlama.auditupload', [
      'basvuru' => $basvuru,
      'plan' => $plan,
      'asama' => $request->asama,
      'pno' => $request->pno,
    ]);
  }

  public static function downloadWordFile(Request $request)
  {
    $path = public_path() . '/setler/' . $request->pno . '/' . $request->asama . '/' . $request->dosya;
    return response()->download($path);
  }

  public static function downloadCertificateFile(Request $request)
  {
    $path = public_path() . '/setler/' . $request->pno . '/' . $request->asama . '/' . $request->dosya;
    return response()->download($path);
  }

  public static function downloadZipFile(Request $request)
  {
    $path = public_path() . '/setler/zips/' . $request->dosya;
    return response()->download($path);
  }

  public static function atananSistemler($denetci, $sistemler = '')
  {
    if ($denetci == '') {
      return '';
    }
    $atanansistemler = '';

    $statement = "denetciler where denetci='" . $denetci . "'";

    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";
    $result = DB::select($sqlSQL);
    //        var_dump($result);
    if ($sistemler == '') {
      foreach ($result as $ret) {
        $atanansistemler .= strlen($ret->atama9001) > 0 ? '9001, ' : '';
        $atanansistemler .= strlen($ret->atama14001) > 0 ? '14001, ' : '';
        $atanansistemler .= strlen($ret->atama22000) > 0 ? '22000, ' : '';
        $atanansistemler .= strlen($ret->atama45001) > 0 ? '45001, ' : '';
        $atanansistemler .= strlen($ret->atama50001) > 0 ? '50001, ' : '';
        $atanansistemler .= strlen($ret->atama27001) > 0 ? '27001, ' : '';
        $atanansistemler .= strlen($ret->atamaOicsmiic) > 0 ? 'OIC/SMIIC 1, ' : '';
        $atanansistemler .= strlen($ret->atamaOicsmiic6) > 0 ? 'OIC/SMIIC 6, ' : '';
        $atanansistemler .= strlen($ret->atamaOicsmiic9) > 0 ? 'OIC/SMIIC 9, ' : '';
        $atanansistemler .= strlen($ret->atamaOicsmiic171) > 0 ? 'OIC/SMIIC 17-1, ' : '';
        $atanansistemler .= strlen($ret->atamaOicsmiic24) > 0 ? 'OIC/SMIIC 24, ' : '';
      }
    } else {
      foreach ($result as $ret) {
        $atanansistemler .= strlen($ret->atama9001) > 0 && self::InStr($sistemler, '9001') > -1 ? '9001, ' : '';
        $atanansistemler .= strlen($ret->atama14001) > 0 && self::InStr($sistemler, '14001') > -1 ? '14001, ' : '';
        $atanansistemler .= strlen($ret->atama22000) > 0 && self::InStr($sistemler, '22000') > -1 ? '22000, ' : '';
        $atanansistemler .= strlen($ret->atama45001) > 0 && self::InStr($sistemler, '45001') > -1 ? '45001, ' : '';
        $atanansistemler .= strlen($ret->atama50001) > 0 && self::InStr($sistemler, '50001') > -1 ? '50001, ' : '';
        $atanansistemler .= strlen($ret->atama27001) > 0 && self::InStr($sistemler, '27001') > -1 ? '27001, ' : '';
        $atanansistemler .=
          strlen($ret->atamaOicsmiic) > 0 && self::InStr($sistemler, 'OIC/SMIIC 1') > -1 ? 'OIC/SMIIC 1, ' : '';
        $atanansistemler .=
          strlen($ret->atamaOicsmiic6) > 0 && self::InStr($sistemler, 'OIC/SMIIC 6') > -1 ? 'OIC/SMIIC 6, ' : '';
        $atanansistemler .=
          strlen($ret->atamaOicsmiic9) > 0 && self::InStr($sistemler, 'OIC/SMIIC 9') > -1 ? 'OIC/SMIIC 9, ' : '';
        $atanansistemler .=
          strlen($ret->atamaOicsmiic171) > 0 && self::InStr($sistemler, 'OIC/SMIIC 17-1') > -1
            ? 'OIC/SMIIC 17-1, '
            : '';
        $atanansistemler .=
          strlen($ret->atamaOicsmiic24) > 0 && self::InStr($sistemler, 'OIC/SMIIC 24') > -1 ? 'OIC/SMIIC 24, ' : '';
      }
    }
    $atanansistemler = substr($atanansistemler, 0, -2);

    return $atanansistemler;
  }

  public static function atananSistemlerAdaylik($denetci)
  {
    if ($denetci == '') {
      return '';
    }
    $atanansistemler = '';

    $statement = "denetciler where denetci='" . $denetci . "'";

    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";
    $result = DB::select($sqlSQL);
    //        var_dump($result);
    foreach ($result as $ret) {
      $atanansistemler .= strlen($ret->atama9001) > 0 && self::InStr($ret->atama9001, 'Aday') > -1 ? '9001, ' : '';
      $atanansistemler .= strlen($ret->atama14001) > 0 && self::InStr($ret->atama14001, 'Aday') > -1 ? '14001, ' : '';
      $atanansistemler .= strlen($ret->atama22000) > 0 && self::InStr($ret->atama22000, 'Aday') > -1 ? '22000, ' : '';
      $atanansistemler .= strlen($ret->atama45001) > 0 && self::InStr($ret->atama45001, 'Aday') > -1 ? '45001, ' : '';
      $atanansistemler .= strlen($ret->atama50001) > 0 && self::InStr($ret->atama50001, 'Aday') > -1 ? '50001, ' : '';
      $atanansistemler .= strlen($ret->atama27001) > 0 && self::InStr($ret->atama27001, 'Aday') > -1 ? '27001, ' : '';
      $atanansistemler .=
        strlen($ret->atamaOicsmiic) > 0 && self::InStr($ret->atamaOicsmiic, 'Aday') > -1 ? 'OIC/SMIIC 1, ' : '';
      $atanansistemler .=
        strlen($ret->atamaOicsmiic6) > 0 && self::InStr($ret->atamaOicsmiic6, 'Aday') > -1 ? 'OIC/SMIIC 6, ' : '';
      $atanansistemler .=
        strlen($ret->atamaOicsmiic9) > 0 && self::InStr($ret->atamaOicsmiic9, 'Aday') > -1 ? 'OIC/SMIIC 9, ' : '';
      $atanansistemler .=
        strlen($ret->atamaOicsmiic171) > 0 && self::InStr($ret->atamaOicsmiic171, 'Aday') > -1
          ? 'OIC/SMIIC 17-1, '
          : '';
      $atanansistemler .=
        strlen($ret->atamaOicsmiic24) > 0 && self::InStr($ret->atamaOicsmiic24, 'Aday') > -1 ? 'OIC/SMIIC 24, ' : '';
    }
    $atanansistemler = substr($atanansistemler, 0, -2);

    return $atanansistemler;
  }

  public static function getEaNacePerDenetci($nace)
  {
    $sonuc = '';
    $nacebol = explode(',', $nace);

    foreach ($nacebol as $nave) {
      $sqlSQL = "select * from eanacekodlari where nace LIKE '%" . trim($nave) . "%'";
      $eakod = DB::select($sqlSQL)[0]->ea;

      if (self::InStr($sonuc, $eakod . '/') == -1) {
        $sonuc .= "<span style='font-weight: bolder;color: red'>" . $eakod . '/</span>' . $nave . ', ';
      } else {
        $sonuc .= $nave . ', ';
      }
      continue;
    }
    $sonuc = substr($sonuc, 0, -1);

    return $sonuc;
  }

  public static function getBasdenetci()
  {
    $statement =
      "denetciler where (atama9001='Başdenetçi' or atama14001='Başdenetçi' or atama22000='Başdenetçi' or atamaOicsmiic='Başdenetçi' or atamaOicsmiic6='Başdenetçi' or atamaOicsmiic9='Başdenetçi' or atamaOicsmiic171='Başdenetçi' or atamaOicsmiic24='Başdenetçi' or atama45001='Başdenetçi' or atama50001='Başdenetçi' or atama27001='Başdenetçi') and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->nace = self::getEaNacePerDenetci($value->nace);
      $denetciler[$key]->sistemler = self::atananSistemler($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }

  public static function getDenetci()
  {
    $statement =
      "denetciler where (atama9001='Başdenetçi' or atama14001='Başdenetçi' or atama22000='Başdenetçi' or atamaOicsmiic='Başdenetçi' or atamaOicsmiic6='Başdenetçi' or atamaOicsmiic9='Başdenetçi' or atamaOicsmiic171='Başdenetçi' or atamaOicsmiic24='Başdenetçi' or atama45001='Başdenetçi' or atama50001='Başdenetçi' or atama27001='Başdenetçi') and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->nace = self::getEaNacePerDenetci($value->nace);
      $denetciler[$key]->sistemler = self::atananSistemler($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }

  public static function getTeknikUzman()
  {
    $statement =
      "denetciler where (atama9001='Teknik Uzman' or atama14001='Teknik Uzman' or atama22000='Teknik Uzman' or atamaOicsmiic='Teknik Uzman' or atamaOicsmiic6='Teknik Uzman' or atamaOicsmiic9='Teknik Uzman' or atamaOicsmiic171='Teknik Uzman' or atamaOicsmiic24='Teknik Uzman' or atama45001='Teknik Uzman' or atama50001='Teknik Uzman' or atama27001='Teknik Uzman') and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->nace = self::getEaNacePerDenetci($value->nace);
      $denetciler[$key]->sistemler = self::atananSistemler($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }

  public static function getGozlemci()
  {
    $statement =
      "denetciler where (atama9001='Gözlemci' or atama14001='Gözlemci' or atama22000='Gözlemci' or atamaOicsmiic='Gözlemci' or atamaOicsmiic6='Gözlemci' or atamaOicsmiic9='Gözlemci' or atamaOicsmiic171='Gözlemci' or atamaOicsmiic24='Gözlemci' or atama45001='Gözlemci' or atama50001='Gözlemci' or atama27001='Gözlemci') and is_active=1";
    //    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";
    $sqlSQL = 'SELECT * FROM denetciler ORDER BY `denetci` ASC';

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->nace = self::getEaNacePerDenetci($value->nace);
      $denetciler[$key]->sistemler = self::atananSistemler($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }

  public static function getIku()
  {
    $statement = "denetciler where iku='Başdenetçi' and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->sistemler = self::atananSistemler($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }

  public static function getAdayDenetci()
  {
    $statement =
      "denetciler where (atama9001='Aday Denetçi' or atama14001='Aday Denetçi' or atama22000='Aday Denetçi' or atamaOicsmiic='Aday Denetçi' or atamaOicsmiic6='Aday Denetçi' or atamaOicsmiic9='Aday Denetçi' or atamaOicsmiic171='Aday Denetçi' or atamaOicsmiic24='Aday Denetçi' or atama45001='Aday Denetçi' or atama50001='Aday Denetçi' or atama27001='Aday Denetçi') and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    foreach ($denetciler as $key => $value) {
      $denetciler[$key]->sistemler = self::atananSistemlerAdaylik($value->denetci);
    }
    return json_encode(['data' => $denetciler]);
  }public static function getDenetimOnerilenBasdenetci(Request $request)
{
  $input = $request->all();
  $auditorsPerNaceCode = [];
  $auditorsPerKat22 = [];
  $auditorsPerKatSmiic = [];
  $auditorsPerTa27001 = [];
  $auditorsPerTa50001 = [];
  $pot = [];

  // Önce aktif baş denetçileri çekelim
  $statement =
    "denetciler where (atama9001='Başdenetçi' or atama14001='Başdenetçi' or atama22000='Başdenetçi' or atamaOicsmiic='Başdenetçi' or atamaOicsmiic6='Başdenetçi' or atamaOicsmiic9='Başdenetçi' or atamaOicsmiic171='Başdenetçi' or atamaOicsmiic24='Başdenetçi' or atama45001='Başdenetçi' or atama50001='Başdenetçi' or atama27001='Başdenetçi') and is_active=1";
  $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

  $denetciler = DB::select($sqlSQL);

  // Parametrelere göre uygun denetçileri çekelim
  $auditorsPerNaceCode = self::getDesignatedAuditorPerNaceCode($input['nace']);
  $auditorsPerKat22 = self::getDesignatedAuditorPerKat22($input['kat22']);
  $auditorsPerKatSmiic = self::getDesignatedAuditorPerKatOic($input['katoic']);
  $auditorsPerTa27001 = self::getDesignatedAuditorPerTaBgys($input['tabgys']);
  $auditorsPerTa50001 = self::getDesignatedAuditorPerTaEnys($input['taenys']);

  $pot = array_merge(
    $auditorsPerNaceCode,
    $auditorsPerKat22,
    $auditorsPerKatSmiic,
    $auditorsPerTa27001,
    $auditorsPerTa50001
  );
  $pot = array_values(array_unique($pot, SORT_REGULAR));

  return $pot;
}

  public static function getOnerilenKararUyeleri(Request $request)
  {
    $input = $request->all();
    $auditorsPerEaCode = [];
    $auditorsPerKat22 = [];
    $auditorsPerKatSmiic = [];
    $auditorsPerTa27001 = [];
    $auditorsPerTa50001 = [];
    $pot = [];

    // Önce aktif baş denetçileri çekelim
    $statement =
      "denetciler where (atama9001='Başdenetçi' or atama14001='Başdenetçi' or atama22000='Başdenetçi' or atamaOicsmiic='Başdenetçi' or atamaOicsmiic6='Başdenetçi' or atamaOicsmiic9='Başdenetçi' or atamaOicsmiic171='Başdenetçi' or atamaOicsmiic24='Başdenetçi' or atama45001='Başdenetçi' or atama50001='Başdenetçi' or atama27001='Başdenetçi') and is_active=1";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";

    $denetciler = DB::select($sqlSQL);

    // Parametrelere göre uygun denetçileri çekelim
    $auditorsPerEaCode = self::getDesignatedAuditorPerEaCode($input['ea']);
    $auditorsPerKat22 = self::getDesignatedAuditorPerKat22($input['kat22']);
    $auditorsPerKatSmiic = self::getDesignatedAuditorPerKatOic($input['katoic']);
    $auditorsPerTa27001 = self::getDesignatedAuditorPerTaBgys($input['tabgys']);
    $auditorsPerTa50001 = self::getDesignatedAuditorPerTaEnys($input['taenys']);

    $pot = array_merge(
      $auditorsPerEaCode,
      $auditorsPerKat22,
      $auditorsPerKatSmiic,
      $auditorsPerTa27001,
      $auditorsPerTa50001
    );
    $pot = array_values(array_unique($pot, SORT_REGULAR));

    return $pot;
  }

  private static function getDesignatedAuditorPerEaCode($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where ea LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  private static function getDesignatedAuditorPerNaceCode($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where nace LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  private static function getDesignatedAuditorPerKat22($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where kategori LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  private static function getDesignatedAuditorPerKatOic($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where kategorioic LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  private static function getDesignatedAuditorPerTaBgys($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where kategoribg LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  private static function getDesignatedAuditorPerTaEnys($kod)
  {
    if (trim($kod) === '') {
      return [];
    }

    $kod = explode(',', $kod);
    $results = [];

    foreach ($kod as $key => $value) {
      $value = trim($value);
      if (empty($value)) continue;

      $sqlSQL = "SELECT * FROM denetciler where teknikalan LIKE '%" . $value . "%' ORDER BY `denetci` ASC";
      $denetciler = DB::select($sqlSQL);

      if (!empty($denetciler)) {
        $results = array_merge($results, $denetciler);
      }
    }

    return $results;
  }

  public static function iso9001SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $rg = $input['rg'];
    $calsay = $input['calsay'];
    self::$ttips['9001'] = '';

    $duzeys = explode(',', $rg);

    $duzey = count($duzeys) === 0 ? 'D' : $rg;
    for ($k = 0; $k < count($duzeys); $k++) {
      //            echo $duzeys[$k];
      if ($duzeys[$k] == 'Y') {
        $duzey = 'Y';
        break;
      }
      if ($duzeys[$k] == 'O') {
        if ($duzey != 'Y') {
          $duzey = 'O';
        }
      }
      if ($duzeys[$k] == 'D') {
        if ($duzey != 'Y' && $duzey != 'O') {
          $duzey = 'D';
        }
      }
    }

    $sqlSQL = "SELECT * FROM ds9001 where calisansayisi > {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    if ($duzey == 'D') {
      $sonuc = $ret->dusuk;
    } elseif ($duzey == 'O') {
      $sonuc = $ret->orta;
    } elseif ($duzey == 'Y') {
      $sonuc = $ret->yuksek;
    }

    self::$sonuc9001 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc9001 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc9001 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc9001 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc9001 * 2) / 3), 1);

    if ($duzey == 'O') {
      self::$ttips['9001'] = "[ISO 9001:2015]{$calsay} çalışan için ORTA karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'D') {
      self::$ttips['9001'] = "[ISO 9001:2015]{$calsay} çalışan için DÜŞÜK karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'Y') {
      self::$ttips['9001'] = "[ISO 9001:2015]{$calsay} çalışan için YÜKSEK karmakşıklık toplam {$sonuc} d/g";
    }

    return json_encode([
      'sonuc' => self::$sonuc9001,
      'tooltip' => self::$ttips['9001'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function iso14001SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $rg = $input['rg'];
    $calsay = $input['calsay'];
    self::$ttips['14001'] = '';

    $duzeys = explode(',', $rg);

    $duzey = count($duzeys) === 0 ? 'D' : $rg;
    for ($k = 0; $k < count($duzeys); $k++) {
      //            echo $duzeys[$k];
      if ($duzeys[$k] == 'Y') {
        $duzey = 'Y';
        break;
      }
      if ($duzeys[$k] == 'O') {
        if ($duzey != 'Y') {
          $duzey = 'O';
        }
      }
      if ($duzeys[$k] == 'D') {
        if ($duzey != 'Y' && $duzey != 'O') {
          $duzey = 'D';
        }
      }
    }

    $sqlSQL = "SELECT * FROM ds14001 where calisansayisi > {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    if ($duzey == 'D') {
      $sonuc = $ret->dusuk;
    } elseif ($duzey == 'O') {
      $sonuc = $ret->orta;
    } elseif ($duzey == 'Y') {
      $sonuc = $ret->yuksek;
    }

    self::$sonuc14001 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc14001 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc14001 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc14001 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc14001 * 2) / 3), 1);

    if ($duzey == 'O') {
      self::$ttips['14001'] = "[ISO 14001:2015]{$calsay} çalışan için ORTA karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'D') {
      self::$ttips['14001'] = "[ISO 14001:2015]{$calsay} çalışan için DÜŞÜK karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'Y') {
      self::$ttips[
        '14001'
      ] = "[ISO 14001:2015]{$calsay} çalışan için YÜKSEK karmakşıklık toplam {$sonuc} d/g";
    }

    return json_encode([
      'sonuc' => self::$sonuc14001,
      'tooltip' => self::$ttips['14001'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function iso45001SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $rg = $input['rg'];
    $calsay = $input['calsay'];
    self::$ttips['45001'] = '';

    $duzeys = explode(',', $rg);

    $duzey = count($duzeys) === 0 ? 'D' : $rg;
    for ($k = 0; $k < count($duzeys); $k++) {
      //            echo $duzeys[$k];
      if ($duzeys[$k] == 'Y') {
        $duzey = 'Y';
        break;
      }
      if ($duzeys[$k] == 'O') {
        if ($duzey != 'Y') {
          $duzey = 'O';
        }
      }
      if ($duzeys[$k] == 'D') {
        if ($duzey != 'Y' && $duzey != 'O') {
          $duzey = 'D';
        }
      }
    }

    $sqlSQL = "SELECT * FROM ds45001 where calisansayisi > {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    if ($duzey == 'D') {
      $sonuc = $ret->dusuk;
    } elseif ($duzey == 'O') {
      $sonuc = $ret->orta;
    } elseif ($duzey == 'Y') {
      $sonuc = $ret->yuksek;
    }

    self::$sonuc45001 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc45001 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc45001 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc45001 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc45001 * 2) / 3), 1);

    if ($duzey == 'O') {
      self::$ttips['45001'] = "[ISO 45001:2018]{$calsay} çalışan için ORTA karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'D') {
      self::$ttips['45001'] = "[ISO 45001:2018]{$calsay} çalışan için DÜŞÜK karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'Y') {
      self::$ttips[
        '45001'
      ] = "[ISO 45001:2018]{$calsay} çalışan için YÜKSEK karmakşıklık toplam {$sonuc} d/g";
    }

    return json_encode([
      'sonuc' => self::$sonuc45001,
      'tooltip' => self::$ttips['45001'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function iso50001SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $calsay = $input['calsay'];
    self::$ttips['50001'] = '';

    $carpan = floatval('0.041868'); //tep -> tj //floatval("0.0000036") // kw -> tj;

    $yet = floatval($input['yet']);
    $keks = intval($input['keks']);
    $oeks = intval($input['oeks']);
    $fec = floatval(25 / 100);
    $fes = floatval(25 / 100);
    $fseu = floatval(50 / 100);
    $wec = 0;
    $wes = 0;
    $wseu = 0;

    //enerji karmaşıklık faktörü W
    $yet = $yet * $carpan;
    if ($yet <= 20) {
      $wec = 1;
    }
    if ($yet > 20 && $yet <= 200) {
      $wec = floatval('1.2');
    }
    if ($yet > 200 && $yet <= 2000) {
      $wec = floatval('1.4');
    }
    if ($yet > 2000) {
      $wec = floatval('1.6');
    }
    //        echo $wec;

    //enerji kaynak sayısı karmaşıklık faktörü
    if ($keks == 1 || $keks == 2) {
      $wes = floatval('1');
    }
    if ($keks == 3) {
      $wes = floatval('1.2');
    }
    if ($keks >= 4) {
      $wes = floatval('1.4');
    }

    //enerji kaynak sayısı karmaşıklık faktörü
    if ($oeks <= 3) {
      $wseu = floatval('1');
    }
    if ($oeks > 3 && $oeks <= 6) {
      $wseu = floatval('1.2');
    }
    if ($oeks > 6 && $oeks <= 10) {
      $wseu = floatval('1.3');
    }
    if ($oeks > 11 && $oeks <= 15) {
      $wseu = floatval('1.4');
    }
    if ($oeks >= 16) {
      $wseu = floatval('1.6');
    }

    //K = (𝐹EC × 𝑊EC) + (𝐹ES × 𝑊ES) + (𝐹SEU × 𝑊SEU)
    $K = floatval($fec * $wec + $fes * $wes + $fseu * $wseu);

    $duzey = 'D';
    if ($K < floatval('1.15')) {
      $duzey = 'D';
    }
    if ($K >= floatval('1.15') && $K <= floatval('1.35')) {
      $duzey = 'O';
    }
    if ($K > floatval('1.35')) {
      $duzey = 'Y';
    }

    $sqlSQL = "SELECT * FROM ds50001 where calisansayisi > {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    if ($duzey == 'D') {
      $sonuc = $ret->dusuk;
    } elseif ($duzey == 'O') {
      $sonuc = $ret->orta;
    } elseif ($duzey == 'Y') {
      $sonuc = $ret->yuksek;
    }

    self::$sonuc50001 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc50001 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc50001 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc50001 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc50001 * 2) / 3), 1);

    if ($duzey == 'O') {
      self::$ttips['50001'] = "[ISO 50001:2018]{$calsay} çalışan için ORTA karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'D') {
      self::$ttips['50001'] = "[ISO 50001:2018]{$calsay} çalışan için DÜŞÜK karmakşıklık toplam {$sonuc} d/g";
    }
    if ($duzey == 'Y') {
      self::$ttips[
        '50001'
      ] = "[ISO 50001:2018]{$calsay} çalışan için YÜKSEK karmakşıklık toplam {$sonuc} d/g";
    }

    return json_encode([
      'sonuc' => self::$sonuc50001,
      'tooltip' => self::$ttips['50001'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function iso27001SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $calsay = $input['calsay'];
    self::$ttips['27001'] = '';

    $sqlSQL = "SELECT * FROM ds27001 where calisansayisi > {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    $sonuc = $ret->ilkbelgelendirme;

    self::$sonuc27001 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc27001 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc27001 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc27001 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc27001 * 2) / 3), 1);

    self::$ttips['27001'] = "{$calsay} çalışan için {$sonuc} d/g";

    return json_encode([
      'sonuc' => self::$sonuc27001,
      'tooltip' => self::$ttips['27001'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public function bgysFaktorDenetimEtkisiHesapla(Request $request)
  {
    $input = $request->all();
    $isf = $input['isFaktor'];
    $btf = $input['btFaktor'];
    $isKarmasikligi = '';
    $btKarmasikligi = '';
    $puan = $isf * $btf;

    if ($isf > 6) {
      $isKarmasikligi = 'yuksek';
    }
    if ($isf > 4 && $isf < 7) {
      $isKarmasikligi = 'orta';
    }
    if ($isf < 5) {
      $isKarmasikligi = 'dusuk';
    }
    //        echo $isKarmasikligi;

    if ($btf > 6) {
      $btKarmasikligi = 'yuksek';
    }
    if ($btf > 4 && $isf < 7) {
      $btKarmasikligi = 'orta';
    }
    if ($btf < 5) {
      $btKarmasikligi = 'dusuk';
    }

    $sqlSQL =
      "select * from bgyskarmasiklik where issinifi='{$isKarmasikligi}' and btsinifi='{$btKarmasikligi}' and puan=" .
      $puan;
    $ret = DB::select($sqlSQL)[0];

    if ($ret) {
      return $ret->yuzde;
    } else {
      return 'err..';
    }
  }

  public static function iso22000SureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $calsay = $input['calsay'];
    $cat = $input['cat'];
    $bb = $input['bb'];
    $cc = $input['cc'];
    $haccpsay = $input['haccpsayisi'];
    //    $ms = $input["mysvarmi"];
    $sahasay = intval($input['sahasayisi']);
    $fte = 0;
    $D = str_replace(',', '.', $bb);

    self::$ttips['22000'] = '';
    self::$ttips['22000'] .= "Kategori(ler) {$cat} için denetim gün süresi: " . $D . "\r\n";

    $cc = str_replace(',', '.', $cc);
    $haccpsay = $haccpsay == 0 || $haccpsay == '' ? 1 : $haccpsay;
    $H = $cc * ($haccpsay - 1);
    self::$ttips['22000'] .= "{$H}({$haccpsay} HACCP planı - 1) x {$cc} = " . $H . "\r\n";

    /* yönetim sistemi eksikliği için ek süre */
    //    $ms = ($ms == "HAYIR") ? "0.25" : 0;
    //    self::$ttips["22000"] .= "Yönetim sistemi eksikliğinden : " . $ms . "\r\n";

    $sqlSQL = "SELECT * FROM ds22000 where aa < {$calsay} and bb >= {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    $fte = str_replace(',', '.', $ret->cc);
    self::$ttips['22000'] .= "{$calsay} çalışan sayısından : " . $fte . "\r\n";

    /* ekstra sahalar için ek süre */
    $enkisasuresonuc = $D + $H + $fte; // ts
    $enkisasure = $enkisasuresonuc / 2; // tm
    $extralan = $sahasay * 1; //($sahasay - 1) * $enkisasure;
    self::$ttips['22000'] .= $sahasay . '(extra alan sayısı) x 1 = ' . $extralan . "\r\n";
    self::$ttips['22000'] .= "-------------------------------\r\n";

    //        if($sahasay>1) {
    $sonuc = $D + $H + $fte + $extralan;

    self::$sonuc22000 = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonuc22000 * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonuc22000 * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonuc22000 * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonuc22000 * 2) / 3), 1);

    session()->put('anadenetimsuresi', $D);
    session()->put('ekhaccpsuresi', $H);
    //    session()->put("mevcutys", $ms);
    session()->put('ftecalisansayisi', $fte);
    session()->put('tsenkisasure', $enkisasuresonuc);
    session()->put('extralan', $extralan);
    session()->put('22basvar', true);

    self::$ttips['22000'] .= 'Toplam = ' . $sonuc . "\r\n";
    self::$ttips['22000'] .= "-------------------------------\r\n";

    return json_encode([
      'sonuc' => self::$sonuc22000,
      'tooltip' => self::$ttips['22000'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function isoOicSmiicSureHesapla(Request $request)
  {
    $input = $request->all();
    $sonuc = '';
    $calsay = $input['calsay'];
    $cat = $input['cat'];
    $bb = $input['bb'];
    $cc = $input['cc'];
    $haccpsay = $input['haccpsayisi'];
    $sahasay = $input['sahasayisi'];
    $havuzsay = $input['havuzsayisi'];
    $mutfaksay = $input['mutfaksayisi'];
    $odasay = $input['odasayisi'];
    $hizmetkategori = $input['hizmetkategorisi'];
    $aracsayisi = $input['aracsayisi'];
    $cck = $input['cck'];
    $pv = $input['pv'];

    $fte = 0;
    $havuz = 0;
    $mutfak = 0;
    $oda = 0;
    $hkat = 0;
    $hkat171 = 0;
    $arac = 0;
    $D = str_replace(',', '.', $bb);

    $cck = substr($cck, 0, 1);
    if ($cck == 4) {
      $cckk = '2';
    }
    if ($cck == 3) {
      $cckk = '1.75';
    }
    if ($cck == 2) {
      $cckk = '1.5';
    }
    if ($cck == 1) {
      $cckk = '1.25';
    }
    if ($pv == '' || ($pv != '' && intval($pv) < 1)) {
      $pvs = '0';
    }
    if (intval($pv) >= 1 && intval($pv) <= 3) {
      $pvs = '0.5';
    }
    if (intval($pv) >= 4 && intval($pv) <= 6) {
      $pvs = '1';
    }
    if (intval($pv) >= 7 && intval($pv) <= 10) {
      $pvs = '1.5';
    }
    if (intval($pv) >= 11 && intval($pv) <= 20) {
      $pvs = '2';
    }
    if (intval($pv) > 20) {
      $pvs = '3';
    }
    if ($havuzsay == 1) {
      $havuz = '0';
    }
    if (intval($havuzsay) >= 2 && intval($havuzsay) <= 10) {
      $havuz = '0.25';
    }
    if (intval($havuzsay) >= 11 && intval($havuzsay) <= 20) {
      $havuz = '0.5';
    }
    if (intval($havuzsay) > 20) {
      $havuz = '1';
    }
    if ($mutfaksay == 1) {
      $mutfak = '0';
    }
    if (intval($mutfaksay) >= 2 && intval($mutfaksay) <= 5) {
      $mutfak = '0.25';
    }
    if (intval($mutfaksay) >= 6 && intval($mutfaksay) <= 10) {
      $mutfak = '0.5';
    }
    if (intval($mutfaksay) >= 11 && intval($mutfaksay) <= 20) {
      $mutfak = '1';
    }
    if (intval($mutfaksay) > 20) {
      $mutfak = '2';
    }
    if (intval($odasay) >= 1 && intval($odasay) <= 50) {
      $oda = '0';
    }
    if (intval($odasay) >= 51 && intval($odasay) <= 100) {
      $oda = '0.25';
    }
    if (intval($odasay) >= 101 && intval($odasay) <= 200) {
      $oda = '0.5';
    }
    if (intval($odasay) >= 201 && intval($odasay) <= 500) {
      $oda = '1';
    }
    if (intval($odasay) > 500) {
      $oda = '2';
    }
    if ($hizmetkategori == 'C') {
      $hkat = '0';
    }
    if ($hizmetkategori == 'B') {
      $hkat = '0.5';
    }
    if ($hizmetkategori == 'A') {
      $hkat = '1';
    }

    $oteleksure = floatval($havuz) + floatval($mutfak) + floatval($oda) + floatval($hkat);

    if (self::InStr($cat, 'GI') > -1) {
      $hkat171 = '1';
    }
    if (self::InStr($cat, 'GI') > -1 && self::InStr($cat, 'GII') > -1) {
      $hkat171 = '1';
    }
    if (self::InStr($cat, 'GII') > -1) {
      $hkat171 = '0.5';
    }

    if (intval($aracsayisi) >= 1 && intval($aracsayisi) <= 20) {
      $arac = '0.25';
    }
    if (intval($aracsayisi) >= 21 && intval($aracsayisi) <= 50) {
      $arac = '0.5';
    }
    if (intval($aracsayisi) >= 51 && intval($aracsayisi) <= 100) {
      $arac = '1';
    }
    if (intval($aracsayisi) > 100) {
      $arac = '2';
    }
    $tasimaeksure = floatval($hkat171) + floatval($arac);
    self::$ttips['oicsmiic'] = '';

    /* her bir ek haccp planı için ek süre */
    self::$ttips['oicsmiic'] .= $cat . ' kategorisi için D: ' . $D . "\r\n";
    $cc = str_replace(',', '.', $cc);
    $haccpsay = $haccpsay == 0 || $haccpsay == '' ? 1 : $haccpsay;
    $H = $cc * $haccpsay;
    self::$ttips['oicsmiic'] .= "H: {$H}({$haccpsay} HACCP planı x {$cc}) = " . $H . "\r\n";

    $sqlSQL = "SELECT * FROM dshelalsmic where aa < {$calsay} and bb >= {$calsay} ORDER BY `id` ASC LIMIT 1";
    $ret = DB::select($sqlSQL)[0];

    $fte = str_replace(',', '.', $ret->cc);
    /* TS = TD + TH + [(TPV + TFTE)*CC] */
    self::$ttips['oicsmiic'] .= "{$calsay} çalışan sayısından : " . $fte . "\r\n";
    self::$ttips['oicsmiic'] .= 'CC Karmaşıklık sınıfı : ' . $cckk . "\r\n";
    self::$ttips['oicsmiic'] .= 'PV Ürün çeşitliliği : ' . $pvs . "\r\n";

    /* ekstra sahalar için ek süre */
    $enkisasuresonuc = $D + $H + (floatval($pvs) + floatval($fte)) * floatval($cckk); // ts
    $extralan = 0; //($sahasay - 1) * 1;
    //    self::$ttips["oicsmiic"] .= ($sahasay - 1) . "(extra alan sayısı) x 1 = " . $extralan . "\r\n";
    self::$ttips['oicsmiic'] .= "-------------------------------\r\n";
    self::$ttips['oicsmiic'] .=
      $D . '+' . $H . '+' . '[(' . floatval($pvs) . '+' . floatval($fte) . ') * ' . floatval($cckk) . "]\r\n";

    $sonuc = $enkisasuresonuc + $extralan;
    if (intval($havuzsay) > 0) {
      $sonuc = $sonuc + $oteleksure;
    }
    if (intval($aracsayisi) > 0) {
      $sonuc = $sonuc + $tasimaeksure;
    }

    self::$sonucSmiic = number_format(floatval(str_replace(',', '.', $sonuc)), 1);
    $a1sure = number_format(self::roundUpTo5((self::$sonucSmiic * 30) / 100), 1);
    $a2sure = number_format(self::roundUpTo5((self::$sonucSmiic * 70) / 100), 1);
    $gsure = number_format(self::roundUpTo5((self::$sonucSmiic * 1) / 3), 1);
    $ybsure = number_format(self::roundUpTo5((self::$sonucSmiic * 2) / 3), 1);

    session()->put('oicanadenetimsuresi', $D);
    session()->put('oicekhaccpsuresi', $H);
    session()->put('oicftecalisansayisi', $fte);
    session()->put('oictsenkisasure', $enkisasuresonuc);
    session()->put('oicextralan', $extralan);
    session()->put('oiccc', $cckk);
    session()->put('oicpv', $pvs);
    session()->put('oicsmiicbasvar', true);

    self::$ttips['oicsmiic'] .= 'Toplam = ' . $sonuc . "\r\n";
    self::$ttips['oicsmiic'] .= "-------------------------------\r\n";

    return json_encode([
      'sonuc' => self::$sonucSmiic,
      'tooltip' => self::$ttips['oicsmiic'],
      'a1sure' => $a1sure,
      'a2sure' => $a2sure,
      'gsure' => $gsure,
      'ybsure' => $ybsure,
    ]);
  }

  public static function denetciSistemleri(Request $request)
  {
    $input = $request->all();
    $denetci = $input['denetci'];
    $planno = $input['planno'];
    $belgelendirileceksistemler = $input['sistemler'];

    $str = 0;
    $strsis = '';

    $sqlSQL = "SELECT * FROM denetciler where denetci like '%" . $denetci . "%'";
    $ret = DB::select($sqlSQL)[0];

    $atama = '';
    $siss = explode(', ', $belgelendirileceksistemler);

    for ($i = 0; $i < count($siss); $i++) {
      if ($siss[$i] == 'ISO 9001:2015') {
        $atama = 'atama9001';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'ISO 14001:2015') {
        $atama = 'atama14001';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'ISO 45001:2018') {
        $atama = 'atama45001';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'ISO 50001:2018') {
        $atama = 'atama50001';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'ISO 22000:2018') {
        $atama = 'atama22000';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'ISO 22000') {
        $atama = 'atama22000';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 1:2019') {
        $atama = 'atamaOicsmiic';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 6:2019') {
        $atama = 'atamaOicsmiic6';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 9:2019') {
        $atama = 'atamaOicsmiic9';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 17-1:2020') {
        $atama = 'atamaOicsmiic171';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 24:2020') {
        $atama = 'atamaOicsmiic24';
        $strsis .= $siss[$i] . '|';
      }
      if ($siss[$i] == 'OIC/SMIIC 23:2022') {
        $atama = 'atamaOicsmiic23';
        $strsis .= $siss[$i] . '|';
      }
      if ($ret->$atama == 'Başdenetçi') {
        $str++;
      }
    }

    $strsis = substr($strsis, 0, -1);
    $namecleared = str_replace(' ', '', self::clearLocale($denetci));
    //    $_SESSION["denstdyetkinsay"][$namecleared] = $str;
    //    $_SESSION["denstdyetkinsis"][$namecleared] = $strsis;
    return $str;
  }

  public static function write9001IndArt()
  {
    $statement = "azaltmaarttirmalar where standart = '9001'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indart9001' .
        $ret->id .
        '"
                             name="chb_indart9001' .
        $ret->id .
        '"  onclick="indartHesapla9001()" />
                      <label class="form-check-label" for="indart9001' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function write14001IndArt()
  {
    $statement = "azaltmaarttirmalar where standart = '14001'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-2 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indart14001' .
        $ret->id .
        '"
                             name="chb_indart14001' .
        $ret->id .
        '"  onclick="indartHesapla14001()" />
                      <label class="form-check-label" for="indart14001' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function write45001IndArt()
  {
    $statement = "azaltmaarttirmalar where standart = '45001'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indart45001' .
        $ret->id .
        '"
                             name="chb_indart45001' .
        $ret->id .
        '"  onclick="indartHesapla45001()" />
                      <label class="form-check-label" for="indart45001' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function write50001IndArt()
  {
    $statement = "azaltmaarttirmalar where standart = '50001'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indart50001' .
        $ret->id .
        '"
                             name="chb_indart50001' .
        $ret->id .
        '"  onclick="indartHesapla50001()" />
                      <label class="form-check-label" for="indart50001' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function write22000IndArt()
  {
    $statement = "azaltmaarttirmalar where standart = '22000'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indart22000' .
        $ret->id .
        '"
                             name="chb_indart22000' .
        $ret->id .
        '"  onclick="indartHesapla22000()" />
                      <label class="form-check-label" for="indart22000' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function writeSmiicIndArt()
  {
    $statement = "azaltmaarttirmalar where standart = 'SMIIC'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `sira` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indartsmiic' .
        $ret->id .
        '"
                             name="chb_indartsmiic' .
        $ret->id .
        '"  onclick="indartHesaplaOicsmiic()" />
                      <label class="form-check-label" for="indartsmiic' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public static function writeEntegreIndArt()
  {
    $statement = "azaltmaarttirmalar where standart = 'entegre'";
    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `id` ASC";
    $result = DB::select($sqlSQL);

    $rowresult = '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">

                  </div>
                </div>';
    foreach ($result as $ret) {
      $rowresult .=
        '
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1 ms-md-2">
                      <input class="form-check-input" type="checkbox" value="' .
        $ret->oran .
        '" id="indartentegre' .
        $ret->id .
        '"
                             name="chb_indartentegre' .
        $ret->id .
        '"  onclick="indartHesaplaEntegre()" />
                      <label class="form-check-label" for="indartentegre' .
        $ret->id .
        '">
                        ' .
        $ret->name .
        '(' .
        $ret->oran .
        ')
                      </label>
                    </div>
                  </div>
                </div>';
    }
    echo $rowresult;
  }

  public function getEntegreDuzeyleri()
  {
    $sqlSQL = 'SELECT * FROM entegreduzeyleri';
    $entegreduzeyarr = [];

    $result = DB::select($sqlSQL);
    foreach ($result as $ret) {
      $entegreduzeyarr[$ret->yatay . '-' . $ret->dikey] = $ret->oran;
    }
    return json_encode($entegreduzeyarr);
  }

  public static function InStr($haystack, $needle)
  {
    $pos1 = @stripos($haystack, $needle);
    if ($pos1 !== false) {
      return $pos1;
    }
    return -1;
  }

  public static function roundUpTo5($X)
  {
    $sayi = round(floatval($X) * 2) / 2;
    return $sayi;
  }

  public static function clearLocale($str)
  {
    $metin = '';
    $metin = str_replace('Ğ', 'G', $str);
    $metin = str_replace('Ü', 'U', $metin);
    $metin = str_replace('Ş', 'S', $metin);
    $metin = str_replace('İ', 'I', $metin);
    $metin = str_replace('Ö', 'O', $metin);
    $metin = str_replace('Ç', 'C', $metin);
    $metin = str_replace('ğ', 'g', $metin);
    $metin = str_replace('ü', 'u', $metin);
    $metin = str_replace('ı', 'i', $metin);
    $metin = str_replace('ş', 's', $metin);
    $metin = str_replace('ö', 'o', $metin);
    $metin = str_replace('ç', 'c', $metin);
    return $metin;
  }

  public static function getEaNaceKategoriPerDenetci(
    $firmaea = '',
    $firmanace = '',
    $firmakat = '',
    $firmakatoic = '',
    $firmakatenys = '',
    $firmakatbgys = '',
    $denetimekibi = ''
  ) {
    $sonuc = '';
    $eabol = explode(',', $firmaea) ?? [];
    $nacebol = explode(',', $firmanace) ?? [];
    $catbol = explode(',', $firmakat) ?? [];
    $catbolenys = explode(',', $firmakatenys) ?? [];
    $catboloic = explode(',', $firmakatoic) ?? [];
    $catbolbgys = explode(',', $firmakatbgys) ?? [];
    $ekibbol = explode(',', $denetimekibi) ?? [];
    $arry = [];

    /* 9001 - 14001 - 45001 */
    foreach ($nacebol as $nave) {
      if (trim($nave) == '') {
        continue;
      }
      foreach ($ekibbol as $de) {
        //                echo $nave . "<br>";
        $result = DB::select("select * from denetciler where denetci='{$de}' and nace LIKE '%" . trim($nave) . "%'");
        foreach ($result as $ret) {
          $pos1 = self::InStr($ret->nace, trim($nave)); // naceleri denetçinin atandığı naceler içinde ara
          if ($pos1 > -1) {
            $pos2 = self::InStr($sonuc, trim($nave)); // denetçide nace var ise sonuç içinde zaten var mı
            if ($pos2 == -1) {
              // sonuc içinde nace yok ise ekle
              $sqll = "select * from eanacekodlari where nace LIKE '%" . trim($nave) . "%'";
              $eakod = DB::select($sqll)[0]->ea;
              if (self::InStr($sonuc, $eakod . '/') == -1) {
                $sonuc .= $eakod . '/' . $nave . ', ';
              } else {
                $sonuc .= $nave . ', ';
              }
              continue;
            }
          }
        }
      }
    }
    //        echo $sonuc = substr($sonuc, 0, -1);
    //        var_dump($arry);

    /* 22000 */
    foreach ($catbol as $kate) {
      if (trim($kate) == '') {
        continue;
      }
      foreach ($ekibbol as $de) {
        //echo $kate . "<br>";
        $result = DB::select(
          "select * from denetciler where denetci='{$de}' and kategori LIKE '%" . trim($kate) . "%'"
        );
        foreach ($result as $ret) {
          //echo $ret["denetci"] . " : " . $ret->eanace . "<br>";
          $pos1 = self::InStr($ret->kategori, trim($kate));
          if ($pos1 > -1) {
            $pos2 = self::InStr($sonuc, trim($kate));
            if ($pos2 == -1) {
              $sonuc .= $kate . ', ';
              continue;
            }
          }
        }
      }
    }
    //        echo $sonuc = substr($sonuc, 0, -1);

    /* helal */
    foreach ($catboloic as $kate) {
      if (trim($kate) == '') {
        continue;
      }
      foreach ($ekibbol as $de) {
        //        echo $kate . "<br>";
        $result = DB::select(
          "select * from denetciler where denetci='{$de}' and kategorioic LIKE '%" . trim($kate) . "%'"
        );
        foreach ($result as $ret) {
          //          echo $ret->denetci . " : " . $ret->kategorioic . "<br>";
          $pos1 = self::InStr($ret->kategorioic, trim($kate));
          if ($pos1 > -1) {
            $pos2 = self::InStr($sonuc, trim($kate));
            if ($pos2 == -1) {
              $sonuc .= $kate . ', ';
              continue;
            }
          }
        }
      }
    }
    //        echo $sonuc = substr($sonuc, 0, -1). " sonuç";

    /* 50001 */
    foreach ($catbolenys as $kate) {
      if (trim($kate) == '') {
        continue;
      }
      foreach ($ekibbol as $de) {
        //                echo $kate . "<br>";
        $result = DB::select(
          "select * from denetciler where denetci='{$de}' and teknikalan LIKE '%" . trim($kate) . "%'"
        );
        foreach ($result as $ret) {
          //echo $ret["denetci"] . " : " . $ret->eanace . "<br>";
          $pos1 = self::InStr($ret->teknikalan, trim($kate));
          if ($pos1 > -1) {
            $pos2 = self::InStr($sonuc, trim($kate));
            if ($pos2 == -1) {
              $sonuc .= $kate . ', ';
              continue;
            }
          }
        }
      }
    }
    //        $sonuc = substr($sonuc, 0, -1);

    /* 27001 */
    foreach ($catbolbgys as $kate) {
      if (trim($kate) == '') {
        continue;
      }
      foreach ($ekibbol as $de) {
        //echo $kate . "<br>";
        $result = DB::select(
          "select * from denetciler where denetci='{$de}' and kategoribg LIKE '%" . trim($kate) . "%'"
        );
        foreach ($result as $ret) {
          //echo $ret["denetci"] . " : " . $ret->eanace . "<br>";
          $pos1 = self::InStr($ret->kategoribg, trim($kate));
          if ($pos1 > -1) {
            $pos2 = self::InStr($sonuc, trim($kate));
            if ($pos2 == -1) {
              $sonuc .= $kate . ', ';
              continue;
            }
          }
        }
      }
    }
    $sonuc = substr($sonuc, 0, -2);

    return $sonuc;
  }

  function zipZip($file_name, $zip_directory, $directory)
  {
    $this->zip = new ZipArchive();
    if ($this->zip->open($zip_directory . $file_name . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE)) {
      $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
      foreach ($files as $name => $file) {
        // We're skipping all subfolders
        if (!$file->isDir()) {
          $filePath = $file->getRealPath();

          // extracting filename with substr/strlen
          $relativePath = '/' . substr($filePath, strlen($directory) + 1);

          $this->zip->addFile($filePath, $relativePath);
        }
      }
      $this->zip->close();
    }
  }

  public function mkdirr($pathname, $mode = 0777)
  {
    // Check if directory already exists
    if (is_dir($pathname) || empty($pathname)) {
      return true;
    }

    // Ensure a file does not already exist with the same name
    $pathname = str_replace(['/', ''], DIRECTORY_SEPARATOR, $pathname);
    if (is_file($pathname)) {
      trigger_error('mkdirr() File exists', E_USER_WARNING);
      return false;
    }
    //$processUser = posix_getpwuid(posix_geteuid());
    //echo "<br/>".$processUser['name'];
    // Crawl up the directory tree
    $next_pathname = substr($pathname, 0, strrpos($pathname, DIRECTORY_SEPARATOR));
    if ($this->mkdirr($next_pathname)) {
      //chown($next_pathname, $processUser['name']);
      if (!file_exists($pathname)) {
        //$olustur = mkdir($pathname, $mode);
        //chown($pathname, $processUser['name']);
        return mkdir($pathname);
      }
    }

    return false;
  }

  public function msgSuccess($msg = null)
  {
    echo '<div class="alert alert-success">' . $msg . '</div>';
  }

  public function msgError($msg = null, $istop = true)
  {
    echo '<div class="alert alert-danger">Detay:<br>' . $msg . '</div>';
    if ($istop) {
      exit();
    }
  }

  public function removePlanDir($pno)
  {
    $path = public_path() . '/setler/' . $pno;

    if (is_dir($path)) {
      File::deleteDirectory($path);
    }
  }

  public function denetimTakvimi(Request $request)
  {
    $reqay = $request->ay;
    $reqyil = $request->yil;

    echo '<div class="row">';
    $mp = $reqay;
    $m = $mp == 1 ? 12 : $mp - 1;
    $y = $mp == 1 ? $reqyil - 1 : $reqyil;

    echo '<div class="row row-cols-1 row-cols-md-3 g-6">
<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($m, $y, 'isom') .
      '
    </div>
  </div>
</div>';

    echo '<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($reqay, $reqyil, 'isom') .
      '
    </div>
  </div>
</div>';

    $mp = $reqay;
    $m = $mp == 12 ? 1 : $mp + 1;
    $y = $mp == 12 ? $reqyil + 1 : $reqyil;

    echo '<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($m, $y, 'isom') .
      '
    </div>
  </div>
</div>';

    echo '</div>';

    echo '<div class="row">';
    echo '<div class="col-md-12 col-lg-12 font-weight-bold">HELAL PLANLAMALARI</div>';
    echo '</div>';

    $mp = $reqay;
    $m = $mp == 1 ? 12 : $mp - 1;
    $y = $mp == 1 ? $reqyil - 1 : $reqyil;

    echo '<div class="row row-cols-1 row-cols-md-3 g-6">
<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($m, $y, 'helal') .
      '
    </div>
  </div>
</div>';

    echo '<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($reqay, $reqyil, 'helal') .
      '
    </div>
  </div>
</div>';

    $mp = $reqay;
    $m = $mp == 12 ? 1 : $mp + 1;
    $y = $mp == 12 ? $reqyil + 1 : $reqyil;

    echo '<div class="col">
  <div class="card h-100">
    <div class="card-body">
      ' .
      $this->draw_calendar($m, $y, 'helal') .
      '
    </div>
  </div>
</div>';

    echo '</div>';
  }

  public function draw_calendar($month, $year, $sistem = 'iso')
  {
    /* draw table */
    $calendar = '<div class="card-datatable text-nowrap">';
    $calendar .= '<div class="table-responsive text-nowrap">';
    $calendar .=
      '<table cellpadding="0" cellspacing="0" class="calendar takvim table table-success" style="padding: 0.1rem 0.15rem !important">';
    $calendar .= '<caption>' . $this->get_month_name_locales($month) . " $year</caption>";

    /* table headings */
    //$headings = array('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday', 'Sunday');
    $headings = ['Pzt', 'Slı', 'Çrş', 'Prş', 'Cma', 'Cmt', 'Pzr']; //reorder labels, starting with monday
    $calendar .=
      '<tr class="calendar-row"><td class="calendar-day-head">' .
      implode('</td><td class="calendar-day-head">', $headings) .
      '</td></tr>';

    /* days and weeks vars now ... */
    //$running_day = date('w',mktime(0,0,0,$month,1,$year));
    $running_day = date('N', mktime(0, 0, 0, $month, 1, $year)) - 1; //date('N') returns 1-7, starting with monday
    $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = [];

    /* row for week one */
    $calendar .= '<tr class="calendar-row">';

    /* print "blank" days until the first of the current week */
    for ($x = 0; $x < $running_day; $x++):
      $calendar .= '<td class="calendar-day-np"> </td>';
      $days_in_this_week++;
    endfor;

    /* keep going with days.... */
    for ($list_day = 1; $list_day <= $days_in_month; $list_day++):
      //$calendar.= '<td class="calendar-day">';
      /* add in the day number */
      //$calendar.= '<div class="day-number">'.$list_day.'</div>';
      /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
      $denetsay = $this->getDenetimler($list_day, $month, $year, $sistem, $list_day . $month . $year, 'info');

      //$calendar.= str_repeat('<p>'.$denetsay.'</p>',2);
      $calendar .= $denetsay;

      //$calendar.= '</td>';
      if ($running_day == 6):
        $calendar .= '</tr>';
        if ($day_counter + 1 != $days_in_month):
          $calendar .= '<tr class="calendar-row">';
        endif;
        $running_day = -1;
        $days_in_this_week = 0;
      endif;
      $days_in_this_week++;
      $running_day++;
      $day_counter++;
    endfor;

    /* finish the rest of the days in the week */
    if ($days_in_this_week < 8):
      for ($x = 1; $x <= 8 - $days_in_this_week; $x++):
        $calendar .= '<td class="calendar-day-np"> </td>';
      endfor;
    endif;

    /* final row */
    $calendar .= '</tr>';

    /* end the table */
    $calendar .= '</table></div></div>';

    /* all done, return result */
    return $calendar;
  }

  public function getDenetimler($day, $month, $year, $sistem, $date = '', $clas = 'info'): string
  {
    $kid = Auth::user()->kurulusid;
    $sqlSQL = '';
    $de = '';
    $modalids = 'mod';
    $saydir = 0;
    $saydir1 = 0;
    $saydir2 = 0;
    $saydir3 = 0;
    $saydir4 = 0;
    $saydir5 = 0;
    $saydir6 = 0;
    $gun = strlen($day) == 1 ? '0' . $day : $day;
    $ay = strlen($month) == 1 ? '0' . $month : $month;
    $tar = $gun . '.' . $ay . '.' . $year;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('asama1', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('asama1', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    //    $result = DB::select($sqlSQL);
    $s = 0;
    foreach ($result as $row) {
      $info =
        '<b>AI</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->asama1, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->asama1));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->asama1);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->bd1 .
        '(BD), ' .
        $row->d1 .
        '(D), ' .
        $row->tu1 .
        '(TU), ' .
        $row->g1 .
        '(G), ' .
        $row->iku1 .
        "(İKU)\r\n\r\n<br>";
      $s++;
    }
    $saydir1 = $s;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('asama2', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('asama2', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    $s = 0;

    foreach ($result as $row) {
      $info =
        '<b>AII</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->asama2, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->asama2));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->asama2);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->bd2 .
        '(BD), ' .
        $row->d2 .
        '(D), ' .
        $row->tu2 .
        '(TU), ' .
        $row->g2 .
        '(G), ' .
        $row->iku2 .
        "(İKU)\r\n\r\n<br>";
      $s++;
    }
    $saydir2 = $s;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('gozetim1', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('gozetim1', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    //    $result = DB::select($sqlSQL);
    $s = 0;
    foreach ($result as $row) {
      $info =
        '<b>GI</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->gozetim1, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->gozetim1));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->gozetim1);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->gbd1 .
        '(BD), ' .
        $row->gd1 .
        '(D), ' .
        $row->gtu1 .
        '(TU), ' .
        $row->gg1 .
        '(G), ' .
        $row->ikug1 .
        "(İKU)\r\n\r\n<br>";
      $s++;
    }
    $saydir3 = $s;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('gozetim2', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('gozetim2', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    //    $result = DB::select($sqlSQL);
    $s = 0;
    foreach ($result as $row) {
      $info =
        '<b>GII</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->gozetim2, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->gozetim2));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->gozetim2);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->gbd2 .
        '(BD), ' .
        $row->gd2 .
        '(D), ' .
        $row->gtu2 .
        '(TU), ' .
        $row->gg2 .
        '(G), ' .
        $row->ikug2 .
        "(İKU)\r\n\r\n<br>";
      $s++;
    }
    $saydir4 = $s;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('ybtar', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('ybtar', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    //    $result = DB::select($sqlSQL);
    $s = 0;
    foreach ($result as $row) {
      $info =
        '<b>YB</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->ybtar, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->ybtar));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->ybtar);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->ybbd .
        '(BD), ' .
        $row->ybd .
        '(D), ' .
        $row->ybtu .
        '(TU), ' .
        $row->ybg .
        '(G), ' .
        $row->ikuyb .
        "(İKU)\r\n\r\n<br>";
      $s++;
    }
    $saydir5 = $s;

    if ($sistem == 'helal') {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('ozeltar', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '1'
        )
        ->get();
      $modalids = 'modh';
    } else {
      $result = DB::table('planlar')
        ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
        ->select('basvuru.*', 'planlar.*')
        ->where('ozeltar', 'like', '%' . $tar . '%', 'and')
        ->where('active', '=', '1', 'and')
        ->where('basvuru.kid', '=', $kid, 'and')
        ->where('planlar.kid', '=', $kid)
        ->whereAny(
          [
            'helalvaryok',
            'oicsmiik6varyok',
            'oicsmiik9varyok',
            'oicsmiik171varyok',
            'oicsmiik23varyok',
            'oicsmiik24varyok',
          ],
          '=',
          '0'
        )
        ->whereAny(
          [
            'iso900115varyok',
            'iso1400115varyok',
            'iso2200018varyok',
            'iso27001varyok',
            'iso4500118varyok',
            'iso5000118varyok',
          ],
          '=',
          '1'
        )
        ->get();
    }
    //    $result = DB::select($sqlSQL);
    $s = 0;
    foreach ($result as $row) {
      $info =
        '<b>ÖD</b> [' .
        $row->planno .
        "]<label style=\"font-weight: bolder;color: #0b1e2b\">" .
        $row->firmaadi .
        "</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label style=\"font-weight: bolder;color: orangered\">" .
        $row->danisman .
        '</label>';
      /* 1. aşama */
      if ($this->InStr($row->ozeltar, ',') > -1) {
        $dentars = explode(',', str_replace(' ', '', $row->ozeltar));
        $bas = $dentars[0];
        $son = $dentars[count($dentars) - 1];
      } else {
        $dentars = str_replace(' ', '', $row->ozeltar);
        $bas = $dentars;
        $son = $dentars;
      }
      $de .= $info . '<br>' . $row->belgelendirileceksistemler . '<br>';
      $de .=
        $row->otbd .
        '(BD), ' .
        $row->otd .
        '(D), ' .
        $row->ottu .
        '(TU), ' .
        $row->otg .
        '(G), ' .
        $row->ikuot .
        '(İKU)';
      $s++;
    }
    $saydir6 = $s;

    $saydir = $saydir1 + $saydir2 + $saydir3 + $saydir4 + $saydir5 + $saydir6;

    $gun = str_pad($day, 2, '0', STR_PAD_LEFT);
    $ay = str_pad($month, 2, '0', STR_PAD_LEFT);
    $sonuc = '';
    if ($saydir > 0) {
      if (date('d.m.Y') == $gun . '.' . $ay . '.' . $year) {
        $sonuc =
          "<td rel='" .
          $date .
          "' style='background-color: firebrick; color: white' data-bs-toggle='tooltip' data-bs-html='true' title='" .
          $de .
          "' class='calendar-day'>";
      } else {
        $sonuc =
          "<td rel='" .
          $date .
          "' style='background-color: orangered; color: white' data-bs-toggle='tooltip' data-bs-html='true' title='" .
          $de .
          "' class='calendar-day'>";
      }
    } else {
      if (date('d.m.Y') == $gun . '.' . $ay . '.' . $year) {
        $sonuc = "<td rel='" . $date . "' class='calendar-day' style='background-color: firebrick; color: white'>";
      } else {
        $sonuc = "<td rel='" . $date . "' class='calendar-day'>";
      }
    }
    $sonuc .= $saydir;
    $sonuc .=
      '<div class="day-number" data-bs-toggle="modal" data-bs-target="#takvim_' .
      $modalids .
      $gun .
      $month .
      $year .
      '">' .
      $day .
      '</div></td>';

    echo $modalss =
      '<div class="modal modal-top fade" id="takvim_' .
      $modalids .
      $gun .
      $month .
      $year .
      '" tabindex="-1">
          <div class="modal-dialog modal-xl">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">' .
      $gun .
      '.' .
      $ay .
      '.' .
      $year .
      ' günü denetim listesi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-4 mt-2 text-wrap">
                    <p>' .
      $de .
      '</p>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </form>
          </div>
        </div>';

    return $sonuc;
  }

  public function get_month_name_locales($wdays)
  {
    $gun = '';
    switch ($wdays) {
      case '01':
        return $gun = 'Ocak';
        break;
      case '02':
        return $gun = 'Şubat';
        break;
      case '03':
        return $gun = 'Mart';
        break;
      case '04':
        return $gun = 'Nisan';
        break;
      case '05':
        return $gun = 'Mayıs';
        break;
      case '06':
        return $gun = 'Haziran';
        break;
      case '07':
        return $gun = 'Temmuz';
        break;
      case '08':
        return $gun = 'Ağustos';
        break;
      case '09':
        return $gun = 'Eylül';
        break;
      case '10':
        return $gun = 'Ekim';
        break;
      case '11':
        return $gun = 'Kasım';
        break;
      case '12':
        return $gun = 'Aralık';
        break;
      default:
        return $gun = 'Ocak';
        break;
    }
    return $gun;
  }

  public function get_day_name_locales($wdays)
  {
    $gun = '';
    switch ($wdays) {
      case '01':
        return $gun = 'Ocak';
        break;
      case '02':
        return $gun = 'Şubat';
        break;
      case '03':
        return $gun = 'Mart';
        break;
      case '04':
        return $gun = 'Nisan';
        break;
      case '05':
        return $gun = 'Mayıs';
        break;
      case '06':
        return $gun = 'Haziran';
        break;
      case '07':
        return $gun = 'Temmuz';
        break;
      case '08':
        return $gun = 'Ağustos';
        break;
      case '09':
        return $gun = 'Eylül';
        break;
      case '10':
        return $gun = 'Ekim';
        break;
      case '11':
        return $gun = 'Kasım';
        break;
      case '12':
        return $gun = 'Aralık';
        break;
      default:
        return $gun = 'Ocak';
        break;
    }
  }

  public function sendNotification($input, $mesaj, $form = 'basvuru')
  {
    $userSchema = Denetciler::first();
    $planData = [
      'firma' => $input['firmaadi'],
      'metin' => $mesaj,
      'planUrl' => url('plan/' . $form . '?fid=' . $input['planno']),
      'planno' => $input['planno'],
    ];
    //    $userSchema->notify((new PlanBilgi($planData))->locale('tr'));
    //    event(new PlanEvents($input["planno"], $input["dentarihi"], $input["dtipi"], $input["firmaadi"]));
    //        Notification::send($userSchema, new PlanBilgi($planData));
  }

  public function als05()
  {
    return view('content.planlama.als05'); //, ['basvuru' => $basvuru, 'sonbasvuru' => $sonbasvuru]);
  }

  public function belgelifirmalarals05()
  {
    $kid = Auth::user()->kurulusid;
    $satir = '';

    $temp = '';
    $ilk = 0;
    $son = 12;
    $yils = 2018;
    $i = 1;
    $a9001sure = 0;
    $a14001sure = 0;
    $a22000sure = 0;
    $a45001sure = 0;
    $a27001sure = 0;
    $a50001sure = 0;

    $result = DB::table('planlar')
      ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
      ->select('basvuru.*', 'planlar.*')
      ->where('basvuru.kid', '=', $kid, 'and')
      ->where('planlar.kid', '=', $kid)
      ->orderBy('planlar.planno', 'DESC')
      ->get();

    foreach ($result as $ret) {
      if ($ret->belgedurum === 'Askıda' || $ret->belgedurum === 'İptal') {
        continue;
      }

      $kyssistemler = Helpers::getSistemler($ret);
      $oicsistemler = Helpers::getOicSistemler($ret);
      $belgelendirileceksistemler = '';

      if ($kyssistemler !== '' && $oicsistemler !== '') {
        $belgelendirileceksistemler = $kyssistemler . ', ' . $oicsistemler;
      }
      if ($kyssistemler === '' && $oicsistemler !== '') {
        $belgelendirileceksistemler = $oicsistemler;
      }
      if ($kyssistemler !== '' && $oicsistemler === '') {
        $belgelendirileceksistemler = $kyssistemler;
      }

      if ($belgelendirileceksistemler === 'OIC/SMIIC 1:2019') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 6:2019') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 1:2019, OIC/SMIIC 6:2019') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 6:2019, OIC/SMIIC 9:2019') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 1:2019, OIC/SMIIC 23:2022') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 1:2019, OIC/SMIIC 24:2020') {
        continue;
      }
      if ($belgelendirileceksistemler === 'OIC/SMIIC 1:2019, OIC/SMIIC 17-1:2020') {
        continue;
      }

      $bitistarihi = date_create_from_format('Y-m-d', $ret->bitistarihi);
      $gecerliliktarihi = $bitistarihi != '' ? date_format($bitistarihi, 'd.m.Y') : '';

      $ilkyayintarihi = date_create_from_format('Y-m-d', $ret->ilkyayintarihi);
      $ilkyayintarihi = $ilkyayintarihi != '' ? date_format($ilkyayintarihi, 'd.m.Y') : '';

      $tar1 = strtotime(date('Y-m-d'));
      $tar2 = strtotime($ret->bitistarihi);

      $bugun = strtotime(date('d.m.Y'));
      if ($bugun > strtotime($gecerliliktarihi)) {
        continue;
      }

      if ($ret->firmaadi == '') {
        continue;
      }

      //    if ($temp == trim($ret->firmaadi)) {
      //        continue;
      //    } else {
      //        $temp = trim($ret->firmaadi);
      //    }

      $cevrim = $ret->belgecevrimi;
      $cevrim = $cevrim == '' ? '1' : $cevrim;

      $dentarihi = '';
      $dtipi = '';
      if ($ret->asama2 != '') {
        $dentarihi = $ret->asama2;
        $dtipi = 'İlk';
      }
      if ($ret->gozetim1 != '') {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if ($ret->gozetim2 != '') {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if ($ret->ybtar != '') {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Yb';
      }
      if ($ret->ozeltar != '' && strtotime($ret->ozeltar) > strtotime($dentarihi)) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }

      if (intval($cevrim) >= 2 && ($ret->asama == 'g1' || $ret->asama == 'g1karar')) {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if (intval($cevrim) >= 2 && ($ret->asama == 'g2' || $ret->asama == 'g2karar')) {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if (intval($cevrim) >= 2 && ($ret->asama == 'yb' || $ret->asama == 'ybkarar')) {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Yb';
      }
      if (
        intval($cevrim) >= 2 &&
        ($ret->asama == 'ozel' || $ret->asama == 'ozelkarar') &&
        strtotime($ret->ozeltar) > strtotime($dentarihi)
      ) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }
      $denyili = explode('.', explode(', ', $dentarihi)[0])[2];
      $dentarihi = wordwrap($dentarihi, 15, '<br>');

      //    if($ph->InStr($dentarihi, "2024") === -1) continue;

      $rowcertno = '';
      $rowdurumrenk = 'primary';
      $rowdurum = 'Devam';
      $kurul0 = trim($ret->firmaadi); //mb_substr($ret["firmaadi"], 0, 10, 'UTF-8');

      if ($tar2 > strtotime('-180 days') && $tar2 < $tar1) {
        $rowdurumrenk = 'warning';
        $rowdurum = 'Askı';
      }
      if ($tar2 < strtotime('-180 days')) {
        $rowdurumrenk = 'danger';
        $rowdurum = 'İptal';
      }
      if ($tar2 >= $tar1 || $tar2 == '') {
        $rowdurumrenk = 'primary';
        $rowdurum = 'Aktif';
      }

      //    $a9001sure += floatval($ret["iso9001kalansure"]);
      //    $a14001sure += floatval($ret["iso14001kalansure"]);
      //    $a22000sure += floatval($ret["iso22000kalansure"]);
      //    $a45001sure += floatval($ret["iso45001kalansure"]);
      //    $a27001sure += floatval($ret["iso27001kalansure"]);
      //    $a50001sure += floatval($ret["iso50001kalansure"]);

      $ea = $ret->eakodu;
      $nace = $ret->nacekodu;
      $kat = str_replace('@', '', $ret->kategori22);
      $oickat = str_replace('ß', '', $ret->kategorioic);
      $enysteknikalan = str_replace('Æ', '', $ret->teknikalanenys);
      $bgkat = str_replace('€', '', $ret->kategoribgys);
      $eanacekat = '';

      if ($nace != '') {
        $nace = '|' . $nace;
      }
      if ($kat != '') {
        $kat = '@' . str_replace('@', '', $kat);
      }
      $oickat = $oickat != '' ? 'ß' . str_replace('ß', '', $oickat) : str_replace('ß', '', $oickat);
      $enysteknikalan =
        $enysteknikalan != '' ? 'Æ' . str_replace('Æ', '', $enysteknikalan) : str_replace('Æ', '', $enysteknikalan);
      if ($bgkat != '') {
        $bgkat = '€' . $bgkat;
      }

      $eanacekat = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

      $ilce = $ret->milce;
      $sehir = $ret->msehir;
      $satir .=
        '	<tr>
                    <td>' .
        $i .
        '</td>
                    <td>' .
        str_pad($ret->planno, 4, '0', STR_PAD_LEFT) .
        '</td>
                    <td>' .
        $belgelendirileceksistemler .
        '</td>
                    <td>' .
        trim($ret->firmaadi) .
        '</td>
                    <td>' .
        trim($ret->firmaadresi) .
        '</td>
                    <td>Türkiye</td>
                    <td>' .
        $eanacekat .
        '</td>
                    <td>' .
        wordwrap(trim($ret->belgelendirmekapsami), 30, '<br>') .
        '</td>
                    <td>' .
        $dtipi .
        '</td>
                    <td>' .
        $ilkyayintarihi .
        '</td>
                    <td>' .
        $ret->certno .
        '</td>
                    <td>Aktif</td>
                    <td>' .
        $gecerliliktarihi .
        '</td>
                    <td>' .
        $ilce .
        '</td>
                    <td>' .
        $sehir .
        '</td>
                    <td>' .
        $ret->vardiyalicalisansayisi .
        '</td>
                    <td>' .
        $ret->vardiyalicalisansayisi1 .
        '</td>
                    <td>' .
        $ret->vardiyalicalisansayisi2 .
        '</td>
                    <td>' .
        $ret->danisman .
        '</td>
               </tr>';
      //            echo $satir;
      $i++;
    }

    return $satir;
  }

  public static function getCompanyName($id)
  {
    return DB::select('select adi from company where id=' . $id)[0]->adi;
  }

  public static function getDenetimBitisTarihi($tar)
  {
    $denetimbitistarihi = '';
    if (self::InStr($tar, ',') > -1) {
      $dentars = explode(',', str_replace(' ', '', $tar));
      if (count($dentars) == 2) {
        $denetimbitistarihi = $dentars[count($dentars) - 1];
      }
      if (count($dentars) == 3) {
        $denetimbitistarihi = $dentars[count($dentars) - 1];
      }
      if (count($dentars) > 3) {
        $denetimbitistarihi = $dentars[count($dentars) - 1];
      }
    } else {
      $dentars = str_replace(' ', '', $tar);
      $denetimbitistarihi = $dentars;
    }
    return $denetimbitistarihi;
  }

  public static function getDenetimTarihi($ret)
  {
    // Cevirim değeri boş ise 1 yap
    $cevrim = empty($ret->belgecevrimi) ? 1 : intval($ret->belgecevrimi);

    // Aşamalar
    $dentarihi = '';
    $dtipi = '';

    // 3.1 Hangi aşamaların dolu olduğuna göre son denetim tarihini seç
    if (!empty($ret->asama2)) {
      $dentarihi = $ret->asama2;
      $dtipi = 'İlk';
    }
    if (!empty($ret->gozetim1)) {
      $dentarihi = $ret->gozetim1;
      $dtipi = 'G1';
    }
    if (!empty($ret->gozetim2)) {
      $dentarihi = $ret->gozetim2;
      $dtipi = 'G2';
    }
    if (!empty($ret->ybtar)) {
      $dentarihi = $ret->ybtar;
      $dtipi = 'Y.b.';
    }
    // Özel tarihin bitişi, mevcut tarihinkinden büyükse özel tar
    if (
      !empty($ret->ozeltar) &&
      strtotime(self::getDenetimBitisTarihi($ret->ozeltar)) > strtotime(self::getDenetimBitisTarihi($dentarihi))
    ) {
      $dentarihi = $ret->ozeltar;
      $dtipi = 'Özel';
    }

    // 3.2 Cevirim >=2 ise asama durumuna göre tekrar güncelle
    if (intval($cevrim) >= 2) {
      if ($ret->asama == 'g1' || $ret->asama == 'g1karar') {
        $dentarihi = $ret->gozetim1;
        $dtipi = 'G1';
      }
      if ($ret->asama == 'g2' || $ret->asama == 'g2karar') {
        $dentarihi = $ret->gozetim2;
        $dtipi = 'G2';
      }
      if ($ret->asama == 'yb' || $ret->asama == 'ybkarar') {
        $dentarihi = $ret->ybtar;
        $dtipi = 'Yb';
      }
      if (
        ($ret->asama == 'ozel' || $ret->asama == 'ozelkarar') &&
        !empty($ret->ozeltar) &&
        strtotime(self::getDenetimBitisTarihi($ret->ozeltar)) > strtotime(self::getDenetimBitisTarihi($dentarihi))
      ) {
        $dentarihi = $ret->ozeltar;
        $dtipi = 'Özel';
      }
    }

    return [$dentarihi, $dtipi];
  }

  public static function getDenetimBaslangicTarihi($dentarihi)
  {
    $denbastarihi = date('d.m.Y'); // default olarak bugün

    // Örneğin InStr kontrolü (projedeki fonksiyonla aynı mantıkta)
    if (self::InStr($dentarihi, ',') > -1) {
      $dentars = explode(',', str_replace(' ', '', $dentarihi));
      // Kaç parça varsa ilkini alıyoruz
      if (count($dentars) >= 1) {
        $denbastarihi = $dentars[0];
      }
    } else {
      $denbastarihi = str_replace(' ', '', $dentarihi);
    }

    return $denbastarihi;
  }

  public static function buildDenetimBildirimHTML($ret, $dentarihi, $dtipi)
  {
    $firma = mb_substr($ret->firmaadi, 0, 25, 'UTF-8');
    $link = route('crm-planlama', ['asama' => $ret->asama, 'pno' => $ret->planno]);

    // Burada $dentarihi '15.04.2025' gibi bir formatta gelebilir
    // veya '2025-04-15' gibi Y-m-d formunda olabilir.
    // Bu nedenle, eğer Y-m-d formatında geliyorsa dönüştürelim:
    // (Örn. basit bir "preg_match" veya "explode" ile kontrol edebilirsiniz.)

    // Basit bir yaklaşım: strtotime başarılı ise, date("d.m.Y", ...) ile format değiştir.
    $timestamp = strtotime($dentarihi);
    if ($timestamp) {
      $dentarihiFormatted = date('d.m.Y', $timestamp);
    } else {
      // Tarihi parse edemediysek olduğu gibi (gelen string) gösteririz.
      $dentarihiFormatted = $dentarihi;
    }

    $html =
      '
      <li class="list-group-item list-group-item-action dropdown-notifications-item">
        <a href="' .
      $link .
      '" target="_blank">
          <div class="d-flex gap-2">
            <div class="flex-shrink-0">
              <div class="avatar me-1">
                [' .
      $dtipi .
      ']
              </div>
            </div>
            <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
              <h7 class="mb-1 text-truncate">' .
      $dentarihiFormatted .
      '</h7>
              <small class="text-truncate text-body">' .
      $firma .
      '...</small>
            </div>
            <div class="flex-shrink-0 dropdown-notifications-actions">
              <small class="text-muted"></small>
            </div>
          </div>
        </a>
      </li>';

    return $html;
  }

  public static function getOncekiTarih($bitisTarihTimestamp, $asama)
  {
    // Varsayılan olarak bitiş tarihinden 2 ay önce (g1/g2 durumuna göre).
    // Yb ise 1 ay önce
    if (in_array($asama, ['g1', 'g1karar', 'g2', 'g2karar'])) {
      return strtotime('-2 month', $bitisTarihTimestamp);
    } elseif (in_array($asama, ['yb', 'ybkarar'])) {
      return strtotime('-1 month', $bitisTarihTimestamp);
    }

    // Diğer durumlar için tarih değiştirmeden döndürelim (isteğe bağlı)
    return $bitisTarihTimestamp;
  }

  public static function buildBelgeSureBildirimHTML($ret)
  {
    $firma = mb_substr($ret->firmaadi, 0, 25, 'UTF-8');
    $link = route('crm-planlama', ['asama' => $ret->asama, 'pno' => $ret->planno]);

    // $ret->bitistarihi, veritabanında Y-m-d tutuluyor, bunu d.m.Y formatına dönüştürelim.
    // Ön koşul: $ret->bitistarihi null değilse bu fonksiyonu çağırıyoruz varsayımıyla...
    $bitistarihTimestamp = strtotime($ret->bitistarihi);
    $bitistarihiFormatted = $bitistarihTimestamp ? date('d.m.Y', $bitistarihTimestamp) : '';

    $html =
      '
      <li class="list-group-item list-group-item-action dropdown-notifications-item">
        <a href="' .
      $link .
      '" target="_blank">
          <div class="d-flex gap-2">
            <div class="flex-shrink-0">
              <div class="avatar me-1">
                <i class="mdi mdi-alert-outline"></i>
              </div>
            </div>
            <div class="d-flex flex-column flex-grow-1 overflow-hidden w-px-200">
              <h7 class="mb-1 text-truncate">Sertifika Geçerlilik Süresi Yaklaşıyor</h7>
              <small class="text-truncate text-body">' .
      $firma .
      '... <br>
                  <span style="color: #d9534f;">
                     Bitiş tarihi: ' .
      $bitistarihiFormatted .
      '
                  </span>
               </small>
            </div>
            <div class="flex-shrink-0 dropdown-notifications-actions">
              <small class="text-muted"></small>
            </div>
          </div>
        </a>
      </li>';

    return $html;
  }

  public function denetciKontrol(Request $request)
  {
    // Yardımcı Fonksiyon: Denetçi ismini normalize eder
    function normalizeName($string)
    {
      $string = preg_replace('/\s+/', '', trim($string));
      $string = mb_strtolower($string, 'UTF-8');
      $mapping = [
        'ç' => 'c',
        'ğ' => 'g',
        'ı' => 'i',
        'ö' => 'o',
        'ş' => 's',
        'ü' => 'u',
      ];
      return strtr($string, $mapping);
    }

    // Yardımcı Fonksiyon: Denetçi isimlerini virgülle ayırarak diziye çevirir
    function getDenetciNamesFromRow($row, $fields)
    {
      $names = [];
      foreach ($fields as $field) {
        if (!empty($row->$field)) {
          $parts = explode(',', $row->$field);
          foreach ($parts as $part) {
            $part = trim($part);
            if (!empty($part)) {
              $names[] = $part;
            }
          }
        }
      }
      return $names;
    }

    // 30 Günlük gün bilgisi hazırlayan fonksiyon
    function prepareEmptyDaysInfo($n = 30)
    {
      $daysInfo = [];
      $tsToday = strtotime('today');
      for ($i = 0; $i < $n; $i++) {
        $ts = strtotime("+$i day", $tsToday);
        $dmy = date('d.m.Y', $ts);
        $ymd = date('Y-m-d', $ts);
        $daysInfo[$ymd] = [
          'gun' => $dmy,
          'denetciler' => [], // Bu gün için meşgul olan denetçiler
        ];
      }
      return $daysInfo;
    }

    // Planlardan gelen tarih verisini işleyip gün bilgilerine denetçi bilgilerini ekler
    function buildDaysInfoFromMultiDates()
    {
      global $db;
      $today = date('Y-m-d');
      $future = date('Y-m-d', strtotime('+30 days'));
      $rows = DB::select('SELECT * FROM planlar');
      $daysInfo = prepareEmptyDaysInfo(30);

      foreach ($rows as $row) {
        $planno = $row->planno ?? '';
        $sistems = $row->belgelendirileceksistemler ?? '';
        $firma = DB::select('SELECT firmaadi FROM basvuru WHERE planno=?', [intval($planno)])[0]->firmaadi ?? '';

        // Tarihleri al
        $dates = [
          'asama1' => getDenetciNamesFromRow($row, ['bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1']),
          'asama2' => getDenetciNamesFromRow($row, ['bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2']),
          'gozetim1' => getDenetciNamesFromRow($row, ['gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1']),
          'gozetim2' => getDenetciNamesFromRow($row, ['gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2']),
          'ybtar' => getDenetciNamesFromRow($row, ['ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb']),
          'ozeltar' => getDenetciNamesFromRow($row, ['otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot']),
        ];

        // Parse tarihleri ve denetçileri günlere ekle
        foreach ($dates as $dateField => $denetciArray) {
          parseDates($row->$dateField, $denetciArray, $planno, $firma, $sistems, $daysInfo);
        }
      }
      return $daysInfo;
    }

    // Tarihleri parse ederek meşguliyet bilgilerini günlere ekler
    function parseDates($dateString, $denetciArray, $planno, $firma, $sistems, &$daysInfo)
    {
      $today = date('Y-m-d');
      $future = date('Y-m-d', strtotime('+30 days'));

      if (empty($dateString)) {
        return;
      }
      $pieces = explode(',', $dateString);
      foreach ($pieces as $dateStr) {
        $dateStr = trim($dateStr);
        if (!$dateStr) {
          continue;
        }
        $dt = DateTime::createFromFormat('d.m.Y', $dateStr);
        if ($dt) {
          $ymd = $dt->format('Y-m-d');
          if ($ymd >= $today && $ymd <= $future) {
            foreach ($denetciArray as $denAd) {
              $denAd = trim($denAd);
              if ($denAd === '') {
                continue;
              }
              $normalizedDenAd = normalizeName($denAd);
              if (!isset($daysInfo[$ymd]['denetciler'][$normalizedDenAd])) {
                $daysInfo[$ymd]['denetciler'][$normalizedDenAd] = [
                  'mesgul' => true,
                  'planno' => $planno,
                  'firmaadi' => $firma,
                  'sistems' => $sistems,
                ];
              }
            }
          }
        }
      }
    }

    // Seçilen gün ile ilgili verileri filtreleme
    function filterDaysInfo($daysInfo, $targetDayDMY)
    {
      $res = [];
      foreach ($daysInfo as $ymd => $arr) {
        if ($arr['gun'] === $targetDayDMY) {
          $res[$ymd] = $arr;
          break;
        }
      }
      return $res;
    }

    // Denetçilerin meşguliyet durumunu gösteren HTML çıktısını üretir
    function kontrolet($deger, $dentar, $sistems = '')
    {
      $gun = $dentar['gun'];
      $formatter = new IntlDateFormatter(
        'tr_TR',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'Europe/Istanbul',
        IntlDateFormatter::GREGORIAN,
        'EEEE'
      );
      $dayName = $formatter->format(strtotime($gun));

      if (!empty($dentar['mesgul'])) {
        return '
        <div class="alert alert-danger alert-dismissible" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-alert-circle-outline mdi-24px me-2"></i>Dikkat!!</h4>
          <p>' .
          htmlspecialchars($gun) .
          ' (' .
          htmlspecialchars($dayName) .
          ') tarihinde seçili ' .
          htmlspecialchars($deger) .
          '
                        başdenetçi/denetçiler ' .
          htmlspecialchars($dentar['sistems'] ?? $sistems) .
          '
                        sistemi için [' .
          htmlspecialchars($dentar['planno'] ?? '') .
          '] ' .
          htmlspecialchars($dentar['firmaadi'] ?? '') .
          ' denetiminde görevliler.
                        </p>
        </div>';
      } else {
        return '<div class="alert alert-success alert-dismissible" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-check-circle-outline mdi-24px me-2"></i>Mükemmel :)</h4>
          <hr>

                        <p class="mb-0">' .
          htmlspecialchars($gun) .
          ' (' .
          htmlspecialchars($dayName) .
          ') tarihinde seçili ' .
          htmlspecialchars($deger) .
          '
                        başdenetçi/denetçiler ' .
          htmlspecialchars($dentar['sistems'] ?? $sistems) .
          '
                        sistemi için görev alabilirler.</p>
        </div>';
      }
    }

    // 5) handleDenetim: GET parametresinde belirtilen denetçi(ler) üzerinden daysInfo'dan HTML mesajlarını toplar.
    function handleDenetim($paramName, $daysInfo, $sistems = '')
    {
      $results = [];
      if (empty($_GET[$paramName])) {
        return $results;
      }
      $denetciList = explode(', ', $_GET[$paramName]);
      foreach ($denetciList as $denetci) {
        $denetciOriginal = trim($denetci);
        if ($denetciOriginal === '') {
          continue;
        }
        $normalizedDenetci = normalizeName($denetciOriginal);
        foreach ($daysInfo as $ymd => $dayData) {
          $isBusy = false;
          $firmaadi = '';
          $sistems2 = $sistems;
          if (isset($dayData['denetciler'][$normalizedDenetci])) {
            $isBusy = true;
            $firmaadi = $dayData['denetciler'][$normalizedDenetci]['firmaadi'] ?? '';
            $sistems2 = $dayData['denetciler'][$normalizedDenetci]['sistems'] ?? $sistems;
          }
          $msg = kontrolet(
            $denetciOriginal,
            [
              'mesgul' => $isBusy,
              'planno' => $dayData['denetciler'][$normalizedDenetci]['planno'] ?? '',
              'firmaadi' => $firmaadi,
              'sistems' => $sistems2,
              'gun' => $dayData['gun'],
            ],
            $sistems
          );
          if (!isset($results[$normalizedDenetci])) {
            $results[$normalizedDenetci] = [
              'original' => $denetciOriginal,
              'msgs' => [],
            ];
          }
          $results[$normalizedDenetci]['msgs'][] = $msg;
        }
      }
      return $results;
    }

    // Ana işlem
    $actt = $request->get('asamatar', '');
    $acttar = $request->get('tarihtar', '');
    $dentar = explode(', ', $acttar);

    $asamaMap = [
      'asama1tar' => ['bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1'],
      'asama2tar' => ['bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2'],
      'gozetim1tar' => ['gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1'],
      'gozetim2tar' => ['gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2'],
      'ybtar' => ['ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb'],
      'ozeltar' => ['otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot'],
    ];

    $denetciVarMi = false;
    $allFields = array_merge(...array_values($asamaMap));
    foreach ($allFields as $df) {
      if (!empty($request->get($df))) {
        $denetciVarMi = true;
        break;
      }
    }

    $collected = [];
    $mergeResults = function ($res) use (&$collected) {
      foreach ($res as $den => $data) {
        if (!isset($collected[$den])) {
          $collected[$den] = $data;
        } else {
          $collected[$den]['msgs'] = array_merge($collected[$den]['msgs'], $data['msgs']);
        }
      }
    };

    // 30 günlük veriyi oluşturuyoruz
    $daysInfo = buildDaysInfoFromMultiDates();

    if (empty($acttar) && $denetciVarMi) {
      if (isset($asamaMap[$actt])) {
        $fields = $asamaMap[$actt];
        foreach ($fields as $field) {
          $mergeResults(handleDenetim($field, $daysInfo));
        }
      }
    } elseif (!empty($acttar) && $denetciVarMi) {
      foreach ($dentar as $oneDay) {
        if (isset($asamaMap[$actt])) {
          $fields = $asamaMap[$actt];
          $filtered = filterDaysInfo($daysInfo, $oneDay);
          foreach ($fields as $field) {
            $mergeResults(handleDenetim($field, $filtered));
          }
        }
      }
    }

    $selectedDays = [];
    if (!empty($_GET['tarihtar'])) {
      $dates = explode(', ', $_GET['tarihtar']);
      foreach ($dates as $date) {
        $date = trim($date);
        if ($date) {
          $selectedDays[] = $date; // Formdan "dd.mm.YYYY" formatında geliyor
        }
      }
    } else {
      foreach ($daysInfo as $ymd => $data) {
        $selectedDays[] = $data['gun'];
      }
    }

    $selectedDays = array_unique($selectedDays);

    // 2. Busy denetçi anahtarlarını gün bazında toplayalım.
    // daysInfo içinde, her gün busy denetçi anahtarları (normalize edilmiş) saklanıyor.
    $busyForDay = []; // Örnek: [ "01.09.2023" => ['huseyinoksuzler', 'sinemacercelik'], ... ]
    foreach ($daysInfo as $ymd => $data) {
      $dayKey = $data['gun']; // "dd.mm.YYYY" formatında
      if (!empty($data['denetciler'])) {
        foreach ($data['denetciler'] as $busyKey => $info) {
          $busyForDay[$dayKey][] = $busyKey;
        }
      }
      if (!isset($busyForDay[$dayKey])) {
        $busyForDay[$dayKey] = [];
      }
    }

    // 3. "denetciler" tablosundan, belirttiğiniz WHERE koşuluna uyan aktif denetçi listesini çekelim.
    $availableQuery = "SELECT id, denetci, ea, nace, kategori, kategorioic, teknikalan, kategoribg FROM denetciler
                   WHERE (atama9001='Başdenetçi'
                         OR atama14001='Başdenetçi'
                         OR atama22000='Başdenetçi'
                         OR atamaOicsmiic='Başdenetçi'
                         OR atamaOicsmiic6='Başdenetçi'
                         OR atamaOicsmiic9='Başdenetçi'
                         OR atamaOicsmiic171='Başdenetçi'
                         OR atamaOicsmiic24='Başdenetçi'
                         OR atama45001='Başdenetçi'
                         OR atama50001='Başdenetçi'
                         OR atama27001='Başdenetçi'
                         OR iku='Başdenetçi'
                         OR atama9001 LIKE '%Teknik Uzman%'
                         OR atama14001 LIKE '%Teknik Uzman%'
                         OR atama45001 LIKE '%Teknik Uzman%'
                         OR atama50001 LIKE '%Teknik Uzman%'
                         OR atama22000 LIKE '%Teknik Uzman%'
                         OR atamaOicsmiic LIKE '%Teknik Uzman%'
                         OR atamaOicsmiic6 LIKE '%Teknik Uzman%'
                         OR atamaOicsmiic9 LIKE '%Teknik Uzman%'
                         OR atamaOicsmiic171 LIKE '%Teknik Uzman%'
                         OR atama27001 LIKE '%Teknik Uzman%')
                         AND is_active = 1";
    $allDenetciler = DB::select($availableQuery);
    if (!is_array($allDenetciler) || (is_array($allDenetciler) && !isset($allDenetciler[0]))) {
      $allDenetciler = [$allDenetciler];
    }

    // 4. Seçili her gün için, busy listesinde olmayan denetçiler (müsait denetçiler) listesini oluşturuyoruz.
    $musaitForDay = []; // Örnek: [ "01.09.2023" => [denetçi1, denetçi2, ...], ... ]
    foreach ($selectedDays as $day) {
      // $day: "dd.mm.YYYY" formatında
      $busyList = isset($busyForDay[$day]) ? $busyForDay[$day] : [];
      $freeList = [];
      foreach ($allDenetciler as $denetci) {
        $norm = normalizeName($denetci->denetci);
        if (!in_array($norm, $busyList)) {
          $freeList[] = $denetci;
        }
      }
      $musaitForDay[$day] = $freeList;
    }

    // HTML Çıktısı oluşturuluyor
    $htmlOutput = '<div class="container my-4">
        <ul class="nav nav-tabs" role="tablist" id="denetciControlTab">';
    $first = true;
    $tabNum = 1;
    foreach ($collected as $denetciKey => $data) {
      $originalName = $data['original'];
      $activeClass = $first ? 'active' : '';
      $ariaSelected = $first ? 'true' : 'false';
      $htmlOutput .=
        '
        <li class="nav-item">
            <button type="button" class="nav-link ' .
        $activeClass .
        '" role="tab" data-bs-toggle="tab" data-bs-target="#tabcontent-' .
        $tabNum .
        '" aria-controls="tabcontent-' .
        $tabNum .
        '" aria-selected="' .
        $ariaSelected .
        '">' .
        htmlspecialchars($originalName) .
        '</button>
        </li>';
      $first = false;
      $tabNum++;
    }

    $htmlOutput .=
      '
        <li class="nav-item">
            <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#tabcontent-' .
      $tabNum .
      '" aria-controls="tabcontent-' .
      $tabNum .
      '" aria-selected="false">Müsait Denetçiler</button>
        </li>';

    $htmlOutput .= '</ul>';

    $htmlOutput .= '<div class="card-body">
        <div class="tab-content p-0">';

    $first = true;
    $currentTab = 1;
    foreach ($collected as $denetciKey => $data) {
      $activePane = $first ? 'show active' : '';
      $htmlOutput .=
        '
        <div class="tab-pane fade ' .
        $activePane .
        '" id="tabcontent-' .
        $currentTab .
        '" role="tabpanel">';
      foreach ($data['msgs'] as $msgHtml) {
        $htmlOutput .= $msgHtml;
      }
      $htmlOutput .= '</div>';
      $first = false;
      $currentTab++;
    }

    // Müsait denetçiler kısmı
    $htmlOutput .=
      '
        <div class="tab-pane fade" id="tabcontent-' .
      $currentTab .
      '" role="tabpanel">
            <h4>Müsait Denetçiler</h4>';

    foreach ($selectedDays as $day) {
      // $day: "dd.mm.YYYY" formatında; gün adını alalım.
      $timestamp = strtotime($day);
      $formattedDate = date('d.m.Y', $timestamp);
      $formatter = new IntlDateFormatter(
        'tr_TR',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'Europe/Istanbul',
        IntlDateFormatter::GREGORIAN,
        'EEEE'
      );
      $dayName = $formatter->format($timestamp);

      // Müsait denetçileri alalım
      $freeDenetciler = $musaitForDay[$day] ?? [];
      $htmlOutput .= '<h5>' . htmlspecialchars($formattedDate . ' (' . $dayName . ')') . '</h5>';

      if (!empty($freeDenetciler)) {
        $htmlOutput .= '<table class="table table-bordered table-responsive denetci-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Denetçi</th>
                    <th style="word-wrap: break-word; max-width: 150px !important;white-space: normal;">EA Kodu</th>
                    <th style="word-wrap: break-word; max-width: 150px !important;white-space: normal;">NACE Kodu</th>
                    <th>22000 Kategori/Alt Kategori</th>
                    <th>Helal Kategori/Alt Kategori</th>
                    <th>EnYs Teknik Alan</th>
                    <th>BGYS Teknik Alan</th>
                </tr>
            </thead>
            <tbody>';
        foreach ($freeDenetciler as $denetci) {
          // Denetçi bilgilerini tabloya ekleyelim
          $htmlOutput .=
            '<tr>
                <td>' .
            $denetci->id .
            '</td>
                <td>' .
            $denetci->denetci .
            '</td>
                <td>' .
            wordwrap($denetci->ea, 50, '<br>', true) .
            '</td>
                <td class="nace-kodu">' .
            wordwrap($denetci->nace, 50, '<br>', true) .
            '</td>
                <td>' .
            wordwrap($denetci->kategori, 50, '<br>', true) .
            '</td>
                <td>' .
            wordwrap($denetci->kategorioic, 50, '<br>', true) .
            '</td>
                <td>' .
            $denetci->teknikalan .
            '</td>
                <td>' .
            $denetci->kategoribg .
            '</td>
            </tr>';
        }
        $htmlOutput .= '</tbody></table>';
      } else {
        $htmlOutput .= '<p>Bu günde müsait denetçi bulunmamaktadır.</p>';
      }
    }

    $htmlOutput .= '</div>'; // Tab content sonu

    $htmlOutput .= '</div>';
    $htmlOutput .= '</div></div>';

    return $htmlOutput;
  }

  /**
   * Hazır 30 günlük tablo (gun / denetciler).
   */
  private function prepareEmptyDaysInfo($n = 30)
  {
    $daysInfo = [];
    $tsToday = strtotime('today'); // Bugünün tarihini alıyoruz
    for ($i = 0; $i < $n; $i++) {
      // Bugünden başlayarak $i gün sonrasını hesaplıyoruz
      $ts = strtotime("+$i day", $tsToday);
      // Gün ve Yıl-Ay-Gün formatlarını elde ediyoruz
      $dmy = date('d.m.Y', $ts); // örn: 01.09.2023
      $ymd = date('Y-m-d', $ts); // örn: 2023-09-01

      // Her gün için bir dizi oluşturup gün ve denetçi bilgilerini ekliyoruz
      $daysInfo[$ymd] = [
        'gun' => $dmy, // Günün tam tarihi
        'denetciler' => [], // Bu gün için meşgul olan denetçiler
      ];
    }
    return $daysInfo;
  }

  /**
   * Tablodaki verileri al, virgüllü tarihleri parse et, 30 gün aralığında meşguliyet kaydet.
   */
  private function buildDaysInfoFromMultiDates()
  {
    $today = date('Y-m-d');
    $future = date('Y-m-d', strtotime('+30 days'));

    // Veritabanından plan verilerini çekiyoruz
    $rows = DB::select('SELECT * FROM planlar');

    // 30 günlük gün bilgilerini hazırlıyoruz
    $daysInfo = $this->prepareEmptyDaysInfo(30);

    // Her bir plan için işlem yapıyoruz
    foreach ($rows as $row) {
      $planno = $row->planno ?? '';
      $sistems = $row->belgelendirileceksistemler ?? '';

      $firma = '';
      $basv = DB::select('SELECT firmaadi FROM basvuru WHERE planno = ? ORDER BY planno DESC', [intval($planno)]);
      if (!empty($basv)) {
        $firma = $basv[0]->firmaadi;
      }

      // Tarih sütunları
      $asama1Dates = $row->asama1 ?? '';
      $asama2Dates = $row->asama2 ?? '';
      $gozetim1Dates = $row->gozetim1 ?? '';
      $gozetim2Dates = $row->gozetim2 ?? '';
      $ybtarDates = $row->ybtar ?? '';
      $ozeltarDates = $row->ozeltar ?? '';

      // Denetçi sütunlarından isimleri alıyoruz
      $denetcilerAsama1 = $this->getDenetciNamesFromRow($row, ['bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1']);
      $denetcilerAsama2 = $this->getDenetciNamesFromRow($row, ['bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2']);
      $denetcilerG1 = $this->getDenetciNamesFromRow($row, ['gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1']);
      $denetcilerG2 = $this->getDenetciNamesFromRow($row, ['gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2']);
      $denetcilerYb = $this->getDenetciNamesFromRow($row, ['ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb']);
      $denetcilerOzel = $this->getDenetciNamesFromRow($row, ['otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot']);

      // Her bir tarih için denetçileri ekliyoruz
      $this->parseDates($asama1Dates, $denetcilerAsama1, $planno, $firma, $sistems, $daysInfo);
      $this->parseDates($asama2Dates, $denetcilerAsama2, $planno, $firma, $sistems, $daysInfo);
      $this->parseDates($gozetim1Dates, $denetcilerG1, $planno, $firma, $sistems, $daysInfo);
      $this->parseDates($gozetim2Dates, $denetcilerG2, $planno, $firma, $sistems, $daysInfo);
      $this->parseDates($ybtarDates, $denetcilerYb, $planno, $firma, $sistems, $daysInfo);
      $this->parseDates($ozeltarDates, $denetcilerOzel, $planno, $firma, $sistems, $daysInfo);
    }

    return $daysInfo;
  }

  private function getDenetciNamesFromRow($row, $fields)
  {
    $names = []; // Denetçi isimlerini tutacak bir dizi başlatıyoruz.
    foreach ($fields as $field) {
      if (isset($row->$field) && !empty($row->$field)) {
        // Virgülle ayrılmış isimleri parçalamak için explode kullanıyoruz
        $parts = explode(',', $row->$field);
        foreach ($parts as $part) {
          $part = trim($part); // İsimlerin başındaki ve sonundaki boşlukları kaldırıyoruz
          if ($part !== '') {
            $names[] = $part; // Eğer isim boş değilse, diziye ekliyoruz
          }
        }
      }
    }
    return $names; // Denetçi isimlerini içeren diziyi döndürüyoruz
  }

  private function parseDates(
    string $dateString,
    array $denetciList,
    string $planno,
    string $firma,
    string $sistems,
    &$daysInfo
  ) {
    if (empty($dateString)) {
      return;
    }
    $pieces = explode(',', $dateString);
    foreach ($pieces as $dateStr) {
      $dateStr = trim($dateStr);
      if (!$dateStr) {
        continue;
      }
      $dt = DateTime::createFromFormat('d.m.Y', $dateStr);
      if ($dt) {
        $ymd = $dt->format('Y-m-d');
        foreach ($denetciList as $denAd) {
          $denAd = trim($denAd);
          if ($denAd === '') {
            continue;
          }
          $normalizedDenAd = $this->normalizeName($denAd);
          if (!isset($daysInfo[$ymd]['denetciler'][$normalizedDenAd])) {
            $daysInfo[$ymd]['denetciler'][$normalizedDenAd] = [
              'mesgul' => true,
              'planno' => $planno,
              'firmaadi' => $firma,
              'sistems' => $sistems,
            ];
          }
        }
      }
    }
  }

  private function normalizeName($string)
  {
    // Tüm boşluk karakterlerini kaldır ve küçük harfe çevir
    $string = preg_replace('/\s+/', '', trim($string));
    $string = mb_strtolower($string, 'UTF-8');
    $mapping = [
      'ç' => 'c',
      'ğ' => 'g',
      'ı' => 'i',
      'ö' => 'o',
      'ş' => 's',
      'ü' => 'u',
    ];
    return strtr($string, $mapping);
  }

  /**
   * Tek günü daysInfo'dan seçmek
   */
  private function filterDaysInfo(array $daysInfo, string $targetDayDMY)
  {
    $res = [];
    foreach ($daysInfo as $ymd => $arr) {
      if (($arr['gun'] ?? '') === $targetDayDMY) {
        $res[$ymd] = $arr;
        break;
      }
    }
    return $res;
  }

  /**
   * Tek denetçi + Tek gün -> meşgul/boş HTML
   */
  private function kontrolet(string $deger, array $dentar, string $sistems = '')
  {
    // Gün bilgisini alalım ve gün adını (Pazar, Pazartesi vb.) elde edelim
    $gun = $dentar['gun']; // Günün tarihi (örneğin: 01.09.2023)
    $formatter = new IntlDateFormatter(
      'tr_TR',
      IntlDateFormatter::NONE,
      IntlDateFormatter::NONE,
      'Europe/Istanbul',
      IntlDateFormatter::GREGORIAN,
      'EEEE' // EEEE: Günün adı (Pazar, Pazartesi, vb.)
    );
    $dayName = $formatter->format(strtotime($gun)); // Gün adını alıyoruz (örneğin: Pazartesi)

    $html = '';
    if (!empty($dentar['mesgul'])) {
      // meşgul
      $html .=
        '
        <div class="alert alert-danger mt-3" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-alert-circle-outline mdi-24px me-2"></i>Dikkat!!</h4>
          <p>' .
        htmlspecialchars($gun) .
        ' (' .
        htmlspecialchars($dayName) .
        ') tarihinde seçili ' .
        htmlspecialchars($deger) .
        '
                başdenetçi/denetçiler ' .
        htmlspecialchars($dentar['sistems'] ?? $sistems) .
        '
                sistemi için [' .
        htmlspecialchars($dentar['planno'] ?? '') .
        '] ' .
        htmlspecialchars($dentar['firmaadi'] ?? '') .
        ' denetiminde görevliler.
                </p>
        </div>';
    } else {
      // boş
      $html .=
        '
        <div class="alert alert-success mt-3" role="alert">
          <h4 class="alert-heading d-flex align-items-center"><i class="mdi mdi-check-circle-outline mdi-24px me-2"></i>Mükemmel :)</h4>
          <hr>
          <p class="mb-0">' .
        htmlspecialchars($gun) .
        ' (' .
        htmlspecialchars($dayName) .
        ') tarihinde seçili ' .
        htmlspecialchars($deger) .
        '
                başdenetçi/denetçiler ' .
        htmlspecialchars($dentar['sistems'] ?? $sistems) .
        '
                sistemi için görev alabilirler.</p>
          </button>
        </div>';
    }
    return $html;
  }

  /**
   * Belirli input paramName için denetçileri alıp daysInfo ile kıyaslayarak HTML üretir.
   */
  function handleDenetim($paramName, $daysInfo, $sistems = '')
  {
    $results = [];
    if (empty($_GET[$paramName])) {
      return $results;
    }
    $denetciList = explode(', ', $_GET[$paramName]);
    foreach ($denetciList as $denetci) {
      $denetciOriginal = trim($denetci);
      if ($denetciOriginal === '') {
        continue;
      }
      $normalizedDenetci = normalizeName($denetciOriginal);
      foreach ($daysInfo as $ymd => $dayData) {
        $isBusy = false;
        $firmaadi = '';
        $sistems2 = $sistems;
        if (isset($dayData['denetciler'][$normalizedDenetci])) {
          $isBusy = true;
          $firmaadi = $dayData['denetciler'][$normalizedDenetci]['firmaadi'] ?? '';
          $sistems2 = $dayData['denetciler'][$normalizedDenetci]['sistems'] ?? $sistems;
        }
        $msg = kontrolet(
          $denetciOriginal,
          [
            'mesgul' => $isBusy,
            'planno' => $dayData['denetciler'][$normalizedDenetci]['planno'] ?? '',
            'firmaadi' => $firmaadi,
            'sistems' => $sistems2,
            'gun' => $dayData['gun'],
          ],
          $sistems
        );
        if (!isset($results[$normalizedDenetci])) {
          $results[$normalizedDenetci] = [
            'original' => $denetciOriginal,
            'msgs' => [],
          ];
        }
        $results[$normalizedDenetci]['msgs'][] = $msg;
      }
    }
    echo $results;
  }

  public function denetimPaketiUpload(Request $request)
  {
    $pno = $request->input('pno');
    $asama = self::turkishToEnglish($request->input('asama'));
    $pati = public_path() . '/uploads/denetimpaketi/' . $pno . '/' . $asama;

    if ($request->hasFile('file')) {
      if (!file_exists($pati)) {
        if (!self::mkdirr($pati)) {
          return response()->json(
            [
              'success' => false,
              'message' => $pati . ' dizini oluşturulamadı...',
            ],
            400
          );
        }
      }

      //      echo $pati;
      $file = $request->file('file');
      $filename = $file->getClientOriginalName();
      // move() metodu doğrudan hedef dizine taşıyacaktır.
      $file->move($pati, $filename);

      // Gerekirse, tam dosya yolunu belirleyin.
      $fullPath = $pati . DIRECTORY_SEPARATOR . $filename;

      // Parametre değerlerine göre farklı işlemler yapabilirsiniz.
      return response()->json([
        'success' => true,
        'message' => $filename . ' başarıyla yüklendi.',
        'path' => $fullPath,
        'filename' => $filename,
        'klasor' => $pno . '/' . $asama,
      ]);
    }

    return response()->json(
      [
        'success' => false,
        'message' => 'Dosya yüklenemedi.',
      ],
      400
    );
  }

  public static function turkishToEnglish($text)
  {
    $turkish = ['ç', 'Ç', 'ğ', 'Ğ', 'ı', 'İ', 'ö', 'Ö', 'ş', 'Ş', 'ü', 'Ü'];
    $english = ['c', 'C', 'g', 'G', 'i', 'I', 'o', 'O', 's', 'S', 'u', 'U'];
    return str_replace($turkish, $english, $text);
  }

  /**
   * @param array $row
   * @return string
   */
  public static function teknikAlan1($row)
  {
// Teknik Alan
    $removeChars = ['|', 'Æ', '@', '€', 'ß'];

    $ea = isset($row->eakodu) ? (new AuditorsController)->cleanValue($row->eakodu, $removeChars) : '';
    $nace = isset($row->nacekodu) ? (new AuditorsController)->cleanValue($row->nacekodu, $removeChars) : '';
    $kat = isset($row->kategori22) ? (new AuditorsController)->cleanValue($row->kategori22, $removeChars) : '';
    $oickat = isset($row->kategorioic) ? (new AuditorsController)->cleanValue($row->kategorioic, $removeChars) : '';
    $enysteknikalan = isset($row->teknikalanenys) ? (new AuditorsController)->cleanValue($row->teknikalanenys, $removeChars) : '';
    $bgkat = isset($row->kategoribgys) ? (new AuditorsController)->cleanValue($row->kategoribgys, $removeChars) : '';


// Boş olmayan değerleri diziye ekleyelim
    $values = [];
    if (trim($ea) !== '') $values[] = trim($ea);
    if (trim($nace) !== '') $values[] = trim($nace);
    if (trim($kat) !== '') $values[] = trim($kat);
    if (trim($oickat) !== '') $values[] = trim($oickat);
    if (trim($enysteknikalan) !== '') $values[] = trim($enysteknikalan);
    if (trim($bgkat) !== '') $values[] = trim($bgkat);
    $teknikAlanTmp = implode(', ', $values);
    $teknikAlanTmp = array_unique(explode(', ', $teknikAlanTmp));

// Birleştirme: Eğer birden çok değer varsa araya boşluk koyarak birleştir, tek değer varsa o değeri direkt kullan.
    $teknikAlan = implode(', ', $teknikAlanTmp);

    return $teknikAlan;
  }

  public static function teknikAlan($row)
  {
    // Characters to remove from values.
    $removeChars = ['|', 'Æ', '@', '€', 'ß'];

    // Clean and retrieve values.
    $ea             = isset($row->eakodu)    ? (new AuditorsController)->cleanValue($row->eakodu, $removeChars) : '';
    $nace           = isset($row->nacekodu)    ? (new AuditorsController)->cleanValue($row->nacekodu, $removeChars) : '';
    $kat            = isset($row->kategori22)  ? (new AuditorsController)->cleanValue($row->kategori22, $removeChars) : '';
    $oickat         = isset($row->kategorioic) ? (new AuditorsController)->cleanValue($row->kategorioic, $removeChars) : '';
    $enysteknikalan = isset($row->teknikalanenys) ? (new AuditorsController)->cleanValue($row->teknikalanenys, $removeChars) : '';
    $bgkat          = isset($row->kategoribgys)  ? (new AuditorsController)->cleanValue($row->kategoribgys, $removeChars) : '';

    // Trim values.
    $ea             = trim($ea);
    $nace           = trim($nace);
    $kat            = trim($kat);
    $oickat         = trim($oickat);
    $enysteknikalan = trim($enysteknikalan);
    $bgkat          = trim($bgkat);

    // Combine $ea and $nace with a "/" separator.
    $eaNace = '';
    if ($ea !== '' || $nace !== '') {
      $eaNace = $ea . '/' . $nace;
    }

    // Prepare an array to hold each line.
    $lines = [];
    if ($eaNace !== '') {
      $lines[] = $eaNace;
    }
    if ($kat !== '') {
      $lines[] = $kat;
    }
    if ($oickat !== '') {
      $lines[] = $oickat;
    }
    if ($enysteknikalan !== '') {
      $lines[] = $enysteknikalan;
    }
    if ($bgkat !== '') {
      $lines[] = $bgkat;
    }

    // Join the lines with a newline character.
    $teknikAlan = implode(" | ", $lines);

    return $teknikAlan;
  }

  public static function isEnysOrBgysTrue($enys, $bgys) {
    return $enys || $bgys;
  }

  public static function anyOicSmiicTrue($oicsmiic, $oicsmiic6, $oicsmiic9, $oicsmiic171, $oicsmiic23, $oicsmiic24) {
    return $oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic171 || $oicsmiic23 || $oicsmiic24;
  }

}
