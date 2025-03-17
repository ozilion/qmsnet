<?php

namespace App\Http\Controllers\denetci_atama;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Planlama;
use App\Http\Controllers\Planlama\Plan;
use App\Models\Auditor;
use App\Models\DenetciAtama;
use App\Models\Denetciler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AuditorsController extends Controller
{
  public function index(Request $request)
  {
    $id = $request->id;

    $where = ['id' => $id];
    $user = User::where($where)->first();

//    $whered = ['uid' => $id];
//    $denetci = Denetciler::where($whered)->first();

    $sistemler = self::atananSistemler($user->name);

    $toplamdenetim = self::toplamPlanKayitSayisi($user->name);

    $user["sistemler"] = $sistemler;

    $user["toplamdenetim"] = $toplamdenetim;

    $user["kurulus"] = Plan::getCompanyName($user->kurulusid);

    $where = ['uid' => $id];
    $auditor = Auditor::where($where)->first();

    return view('content.denetci-atama.app-user-view-account', ['user' => $user, "auditor" => $auditor]);
  }

  public function denetciAtamaDosyasiYukle(Request $request)
  {
    //    $uploadParam1 = $request->input('uid');
    $klasor = Plan::turkishToEnglish($request->input('klasor'));
    $altklasor = Plan::turkishToEnglish($request->input('altklasor'));
    $pati = public_path() . '/uploads/denetci/' . $klasor . '/' . $altklasor;

    if ($request->hasFile('file')) {
      if (!file_exists($pati)) {
        if (!(new Planlama\Plan)->mkdirr($pati)) {
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
        'klasor' => $klasor,
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

  public function auditLogView(Request $request)
  {
    $id = $request->id;

    $where = ['id' => $id];
    $user = User::where($where)->first();

//    $whered = ['uid' => $id];
//    $denetci = Denetciler::where($whered)->first();

    $sistemler = self::atananSistemler($user->name);

    $toplamdenetim = self::toplamPlanKayitSayisi($user->name);

    $user["sistemler"] = $sistemler;

    $user["toplamdenetim"] = $toplamdenetim;

    $user["kurulus"] = Plan::getCompanyName($user->kurulusid);

    $where = ['uid' => $id];
    $auditor = Auditor::where($where)->first();

    return view('content.denetci-atama.app-user-audit-log', ['user' => $user, "auditor" => $auditor]);
  }

  public function periyodicSiteMonitoringView(Request $request)
  {
    $id = $request->id;

    $where = ['id' => $id];
    $user = User::where($where)->first();

//    $whered = ['uid' => $id];
//    $denetci = Denetciler::where($whered)->first();

    $sistemler = self::atananSistemler($user->name);

    $toplamdenetim = self::toplamPlanKayitSayisi($user->name);

    $user["sistemler"] = $sistemler;

    $user["toplamdenetim"] = $toplamdenetim;

    $user["kurulus"] = Plan::getCompanyName($user->kurulusid);

    $where = ['uid' => $id];
    $auditor = Auditor::where($where)->first();

    return view('content.denetci-atama.app-user-site-monitoring', ['user' => $user, "auditor" => $auditor]);
  }

  public static function atananSistemler($denetci)
  {
    if (empty($denetci)) {
      return "";
    }

    // Laravel Query Builder ile ilgili kayıtları çekelim
    $results = DB::table('denetciler')
      ->where('denetci', $denetci)
      ->orderBy('denetci', 'asc')
      ->get();

    if ($results->isEmpty()) {
      return "";
    }

    // Hangi kolonun hangi sistem adını temsil ettiğini tanımlıyoruz.
    $mapping = [
      'atama9001' => '9001',
      'atama14001' => '14001',
      'atama45001' => '45001',
      'atama22000' => '22000',
      'atama50001' => '50001',
      'atama27001' => '27001',
      'atamaOicsmiic' => 'OIC/SMIIC 1',
      'atamaOicsmiic6' => 'OIC/SMIIC 6',
      'atamaOicsmiic9' => 'OIC/SMIIC 9',
      'atamaOicsmiic171' => 'OIC/SMIIC 17-1',
      'atamaOicsmiic23' => 'OIC/SMIIC 23',
      'atamaOicsmiic24' => 'OIC/SMIIC 24',
    ];

    $systems = [];

    // Her bir kayıt için mapping’e göre hangi sistemin atanmış olduğunu kontrol ediyoruz.
    foreach ($results as $row) {
      foreach ($mapping as $column => $systemName) {
        if (!empty($row->$column)) {
          $systems[] = $systemName;
        }
      }
    }

    // Aynı sistem birden fazla eklenmişse, array_unique ile temizleyelim.
    $systems = array_unique($systems);

    // Şimdi, sistem adlarını Bootstrap badge'leri ile gösteren HTML çıktısı üretelim.
    $htmlOutput = '';
    foreach ($systems as $system) {
      // İsteğe bağlı olarak badge renklerini değiştirebilirsiniz.
      $htmlOutput .= '<span class="badge bg-primary me-1">' . e($system) . '</span>';
    }

    return $htmlOutput;
  }

  public static function atananSistemlerAdaylik($denetci)
  {
    if ($denetci == "") return "";
    $atanansistemler = "";

    $statement = "denetciler where denetci='" . $denetci . "'";

    $sqlSQL = "SELECT * FROM {$statement} ORDER BY `denetci` ASC";
    $result = DB::select($sqlSQL);
//        var_dump($result);
    foreach ($result as $ret) {
      $atanansistemler .= (strlen($ret->atama9001) > 0 && self::InStr($ret->atama9001, "Aday") > -1) ? "9001, " : "";
      $atanansistemler .= (strlen($ret->atama14001) > 0 && self::InStr($ret->atama14001, "Aday") > -1) ? "14001, " : "";
      $atanansistemler .= (strlen($ret->atama22000) > 0 && self::InStr($ret->atama22000, "Aday") > -1) ? "22000, " : "";
      $atanansistemler .= (strlen($ret->atama45001) > 0 && self::InStr($ret->atama45001, "Aday") > -1) ? "45001, " : "";
      $atanansistemler .= (strlen($ret->atama50001) > 0 && self::InStr($ret->atama50001, "Aday") > -1) ? "50001, " : "";
      $atanansistemler .= (strlen($ret->atama27001) > 0 && self::InStr($ret->atama27001, "Aday") > -1) ? "27001, " : "";
      $atanansistemler .= (strlen($ret->atamaOicsmiic) > 0 && self::InStr($ret->atamaOicsmiic, "Aday") > -1) ? "OIC/SMIIC 1, " : "";
      $atanansistemler .= (strlen($ret->atamaOicsmiic6) > 0 && self::InStr($ret->atamaOicsmiic6, "Aday") > -1) ? "OIC/SMIIC 6, " : "";
      $atanansistemler .= (strlen($ret->atamaOicsmiic9) > 0 && self::InStr($ret->atamaOicsmiic9, "Aday") > -1) ? "OIC/SMIIC 9, " : "";
      $atanansistemler .= (strlen($ret->atamaOicsmiic171) > 0 && self::InStr($ret->atamaOicsmiic171, "Aday") > -1) ? "OIC/SMIIC 17-1, " : "";
      $atanansistemler .= (strlen($ret->atamaOicsmiic24) > 0 && self::InStr($ret->atamaOicsmiic24, "Aday") > -1) ? "OIC/SMIIC 24, " : "";
    }
    $atanansistemler = substr($atanansistemler, 0, -2);

    return $atanansistemler;
  }

  public static function toplamPlanKayitSayisi($denetci)
  {
    if (empty($denetci)) {
      return "0";
    }

    // Grupların kolon adlarını tanımlıyoruz.
    $asamaMap = [
      'asama1tar' => ['bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1'],
      'asama2tar' => ['bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2'],
      'gozetim1tar' => ['gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1'],
      'gozetim2tar' => ['gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2'],
      'ybtar' => ['ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb'],
      'ozeltar' => ['otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot'],
    ];

    // Planlar tablosundan, asamaMap içindeki kolonlardan herhangi birinde
    // '$denetci' değerini LIKE ile arıyoruz.
    $results = \DB::table('planlar')
      ->where(function ($q) use ($asamaMap, $denetci) {
        foreach ($asamaMap as $group => $columns) {
          $q->orWhere(function ($subQ) use ($columns, $denetci) {
            foreach ($columns as $col) {
              $subQ->orWhere($col, 'like', '%' . $denetci . '%');
            }
          });
        }
      })
      ->get();

    if ($results->isEmpty()) {
      return "0";
    }

    $totalCount = 0;

    // Her kayıt için ilgili grupların kontrolünü yapıyoruz.
    foreach ($results as $row) {
      $recordCount = 0;

      // "Asama" grubu: asama1tar ve asama2tar gruplarını birlikte kontrol ediyoruz.
      $asamaFilled = false;
      foreach (['asama1tar', 'asama2tar'] as $group) {
        if (isset($asamaMap[$group])) {
          foreach ($asamaMap[$group] as $col) {
            // Eğer ilgili kolon değeri varsa ve $denetci değeri, case-insensitive olarak
            // o değerin içinde geçiyorsa (LIKE benzeri kontrol)
            if (isset($row->$col) && stripos($row->$col, $denetci) !== false) {
              $asamaFilled = true;
              break 2; // Bu grup için eşleşme bulundu, çıkıyoruz.
            }
          }
        }
      }
      if ($asamaFilled) {
        $recordCount++;
      }

      // Diğer gruplar: gozetim1tar, gozetim2tar, ybtar, ozeltar
      foreach (['gozetim1tar', 'gozetim2tar', 'ybtar', 'ozeltar'] as $group) {
        $found = false;
        if (isset($asamaMap[$group])) {
          foreach ($asamaMap[$group] as $col) {
            if (isset($row->$col) && stripos($row->$col, $denetci) !== false) {
              $found = true;
              break; // Bu grup için eşleşme bulundu.
            }
          }
        }
        if ($found) {
          $recordCount++;
        }
      }

      $totalCount += $recordCount;
    }

    // Toplam sayıyı, 1000 ve üzeri için "1.23k" formatına dönüştürelim.
    if ($totalCount >= 1000) {
      $formatted = number_format($totalCount / 1000, 2) . 'k';
    } else {
      $formatted = (string)$totalCount;
    }

    return $formatted;
  }

  public function denetciDosyaIcerigi()
  {
    return view('content.denetci-atama.denetci-dosya-icerigi');
  }

  public function denetciAtama()
  {
    return view('content.denetci-atama.denetci-atama');
  }

  public function denetciUserKaydet(Request $request)
  {
    if ($request->isMethod('post')) {
      // Form alanlarına göre validasyon kuralları:
      $validated = $request->validate([
        'name' => 'required|string|max:255',
        // Checkbox alanları; işaretli ise "1" gönderilecek.
        'basdenetci' => 'nullable|in:1',
        'denetci' => 'nullable|in:1',
        'adaydenetci' => 'nullable|in:1',
        'teknikuzman' => 'nullable|in:1',
        'iku' => 'nullable|in:1',
        'teknikgozdengeciren' => 'nullable|in:1',
        'kararverici' => 'nullable|in:1',
        'belgelendirmemuduru' => 'nullable|in:1',
        'belgelendirmesorumlusu' => 'nullable|in:1',
        'planlamasorumlusu' => 'nullable|in:1',
      ]);

      // Checkbox alanları gönderilmediyse, 0 olarak atayalım:
      $checkboxes = [
        'basdenetci',
        'denetci',
        'adaydenetci',
        'teknikuzman',
        'iku',
        'teknikgozdengeciren',
        'kararverici',
        'belgelendirmemuduru',
        'belgelendirmesorumlusu',
        'planlamasorumlusu',
      ];

      foreach ($checkboxes as $field) {
        if (!isset($validated[$field])) {
          $validated[$field] = 0;
        }
      }

      // Eğer formda gizli "uid" (yani mevcut auditor kaydı id'si) gönderilmişse, güncelleme yap.
      if ($request->has('uid') && !empty($request->input('uid'))) {
        $auditor = Auditor::find($request->input('uid'));
        if ($auditor) {
          $auditor->update($validated);
          $message = 'Denetçi bilgileri başarıyla güncellendi.';
        } else {
          // Eğer id gönderilmiş fakat kayıt bulunamadıysa, yeni kayıt oluşturalım.
          $auditor = Auditor::create($validated);
          $message = 'Kayıt bulunamadı, yeni denetçi bilgileri oluşturuldu.';
        }
      } else {

        // Yeni auditor kaydı oluşturulduysa, User kaydı da oluşturulmalı.
        // Geçici email ve şifre oluşturuyoruz.
        $tempEmail = 'auditor_' . uniqid() . '@temporary.com';
        $tempPassword = Str::random(8); // 8 karakterlik rastgele şifre

        $user = User::create([
          'name' => $request->input('name'),
          'email' => $tempEmail,
          'password' => bcrypt($tempPassword),
          'role' => 'auditor',
        ]);

        $denetci = Denetciler::create([
          'uid' => $user->id,
          'denetci' => $request->input('name'),
        ]);

        // Geçici şifre bilgilerini kullanıcıya bildirmek amacıyla flash edebilirsiniz:
        session()->flash('tempCredentials', [
          'email' => $tempEmail,
          'password' => $tempPassword,
        ]);
        $validated["uid"] = $user->id;

        // Yeni kayıt oluşturma işlemi
        $auditor = Auditor::create($validated);
        $message = 'Denetçi bilgileri başarıyla kaydedildi.';

        // Eğer Auditor modelinizde user_id alanı varsa, onu da güncelleyin:
//        $auditor->uid = $user->id;
//        $auditor->save();
      }

      // Kayıt/güncelleme işlemi sonrası, auditor kaydını flash data ile gönderiyoruz.
      return view('content.denetci-atama.denetci-dosya-icerigi', ['success' => $message, 'auditor' => $auditor]);
    }

    return view('content.denetci-atama.denetci-dosya-icerigi');
  }

  public function denetciAta(Request $request)
  {
    // ---------------------------
    // 1. Temel Validasyon
    // ---------------------------
    // Tüm standartlar için radio butonların gönderilmesi zorunlu kılınıyor.
    $request->validate([
      'iso9001' => 'required',
      'iso14001' => 'required',
      'iso45001' => 'required',
      'iso50001' => 'required',
      'iso22000' => 'required',
      'iso27001' => 'required',
      // OIC/SMIIC varyantları bağımsız:
      'oicsmiic1' => 'required',
      'oicsmiic6' => 'required',
      'oicsmiic9' => 'required',
      'oicsmiic171' => 'required',
      'oicsmiic23' => 'required',
      'oicsmiic24' => 'required',
    ]);

    // ---------------------------
    // 2. Standart ve Alan Tanımları
    // ---------------------------
    // Her standart için formdan gönderilecek alanların uzantılarını tanımlıyoruz.
    $standards = [
      'iso9001' => ['ea', 'nace', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'iso14001' => ['ea', 'nace', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'iso45001' => ['ea', 'nace', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'iso50001' => ['teknikAlan', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'iso27001' => ['teknikAlan', 'teknolojikAlan', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'iso22000' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      // OIC/SMIIC varyantları bağımsız standartlar:
      'oicsmiic1' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'oicsmiic6' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'oicsmiic9' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'oicsmiic171' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'oicsmiic23' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
      'oicsmiic24' => ['kategori', 'altKategori', 'isTecrubesi', 'danismanlikTecrubesi', 'atamaReferansi'],
    ];

    // Denetçi ID'sini formdan veya oturumdan alıyoruz.
    $denetciId = $request->input('uid');

// ---------------------------
// 3. Form Validasyonu (Alan Dolu mu?)
// ---------------------------
    $errors = [];
// Tüm standartlar için (ISO ve OIC/SMIIC) form verilerini satır bazında kontrol ediyoruz.
    foreach ($standards as $standard => $fields) {
      $radioValue = $request->input($standard); // Örneğin: 'oicsmiic6'in radio değeri
      $prefix = $standard . '_';
      $allData = $request->all();
      $rows = [];

      // İlgili standartın form verilerini satır bazında ayıklıyoruz.
      // Önce standard ismini düzeltiyoruz - OIC/SMIIC için Büyük/küçük harf duyarlılığı ve - işareti sorun olabilir
      $standardName = str_replace('-', '', strtolower($standard));

      foreach ($allData as $key => $value) {
        $lowercaseKey = strtolower($key);
        if (strpos($lowercaseKey, strtolower($prefix)) === 0) {
          // Prefixten sonraki kısmı alıp field ve rowIndex'e ayırıyoruz
          $keyParts = substr($key, strlen($prefix));

          // Regex ile alanları ve satır indeksini ayıklıyoruz
          if (preg_match('/^([a-zA-Z]+)(\d+)$/', $keyParts, $matches)) {
            $field = $matches[1];
            $rowIndex = $matches[2];

            // Çoklu seçim yapmak için NACE değerleri dizi olarak geliyorsa, virgülle birleştirelim
            if (is_array($value) && $field === 'nace') {
              $value = implode(',', $value);
            }

            $rows[$rowIndex][$field] = $value;
          }
        }
      }

      // En az bir satırda bir alanın dolu olup olmadığını kontrol ediyoruz.
      $hasFilled = false;
      foreach ($rows as $rowData) {
        foreach ($fields as $field) {
          if (isset($rowData[$field]) && !empty(trim($rowData[$field]))) {
            $hasFilled = true;
            break;
          }
        }

        if ($hasFilled) break;
      }

      // Radio değeri "var" ise, en az bir dolu satır bekleniyor.
      if ($radioValue === 'var' && !$hasFilled) {
        $errors[$standard] = "Seçili '{$standard}' 'var', ancak ilgili formdan veri girmediniz. Lütfen formu doldurunuz.";
      }

      // Radio değeri "yok" ise, hiçbir alan dolu olmamalı.
      if ($radioValue === 'yok' && $hasFilled) {
        $errors[$standard] = "Seçili '{$standard}' 'yok', ancak ilgili formda veri girilmiş. Lütfen formu doldurmayınız.";
      }
    }

    // Hata varsa, form verilerini kaybetmeden geri dönüyoruz.
    if (!empty($errors)) {
      return response()->json(['error' => $errors]);
    }

    // ---------------------------
    // 4. Global Aggregate Veriler İçin Diziler
    // ---------------------------
    $globalEaArr = [];
    $globalNaceArr = [];
    $globalTeknikAlanArr = [];
    $globalTeknolojikAlanArr = [];
    $globalAltKategoriArr = [];
    $globalAltKategoriOicArr = [];

    // İşlenen standartlara ait form verilerinden satır ID'lerini tutan dizi.
    $submittedRowIds = [];

    // ---------------------------
    // 5. Kayıt İşlemleri
    // ---------------------------
    foreach ($standards as $standard => $fields) {
      // Sadece radio değeri "var" olan standartları işliyoruz.
      if ($request->input($standard) !== 'var') {
        continue;
      }

      $prefix = $standard . '_';
      $allData = $request->all();
      $rows = [];

      // İlgili standartın form verilerini satır bazında ayıklıyoruz.
      foreach ($allData as $key => $value) {
        $lowercaseKey = strtolower($key);
        if (strpos($lowercaseKey, strtolower($prefix)) === 0) {
          // Prefixten sonraki kısmı alıp field ve rowIndex'e ayırıyoruz
          $keyParts = substr($key, strlen($prefix));

          // Regex ile alanları ve satır indeksini ayıklıyoruz
          if (preg_match('/^([a-zA-Z]+)(\d+)$/', $keyParts, $matches)) {
            $field = $matches[1];
            $rowIndex = $matches[2];

            // Çoklu seçim yapmak için NACE değerleri işleme
            if ($field === 'nace') {
              // Bootstrap-select multiple değerleri dizi olarak veya virgülle ayrılmış string olarak gelebilir
              if (is_array($value)) {
                $value = implode(',', $value);
              } elseif (is_string($value) && strpos($value, '[') === 0) {
                // JSON olarak gelebilir
                $decoded = json_decode($value, true);
                if (is_array($decoded)) {
                  $value = implode(',', $decoded);
                }
              }
            }

            $rows[$rowIndex][$field] = $value;
          }
        }
      }

      $currentRowIds = [];
      foreach ($rows as $rowData) {
        $isFilled = false;
        foreach ($fields as $field) {
          if (isset($rowData[$field]) && trim($rowData[$field]) !== '') {
            $isFilled = true;
            break;
          }
        }
        if (!$isFilled) {
          continue;
        }
        $data = [
          'denetci_id' => $denetciId,
          'standard' => $standard,
        ];
        foreach ($fields as $field) {
          $data[$field] = $rowData[$field] ?? null;
        }
        if (isset($rowData['id']) && !empty($rowData['id'])) {
          $record = \App\Models\DenetciAtama::find($rowData['id']);
          if ($record) {
            $record->update($data);
            $currentRowIds[] = $record->id;
          } else {
            $newRecord = \App\Models\DenetciAtama::create($data);
            $currentRowIds[] = $newRecord->id;
          }
        } else {
          $newRecord = \App\Models\DenetciAtama::create($data);
          $currentRowIds[] = $newRecord->id;
        }
        // Global aggregate dizilerine ekleme:
        if (in_array('ea', $fields)) {
          $eaVal = trim($rowData['ea'] ?? '');
          if (!empty($eaVal)) {
            $globalEaArr[] = $eaVal;
          }
        }
        if (in_array('nace', $fields)) {
          $naceVal = trim($rowData['nace'] ?? '');
          if (!empty($naceVal)) {
            // NACE değeri virgülle ayrılmış bir string olabilir, her bir değeri ayrı ayrı ekleyelim
            $naceValues = array_map('trim', explode(',', $naceVal));
            foreach ($naceValues as $singleNace) {
              if (!empty($singleNace)) {
                $globalNaceArr[] = $singleNace;
              }
            }
          }
        }
        if (in_array('teknikAlan', $fields)) {
          $teknikAlanVal = trim($rowData['teknikAlan'] ?? '');
          if (!empty($teknikAlanVal)) {
            $globalTeknikAlanArr[] = $teknikAlanVal;
          }
        }
        if (in_array('teknolojikAlan', $fields)) {
          $teknolojikAlanVal = trim($rowData['teknolojikAlan'] ?? '');
          if (!empty($teknolojikAlanVal)) {
            $globalTeknolojikAlanArr[] = $teknolojikAlanVal;
          }
        }
        if ($standard === 'iso22000') {
          if (in_array('altKategori', $fields)) {
            $altKategoriVal = trim($rowData['altKategori'] ?? '');
            if (!empty($altKategoriVal)) {
              $globalAltKategoriArr[] = $altKategoriVal;
            }
          }
        } else {
          if (in_array('altKategori', $fields)) {
            $altKategoriOicVal = trim($rowData['altKategori'] ?? '');
            if (!empty($altKategoriOicVal)) {
              $globalAltKategoriOicArr[] = $altKategoriOicVal;
            }
          }
        }
      }
      $submittedRowIds[$standard] = $currentRowIds;
      $existingRecords = \App\Models\DenetciAtama::where('denetci_id', $denetciId)
        ->where('standard', $standard)
        ->pluck('id')
        ->toArray();
      $toDelete = array_diff($existingRecords, $currentRowIds);
      if (!empty($toDelete)) {
        \App\Models\DenetciAtama::destroy($toDelete);
      }
    }

    // ---------------------------
    // 6. Aggregate Verilerin Hazırlanması (Unique)
    // ---------------------------
    $ea = !empty($globalEaArr) ? implode(', ', array_unique($globalEaArr)) : null;
    $nace = !empty($globalNaceArr) ? implode(', ', array_unique($globalNaceArr)) : null;
    $teknikAlan = !empty($globalTeknikAlanArr) ? implode(', ', array_unique($globalTeknikAlanArr)) : null;
    $teknolojikAlan = !empty($globalTeknolojikAlanArr) ? implode(', ', array_unique($globalTeknolojikAlanArr)) : null;
    $altKategori = !empty($globalAltKategoriArr) ? implode(', ', array_unique($globalAltKategoriArr)) : null;
    $altKategoriOic = !empty($globalAltKategoriOicArr) ? implode(', ', array_unique($globalAltKategoriOicArr)) : null;

    // ---------------------------
    // 7. Auditor Tablosunun Güncellenmesi
    // ---------------------------
    $auditorInfo = \App\Models\Auditor::where('uid', $denetciId)->first();
    if ($auditorInfo) {
      $atamaGenel = ($auditorInfo->basdenetci == 1) ? "Başdenetçi" :
        (($auditorInfo->denetci == 1) ? "Denetçi" :
          (($auditorInfo->adaydenetci == 1) ? "Aday Denetçi" :
            (($auditorInfo->teknikuzman == 1) ? "Teknik Uzman" : null)));

      $atama9001 = $atamaGenel;
      $atama14001 = $atamaGenel;
      $atama45001 = $atamaGenel;
      $atama22000 = $atamaGenel;
      $atama50001 = $atamaGenel;
      $atama27001 = $atamaGenel;
      $atamaOicsmiic1 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $atamaOicsmiic6 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $atamaOicsmiic9 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $atamaOicsmiic171 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $atamaOicsmiic23 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $atamaOicsmiic24 = ($auditorInfo->iku == 1) ? "Teknik Uzman" : null;
      $iku = ($auditorInfo->iku == 1) ? "Başdenetçi" : null;

      $updateData = [
        'ea' => $ea,
        'nace' => $nace,
        'teknikalan' => $teknikAlan,
        'kategoribg' => $teknolojikAlan,
        'kategori' => $altKategori,
        'kategorioic' => $altKategoriOic,
        'atama9001' => $atama9001,
        'atama14001' => $atama14001,
        'atama45001' => $atama45001,
        'atama22000' => $atama22000,
        'atama50001' => $atama50001,
        'atama27001' => $atama27001,
        'atamaOicsmiic' => $atamaOicsmiic1,
        'atamaOicsmiic6' => $atamaOicsmiic6,
        'atamaOicsmiic9' => $atamaOicsmiic9,
        'atamaOicsmiic171' => $atamaOicsmiic171,
        'atamaOicsmiic23' => $atamaOicsmiic23,
        'atamaOicsmiic24' => $atamaOicsmiic24,
        'iku' => $iku,
      ];
      \App\Models\Denetciler::where('uid', $denetciId)->update($updateData);
    }

    // Sonuç olarak form başarıyla kaydedildi.
    return response()->json(['success' => 'Form başarıyla kaydedildi.']);
  }

  public function denetciAtamaFormuAc(Request $request)
  {
    $uid = $request->uid;

    $where = ['denetci_id' => $uid];
    $auditor = DenetciAtama::where($where)->get();

    $where = ['id' => $uid];
    $user = User::where($where)->first();

    return view('content.denetci-atama.denetci-atama', ['uid' => $uid, 'auditor' => $auditor, 'user' => $user]);

  }

// Fonksiyon: Verilen değerden istenmeyen karakterleri temizler ve
// eğer sonuç yalnızca virgül (ve boşluk) içeriyorsa boş string döndürür.
  public function cleanValue($value, $removeChars)
  {
    // Önce genel boşlukları temizle
    $value = trim($value);
    // İstenmeyen karakterleri kaldır
    $cleaned = str_replace($removeChars, '', $value);
    // Başında ve sonunda fazla virgül ve boşlukları temizle
    $cleaned = trim($cleaned, " ,");
    // Virgül ve boşluklar kaldırıldığında değer boşsa boş string döndür
    if (trim(str_replace([',', ' '], '', $cleaned)) === '') {
      return '';
    }
    return $cleaned;
  }

  public function auditLog(Request $request)
  {
    // DataTables'dan gelen "draw" değeri (opsiyonel)
    $draw = $request->input('draw', 1);
    $uid = $request->uid;
    if (empty($uid)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Parametre 'uid' eksik."
      ], 400);
    }

    // Denetçi bilgisi
    $auditor = \App\Models\Denetciler::where('uid', $uid)->first();
    $denet = $auditor ? $auditor->denetci : null;
    if (empty($denet)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Denetçi bilgisi bulunamadı."
      ], 404);
    }

    // Geçerli yılı belirleyelim (bu kısım sizin istediğiniz şartlara göre ayarlanabilir)
    $year = date('Y');

    // Planlar tablosundan, denetçi adını içeren kayıtları çekiyoruz.
    $columns = [
      'bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1',
      'bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2',
      'gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1',
      'gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2',
      'ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb',
      'otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot',
    ];

    $results = \DB::table('planlar')
      ->where(function ($q) use ($columns, $denet) {
        foreach ($columns as $col) {
          $q->orWhere($col, 'like', '%' . $denet . '%');
        }
      })
      ->orderBy('planno', 'asc')
      ->get();

    if ($results->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Plan kaydı bulunamadı.'
      ], 404);
    }

    // Plan tabloları ve süre sütunları eşleştirmesi
    $tables = [
      'plan_9001',
      'plan_14001',
      'plan_45001',
      'plan_22000',
      'plan_50001',
      'plan_27001',
      'plan_smiic1'
    ];
    $columnsMapping = [
      'plan_9001' => ['iso9001a1sure', 'iso9001a2sure', 'iso9001gsure', 'iso9001ybsure'],
      'plan_14001' => ['iso14001a1sure', 'iso14001a2sure', 'iso14001gsure', 'iso14001ybsure'],
      'plan_45001' => ['iso45001a1sure', 'iso45001a2sure', 'iso45001gsure', 'iso45001ybsure'],
      'plan_22000' => ['iso22000a1sure', 'iso22000a2sure', 'iso22000gsure', 'iso22000ybsure'],
      'plan_50001' => ['iso50001a1sure', 'iso50001a2sure', 'iso50001gsure', 'iso50001ybsure'],
      'plan_27001' => ['iso27001a1sure', 'iso27001a2sure', 'iso27001gsure', 'iso27001ybsure'],
      'plan_smiic1' => ['oicsmiica1sure', 'oicsmiica2sure', 'oicsmiicgsure', 'oicsmiicybsure'],
    ];

    $data = [];
    $rowCounter = 0; // Global sayaç: 0'dan başlar

    // Yuvarlamaya yardımcı fonksiyon
    $roundToHalf = function($value) {
      return round($value * 2) / 2;
    };

    // Her plan kaydını işle
    foreach ($results as $row) {
      $row = (array)$row;

      // Firma bilgileri
      $basvuru = \App\Models\Basvuru::where('planno', $row['planno'])->first();
      $kurulus = $basvuru ? $basvuru->firmaadi : "N/A";
      $adres = $basvuru ? $basvuru->firmaadresi : "N/A";

      // Denetim Standardı: KYS ve OIC sistemlerini global helper fonksiyonlarıyla alıyoruz
      $kyssistemler = \App\Helpers\Helpers::getSistemler($basvuru);
      $oicsistemler = \App\Helpers\Helpers::getOicSistemler($basvuru);
      if (!empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler . ", " . $oicsistemler;
      } elseif (empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $oicsistemler;
      } elseif (!empty($kyssistemler) && empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler;
      } else {
        $denetimStandardi = "";
      }

      // Teknik alan bilgisi: metodunuz teknik alanları virgülle ayrılmış string olarak döndürsün
      $teknikAlanStr = $this->teknikAlan($row);
      // Virgülle ayrılmış teknik alan kodlarını, tekrarlananları kaldırarak diziye alalım
      $teknikAlanArr = array_unique(array_filter(array_map('trim', explode(',', $teknikAlanStr))));
      $totalEa = count($teknikAlanArr);
      $teknikAlanFormatted = implode(', ', $teknikAlanArr);

      // Statü: determineStatus() metodu kullanılarak hesaplanıyor
      $statu = $this->determineStatus($denet, $row, $columns);

      // Denetçi, bu planda hangi aşamalarda yer alıyor kontrol edelim
      $stagesFound = [];

      // İlk aşama 1 kontrol
      $a1Found = false;
      foreach (['bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $a1Found = true;
          break;
        }
      }
      if ($a1Found && !empty($row['asama1'])) {
        $stagesFound[] = [
          'type' => 'İlk',
          'date' => $row['asama1'],
          'stage_key' => 'asama1',
          'duration_index' => 0 // Süre sütunu indeksi
        ];
      }

      // İlk aşama 2 kontrol
      $a2Found = false;
      foreach (['bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $a2Found = true;
          break;
        }
      }
      if ($a2Found && !empty($row['asama2'])) {
        $stagesFound[] = [
          'type' => 'İlk',
          'date' => $row['asama2'],
          'stage_key' => 'asama2',
          'duration_index' => 1 // Süre sütunu indeksi
        ];
      }

      // Gözetim 1 kontrol
      $g1Found = false;
      foreach (['gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $g1Found = true;
          break;
        }
      }
      if ($g1Found && !empty($row['gozetim1'])) {
        $stagesFound[] = [
          'type' => 'Gözetim',
          'date' => $row['gozetim1'],
          'stage_key' => 'gozetim1',
          'duration_index' => 2 // Süre sütunu indeksi
        ];
      }

      // Gözetim 2 kontrol
      $g2Found = false;
      foreach (['gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $g2Found = true;
          break;
        }
      }
      if ($g2Found && !empty($row['gozetim2'])) {
        $stagesFound[] = [
          'type' => 'Gözetim',
          'date' => $row['gozetim2'],
          'stage_key' => 'gozetim2',
          'duration_index' => 2 // Süre sütunu indeksi
        ];
      }

      // YB kontrol
      $ybFound = false;
      foreach (['ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $ybFound = true;
          break;
        }
      }
      if ($ybFound && !empty($row['ybtar'])) {
        $stagesFound[] = [
          'type' => 'YB',
          'date' => $row['ybtar'],
          'stage_key' => 'ybtar',
          'duration_index' => 3 // Süre sütunu indeksi
        ];
      }

      // Özel kontrol
      $otFound = false;
      foreach (['otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot'] as $col) {
        if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
          $otFound = true;
          break;
        }
      }
      if ($otFound && !empty($row['ozeltar'])) {
        $stagesFound[] = [
          'type' => 'Özel',
          'date' => $row['ozeltar'],
          'stage_key' => 'ozeltar',
          'duration_index' => 2 // Süre sütunu indeksi (özel için gözetim süresi + 1)
        ];
      }

      // Her aşama için ayrı satır oluşturalım
      foreach ($stagesFound as $stage) {
        $duration = 0;

        // Özel denetim tipindeyse ek 1 gün eklenecek
        $extraDay = ($stage['type'] === 'Özel') ? 1 : 0;

        // Tüm tablolardaki süreleri toplayalım
        foreach ($tables as $table) {
          if (isset($columnsMapping[$table])) {
            $cols = $columnsMapping[$table];
            $durationCol = $cols[$stage['duration_index']];

            $monitor = \DB::table($table)
              ->where('planno', $row['planno'])
              ->selectRaw("COALESCE(SUM({$durationCol}),0) as stage_duration")
              ->first();

            $duration += $monitor ? floatval($monitor->stage_duration) : 0;
          }
        }

        // Özel denetim için ekleme yap
        $duration += $extraDay;

        // Süre 0 ise bu aşamayı atla
        if(intval($duration) === 0) continue;

        // Süreyi en yakın yarıma yuvarlama
        $duration = $roundToHalf($duration);

        $rowCounter++; // satır sayacını artır
        $data[] = [
          'id' => $uid,
          'fake_id' => $rowCounter,
          'kurulus' => $kurulus,
          'adres' => $adres,
          'denetim_standardi' => $denetimStandardi, // Tüm standartları tek bir alanda birleştir
          'teknik_alan' => $teknikAlanFormatted,
          'denetim_tarihi' => $stage['date'],
          'statu' => $statu,
          'denetim_tipi' => $stage['type'],
          'denetim_gun' => number_format($duration, 2),
          'total_ea' => $totalEa,
        ];
      }
    }

    $recordsTotal = count($data);
    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $data
    ], 200);
  }

  public function auditLog1(Request $request)
  {
    // DataTables'dan gelen "draw" değeri (opsiyonel)
    $draw = $request->input('draw', 1);
    $uid = $request->uid;
    if (empty($uid)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Parametre 'uid' eksik."
      ], 400);
    }

    // Denetçi bilgisi
    $auditor = \App\Models\Denetciler::where('uid', $uid)->first();
    $denet = $auditor ? $auditor->denetci : null;
    if (empty($denet)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Denetçi bilgisi bulunamadı."
      ], 404);
    }

    // Geçerli yılı belirleyelim (bu kısım sizin istediğiniz şartlara göre ayarlanabilir)
    $year = date('Y');

    // Planlar tablosundan, denetçi adını içeren kayıtları çekiyoruz.
    $columns = [
      'bd1', 'd1', 'tu1', 'g1', 'iku1', 'ad1', 'sid1',
      'bd2', 'd2', 'tu2', 'g2', 'iku2', 'ad2', 'sid2',
      'gbd1', 'gd1', 'gtu1', 'gg1', 'ikug1', 'adg1', 'sidg1',
      'gbd2', 'gd2', 'gtu2', 'gg2', 'ikug2', 'adg2', 'sidg2',
      'ybbd', 'ybd', 'ybtu', 'ybg', 'ikuyb', 'adyb', 'sidyb',
      'otbd', 'otd', 'ottu', 'otg', 'ikuot', 'adot', 'sidot',
    ];

    $results = \DB::table('planlar')
      ->where(function ($q) use ($columns, $denet) {
        foreach ($columns as $col) {
          $q->orWhere($col, 'like', '%' . $denet . '%');
        }
      })
      ->orderBy('planno', 'asc')
      ->get();

    if ($results->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Plan kaydı bulunamadı.'
      ], 404);
    }

    // Plan tabloları ve süre sütunları eşleştirmesi
    $tables = [
      'plan_9001',
      'plan_14001',
      'plan_45001',
      'plan_22000',
      'plan_50001',
      'plan_27001',
      'plan_smiic1'
    ];
    $columnsMapping = [
      'plan_9001' => ['iso9001a1sure', 'iso9001a2sure', 'iso9001gsure', 'iso9001ybsure'],
      'plan_14001' => ['iso14001a1sure', 'iso14001a2sure', 'iso14001gsure', 'iso14001ybsure'],
      'plan_45001' => ['iso45001a1sure', 'iso45001a2sure', 'iso45001gsure', 'iso45001ybsure'],
      'plan_22000' => ['iso22000a1sure', 'iso22000a2sure', 'iso22000gsure', 'iso22000ybsure'],
      'plan_50001' => ['iso50001a1sure', 'iso50001a2sure', 'iso50001gsure', 'iso50001ybsure'],
      'plan_27001' => ['iso27001a1sure', 'iso27001a2sure', 'iso27001gsure', 'iso27001ybsure'],
      'plan_smiic1' => ['oicsmiica1sure', 'oicsmiica2sure', 'oicsmiicgsure', 'oicsmiicybsure'],
    ];

    $data = [];
    $rowCounter = 0; // Global sayaç: 0'dan başlar

    // Process each plan record
    foreach ($results as $row) {
      $row = (array)$row;

      // Firma bilgileri
      $basvuru = \App\Models\Basvuru::where('planno', $row['planno'])->first();
      $kurulus = $basvuru ? $basvuru->firmaadi : "N/A";
      $adres = $basvuru ? $basvuru->firmaadresi : "N/A";

      // Denetim Standardı: KYS ve OIC sistemlerini global helper fonksiyonlarıyla alıyoruz
      $kyssistemler = \App\Helpers\Helpers::getSistemler($basvuru);
      $oicsistemler = \App\Helpers\Helpers::getOicSistemler($basvuru);
      if (!empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler . ", " . $oicsistemler;
      } elseif (empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $oicsistemler;
      } elseif (!empty($kyssistemler) && empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler;
      } else {
        $denetimStandardi = "";
      }

      // Standartları virgülle ayırıp temizleyelim; eğer boşsa tüm standartı kullan
      $stdArray = array_filter(array_map('trim', explode(',', $denetimStandardi)));
      if (empty($stdArray)) {
        $stdArray = [$denetimStandardi];
      }

      // Teknik alan bilgisi: metodunuz teknik alanları virgülle ayrılmış string olarak döndürsün
      $teknikAlanStr = $this->teknikAlan($row);
      // Virgülle ayrılmış teknik alan kodlarını, tekrarlananları kaldırarak diziye alalım
      $teknikAlanArr = array_unique(array_filter(array_map('trim', explode(',', $teknikAlanStr))));
      $totalEa = count($teknikAlanArr);

      // Denetim Tarihi: Belirli öncelik sırasına göre
      if (!empty($row["asama1"])) {
        $denetimTarihi = $row["asama1"];
      } elseif (!empty($row["asama2"])) {
        $denetimTarihi = $row["asama2"];
      } elseif (!empty($row["gozetim1"])) {
        $denetimTarihi = $row["gozetim1"];
      } elseif (!empty($row["gozetim2"])) {
        $denetimTarihi = $row["gozetim2"];
      } elseif (!empty($row["ybtar"])) {
        $denetimTarihi = $row["ybtar"];
      } elseif (!empty($row["ozeltar"])) {
        $denetimTarihi = $row["ozeltar"];
      } else {
        $denetimTarihi = "";
      }

      // Statü: determineStatus() metodu kullanılarak hesaplanıyor
      $statu = $this->determineStatus($denet, $row, $columns);

      // Denetim Tipi: Basit örnek mantık
      if (!empty($row["asama1"])) {
        $denetimTipi = "İlk";
      } elseif (!empty($row["asama2"])) {
        $denetimTipi = "İlk";
      } elseif (!empty($row["gozetim1"]) || !empty($row["gozetim2"])) {
        $denetimTipi = "Gözetim";
      } elseif (!empty($row["ybtar"])) {
        $denetimTipi = "YB";
      } elseif (!empty($row["ozeltar"])) {
        $denetimTipi = "Özel";
      } else {
        $denetimTipi = "";
      }

      // Denetim Gün Süresi: Her plan kaydı için, ilgili plan tablolarından (süre sütunları) yalnızca o aşamanın süresini ayrı satır olarak alacağız.
      // İlk aşama için: asama1 ve asama2 tek satırda, diğer aşamalar ayrı.
      if (!empty($row['asama1']) && !empty($row['asama2'])) {
        // İlk aşama: Kombine A1 ve A2 süreleri
        foreach ($stdArray as $stdItem) {
          $stdLower = strtolower($stdItem);
          // Belirle: ilgili plan tablosunu seçelim
          if (stripos($stdLower, '9001') !== false) {
            $table = 'plan_9001';
          } elseif (stripos($stdLower, '14001') !== false) {
            $table = 'plan_14001';
          } elseif (stripos($stdLower, '45001') !== false) {
            $table = 'plan_45001';
          } elseif (stripos($stdLower, '22000') !== false) {
            $table = 'plan_22000';
          } elseif (stripos($stdLower, '50001') !== false) {
            $table = 'plan_50001';
          } elseif (stripos($stdLower, '27001') !== false) {
            $table = 'plan_27001';
          } elseif (stripos($stdLower, 'oic') !== false || stripos($stdLower, 'smiic') !== false) {
            $table = 'plan_smiic1';
          } else {
            continue;
          }

          $durationA1 = 0;
          $durationA2 = 0;
          if (isset($columnsMapping[$table])) {
            $cols = $columnsMapping[$table];
            // asama1 süresi (index 0)
            $monitorA1 = \DB::table($table)
              ->where('planno', $row['planno'])
              ->selectRaw("COALESCE(SUM({$cols[0]}),0) as a1")
              ->first();
            $durationA1 = $monitorA1 ? floatval($monitorA1->a1) : 0;
            // asama2 süresi (index 1)
            $monitorA2 = \DB::table($table)
              ->where('planno', $row['planno'])
              ->selectRaw("COALESCE(SUM({$cols[1]}),0) as a2")
              ->first();
            $durationA2 = $monitorA2 ? floatval($monitorA2->a2) : 0;
          }

          $combinedDuration = "A1: " . number_format($durationA1, 2) . ", A2: " . number_format($durationA2, 2);

          $rowCounter++; // $rowCounter'ı burada artırıyoruz
          $data[] = [
            'id' => $uid,
            'fake_id' => $rowCounter,
            'kurulus' => $kurulus,
            'adres' => $adres,
            'denetim_standardi' => $stdItem,
            'teknik_alan' => implode(', ', $teknikAlanArr),
            'denetim_tarihi' => $row['asama1'], // İlk aşama tarihi
            'statu' => $statu,
            'denetim_tipi' => 'İlk',
            'denetim_gun' => $combinedDuration,
            'total_ea' => $totalEa,
          ];
        }
      }

      // Diğer aşamalar için ayrı satırlar: gozetim1, gozetim2, ybtar, ozeltar
      $otherStages = ['gozetim1', 'gozetim2', 'ybtar', 'ozeltar'];
      foreach ($otherStages as $stageKey) {
        if (!empty($row[$stageKey])) {
          foreach ($stdArray as $stdItem) {
            $stdLower = strtolower($stdItem);
            if (stripos($stdLower, '9001') !== false) {
              $table = 'plan_9001';
            } elseif (stripos($stdLower, '14001') !== false) {
              $table = 'plan_14001';
            } elseif (stripos($stdLower, '45001') !== false) {
              $table = 'plan_45001';
            } elseif (stripos($stdLower, '22000') !== false) {
              $table = 'plan_22000';
            } elseif (stripos($stdLower, '50001') !== false) {
              $table = 'plan_50001';
            } elseif (stripos($stdLower, '27001') !== false) {
              $table = 'plan_27001';
            } elseif (stripos($stdLower, 'oic') !== false || stripos($stdLower, 'smiic') !== false) {
              $table = 'plan_smiic1';
            } else {
              continue;
            }

            $duration = 0;
            if (isset($columnsMapping[$table])) {
              $cols = $columnsMapping[$table];
              // Belirli aşama için sütun indeksi: gozetim için index 2, ybtar için index 3, ozeltar için index 2 (+1)
              if (in_array($stageKey, ['gozetim1', 'gozetim2'])) {
                $stageIndex = 2;
              } elseif ($stageKey == 'ybtar') {
                $stageIndex = 3;
              } elseif ($stageKey == 'ozeltar') {
                $stageIndex = 2;
              } else {
                $stageIndex = 0;
              }
              $monitor = \DB::table($table)
                ->where('planno', $row['planno'])
                ->selectRaw("COALESCE(SUM({$cols[$stageIndex]}),0) as stageValue")
                ->first();
              if ($monitor) {
                $duration = floatval($monitor->stageValue);
                if ($stageKey == 'ozeltar') {
                  $duration += 1;
                }
              }
            }
            // Set stage tip
            if (in_array($stageKey, ['gozetim1', 'gozetim2'])) {
              $currentTip = 'Gözetim';
            } elseif ($stageKey == 'ybtar') {
              $currentTip = 'YB';
            } elseif ($stageKey == 'ozeltar') {
              $currentTip = 'Özel';
            } else {
              $currentTip = '';
            }

            $rowCounter++; // $rowCounter'ı burada artırıyoruz
            $data[] = [
              'id' => $uid,
              'fake_id' => $rowCounter,
              'kurulus' => $kurulus,
              'adres' => $adres,
              'denetim_standardi' => $stdItem,
              'teknik_alan' => implode(', ', $teknikAlanArr),
              'denetim_tarihi' => $row[$stageKey],
              'statu' => $statu,
              'denetim_tipi' => $currentTip,
              'denetim_gun' => number_format($duration, 2),
              'total_ea' => $totalEa,
            ];
          }
        }
      }
    }

    $recordsTotal = count($data);
    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $data
    ], 200);
  }

  public function periyodicSiteMonitoring(Request $request)
  {
    // DataTables'dan gelen "draw" değeri
    $draw = $request->input('draw', 1);
    $uid = $request->uid;
    if (empty($uid)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Parametre 'uid' eksik."
      ], 400);
    }

    // Geçerli yıl
    $year = date('Y');

    // Denetçi bilgisi
    $auditor = \App\Models\Denetciler::where('uid', $uid)->first();
    $denet = $auditor ? $auditor->denetci : null;

    if (empty($denet)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Denetçi bilgisi bulunamadı."
      ], 404);
    }

    // Sorguda kullanılacak aşama tanımlamaları (hem metin arama hem tarih alanı için)
    $columns = [
      'asama1' => ['cols' => ['bd1', 'd1', 'tu1', 'g1', 'ad1', 'sid1'], 'date' => 'asama1'],
      'asama2' => ['cols' => ['bd2', 'd2', 'tu2', 'g2', 'ad2', 'sid2'], 'date' => 'asama2'],
      'gozetim1' => ['cols' => ['gbd1', 'gd1', 'gtu1', 'gg1', 'adg1', 'sidg1'], 'date' => 'gozetim1'],
      'gozetim2' => ['cols' => ['gbd2', 'gd2', 'gtu2', 'gg2', 'adg2', 'sidg2'], 'date' => 'gozetim2'],
      'ybtar' => ['cols' => ['ybbd', 'ybd', 'ybtu', 'ybg', 'adyb', 'sidyb'], 'date' => 'ybtar'],
      'ozeltar' => ['cols' => ['otbd', 'otd', 'ottu', 'otg', 'adot', 'sidot'], 'date' => 'ozeltar'],
    ];

    // Planlar tablosundan; burada herhangi bir aşamada denetçi adı geçiyorsa ilgili planları alıyoruz.
    $query = \DB::table('planlar')
      ->where(function ($q) use ($columns, $denet, $year) {
        foreach ($columns as $info) {
          $q->orWhere(function ($subQuery) use ($info, $denet, $year) {
            $subQuery->where(function ($innerQuery) use ($info, $denet) {
              foreach ($info['cols'] as $col) {
                $innerQuery->orWhere($col, 'like', '%' . $denet . '%');
              }
            })
              ->where($info['date'], 'like', '%' . $year . '%');
          });
        }
      })
      ->orderBy('planno', 'asc');

    $results = $query->get();

    if ($results->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Plan kaydı bulunamadı.'
      ], 404);
    }

    // Denetçi atamaları kontrolü
    $assignments = \DB::table('denetci_atamalar')
      ->select('standard', 'ea', 'teknikAlan', 'teknolojikAlan', 'altKategori')
      ->where('denetci_id', $uid)
      ->get();

    if ($assignments->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Atama kaydı bulunamadı.'
      ], 404);
    }

    // Kritik kodlar için sütun adı eşlemeleri
    $kritikMap = [
      'iso9001' => 'iso9001',
      'iso14001' => 'iso14001',
      'iso45001' => 'iso45001',
    ];

    $resultsArray = [];
    $globalCounter = 0;

    foreach ($results as $rowObj) {
      $row = (array)$rowObj;

      // Başvuru bilgisi alınıyor
      $basvuru = \App\Models\Basvuru::where('planno', $row['planno'])->first();

      // Denetim standardı: KYS ve OIC sistemleri birleştiriliyor
      $kyssistemler = \App\Helpers\Helpers::getSistemler($basvuru);
      $oicsistemler = \App\Helpers\Helpers::getOicSistemler($basvuru);
      if (!empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler . ", " . $oicsistemler;
      } elseif (empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $oicsistemler;
      } elseif (!empty($kyssistemler) && empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler;
      } else {
        $denetimStandardi = "";
      }

      // Standart bilgileri (virgül ile ayrılmış)
      $stdArray = array_filter(array_map('trim', explode(',', $denetimStandardi)));

      // Teknik alan bilgisi (fonksiyon içinde detaylandırılıyor)
      $teknikAlanStr = $this->teknikAlanIzleme($row);
      // Yinelenen değerler kaldırılmadan işleme sokuluyor
      $teknikAlanArr = array_filter(array_map('trim', explode(',', $teknikAlanStr)));
      $totalEa = count($teknikAlanArr);

      // Plan düzeyinde; tüm standart ve teknik kodlardan elde edilen sonuçlar
      $agg = [
        '9001_kritik' => [],
        '9001_olmayan' => [],
        '14001_kritik' => [],
        '14001_olmayan' => [],
        '45001_kritik' => [],
        '45001_olmayan' => [],
        '22000_yuksek' => [],
        '22000_orta' => [],
        'iso50001' => [],
        'iso27001' => [],
        'oicsmiic' => [],
        'kritik' => [],
        'non_kritik' => [],
      ];

      if (!empty($stdArray)) {
        foreach ($stdArray as $stdItem) {
          $stdLower = strtolower($stdItem);
          if (stripos($stdLower, '9001') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso9001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso9001'], $code)
                ->first();
              if ($kritikRecord) {
                $agg['9001_kritik'][] = $code;
                $agg['kritik'][] = $code;
              } else {
                $agg['9001_olmayan'][] = $code;
                $agg['non_kritik'][] = $code;
              }
            }
          } elseif (stripos($stdLower, '14001') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso14001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso14001'], $code)
                ->first();
              if ($kritikRecord) {
                $agg['14001_kritik'][] = $code;
                $agg['kritik'][] = $code;
              } else {
                $agg['14001_olmayan'][] = $code;
                $agg['non_kritik'][] = $code;
              }
            }
          } elseif (stripos($stdLower, '45001') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso45001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso45001'], $code)
                ->first();
              if ($kritikRecord) {
                $agg['45001_kritik'][] = $code;
                $agg['kritik'][] = $code;
              } else {
                $agg['45001_olmayan'][] = $code;
                $agg['non_kritik'][] = $code;
              }
            }
          } elseif (stripos($stdLower, '50001') !== false) {
            foreach ($teknikAlanArr as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso50001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $agg['iso50001'][] = $code;
              $agg['kritik'][] = $code;
            }
          } elseif (stripos($stdLower, '27001') !== false) {
            foreach ($teknikAlanArr as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso27001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $agg['iso27001'][] = $code;
              $agg['kritik'][] = $code;
            }
          } elseif (stripos($stdLower, '22000') !== false) {
            // ISO 22000: yalnızca harf ile başlayan kodlar için
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', '22000')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $record = \DB::table('cls22000')
                ->where('kategori', $code)
                ->first();
              if ($record) {
                if ($record->kk == "Y") {
                  $agg['22000_yuksek'][] = $code;
                  $agg['kritik'][] = $code;
                } else {
                  $agg['22000_orta'][] = $code;
                  $agg['non_kritik'][] = $code;
                }
              } else {
                $agg['non_kritik'][] = $code;
              }
            }
          } elseif (stripos($stdLower, 'oic') !== false || stripos($stdLower, 'smiic') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'oicsmiic')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $agg['oicsmiic'][] = $code;
              $agg['kritik'][] = $code;
            }
          } else {
            foreach ($teknikAlanArr as $code) {
              $agg['non_kritik'][] = $code;
            }
          }
        }
      }

      // Burayı değiştiriyoruz - Her plan için tek bir satır oluşturacağız
      // Denetçinin bulunduğu tüm aşamaları birleştireceğiz
      $stagesFound = [];
      $stagesDates = [];

      // Denetçinin hangi aşamalarda olduğunu kontrol edelim
      foreach ($columns as $stageKey => $info) {
        foreach ($info['cols'] as $col) {
          if (!empty($row[$col]) && stripos($row[$col], $denet) !== false) {
            $stagesFound[] = $stageKey;
            $stagesDates[$stageKey] = $row[$info['date']] ?? null;
            break; // Bu aşama için bir kolon bulduğumuzda diğer kolonları kontrol etmeye gerek yok
          }
        }
      }

      // Eğer denetçi hiçbir aşamada bulunmuyorsa, bu plan için satır oluşturmuyoruz
      if (empty($stagesFound)) {
        continue;
      }

      // Plan için tek bir satır oluştur
      $globalCounter++;
      $resultsArray[] = [
        'sira_no' => $globalCounter,
        'plan_no' => $row['planno'],
        'stage' => implode(', ', $stagesFound), // Tüm aşamaları virgülle birleştirilmiş şekilde göster
        'stage_date' => implode(', ', array_filter($stagesDates)), // Tüm aşama tarihlerini virgülle birleştirilmiş şekilde göster
        'total_ea' => $totalEa,
        '9001_kritik' => count($agg['9001_kritik']) . " (" . implode(', ', $agg['9001_kritik']) . ")",
        '9001_olmayan' => count($agg['9001_olmayan']) . " (" . implode(', ', $agg['9001_olmayan']) . ")",
        '14001_kritik' => count($agg['14001_kritik']) . " (" . implode(', ', $agg['14001_kritik']) . ")",
        '14001_olmayan' => count($agg['14001_olmayan']) . " (" . implode(', ', $agg['14001_olmayan']) . ")",
        '45001_kritik' => count($agg['45001_kritik']) . " (" . implode(', ', $agg['45001_kritik']) . ")",
        '45001_olmayan' => count($agg['45001_olmayan']) . " (" . implode(', ', $agg['45001_olmayan']) . ")",
        '22000_yuksek' => count($agg['22000_yuksek']) . " (" . implode(', ', $agg['22000_yuksek']) . ")",
        '22000_orta' => count($agg['22000_orta']) . " (" . implode(', ', $agg['22000_orta']) . ")",
        'iso50001' => count($agg['iso50001']) . " (" . implode(', ', $agg['iso50001']) . ")",
        'iso27001' => count($agg['iso27001']) . " (" . implode(', ', $agg['iso27001']) . ")",
        'oicsmiic' => count($agg['oicsmiic']) . " (" . implode(', ', $agg['oicsmiic']) . ")",
        'kritik' => count($agg['kritik']) . " (" . implode(', ', $agg['kritik']) . ")",
        'non_kritik' => count($agg['non_kritik']) . " (" . implode(', ', $agg['non_kritik']) . ")",
      ];
    }

    $recordsTotal = count($resultsArray);
    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $resultsArray,
    ], 200);
  }

  public function periyodicSiteMonitoring1(Request $request)
  {
    // DataTables'dan gelen "draw" değeri
    $draw = $request->input('draw', 1);
    $uid = $request->uid;
    if (empty($uid)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Parametre 'uid' eksik."
      ], 400);
    }

    // Geçerli yıl (örneğin sistemin yılını kullanıyoruz)
    $year = date('Y');

    // Denetçi bilgisi
    $auditor = \App\Models\Denetciler::where('uid', $uid)->first();
    $denet = $auditor ? $auditor->denetci : null;

    if (empty($denet)) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => "Denetçi bilgisi bulunamadı."
      ], 404);
    }

    $searchDenet = "%{$denet}%";
    $searchYear = "%{$year}%";

    $whereClauses = [
      "((bd1 LIKE ? OR d1 LIKE ? OR tu1 LIKE ? OR g1 LIKE ? OR ad1 LIKE ? OR sid1 LIKE ?) AND asama1 LIKE ?)",
      "((bd2 LIKE ? OR d2 LIKE ? OR tu2 LIKE ? OR g2 LIKE ? OR ad2 LIKE ? OR sid2 LIKE ?) AND asama2 LIKE ?)",
      "((gbd1 LIKE ? OR gd1 LIKE ? OR gtu1 LIKE ? OR gg1 LIKE ? OR adg1 LIKE ? OR sidg1 LIKE ?) AND gozetim1 LIKE ?)",
      "((gbd2 LIKE ? OR gd2 LIKE ? OR gtu2 LIKE ? OR gg2 LIKE ? OR adg2 LIKE ? OR sidg2 LIKE ?) AND gozetim2 LIKE ?)",
      "((ybbd LIKE ? OR ybd LIKE ? OR ybtu LIKE ? OR ybg LIKE ? OR adyb LIKE ? OR sidyb LIKE ?) AND ybtar LIKE ?)",
      "((otbd LIKE ? OR otd LIKE ? OR ottu LIKE ? OR otg LIKE ? OR adot LIKE ? OR sidot LIKE ?) AND ozeltar LIKE ?)"
    ];

    $bindings = [];
    foreach ($whereClauses as $clause) {
      // Her grup için 4 tane denetçi araması ve 1 tane yıl filtresi eklenir.
      $bindings = array_merge($bindings, array_fill(0, 6, $searchDenet), [$searchYear]);
    }

    $sqlSQL = "SELECT * FROM planlar WHERE " . implode(" OR ", $whereClauses) . " ORDER BY planno ASC";

