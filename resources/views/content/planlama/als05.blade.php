@php use Illuminate\Support\Facades\DB; @endphp
@extends('layouts/layoutMaster')

@section('title', 'ALS.05 Belgeli Firma Listesi')


@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-select-bs5/select.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-fixedcolumns-bs5/fixedcolumns.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-fixedheader-bs5/fixedheader.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/swiper/swiper.css')}}"/>
@endsection

@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-analytics.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/css/style.css')}}">
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/plan-tables-datatables-planlar.js')}}"></script>
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
@endsection

@section('content')
  <div class="row gy-4 mb-4">
    <div class="col-xl-12">
      <!-- Fixed Header -->
      <div class="card">
        <div class="card-datatable table-responsive-sm pt-0 text-wrap">
          <?php

          echo '       <table id="tblbelgelifirmalarals05" class="table table-bordered">

                        <thead>

                        <tr>
                            <th>Sıra No</th>
                            <th>Dosya No</th>
                            <th>İlgili Standart</th>
                            <th>Firma Adı</th>
                            <th>Adres</th>
                            <th>Ülke</th>
                            <th>Sektör Kodu</th>
                            <th>Belge Kapsamı</th>
                            <th>Son Denetim Türü</th>
                            <th>Sertifika Güncel Yayın Tarihi</th>
                            <th>Sertifika No</th>
                            <th>Geçerlilik Durumu</th>
                            <th>Geçerlilik Tarihi(Türkaka iletilmeyecek)</th>
                            <th>İlçe</th>
                            <th>Şehir</th>
                            <th>Vardiya 1</th>
                            <th>Vardiya 2</th>
                            <th>Vardiya 3</th>
                            <th>Danışman</th>
                        </tr>

                        </thead>

                        <tbody>';
          $satir = "";
//select id,planno,firmaadi,belgelendirileceksistemler from splanlar WHERE asama2 LIKE '%%' or gozetim1 LIKE '%%' or gozetim2 LIKE '%%' or ybtar LIKE '%%'
          $temp = "";
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

          $sqlSQLs = "SELECT * FROM `planlar` ORDER BY `planlar`.`planno` DESC ";
//$sqlSQLcert = "SELECT * FROM sertifikalar WHERE akreditasyon='Akreditasyonlu' order by ilkyayintarihi DESC";

//$users = Capsule::select('select * from splanlar  ORDER BY planno DESC '); //Capsule::table('splanlar')->orderBy("planno", "DESC")->get();
//var_dump($users);

          $result = DB::table('planlar')
            ->join('basvuru', 'planlar.planno', '=', 'basvuru.planno')
            ->select('basvuru.*', 'planlar.*')
            ->orderBy('planlar.planno', 'DESC')
            ->get();

          foreach ($result as $ret) {

            $kyssistemler = \App\Helpers\Helpers::getSistemler($ret);
            $oicsistemler = \App\Helpers\Helpers::getOicSistemler($ret);
            $belgelendirileceksistemler = "";

            if ($kyssistemler !== "" && $oicsistemler !== "") {
              $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
            }
            if ($kyssistemler === "" && $oicsistemler !== "") {
              $belgelendirileceksistemler = $oicsistemler;
            }
            if ($kyssistemler !== "" && $oicsistemler === "") {
              $belgelendirileceksistemler = $kyssistemler;
            }

            if ($belgelendirileceksistemler === "OIC/SMIIC 1:2019") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 6:2019") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 1:2019, OIC/SMIIC 6:2019") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 6:2019, OIC/SMIIC 9:2019") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 1:2019, OIC/SMIIC 23:2022") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 1:2019, OIC/SMIIC 24:2020") continue;
            if ($belgelendirileceksistemler === "OIC/SMIIC 1:2019, OIC/SMIIC 17-1:2020") continue;


            $bitistarihi = date_create_from_format("Y-m-d", $ret->bitistarihi);
            $gecerliliktarihi = ($bitistarihi != "") ? date_format($bitistarihi, "d.m.Y") : "";

            $ilkyayintarihi = date_create_from_format("Y-m-d", $ret->ilkyayintarihi);
            $ilkyayintarihi = ($ilkyayintarihi != "") ? date_format($ilkyayintarihi, "d.m.Y") : "";

            $tar1 = strtotime(date("Y-m-d"));
            $tar2 = strtotime($ret->bitistarihi);

            $bugun = strtotime(date("d.m.Y"));
            if ($bugun > strtotime($gecerliliktarihi)) continue;

            if ($ret->firmaadi == "") continue;

//    if ($temp == trim($ret->firmaadi)) {
//        continue;
//    } else {
//        $temp = trim($ret->firmaadi);
//    }

            $cevrim = $ret->belgecevrimi;
            $cevrim = ($cevrim == "") ? "1" : $cevrim;

            $dentarihi = "";
            $dtipi = "";
            if ($ret->asama2 != "") {
              $dentarihi = $ret->asama2;
              $dtipi = "İlk";
            }
            if ($ret->gozetim1 != "") {
              $dentarihi = $ret->gozetim1;
              $dtipi = "G1";
            }
            if ($ret->gozetim2 != "") {
              $dentarihi = $ret->gozetim2;
              $dtipi = "G2";
            }
            if ($ret->ybtar != "") {
              $dentarihi = $ret->ybtar;
              $dtipi = "Yb";
            }
            if ($ret->ozeltar != "" && (strtotime($ret->ozeltar) > strtotime($dentarihi))) {
              $dentarihi = $ret->ozeltar;
              $dtipi = "Özel";
            }

            if (intval($cevrim) >= 2 && ($ret->asama == "g1" || $ret->asama == "g1karar")) {
              $dentarihi = $ret->gozetim1;
              $dtipi = "G1";
            }
            if (intval($cevrim) >= 2 && ($ret->asama == "g2" || $ret->asama == "g2karar")) {
              $dentarihi = $ret->gozetim2;
              $dtipi = "G2";
            }
            if (intval($cevrim) >= 2 && ($ret->asama == "yb" || $ret->asama == "ybkarar")) {
              $dentarihi = $ret->ybtar;
              $dtipi = "Yb";
            }
            if (intval($cevrim) >= 2 && ($ret->asama == "ozel" || $ret->asama == "ozelkarar") && (strtotime($ret->ozeltar) > strtotime($dentarihi))) {
              $dentarihi = $ret->ozeltar;
              $dtipi = "Özel";
            }
            $denyili = explode(".", explode(", ", $dentarihi)[0])[2];
            $dentarihi = wordwrap($dentarihi, 15, "<br>");