// Örnek kullanım:
//    $resultss = \DB::select($sqlSQL, $bindings);

//    var_dump($resultss);

    $columns = [
      'asama1' => ['cols' => ['bd1', 'd1', 'tu1', 'g1', 'ad1', 'sid1'], 'date' => 'asama1'],
      'asama2' => ['cols' => ['bd2', 'd2', 'tu2', 'g2', 'ad2', 'sid2'], 'date' => 'asama2'],
      'gozetim1' => ['cols' => ['gbd1', 'gd1', 'gtu1', 'gg1', 'adg1', 'sidg1'], 'date' => 'gozetim1'],
      'gozetim2' => ['cols' => ['gbd2', 'gd2', 'gtu2', 'gg2', 'adg2', 'sidg2'], 'date' => 'gozetim2'],
      'ybtar' => ['cols' => ['ybbd', 'ybd', 'ybtu', 'ybg', 'adyb', 'sidyb'], 'date' => 'ybtar'],
      'ozeltar' => ['cols' => ['otbd', 'otd', 'ottu', 'otg', 'adot', 'sidot'], 'date' => 'ozeltar'],
    ];

    $query = \DB::table('planlar')
      ->where(function ($q) use ($columns, $denet, $year) {
        // Her aşama için ayrı alt sorgu ekliyoruz:
        foreach ($columns as $stage => $info) {
          $q->orWhere(function ($subQuery) use ($info, $denet, $year) {
            // İlgili kolonlarda denetçi adını arıyoruz
            $subQuery->where(function ($innerQuery) use ($info, $denet) {
              foreach ($info['cols'] as $col) {
                $innerQuery->orWhere($col, 'like', '%' . $denet . '%');
              }
            })
              // Ve ilgili tarih sütununda yıl filtresi uyguluyoruz
              ->where($info['date'], 'like', '%' . $year . '%');
          });
        }
      })
      ->orderBy('planno', 'asc');