//    if($ph->InStr($dentarihi, "2024") === -1) continue;

            $rowcertno = "";
            $rowdurumrenk = "primary";
            $rowdurum = "Devam";
            $kurul0 = trim($ret->firmaadi); //mb_substr($ret["firmaadi"], 0, 10, 'UTF-8');

            if ($tar2 > strtotime('-180 days') && $tar2 < $tar1) {
              $rowdurumrenk = "warning";
              $rowdurum = "Askı";
            }
            if ($tar2 < strtotime('-180 days')) {
              $rowdurumrenk = "danger";
              $rowdurum = "İptal";
            }
            if ($tar2 >= $tar1 || $tar2 == "") {
              $rowdurumrenk = "primary";
              $rowdurum = "Aktif";
            }

//    $a9001sure += floatval($ret["iso9001kalansure"]);
//    $a14001sure += floatval($ret["iso14001kalansure"]);
//    $a22000sure += floatval($ret["iso22000kalansure"]);
//    $a45001sure += floatval($ret["iso45001kalansure"]);
//    $a27001sure += floatval($ret["iso27001kalansure"]);
//    $a50001sure += floatval($ret["iso50001kalansure"]);


            $ea = $ret->eakodu;
            $nace = $ret->nacekodu;
            $kat = str_replace("@", "", $ret->kategori22);
            $oickat = str_replace("ß", "", $ret->kategorioic);
            $enysteknikalan = str_replace("Æ", "", $ret->teknikalanenys);
            $bgkat = str_replace("€", "", $ret->kategoribgys);
            $eanacekat = "";

            if ($nace != "") $nace = "|" . $nace;
            if ($kat != "") $kat = "@" . str_replace("@", "", $kat);
            $oickat = ($oickat != "") ? "ß" . str_replace("ß", "", $oickat) : str_replace("ß", "", $oickat);
            $enysteknikalan = ($enysteknikalan != "") ? "Æ" . str_replace("Æ", "", $enysteknikalan) : str_replace("Æ", "", $enysteknikalan);
            if ($bgkat != "") $bgkat = "€" . $bgkat;

            $eanacekat = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

            $ilce = $ret->milce;
            $sehir = $ret->msehir;
            $satir .= '	<tr>
                    <td>' . $i . '</td>
                    <td>' . str_pad($ret->planno, 4, "0", STR_PAD_LEFT) . '</td>
                    <td>' . $belgelendirileceksistemler . '</td>
                    <td>' . trim($ret->firmaadi) . '</td>
                    <td>' . trim($ret->firmaadresi) . '</td>
                    <td>Türkiye</td>
                    <td>' . $eanacekat . '</td>
                    <td>' . wordwrap(trim($ret->belgelendirmekapsami), 30, "<br>") . '</td>
                    <td>' . $dtipi . '</td>
                    <td>' . $ilkyayintarihi . '</td>
                    <td>' . $ret->certno . '</td>
                    <td>Aktif</td>
                    <td>' . $gecerliliktarihi . '</td>
                    <td>' . $ilce . '</td>
                    <td>' . $sehir . '</td>
                    <td>' . $ret->vardiyalicalisansayisi . '</td>
                    <td>' . $ret->vardiyalicalisansayisi1 . '</td>
                    <td>' . $ret->vardiyalicalisansayisi2 . '</td>
                    <td>' . $ret->danisman . '</td>
               </tr>';
//            echo $satir;
            $i++;
          }
          echo $satir;
          echo '</tbody>
                        <tfoot>

                        <tr>
                            <th>Sıra No</th>
                            <th>Dosya No</th>
                            <th>İlgili Standart</th>
                            <th>Firma Adı</th>
                            <th>Adres</th>
                            <th>Ülke</th>
                            <th>Sektör Kodu</th>
                            <th>Belge Kapsamı</th>
                            <th>Son Denetim Türü</th>
                            <th>Sertifika Güncel Yayın Tarihi</th>
                            <th>Sertifika No</th>
                            <th>Geçerlilik Durumu</th>
                            <th>Geçerlilik Tarihi(Türkaka iletilmeyecek)</th>
                            <th>İlçe</th>
                            <th>Şehir</th>
                            <th>Vardiya 1</th>
                            <th>Vardiya 2</th>
                            <th>Vardiya 3</th>
                            <th>Danışman</th>
                        </tr>

                        </tfoot>

                    </table>';


//  echo "9001sure: " . $a9001sure . "<br>";
//  echo "14001sure: " . $a14001sure . "<br>";
//  echo "22000sure: " . $a22000sure . "<br>";
//  echo "45001sure: " . $a45001sure . "<br>";
//  echo "27001sure: " . $a27001sure . "<br>";
//  echo "50001sure: " . $a50001sure . "<br>";
          ?>
        </div>
      </div>
    </div>
  </div>

@endsection