// Sorguyu çalıştırıp sonuçları alalım
    $results = $query->get();


//foreach($results as $result) {
//  echo $result->eakodu;
//};

    if ($results->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Plan kaydı bulunamadı.'
      ], 404);
    }

    // Denetçi atamalarını, denetci_atamalar tablosundan; ilgili kolonlar: standard, ea, teknikAlan, teknolojikAlan, altKategori
    $assignments = \DB::table('denetci_atamalar')
      ->select('standard', 'ea', 'teknikAlan', 'teknolojikAlan', 'altKategori')
      ->where('denetci_id', $uid)
      ->get();

    if ($assignments->isEmpty()) {
      return response()->json([
        'draw' => $draw,
        'recordsTotal' => 0,
        'recordsFiltered' => 0,
        'data' => [],
        'error' => 'Atama kaydı bulunamadı.'
      ], 404);
    }

    // Map: ISO 9001, 14001, 45001 için kritikkodlar sorgusunda kullanılacak sütun adları.
    $kritikMap = [
      'iso9001' => 'iso9001',
      'iso14001' => 'iso14001',
      'iso45001' => 'iso45001',
    ];

    $resultsArray = [];
    $globalCounter = 0;

    // Döngü: Her plan kaydı için
    foreach ($results as $row) {
      $row = (array)$row;

      // Basvuru bilgileri
      $basvuru = \App\Models\Basvuru::where('planno', $row['planno'])->first();

      // Denetim standardı: KYS ve OIC sistemlerini, global helper fonksiyonları (Helpers::getSistemler, Helpers::getOicSistemler) kullanarak alıyoruz.
      $kyssistemler = \App\Helpers\Helpers::getSistemler($basvuru);
      $oicsistemler = \App\Helpers\Helpers::getOicSistemler($basvuru);
      if (!empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler . ", " . $oicsistemler;
      } elseif (empty($kyssistemler) && !empty($oicsistemler)) {
        $denetimStandardi = $oicsistemler;
      } elseif (!empty($kyssistemler) && empty($oicsistemler)) {
        $denetimStandardi = $kyssistemler;
      } else {
        $denetimStandardi = "";
      }

      // Standartları virgülle ayırıp temizleyelim
      $stdArray = array_filter(array_map('trim', explode(',', $denetimStandardi)));

      // Teknik alan bilgisi: $this->teknikAlanIzleme($row) metodu kullanılacak
      $teknikAlanStr = $this->teknikAlanIzleme($row);
      // DÜZENLEME: array_unique kaldırıldı, böylece yinelenen değerler sayılacak
      $teknikAlanArr = array_filter(array_map('trim', explode(',', $teknikAlanStr)));
      $totalEa = count($teknikAlanArr);

      // Eğer standart belirtilmemişse, tek bir satır oluşturuyoruz.
      if (empty($stdArray)) {
        $globalCounter++;
        $resultsArray[] = [
          'sira_no' => $globalCounter,
          '9001_kritik' => "0",
          '9001_olmayan' => "0",
          '14001_kritik' => "0",
          '14001_olmayan' => "0",
          '45001_kritik' => "0",
          '45001_olmayan' => "0",
          '22000_yuksek' => "0",
          '22000_orta' => "0",
          '50001' => "0",
          '27001' => "0",
          'oicsmiic' => "0",
          'total_ea' => $totalEa,
          'kritik' => "0",
          'non_kritik' => "0",
        ];
      } else {
        // Her standart için ayrı satır üreteceğiz.
        foreach ($stdArray as $stdItem) {
          $globalCounter++;
          $stdLower = strtolower($stdItem);

          // Sayaç ve EA kodlarını toplayacağımız diziler
          $kritik9001Codes = [];
          $kritikOlmayan9001Codes = [];
          $kritik14001Codes = [];
          $kritikOlmayan14001Codes = [];
          $kritik45001Codes = [];
          $kritikOlmayan45001Codes = [];
          $yuksek22000Codes = [];
          $orta22000Codes = [];
          $count50001Codes = [];
          $count27001Codes = [];
          $countoicsmiicCodes = [];
          $kritikCountCodes = [];
          $nonKritikCountCodes = [];

          // İşlem: Standart adının içerdiği anahtar kelimeye göre kontrol yapıyoruz.
          if (stripos($stdLower, '9001') !== false) {
            // Filtre: "Harf.01" ile "Harf.04" şeklindeki ifadeleri kaldırıyoruz
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso9001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso9001'], $code)
                ->first();
              if ($kritikRecord) {
                $kritik9001Codes[] = $code;
                $kritikCountCodes[] = $code;
              } else {
                $kritikOlmayan9001Codes[] = $code;
                $nonKritikCountCodes[] = $code;
              }
            }
          } elseif (stripos($stdLower, '14001') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso14001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso14001'], $code)
                ->first();
              if ($kritikRecord) {
                $kritik14001Codes[] = $code;
                $kritikCountCodes[] = $code;
              } else {
                $kritikOlmayan14001Codes[] = $code;
                $nonKritikCountCodes[] = $code;
              }
            }
          } elseif (stripos($stdLower, '45001') !== false) {
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return !preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso45001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $kritikRecord = \DB::table('kritikkodlar')
                ->where($kritikMap['iso45001'], $code)
                ->first();
              if ($kritikRecord) {
                $kritik45001Codes[] = $code;
                $kritikCountCodes[] = $code;
              } else {
                $kritikOlmayan45001Codes[] = $code;
                $nonKritikCountCodes[] = $code;
              }
            }
          } elseif (stripos($stdLower, '50001') !== false) {
            foreach ($teknikAlanArr as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso50001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $count50001Codes[] = $code;
              $kritikCountCodes[] = $code;
            }
          } elseif (stripos($stdLower, '27001') !== false) {
            foreach ($teknikAlanArr as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'iso27001')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $count27001Codes[] = $code;
              $kritikCountCodes[] = $code;
            }
          } elseif (stripos($stdLower, '22000') !== false) {
            // ISO 22000: sadece harf ile başlayan kodlar
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', '22000')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $record = \DB::table('cls22000')
                ->where('kategori', $code)
                ->first();
              if ($record) {
                if ($record->kk == "Y") {
                  $yuksek22000Codes[] = $code;
                  $kritikCountCodes[] = $code;
                } else {
                  $orta22000Codes[] = $code;
                  $nonKritikCountCodes[] = $code;
                }
              } else {
                $nonKritikCountCodes[] = $code;
              }
            }
          } elseif (stripos($stdLower, 'oic') !== false || stripos($stdLower, 'smiic') !== false) {
            // OIC/SMIIC: sadece harf ile başlayan kodlar
            $filteredCodes = array_filter($teknikAlanArr, function ($code) {
              return preg_match('/^[A-Za-z]/', $code);
            });
            foreach ($filteredCodes as $code) {
              $monitoring = \DB::table('periyodiksahaizleme')
                ->where('standart', 'oicsmiic')
                ->where('teknikalan', $code)
                ->orderBy('izlemetarihi', 'desc')
                ->first();
              if ($monitoring) continue;
              $countoicsmiicCodes[] = $code;
              $kritikCountCodes[] = $code;
            }
          } else {
            foreach ($teknikAlanArr as $code) {
              $nonKritikCountCodes[] = $code;
            }
          }

          // Sonuç satırını oluşturuyoruz
          $resultsArray[] = [
            'sira_no' => $globalCounter,
            '9001_kritik' => count($kritik9001Codes) . " (" . implode(', ', $kritik9001Codes) . ")",
            '9001_olmayan' => count($kritikOlmayan9001Codes) . " (" . implode(', ', $kritikOlmayan9001Codes) . ")",
            '14001_kritik' => count($kritik14001Codes) . " (" . implode(', ', $kritik14001Codes) . ")",
            '14001_olmayan' => count($kritikOlmayan14001Codes) . " (" . implode(', ', $kritikOlmayan14001Codes) . ")",
            '45001_kritik' => count($kritik45001Codes) . " (" . implode(', ', $kritik45001Codes) . ")",
            '45001_olmayan' => count($kritikOlmayan45001Codes) . " (" . implode(', ', $kritikOlmayan45001Codes) . ")",
            '22000_yuksek' => count($yuksek22000Codes) . " (" . implode(', ', $yuksek22000Codes) . ")",
            '22000_orta' => count($orta22000Codes) . " (" . implode(', ', $orta22000Codes) . ")",
            '50001' => count($count50001Codes) . " (" . implode(', ', $count50001Codes) . ")",
            '27001' => count($count27001Codes) . " (" . implode(', ', $count27001Codes) . ")",
            'oicsmiic' => count($countoicsmiicCodes) . " (" . implode(', ', $countoicsmiicCodes) . ")",
            'total_ea' => $totalEa,
            'kritik' => count($kritikCountCodes) . " (" . implode(', ', $kritikCountCodes) . ")",
            'non_kritik' => count($nonKritikCountCodes) . " (" . implode(', ', $nonKritikCountCodes) . ")",
          ];
        }
      }
    }

    $recordsTotal = count($resultsArray);
    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsTotal,
      'data' => $resultsArray,
    ], 200);
  }

  /**
   * Plan kaydındaki belirli sütun gruplarında auditor adını arar ve uygun statü döndürür.
   *
   * Her satır 7 sütundan oluşuyor. Aşağıdaki indeksler kontrol edilecek:
   * - 0, 7, 14, 21, 28, 35: Eşleşme varsa "BD"
   * - 1, 8, 15, 22, 29, 36: Eşleşme varsa "D"
   * - 5, 12, 19, 26, 33, 40: Eşleşme varsa "AD"
   *
   * @param string $denet Auditor adı
   * @param array $row Plan kaydı (anahtarlar, planlar tablosundaki kolon adları; ör. 'bd1', 'd1', vs.)
   * @param array $columns Düz bir dizi olarak planlar tablosundaki kolon adları (örneğin, ['bd1','d1','tu1','g1','iku1','ad1','sid1', ...])
   * @return string  "BD", "D", "AD" veya boş string ("") eğer eşleşme yoksa.
   */
  function determineStatus($denet, array $row, array $columns)
  {
    // Belirlenen indeks grupları:
    $bdIndices = [0, 7, 14, 21, 28, 35];
    $dIndices = [1, 8, 15, 22, 29, 36];
    $adIndices = [5, 12, 19, 26, 33, 40];

    // BD kontrolleri
    foreach ($bdIndices as $idx) {
      if (!isset($columns[$idx])) continue;
      $colName = $columns[$idx];
      if (!empty($row[$colName]) && stripos($row[$colName], $denet) !== false) {
        return 'BD';
      }
    }
    // D kontrolleri
    foreach ($dIndices as $idx) {
      if (!isset($columns[$idx])) continue;
      $colName = $columns[$idx];
      if (!empty($row[$colName]) && stripos($row[$colName], $denet) !== false) {
        return 'D';
      }
    }
    // AD kontrolleri
    foreach ($adIndices as $idx) {
      if (!isset($columns[$idx])) continue;
      $colName = $columns[$idx];
      if (!empty($row[$colName]) && stripos($row[$colName], $denet) !== false) {
        return 'AD';
      }
    }

    return "";
  }

  public function getKritikKod9001($ea, $denetci)
  {
    $sonuc = false;
    // Virgül ile ayrılmış nace değerlerini temizleyerek diziye çeviriyoruz.
    $naceBol = array_map('trim', explode(',', $ea));

    foreach ($naceBol as $nave) {
      // Denetciler tablosunda denetçi ve eanace alanında arama yapıyoruz.
      $denetciler = \DB::table('denetciler')
        ->where('denetci', $denetci)
        ->where('eanace', 'like', '%' . $nave . '%')
        ->get();

      foreach ($denetciler as $ret) {
        // eanace değeri "|" karakteri ile ayrılmış kabul ediliyor.
        $row = explode("|", $ret->eanace);
        // row[1]'de aranacak; eğer trim($nave) bulunuyorsa
        if (isset($row[1]) && strpos($row[1], $nave) !== false) {
          // eanacekodlari tablosundan nace ile eşleşen kaydı alıyoruz.
          $eanaceKod = \DB::table('eanacekodlari')
            ->where('nace', 'like', '%' . $nave . '%')
            ->first();
          if ($eanaceKod) {
            $eakod = $eanaceKod->ea;
            // kritikkodlar tablosunu alıp kontrol ediyoruz.
            $kritikKodlar = \DB::table('kritikkodlar')->get();
            foreach ($kritikKodlar as $res) {
              if (intval($eakod) == intval($res->iso9)) {
                return true;
              }
            }
          }
        }
      }
    }

    return $sonuc;
  }

  public function getKritikKod14001($firmanace, $denetci)
  {
    $sonuc = false;
    $naceBol = array_map('trim', explode(',', $firmanace));

    foreach ($naceBol as $nave) {
      $denetciler = \DB::table('denetciler')
        ->where('denetci', $denetci)
        ->where('eanace', 'like', '%' . $nave . '%')
        ->get();

      foreach ($denetciler as $ret) {
        $row = explode("|", $ret->eanace);
        if (isset($row[1]) && strpos($row[1], $nave) !== false) {
          $eanaceKod = \DB::table('eanacekodlari')
            ->where('nace', 'like', '%' . $nave . '%')
            ->first();
          if ($eanaceKod) {
            $eakod = $eanaceKod->ea;
            $kritikKodlar = \DB::table('kritikkodlar')->get();
            foreach ($kritikKodlar as $res) {
              if (intval($eakod) == intval($res->iso14)) {
                return true;
              }
            }
          }
        }
      }
    }

    return $sonuc;
  }

  public function getKritikKod22000($firmakat, $denetci)
  {
    $sonuc = false;
    $catBol = array_map('trim', explode(',', $firmakat));

    foreach ($catBol as $kate) {
      $denetciler = \DB::table('denetciler')
        ->where('denetci', $denetci)
        ->where('kategori', 'like', '%' . $kate . '%')
        ->get();

      foreach ($denetciler as $ret) {
        // Kategori alanındaki köşeli parantezleri kaldırıyoruz
        $cat = str_replace(["[", "]"], "", $ret->kategori);
        if (strpos(trim($cat), $kate) !== false) {
          // cls22000 tablosundaki ilgili kayıtları alıyoruz
          $cls22000 = \DB::table('cls22000')->get();
          foreach ($cls22000 as $res) {
            // Eğer kategori eşleşiyor ve "kk" alanı "Y" ise true döndür
            if ($kate == $res->kategori && $res->kk == "Y") {
              return true;
            }
          }
        }
      }
    }

    return $sonuc;
  }

  /**
   * @param array $row
   * @return string
   */
  public function teknikAlan(array $row)
  {
// Teknik Alan
    $removeChars = ['|', 'Æ', '@', '€', 'ß'];

    $ea = isset($row["eakodu"]) ? $this->cleanValue($row["eakodu"], $removeChars) : '';
//    $nace = isset($row["nacekodu"]) ? $this->cleanValue($row["nacekodu"], $removeChars) : '';
    $kat = isset($row["kategori22"]) ? $this->cleanValue($row["kategori22"], $removeChars) : '';
    $oickat = isset($row["kategorioic"]) ? $this->cleanValue($row["kategorioic"], $removeChars) : '';
    $enysteknikalan = isset($row["teknikalanenys"]) ? $this->cleanValue($row["teknikalanenys"], $removeChars) : '';
    $bgkat = isset($row["kategoribgys"]) ? $this->cleanValue($row["kategoribgys"], $removeChars) : '';


// Boş olmayan değerleri diziye ekleyelim
    $values = [];
    if (trim($ea) !== '') $values[] = trim($ea);
//    if (trim($nace) !== '') $values[] = trim($nace);
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

  /**
   * @param array $row
   * @return string
   */
  public function teknikAlanIzleme(array $row)
  {
// Teknik Alan
    $removeChars = ['|', 'Æ', '@', '€', 'ß'];

    $ea = isset($row["eakodu"]) ? $this->cleanValue($row["eakodu"], $removeChars) : '';
    $kat = isset($row["kategori22"]) ? $this->cleanValue($row["kategori22"], $removeChars) : '';
    $oickat = isset($row["kategorioic"]) ? $this->cleanValue($row["kategorioic"], $removeChars) : '';
    $enysteknikalan = isset($row["teknikalanenys"]) ? $this->cleanValue($row["teknikalanenys"], $removeChars) : '';
    $bgkat = isset($row["kategoribgys"]) ? $this->cleanValue($row["kategoribgys"], $removeChars) : '';


// Boş olmayan değerleri diziye ekleyelim
    $values = [];
    if (trim($ea) !== '') $values[] = trim($ea);
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

  /**
   * Denetçi dosyasının var olup olmadığını kontrol eder.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function denetciDosyaKontrol(Request $request)
  {
    $klasor = Plan::turkishToEnglish($request->input('klasor'));
    $altklasor = Plan::turkishToEnglish($request->input('altklasor'));
    $path = public_path() . '/uploads/denetci/' . $klasor . '/' . $altklasor;

    $fileExists = false;
    $lastModified = null;

    if (file_exists($path)) {
      // Dizin içindeki dosya sayısını kontrol et (. ve .. hariç)
      $files = array_diff(scandir($path), ['.', '..']);
      $fileExists = count($files) > 0;

      // Eğer dosya varsa, en son değiştirilen dosyanın tarihini al
      if ($fileExists) {
        $latestFile = null;
        $latestTime = 0;

        foreach ($files as $file) {
          $fileTime = filemtime($path . '/' . $file);
          if ($fileTime > $latestTime) {
            $latestTime = $fileTime;
            $latestFile = $file;
          }
        }

        if ($latestTime > 0) {
          $lastModified = date('d.m.Y H:i', $latestTime);
        }
      }
    }

    return response()->json([
      'fileExists' => $fileExists,
      'lastModified' => $lastModified
    ]);
  }

  public function denetciAtamaKontrol(Request $request)
  {
    $request->validate([
      'uid' => 'required'
    ]);

    $uid = $request->input('uid');

    // Denetci_atamalar tablosunda ilgili denetçi ID'sine ait kayıt var mı kontrol et
    $hasRecords = \App\Models\DenetciAtama::where('denetci_id', $uid)->exists();

    // Eğer kayıt varsa, en son güncelleme tarihini al
    $lastUpdated = null;
    if ($hasRecords) {
      $latestRecord = \App\Models\DenetciAtama::where('denetci_id', $uid)
        ->orderBy('updated_at', 'desc')
        ->first();

      if ($latestRecord && $latestRecord->updated_at) {
        $lastUpdated = $latestRecord->updated_at->format('d.m.Y H:i');
      }
    }

    return response()->json([
      'hasRecords' => $hasRecords,
      'lastUpdated' => $lastUpdated
    ]);
  }

  /**
   * Get a list of files in a folder
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  /**
   * Get a list of files in a folder
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function denetciDosyaListesi(Request $request)
  {
    $request->validate([
      'klasor' => 'required',
      'altklasor' => 'required',
    ]);

    $klasor = Plan::turkishToEnglish($request->input('klasor'));
    $altklasor = Plan::turkishToEnglish($request->input('altklasor'));
    $path = public_path() . '/uploads/denetci/' . $klasor . '/' . $altklasor;

    $files = [];

    if (file_exists($path)) {
      $fileList = array_diff(scandir($path), ['.', '..']);

      foreach ($fileList as $fileName) {
        $filePath = $path . '/' . $fileName;
        if (is_file($filePath)) {
          $files[] = [
            'name' => $fileName,
            'size' => filesize($filePath),
            'type' => mime_content_type($filePath),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath))
          ];
        }
      }
    }

    return response()->json([
      'success' => true,
      'files' => $files
    ]);
  }
}
