@extends('layouts/layoutMaster')

@section('title', '[' . $pno . '] ' . $asama . " | " . $plan[0]->firmaadi)

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/spinkit/spinkit.css')}}" />
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
@endsection

@section('page-style')
  <!-- Page -->
  <link rel="stylesheet" href="{{asset('assets/vendor/css/pages/cards-statistics.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
{{--  <link rel="stylesheet" href="{{asset('assets/vendor/css/style.css')}}">--}}
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>

@endsection

@section('page-script')
  <script src="{{asset('assets/js/plan-tables-datatables-planlar.js')}}"></script>
  <script src="{{asset('assets/js/plan-hesaplamalar-planlar.js')}}"></script>
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
@endsection

@section('content')
  <?php
  $pot = $plan[0];

//  $pot = array_merge($pot, $basvurubgys);


  $asama1 = "";
  $asama1 = $pot->asama1;
  $bd1 = $pot->bd1;
  $d1 = $pot->d1;
  $tu1 = $pot->tu1;
  $g1 = $pot->g1;
  $iku1 = $pot->iku1;

  $asama2 = $pot->asama2;
  $bd2 = $pot->bd2;
  $d2 = $pot->d2;
  $tu2 = $pot->tu2;
  $g2 = $pot->g2;
  $iku2 = $pot->iku2;

  $gozetim1 = $pot->gozetim1;
  $gbd1 = $pot->gbd1;
  $gd1 = $pot->gd1;
  $gtu1 = $pot->gtu1;
  $gg1 = $pot->gg1;
  $ikug1 = $pot->ikug1;

  $gozetim2 = $pot->gozetim2;
  $gbd2 = $pot->gbd2;
  $gd2 = $pot->gd2;
  $gtu2 = $pot->gtu2;
  $gg2 = $pot->gg2;
  $ikug2 = $pot->ikug2;

  $ybtar = $pot->ybtar;
  $ybbd = $pot->ybbd;
  $ybd = $pot->ybd;
  $ybtu = $pot->ybtu;
  $ybg = $pot->ybg;
  $ikuyb = $pot->ikuyb;

  $ozeltar = $pot->ozeltar;
  $otbd = $pot->otbd;
  $otd = $pot->otd;
  $ottu = $pot->ottu;
  $otg = $pot->otg;
  $ikuot = $pot->ikuot;

  $asama = trim($asama);
  if ($asama == "") $asama = "ilkplan";

  $tab1button = $asama === "ilkplan" ? " active" : " readonly";
  $tab2button = $asama === "g1" ? " active" : " readonly";
  $tab3button = $asama === "g2" ? " active" : " readonly";
  $tab4button = $asama === "yb" ? " active" : " readonly";
  $tab5button = $asama === "ozel" ? " active" : " readonly";

  $tab1 = $asama === "ilkplan" ? " active" : " readonly";
  $tab2 = $asama === "g1" ? " active" : " readonly";
  $tab3 = $asama === "g2" ? " active" : " readonly";
  $tab4 = $asama === "yb" ? " active" : " readonly";
  $tab5 = $asama === "ozel" ? " active" : " readonly";

  $tab1show = $asama === "ilkplan" ? " show" : "";
  $tab2show = $asama === "g1" ? " show" : "";
  $tab3show = $asama === "g2" ? " show" : "";
  $tab4show = $asama === "yb" ? " show" : "";
  $tab5show = $asama === "ozel" ? " show" : "";

  $asama1 = substr($asama1, 1);
  $asama1 = substr($asama1, 0, -1);
  $asama1 = str_replace("|", ", ", $asama1);

  $asama2 = substr($asama2, 1);
  $asama2 = substr($asama2, 0, -1);
  $asama2 = str_replace("|", ", ", $asama2);

  $gozetim1 = substr($gozetim1, 1);
  $gozetim1 = substr($gozetim1, 0, -1);
  $gozetim1 = str_replace("|", ", ", $gozetim1);

  $gozetim2 = substr($gozetim2, 1);
  $gozetim2 = substr($gozetim2, 0, -1);
  $gozetim2 = str_replace("|", ", ", $gozetim2);

  $ozeltar = substr($ozeltar, 1);
  $ozeltar = substr($ozeltar, 0, -1);
  $ozeltar = str_replace("|", ", ", $ozeltar);

  $ea = $pot->eakodu;
  $nace = $pot->nacekodu;
  $kat = str_replace("@", "", $pot->kategori22);
  $oickat = str_replace("ß", "", $pot->kategorioic);
  $enysteknikalan = str_replace("Æ", "", $pot->teknikalanenys);
  $bgkat = str_replace("€", "", $pot->kategoribgys);
  $katnace = "";

  if ($nace != "") $nace = "|" . $nace;
  if ($kat != "") $kat = "@" . str_replace("@", "", $kat);
  $oickat = ($oickat != "") ? "ß" . str_replace("ß", "", $oickat) : str_replace("ß", "", $oickat);
  $enysteknikalan = ($enysteknikalan != "") ? "Æ" . str_replace("Æ", "", $enysteknikalan) : str_replace("Æ", "", $enysteknikalan);
  if ($bgkat != "") $bgkat = "€" . $bgkat;

  echo $katnace = $ea . $nace . $kat . $oickat . $enysteknikalan . $bgkat;

  $iso900115 = $pot->iso900115varyok == 1 ? true : false;
  $iso1400115 = $pot->iso1400115varyok == 1 ? true : false;
  $iso2200018 = $pot->iso2200018varyok == 1 ? true : false;
  $oicsmiic = $pot->helalvaryok == 1 ? true : false;
  $oicsmiic6 = $pot->oicsmiik6varyok == 1 ? true : false;
  $oicsmiic9 = $pot->oicsmiik9varyok == 1 ? true : false;
  $oicsmiic171 = $pot->oicsmiik171varyok == 1 ? true : false;
  $oicsmiic23 = $pot->oicsmiik23varyok == 1 ? true : false;
  $oicsmiic24 = $pot->oicsmiik24varyok == 1 ? true : false;
  $iso45001 = $pot->iso4500118varyok == 1 ? true : false;
  $iso50001 = $pot->iso5000118varyok == 1 ? true : false;
  $iso27001 = $pot->iso27001varyok == 1 ? true : false;

  $inceleneceksahasayisi = (!is_null($pot->inceleneceksahasayisi) || intval($pot->inceleneceksahasayisi) > 0) ? $pot->inceleneceksahasayisi : 0;

  $inceleneceksahasayisisec = $inceleneceksahasayisi;
  if($inceleneceksahasayisi > 2) {
    if($asama === "ilkplan")
      $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi));
    if($asama === "g1")
      $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.6"));
    if($asama === "g2")
      $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.6"));
    if($asama === "yb")
      $inceleneceksahasayisisec = ceil(sqrt($inceleneceksahasayisi) * floatval("0.8"));
  }

  $sahaa = array(1=>$pot->subevardaa, 2=>$pot->subevardba, 3=>$pot->subevardca);
  $sahab = array(1=>$pot->subevardab, 2=>$pot->subevardbb, 3=>$pot->subevardcb);
  $sahac = array(1=>$pot->subevardac, 2=>$pot->subevardbc, 3=>$pot->subevardcc);

  $sistemsay = 0;
  if ($iso900115)
    $sistemsay++;
  if ($iso1400115)
    $sistemsay++;
  if ($iso2200018)
    $sistemsay++;
  if ($oicsmiic || $oicsmiic6 || $oicsmiic9 || $oicsmiic23 || $oicsmiic24)
    $sistemsay++;
  if ($oicsmiic171)
    $sistemsay++;
  if ($iso45001)
    $sistemsay++;
  if ($iso50001)
    $sistemsay++;
  if ($iso27001)
    $sistemsay++;

  $kyssistemler = \App\Http\Controllers\Planlama\Plan::getSistemler($pot);
  $oicsistemler = \App\Http\Controllers\Planlama\Plan::getOicSistemler($pot);

  if ($kyssistemler !== "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
  }
  if ($kyssistemler === "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $oicsistemler;
  }
  if ($kyssistemler !== "" && $oicsistemler === "") {
    $belgelendirileceksistemler = $kyssistemler;
  }

  $sistemversiyongecis = (isset($pot->sistemversiyongecis)) ? $pot->sistemversiyongecis : "";
  $cevrim = $pot->belgecevrimi;
  $cevrim = ($cevrim == "") ? "1" : $cevrim;

  $kurul0 = mb_substr($pot->firmaadi, 0, 10, 'UTF-8');
  $bitistarihi = date_create_from_format("Y-m-d", $pot->bitistarihi);
  $bitistarihi = ($bitistarihi != "") ? date_format($bitistarihi, "d.m.Y") : "Sertifika kaydı yok...";
  ?>
  <div class="row">
    <div class="col-12 text-danger">
      <h5>[{{$pno}}] {{$pot->firmaadi}}</h5>
      {{$pot->belgelendirmekapsami}}
    </div>

    <!-- Cards with separator -->
    <div class="col-12">
      <div class="card">
        <div class="card-widget-separator-wrapper">
          <div class="card-body card-widget-separator">
            <div class="row gy-4 gy-sm-1">
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-1 border-end pb-3 pb-sm-0">
                  <div>
                    <h3 class="mb-1">{{$cevrim}}</h3>
                    <p class="mb-0">Çevrim</p>
                  </div>
                  <div class="avatar me-sm-4">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-account-outline text-heading mdi-20px"></i>
                  </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none me-4">
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start card-widget-2 border-end pb-3 pb-sm-0">
                  <div>
                    <h3 class="mb-1">{{$asama}}</h3>
                    <p class="mb-0">Aşama</p>
                  </div>
                  <div class="avatar  me-lg-4">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-content-paste text-heading mdi-20px"></i>
                  </span>
                  </div>
                </div>
                <hr class="d-none d-sm-block d-lg-none">
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start border-end pb-3 pb-sm-0 card-widget-3">
                  <div>
                    <h3 class="mb-1">{{$bitistarihi}}</h3>
                    <p class="mb-0">Sertifika geçerlilik tarihi</p>
                  </div>
                  <div class="avatar me-sm-4">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-calendar text-heading mdi-20px"></i>
                  </span>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-3">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h3 class="mb-1">
                      <div id="lbl_ea">{{$ea}}</div>
                    </h3>
                    <p class="mb-0">&nbsp;</p>
                  </div>
                  <div class="avatar">
                  <span class="avatar-initial rounded bg-label-secondary">
                    <i class="mdi mdi-code-tags text-heading mdi-20px"></i>
                  </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!--/ Cards with separator -->

    <div class="row gy-4 mb-4">
      <div class="col-xl-12">
        <form id="planlama-form" onSubmit="return false">
          {{ csrf_field() }}
          <div class="card">
            <div
              class="card-header sticky-element bg-info d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
              <h5 class="card-title mb-sm-0 me-2">{{$belgelendirileceksistemler}}</h5>
              @include('_partials/planlama-menu', ['pno' => $pno])
            </div>
            <div class="card-body">
              <input type="hidden" id="formPlanlamaRoute" value="{{route('splanyeni')}}">
              <input type="hidden" id="iso9001SureHesaplaRoute" value="{{url('iso9001SureHesaplaRoute')}}">
              <input type="hidden" id="iso14001SureHesaplaRoute" value="{{url('iso14001SureHesaplaRoute')}}">
              <input type="hidden" id="iso45001SureHesaplaRoute" value="{{url('iso45001SureHesaplaRoute')}}">
              <input type="hidden" id="iso27001SureHesaplaRoute" value="{{url('iso27001SureHesaplaRoute')}}">
              <input type="hidden" id="bgysFaktorDenetimEtkisiRoute" value="{{url('bgysFaktorDenetimEtkisiRoute')}}">
              <input type="hidden" id="iso50001SureHesaplaRoute" value="{{url('iso50001SureHesaplaRoute')}}">
              <input type="hidden" id="iso22000SureHesaplaRoute" value="{{url('iso22000SureHesaplaRoute')}}">
              <input type="hidden" id="isoOicSmiicSureHesaplaRoute" value="{{url('isoOicSmiicSureHesaplaRoute')}}">
              <input type="hidden" id="denetciSistemleriRoute" value="{{url('denetciSistemleriRoute')}}">
              <input type="hidden" id="entegreDuzeyleriRoute" value="{{url('entegreDuzeyleriRoute')}}">
              <input type="hidden" id="denetciKontrolRoute" value="{{url('denetciKontrolRoute')}}">
              <input type="hidden" id="firmaadi" name="firmaadi" value="{{$pot->firmaadi}}">
              <input type="hidden" id="belgelendirileceksistemler" name="belgelendirileceksistemler"
                     value="{{$belgelendirileceksistemler}}">
              <input type="hidden" id="denetcisay" value="0">
              <input type="hidden" id="planno" name="planno" value="{{$pno}}">
              <input type="hidden" id="asama" name="asama" value="{{$asama}}">
              <input type="hidden" id="iso900115varyok" name="iso900115varyok" value="{{$pot->iso900115varyok}}">
              <input type="hidden" id="iso1400115varyok" name="iso1400115varyok" value="{{$pot->iso1400115varyok}}">
              <input type="hidden" id="iso2200018varyok" name="iso2200018varyok" value="{{$pot->iso2200018varyok}}">
              <input type="hidden" id="iso4500118varyok" name="iso4500118varyok" value="{{$pot->iso4500118varyok}}">
              <input type="hidden" id="iso5000118varyok" name="iso5000118varyok" value="{{$pot->iso5000118varyok}}">
              <input type="hidden" id="iso27001varyok" name="iso27001varyok" value="{{$pot->iso27001varyok}}">
              <input type="hidden" id="helalvaryok" name="helalvaryok" value="{{$pot->helalvaryok}}">
              <input type="hidden" id="oicsmiik6varyok" name="oicsmiik6varyok" value="{{$pot->oicsmiik6varyok}}">
              <input type="hidden" id="oicsmiik9varyok" name="oicsmiik9varyok" value="{{$pot->oicsmiik9varyok}}">
              <input type="hidden" id="oicsmiik171varyok" name="oicsmiik171varyok" value="{{$pot->oicsmiik171varyok}}">
              <input type="hidden" id="oicsmiik23varyok" name="oicsmiik23varyok" value="{{$pot->oicsmiik23varyok}}">
              <input type="hidden" id="oicsmiik24varyok" name="oicsmiik24varyok" value="{{$pot->oicsmiik24varyok}}">
              <input type="hidden" id="digersistemlerneler" name="digersistemlerneler" value="{{$pot->digersistemlerneler}}">
              <input type="hidden" id="indart9001varmi" name="indart9001varmi" value="">
              <input type="hidden" id="indart14001varmi" name="indart14001varmi" value=""/>
              <input type="hidden" id="indart45001varmi" name="indart45001varmi" value=""/>
              <input type="hidden" id="indart50001varmi" name="indart50001varmi" value=""/>
              <input type="hidden" id="indart27001varmi" name="indart27001varmi" value=""/>
              <input type="hidden" id="indartentvarmi" name="indartentvarmi" value=""/>
              <input type="hidden" id="art22000varmi" name="art22000varmi" value=""/>
              <input type="hidden" id="indartoicsmiicvarmi" name="indartoicsmiicvarmi"
                     value=""/>
              <input type="hidden" id="indartneden" name="indartneden" value=""/>
              <input type="hidden" id="gizliea" name="eakodu" value="<?php echo $ea; ?>"/>
              <input type="hidden" id="gizlinace" name="firmanacekodu" class="form-control"
                     value="<?php echo $nace; ?>"/>
              <input type="hidden" id="gizlikat" name="categories" class="form-control"
                     value="<?php echo $kat; ?>"/>
              <input type="hidden" id="gizlikatbb" name="gizlikatbb" class="form-control" value=""/>
              <input type="hidden" id="gizlikatcc" name="gizlikatcc" class="form-control" value=""/>
              <input type="hidden" id="gizlioickat" name="oiccategories" class="form-control"
                     value="<?php echo $oickat; ?>"/>
              <input type="hidden" id="gizlikatbboic" name="gizlikatbboic" class="form-control" value=""/>
              <input type="hidden" id="gizlikatccoic" name="gizlikatccoic" class="form-control" value=""/>
              <input type="hidden" id="gizlienysta" name="enysteknikalan" class="form-control"
                     value="<?php echo $enysteknikalan; ?>"/>
              <input type="hidden" id="gizlibgys" name="bgcategories" class="form-control"
                     value="<?php echo $bgkat; ?>"/>
              <input type="hidden" id="riskgrubu9" name="riskgrubu9" class="form-control" value=""/>
              <input type="hidden" id="riskgrubu14" name="riskgrubu14" class="form-control" value=""/>
              <input type="hidden" id="riskgrubu45" name="riskgrubu45" class="form-control" value=""/>
              <input type="hidden" id="riskgrubu27" name="riskgrubu27" class="form-control" value=""/>
              <input type="hidden" id="havuzsayisi" name="havuzsayisi" class="form-control"
                     value="<?php echo $pot->havuzsayisi; ?>"/>
              <input type="hidden" id="mutfaksayisi" name="mutfaksayisi" class="form-control"
                     value="<?php echo $pot->mutfaksayisi; ?>"/>
              <input type="hidden" id="odasayisi" name="odasayisi" class="form-control"
                     value="<?php echo $pot->odasayisi; ?>"/>
              <input type="hidden" id="hizmetkategorisi" name="hizmetkategorisi" class="form-control"
                     value="<?php echo $pot->hizmetkategorisi; ?>"/>
              <input type="hidden" id="aracsayisi" name="aracsayisi" class="form-control"
                     value="<?php echo $pot->aracsayisi; ?>"/>
              <input type="hidden" id="inceleneceksahasayisi" name="inceleneceksahasayisi" class="form-control"
                     value="<?php echo $inceleneceksahasayisi; ?>"/>
              <div class="row g-4">
                <div class="col-sm-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="toplamcalisansayisi"
                             name="toplamcalisansayisi" class="form-control" placeholder=""
                             value="{{$pot->toplamcalisansayisi}}"/>
                      <label for="toplamcalisansayisi">Çalışan sayısı</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-8">
                  <div class="input-group">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#myModaleaNaceKat">Kod/Kategori/Teknik Alan
                    </button>
                    <input type="text" id="lbl_eanacekat" name="lbl_eanacekat" class="form-control" placeholder=""
                           value="{{$katnace}}"
                           aria-label="Example text with two button addons">
                    <button type="button" class="btn btn-icon btn-primary btn-fab demo"
                            onclick="getDenetimOnerilenBasdenetci()">
                      <span class="tf-icons mdi mdi-reload mdi-24px"></span>
                    </button>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="gozdengecirmetarihi"
                             id="gozdengecirmetarihi" value="{{$pot->gozdengecirmetarihi}}"/>
                      <label for="gozdengecirmetarihi">Başvuru gözden geçirme tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="belgelendirmedenetimucreti"
                             name="belgelendirmedenetimucreti"
                             class="form-control" placeholder="" readonly
                             value="{{$pot->belgelendirmedenetimucreti}}"/>
                      <label for="belgelendirmedenetimucreti">Belgelendirme Denetim Ücreti</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gozetimdenetimucreti" name="gozetimdenetimucreti"
                             class="form-control"
                             placeholder="" readonly
                             value="{{$pot->gozetimdenetimucreti}}"/>
                      <label for="gozetimdenetimucreti">Gözetim Denetim Ücreti</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="belgelendirmedenetimucretihelal"
                             name="belgelendirmedenetimucretihelal"
                             class="form-control" placeholder="" readonly
                             value="{{$pot->belgelendirmedenetimucretihelal}}"/>
                      <label for="belgelendirmedenetimucretihelal">Helal Belgelendirme Denetim Ücreti</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gozetimdenetimucretihelal" name="gozetimdenetimucretihelal"
                             class="form-control"
                             placeholder="" readonly
                             value="{{$pot->gozetimdenetimucretihelal}}"/>
                      <label for="gozetimdenetimucretihelal">Helal Gözetim Denetim Ücreti</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="btn-group-vertical w-100">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#myModalbddenetim">Önerilen denetçi
                    </button>
                    <input type="text" id="divbddenetime" name="denetimeonerilendenetci" class="form-control"
                           placeholder="" value="{{$pot->denetimeonerilendenetci}}"
                           aria-label="denetçi seçiniz">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="btn-group-vertical w-100">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#myModalbdkarar">Karar Verici
                    </button>
                    <input type="text" id="divbdkarara" name="kararaonerilendenetci" class="form-control" placeholder=""
                           value="{{$pot->kararaonerilendenetci ?? 'Özcan ARSLAN'}}"
                           aria-label="Komite başkanını seçiniz">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="btn-group-vertical w-100">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#myModalbdkararu">Karar Verici
                    </button>
                    <input type="text" id="divbdkararu" name="kararuonerilendenetciuye" class="form-control"
                           placeholder="" value="{{$pot->kararuonerilendenetciuye}}"
                           aria-label="Komite üyesi seçiniz">
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="btn-group-vertical w-100">
                    <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#myModalikukararu">OIC/SMIIC Karar Komite
                    </button>
                    <input type="text" id="divikukararu" name="uyeikuadi" class="form-control" placeholder=""
                           value="{{($oicsmiic | $oicsmiic6 | $oicsmiic9 | $oicsmiic171 | $oicsmiic24) ? $pot->uyeikuadi ?? 'Abdulkadir CAN' : ''}}"
                           aria-label="İslami Konular Uzmanı seçiniz">
                  </div>
                </div>

                {{--                Tabs - Denetim ekibi seçimi başlangıcı--}}
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <div class="card mb-4">
                      <div class="card-body"><div id="planlamatabs" class="nav-align-left">
                          <!-- Tab headers -->
                          <ul class="nav nav-tabs" role="tablist">

                            <!-- 1) ilkplan sekmesi butonu -->
                            <li class="nav-item">
                              <button type="button"
                                      class="nav-link{{ $tab1button }}"
                                      role="tab"
                                      data-bs-toggle="tab"
                                      data-bs-target="#navs-left-ilkplan"
                                      aria-controls="navs-left-ilkplan"
                                      aria-selected="{{ $asama === 'ilkplan' ? 'true' : 'false' }}"
                              >
                                İlk
                              </button>
                            </li>

                            <!-- 2) g1 sekmesi butonu -->
                            <li class="nav-item">
                              <button type="button"
                                      class="nav-link{{ $tab2button }}"
                                      role="tab"
                                      data-bs-toggle="tab"
                                      data-bs-target="#navs-left-g1"
                                      aria-controls="navs-left-g1"
                                      aria-selected="{{ $asama === 'g1' ? 'true' : 'false' }}"
                              >
                                1. Gözetim
                              </button>
                            </li>

                            <!-- 3) g2, 4) yb, 5) ozel butonları da benzer şekilde -->
                            <li class="nav-item">
                              <button type="button"
                                      class="nav-link{{ $tab3button }}"
                                      role="tab"
                                      data-bs-toggle="tab"
                                      data-bs-target="#navs-left-g2"
                                      aria-controls="navs-left-g2"
                                      aria-selected="{{ $asama === 'g2' ? 'true' : 'false' }}"
                              >
                                2. Gözetim
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button"
                                      class="nav-link{{ $tab4button }}"
                                      role="tab"
                                      data-bs-toggle="tab"
                                      data-bs-target="#navs-left-yb"
                                      aria-controls="navs-left-yb"
                                      aria-selected="{{ $asama === 'yb' ? 'true' : 'false' }}"
                              >
                                Y.B.
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button"
                                      class="nav-link{{ $tab5button }}"
                                      role="tab"
                                      data-bs-toggle="tab"
                                      data-bs-target="#navs-left-ozel"
                                      aria-controls="navs-left-ozel"
                                      aria-selected="{{ $asama === 'ozel' ? 'true' : 'false' }}"
                              >
                                Özel Denetim
                              </button>
                            </li>
                          </ul>

                          <!-- Tab body -->
                          <div class="tab-content">

                            <!-- 1) ilkplan sekmesi içeriği -->
                            <div class="tab-pane fade{{ $tab1show }}{{ $tab1 }}"
                                 id="navs-left-ilkplan"
                                 role="tabpanel"
                            >
                              <!-- Eğer $asama !== 'ilkplan' ise readonly-fields -->
                              <div class="{{ $asama !== 'ilkplan' ? 'readonly-fields' : '' }}">
                                <div class="card mb-4">
                                  <h5 class="card-title">Aşama 1</h5>
                                  <div class="card-body">
                                    <div class="row g-1 mb-1">
                                      <div class="col-sm-2">
                                        <div class="input-group input-group-merge">
                                          <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                   name="tarihrevasama1"
                                                   id="tarihrevasama1" value="{{$pot->tarihrevasama1}}"/>
                                            <label for="tarihrevasama1">Aşama 1 bildirim tarihi</label>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-10">
                                        <div class="input-group input-group-merge">
                                          <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                   name="asama1"
                                                   id="asama1tar" value="{{$pot->asama1}}"/>
                                            <label for="asama1tar">Aşama 1 denetim tarihi</label>
                                          </div>
                                          <button class="btn btn-primary" type="button" onclick="denetcikontrol('asama1tar')">
                                            <span class="mdi mdi-database-search"></span>
                                          </button>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row g-1 mb-1">
                                      <div class="col-sm-4">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalbd1">Başdenetçi
                                          </button>
                                          <input type="text" id="divbd1" name="bd1" class="form-control" placeholder=""
                                                 value="{{$bd1}}"
                                                 aria-label="Başdenetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-8">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModald1">Denetçi(ler)
                                          </button>
                                          <input type="text" id="divd1" name="d1" class="form-control" placeholder=""
                                                 value="{{$d1}}"
                                                 aria-label="Denetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModaltu1">T. UZMAN
                                          </button>
                                          <input type="text" id="divtu1" name="tu1" class="form-control" placeholder=""
                                                 value="{{$tu1}}"
                                                 aria-label="Teknik uzman seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalg1">Gözlemci
                                          </button>
                                          <input type="text" id="divg1" name="g1" class="form-control" placeholder=""
                                                 value="{{$g1}}"
                                                 aria-label="Gözlemci seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModaliku1">İ. Uzman
                                          </button>
                                          <input type="text" id="diviku1" name="iku1" class="form-control" placeholder=""
                                                 value="{{$iku1}}"
                                                 aria-label="İslami uzman seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalad1">A. Denetçi
                                          </button>
                                          <input type="text" id="divad1" name="ad1" class="form-control" placeholder=""
                                                 value=""
                                                 aria-label="Aday denetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-12">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalsid1">Değerlendirici
                                          </button>
                                          <input type="text" id="divsid1" name="sid1" class="form-control" placeholder=""
                                                 value=""
                                                 aria-label="Değerlendirici seçiniz">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="card mb-4">
                                  <h5 class="card-title">Aşama 2</h5>
                                  <div class="card-body">
                                    <div class="row g-1 mb-1">
                                      <div class="col-sm-2">
                                        <div class="input-group input-group-merge">
                                          <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                   name="tarihrevasama2"
                                                   id="tarihrevasama2" value="{{$pot->tarihrevasama2}}"/>
                                            <label for="tarihrevasama2">Aşama 2 bildirim tarihi</label>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-sm-10">
                                        <div class="input-group input-group-merge">
                                          <div class="form-floating form-floating-outline">
                                            <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                   name="asama2"
                                                   id="asama2tar" value="{{$pot->asama2}}"/>
                                            <label for="asama2tar">Aşama 2 denetim tarihi</label>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="row g-1 mb-1">
                                      <div class="col-sm-4">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalbd2">Başdenetçi
                                          </button>
                                          <input type="text" id="divbd2" name="bd2" class="form-control" placeholder=""
                                                 value="{{$bd2}}"
                                                 aria-label="Başdenetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-8">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModald2">Denetçi(ler)
                                          </button>
                                          <input type="text" id="divd2" name="d2" class="form-control" placeholder=""
                                                 value="{{$d2}}"
                                                 aria-label="Denetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModaltu2">T. UZMAN
                                          </button>
                                          <input type="text" id="divtu2" name="tu2" class="form-control" placeholder=""
                                                 value="{{$tu2}}"
                                                 aria-label="Teknik uzman seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalg2">Gözlemci
                                          </button>
                                          <input type="text" id="divg2" name="g2" class="form-control" placeholder=""
                                                 value="{{$g2}}"
                                                 aria-label="Gözlemci seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModaliku2">İ. Uzman
                                          </button>
                                          <input type="text" id="diviku2" name="iku2" class="form-control" placeholder=""
                                                 value="{{$iku2}}"
                                                 aria-label="İslami uzman seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-6">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalad2">A. Denetçi
                                          </button>
                                          <input type="text" id="divad2" name="ad2" class="form-control" placeholder=""
                                                 value=""
                                                 aria-label="Aday denetçi seçiniz">
                                        </div>
                                      </div>
                                      <div class="col-sm-12">
                                        <div class="input-group">
                                          <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                  data-bs-toggle="modal"
                                                  data-bs-target="#myModalsid2">Değerlendirici
                                          </button>
                                          <input type="text" id="divsid2" name="sid2" class="form-control" placeholder=""
                                                 value=""
                                                 aria-label="Değerlendirici seçiniz">
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- 2) g1 sekmesi içeriği -->
                            <div class="tab-pane fade{{ $tab2show }}{{ $tab2 }}"
                                 id="navs-left-g1"
                                 role="tabpanel"
                            >
                              <div class="{{ $asama !== 'g1' ? 'readonly-fields' : '' }}">
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-2">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="tarihrevgozetim1"
                                               id="tarihrevgozetim1" value="{{$pot->tarihrevgozetim1}}"/>
                                        <label for="tarihrevgozetim1">Gözetim 1 bildirim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-4">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="gozetim1"
                                               id="gozetim1tar" value="{{$pot->gozetim1}}"/>
                                        <label for="gozetim1tar">Gözetim 1 denetim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-2">
                                    <div class="form-floating form-floating-outline">
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1"
                                               id="sistemversiyongecis" {{($pot->sistemversiyongecis === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="sistemversiyongecis">
                                          Sistem geçişi var mı?
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-2">
                                    <div class="form-floating form-floating-outline">
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1"
                                               id="kapsamgenisletme" name="kapsamgenisletme" {{($pot->kapsamgenisletme === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="kapsamgenisletme">
                                          Kapsam genişletme var mı?
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-2">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <select id="belgecevrimi" name="belgecevrimi" class="selectpicker w-100"
                                                data-style="btn-default">
                                          <option value="1" readonly selected>Çevrim seçiniz...
                                          </option>
                                          <option value="2">2. Çevrim</option>
                                          <option value="3">3. Çevrim</option>
                                          <option value="4">4. Çevrim</option>
                                          <option value="5">5. Çevrim</option>
                                        </select>
                                        <label for="belgecevrimi">Belge çevrimi</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgbd1">Başdenetçi
                                      </button>
                                      <input type="text" id="divgbd1" name="gbd1" class="form-control" placeholder=""
                                             value="{{$gbd1}}"
                                             aria-label="Başdenetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-8">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgd1">Denetçi(ler)
                                      </button>
                                      <input type="text" id="divgd1" name="gd1" class="form-control" placeholder=""
                                             value="{{$gd1}}"
                                             aria-label="Denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgtu1">T. UZMAN
                                      </button>
                                      <input type="text" id="divgtu1" name="gtu1" class="form-control" placeholder=""
                                             value="{{$gtu1}}"
                                             aria-label="Teknik uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgg1">Gözlemci
                                      </button>
                                      <input type="text" id="divgg1" name="gg1" class="form-control" placeholder=""
                                             value="{{$gg1}}"
                                             aria-label="Gözlemci seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgiku1">İ. Uzman
                                      </button>
                                      <input type="text" id="divgiku1" name="ikug1" class="form-control" placeholder=""
                                             value="{{$ikug1}}"
                                             aria-label="İslami uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgad1">A. Denetçi
                                      </button>
                                      <input type="text" id="divgad1" name="adg1" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Aday denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-12">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalsidg1">Değerlendirici
                                      </button>
                                      <input type="text" id="divsidg1" name="sidg1" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Değerlendirici seçiniz">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- 3) g2 sekmesi -->
                            <div class="tab-pane fade{{ $tab3show }}{{ $tab3 }}"
                                 id="navs-left-g2"
                                 role="tabpanel"
                            >
                              <div class="{{ $asama !== 'g2' ? 'readonly-fields' : '' }}">
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-2">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="tarihrevgozetim2"
                                               id="tarihrevgozetim2" value="{{$pot->tarihrevgozetim2}}"/>
                                        <label for="tarihrevgozetim2">Gözetim 2 bildirim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="gozetim2"
                                               id="gozetim2tar" value="{{$pot->gozetim2}}"/>
                                        <label for="gozetim2tar">Gözetim 2 denetim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgbd2">Başdenetçi
                                      </button>
                                      <input type="text" id="divgbd2" name="gbd2" class="form-control" placeholder=""
                                             value="{{$gbd2}}"
                                             aria-label="Başdenetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-8">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgd2">Denetçi(ler)
                                      </button>
                                      <input type="text" id="divgd2" name="gd2" class="form-control" placeholder=""
                                             value="{{$gd2}}"
                                             aria-label="Denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgtu2">T. UZMAN
                                      </button>
                                      <input type="text" id="divgtu2" name="gtu2" class="form-control" placeholder=""
                                             value="{{$gtu2}}"
                                             aria-label="Teknik uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgg2">Gözlemci
                                      </button>
                                      <input type="text" id="divgg2" name="gg2" class="form-control" placeholder=""
                                             value="{{$gg2}}"
                                             aria-label="Gözlemci seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgiku2">İ. Uzman
                                      </button>
                                      <input type="text" id="divgiku2" name="ikug2" class="form-control" placeholder=""
                                             value="{{$ikug2}}"
                                             aria-label="İslami uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalgad2">A. Denetçi
                                      </button>
                                      <input type="text" id="divgad2" name="adg2" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Aday denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-12">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalsidg2">Değerlendirici
                                      </button>
                                      <input type="text" id="divsidg2" name="sidg2" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Değerlendirici seçiniz">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- 4) yb sekmesi -->
                            <div class="tab-pane fade{{ $tab4show }}{{ $tab4 }}"
                                 id="navs-left-yb"
                                 role="tabpanel"
                            >
                              <div class="{{ $asama !== 'yb' ? 'readonly-fields' : '' }}">
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-2">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="tarihrevyb"
                                               id="tarihrevyb" value="{{$pot->tarihrevyb}}"/>
                                        <label for="tarihrevyb">Yeniden belgelendirme bildirim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="ybtar"
                                               id="ybtar" value="{{$pot->ybtar}}"/>
                                        <label for="ybtar">Yeniden belgelendirme denetim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybbd">Başdenetçi
                                      </button>
                                      <input type="text" id="divybbd" name="ybbd" class="form-control" placeholder=""
                                             value="{{$ybbd}}"
                                             aria-label="Başdenetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-8">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybd">Denetçi(ler)
                                      </button>
                                      <input type="text" id="divybd" name="ybd" class="form-control" placeholder=""
                                             value="{{$ybd}}"
                                             aria-label="Denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybtu">T. UZMAN
                                      </button>
                                      <input type="text" id="divybtu" name="ybtu" class="form-control" placeholder=""
                                             value="{{$ybtu}}"
                                             aria-label="Teknik uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybg">Gözlemci
                                      </button>
                                      <input type="text" id="divybg" name="ybg" class="form-control" placeholder=""
                                             value="{{$ybg}}"
                                             aria-label="Gözlemci seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybiku">İ. Uzman
                                      </button>
                                      <input type="text" id="divybiku" name="ikuyb" class="form-control" placeholder=""
                                             value="{{$ikuyb}}"
                                             aria-label="İslami uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalybad">A. Denetçi
                                      </button>
                                      <input type="text" id="divybad" name="adyb" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Aday denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-12">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalsidyb">Değerlendirici
                                      </button>
                                      <input type="text" id="divsidyb" name="sidyb" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Değerlendirici seçiniz">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <!-- 5) ozel sekmesi -->
                            <div class="tab-pane fade{{ $tab5show }}{{ $tab5 }}"
                                 id="navs-left-ozel"
                                 role="tabpanel"
                            >
                              <div class="{{ $asama !== 'ozel' ? 'readonly-fields' : '' }}">
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-2">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="tarihrevozel"
                                               id="tarihrevozel" value="{{$pot->tarihrevozel}}"/>
                                        <label for="tarihrevozel">Özel Denetim bildirim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-10">
                                    <div class="input-group input-group-merge">
                                      <div class="form-floating form-floating-outline">
                                        <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                               name="ozeltar"
                                               id="ozeltar" value="{{$pot->ozeltar}}"/>
                                        <label for="ozeltar">Özel Denetim denetim tarihi</label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-1 mb-1">
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalotbd">Başdenetçi
                                      </button>
                                      <input type="text" id="divotbd" name="otbd" class="form-control" placeholder=""
                                             value="{{$otbd}}"
                                             aria-label="Başdenetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-8">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalotd">Denetçi(ler)
                                      </button>
                                      <input type="text" id="divotd" name="otd" class="form-control" placeholder=""
                                             value="{{$otd}}"
                                             aria-label="Denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalottu">T. UZMAN
                                      </button>
                                      <input type="text" id="divottu" name="ottu" class="form-control" placeholder=""
                                             value="{{$ottu}}"
                                             aria-label="Teknik uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalotg">Gözlemci
                                      </button>
                                      <input type="text" id="divotg" name="otg" class="form-control" placeholder=""
                                             value="{{$otg}}"
                                             aria-label="Gözlemci seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalotiku">İ. Uzman
                                      </button>
                                      <input type="text" id="divotiku" name="ikuot" class="form-control" placeholder=""
                                             value="{{$ikuot}}"
                                             aria-label="İslami uzman seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalotad">A. Denetçi
                                      </button>
                                      <input type="text" id="divotad" name="adot" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Aday denetçi seçiniz">
                                    </div>
                                  </div>
                                  <div class="col-sm-12">
                                    <div class="input-group">
                                      <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                              data-bs-toggle="modal"
                                              data-bs-target="#myModalsidot">Değerlendirici
                                      </button>
                                      <input type="text" id="divsidot" name="sidot" class="form-control" placeholder=""
                                             value=""
                                             aria-label="Değerlendirici seçiniz">
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="card overflow-hidden mb-4" style="height: 300px;">
                              <h5 class="card-header">Denetçi Kontrol</h5>
                              <div class="card-body" id="vertical-div-scrollbar">
                                <div id="divdenetcikontrol"></div>
                              </div>
                            </div>

                          </div>
                        </div>

                        <div id="planlamatabs" class="nav-align-left">
                          {{--                          Tab headers--}}
                          <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                              <button type="button" class="nav-link{{$tab1button}}" role="tab" data-bs-toggle="tab"
                                      data-bs-target="#navs-left-ilkplan" aria-controls="navs-left-ilkplan"
                                      aria-selected="true">İlk
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button" class="nav-link{{$tab2button}}" role="tab" data-bs-toggle="tab"
                                      data-bs-target="#navs-left-g1" aria-controls="navs-left-g1" aria-selected="false">
                                1. Gözetim
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button" class="nav-link{{$tab3button}}" role="tab" data-bs-toggle="tab"
                                      data-bs-target="#navs-left-g2" aria-controls="navs-left-g2" aria-selected="false">
                                2. Gözetim
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button" class="nav-link{{$tab4button}}" role="tab" data-bs-toggle="tab"
                                      data-bs-target="#navs-left-yb" aria-controls="navs-left-yb" aria-selected="false">
                                Y.B.
                              </button>
                            </li>
                            <li class="nav-item">
                              <button type="button" class="nav-link{{$tab5button}}" role="tab" data-bs-toggle="tab"
                                      data-bs-target="#navs-left-ozel" aria-controls="navs-left-ozel"
                                      aria-selected="false">Özel Denetim
                              </button>
                            </li>
                          </ul>
                          {{--                          Tab body--}}
                          <div class="tab-content">
                            <div class="tab-pane fade{{$tab1show}}{{$tab1}}" id="navs-left-ilkplan">
                              <div class="card mb-4">
                                <h5 class="card-title">Aşama 1</h5>
                                <div class="card-body">
                                  <div class="row g-1 mb-1">
                                    <div class="col-sm-2">
                                      <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                          <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                 name="tarihrevasama1"
                                                 id="tarihrevasama1" value="{{$pot->tarihrevasama1}}"/>
                                          <label for="tarihrevasama1">Aşama 1 bildirim tarihi</label>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-sm-10">
                                      <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                          <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                 name="asama1"
                                                 id="asama1tar" value="{{$pot->asama1}}"/>
                                          <label for="asama1tar">Aşama 1 denetim tarihi</label>
                                        </div>
                                        <button class="btn btn-primary" type="button" onclick="denetcikontrol('asama1tar')">
                                          <span class="mdi mdi-database-search"></span>
                                        </button>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row g-1 mb-1">
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalbd1">Başdenetçi
                                        </button>
                                        <input type="text" id="divbd1" name="bd1" class="form-control" placeholder=""
                                               value="{{$bd1}}"
                                               aria-label="Başdenetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-8">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModald1">Denetçi(ler)
                                        </button>
                                        <input type="text" id="divd1" name="d1" class="form-control" placeholder=""
                                               value="{{$d1}}"
                                               aria-label="Denetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModaltu1">T. UZMAN
                                        </button>
                                        <input type="text" id="divtu1" name="tu1" class="form-control" placeholder=""
                                               value="{{$tu1}}"
                                               aria-label="Teknik uzman seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalg1">Gözlemci
                                        </button>
                                        <input type="text" id="divg1" name="g1" class="form-control" placeholder=""
                                               value="{{$g1}}"
                                               aria-label="Gözlemci seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModaliku1">İ. Uzman
                                        </button>
                                        <input type="text" id="diviku1" name="iku1" class="form-control" placeholder=""
                                               value="{{$iku1}}"
                                               aria-label="İslami uzman seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalad1">A. Denetçi
                                        </button>
                                        <input type="text" id="divad1" name="ad1" class="form-control" placeholder=""
                                               value=""
                                               aria-label="Aday denetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-12">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalsid1">Değerlendirici
                                        </button>
                                        <input type="text" id="divsid1" name="sid1" class="form-control" placeholder=""
                                               value=""
                                               aria-label="Değerlendirici seçiniz">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="card mb-4">
                                <h5 class="card-title">Aşama 2</h5>
                                <div class="card-body">
                                  <div class="row g-1 mb-1">
                                    <div class="col-sm-2">
                                      <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                          <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                 name="tarihrevasama2"
                                                 id="tarihrevasama2" value="{{$pot->tarihrevasama2}}"/>
                                          <label for="tarihrevasama2">Aşama 2 bildirim tarihi</label>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-sm-10">
                                      <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                          <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                                 name="asama2"
                                                 id="asama2tar" value="{{$pot->asama2}}"/>
                                          <label for="asama2tar">Aşama 2 denetim tarihi</label>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="row g-1 mb-1">
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalbd2">Başdenetçi
                                        </button>
                                        <input type="text" id="divbd2" name="bd2" class="form-control" placeholder=""
                                               value="{{$bd2}}"
                                               aria-label="Başdenetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-8">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModald2">Denetçi(ler)
                                        </button>
                                        <input type="text" id="divd2" name="d2" class="form-control" placeholder=""
                                               value="{{$d2}}"
                                               aria-label="Denetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModaltu2">T. UZMAN
                                        </button>
                                        <input type="text" id="divtu2" name="tu2" class="form-control" placeholder=""
                                               value="{{$tu2}}"
                                               aria-label="Teknik uzman seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalg2">Gözlemci
                                        </button>
                                        <input type="text" id="divg2" name="g2" class="form-control" placeholder=""
                                               value="{{$g2}}"
                                               aria-label="Gözlemci seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModaliku2">İ. Uzman
                                        </button>
                                        <input type="text" id="diviku2" name="iku2" class="form-control" placeholder=""
                                               value="{{$iku2}}"
                                               aria-label="İslami uzman seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-6">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalad2">A. Denetçi
                                        </button>
                                        <input type="text" id="divad2" name="ad2" class="form-control" placeholder=""
                                               value=""
                                               aria-label="Aday denetçi seçiniz">
                                      </div>
                                    </div>
                                    <div class="col-sm-12">
                                      <div class="input-group">
                                        <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#myModalsid2">Değerlendirici
                                        </button>
                                        <input type="text" id="divsid2" name="sid2" class="form-control" placeholder=""
                                               value=""
                                               aria-label="Değerlendirici seçiniz">
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade{{$tab2show}}{{$tab2}}" id="navs-left-g1">
                              <div class="row g-1 mb-1">
                                <div class="col-sm-2">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="tarihrevgozetim1"
                                             id="tarihrevgozetim1" value="{{$pot->tarihrevgozetim1}}"/>
                                      <label for="tarihrevgozetim1">Gözetim 1 bildirim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-4">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="gozetim1"
                                             id="gozetim1tar" value="{{$pot->gozetim1}}"/>
                                      <label for="gozetim1tar">Gözetim 1 denetim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-floating form-floating-outline">
                                    <div class="form-check mt-3">
                                      <input class="form-check-input" type="checkbox" value="1"
                                             id="sistemversiyongecis" {{($pot->sistemversiyongecis === 1) ? 'checked' : ''}} />
                                      <label class="form-check-label" for="sistemversiyongecis">
                                        Sistem geçişi var mı?
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="form-floating form-floating-outline">
                                    <div class="form-check mt-3">
                                      <input class="form-check-input" type="checkbox" value="1"
                                             id="kapsamgenisletme" name="kapsamgenisletme" {{($pot->kapsamgenisletme === 1) ? 'checked' : ''}} />
                                      <label class="form-check-label" for="kapsamgenisletme">
                                        Kapsam genişletme var mı?
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-2">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <select id="belgecevrimi" name="belgecevrimi" class="selectpicker w-100"
                                              data-style="btn-default">
                                        <option value="1" readonly selected>Çevrim seçiniz...
                                        </option>
                                        <option value="2">2. Çevrim</option>
                                        <option value="3">3. Çevrim</option>
                                        <option value="4">4. Çevrim</option>
                                        <option value="5">5. Çevrim</option>
                                      </select>
                                      <label for="belgecevrimi">Belge çevrimi</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row g-1 mb-1">
                                <div class="col-sm-4">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgbd1">Başdenetçi
                                    </button>
                                    <input type="text" id="divgbd1" name="gbd1" class="form-control" placeholder=""
                                           value="{{$gbd1}}"
                                           aria-label="Başdenetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-8">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgd1">Denetçi(ler)
                                    </button>
                                    <input type="text" id="divgd1" name="gd1" class="form-control" placeholder=""
                                           value="{{$gd1}}"
                                           aria-label="Denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgtu1">T. UZMAN
                                    </button>
                                    <input type="text" id="divgtu1" name="gtu1" class="form-control" placeholder=""
                                           value="{{$gtu1}}"
                                           aria-label="Teknik uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgg1">Gözlemci
                                    </button>
                                    <input type="text" id="divgg1" name="gg1" class="form-control" placeholder=""
                                           value="{{$gg1}}"
                                           aria-label="Gözlemci seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgiku1">İ. Uzman
                                    </button>
                                    <input type="text" id="divgiku1" name="ikug1" class="form-control" placeholder=""
                                           value="{{$ikug1}}"
                                           aria-label="İslami uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgad1">A. Denetçi
                                    </button>
                                    <input type="text" id="divgad1" name="adg1" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Aday denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalsidg1">Değerlendirici
                                    </button>
                                    <input type="text" id="divsidg1" name="sidg1" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Değerlendirici seçiniz">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade{{$tab3show}}{{$tab3}}" id="navs-left-g2">
                              <div class="row g-1 mb-1">
                                <div class="col-sm-2">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="tarihrevgozetim2"
                                             id="tarihrevgozetim2" value="{{$pot->tarihrevgozetim2}}"/>
                                      <label for="tarihrevgozetim2">Gözetim 2 bildirim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-10">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="gozetim2"
                                             id="gozetim2tar" value="{{$pot->gozetim2}}"/>
                                      <label for="gozetim2tar">Gözetim 2 denetim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row g-1 mb-1">
                                <div class="col-sm-4">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgbd2">Başdenetçi
                                    </button>
                                    <input type="text" id="divgbd2" name="gbd2" class="form-control" placeholder=""
                                           value="{{$gbd2}}"
                                           aria-label="Başdenetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-8">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgd2">Denetçi(ler)
                                    </button>
                                    <input type="text" id="divgd2" name="gd2" class="form-control" placeholder=""
                                           value="{{$gd2}}"
                                           aria-label="Denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgtu2">T. UZMAN
                                    </button>
                                    <input type="text" id="divgtu2" name="gtu2" class="form-control" placeholder=""
                                           value="{{$gtu2}}"
                                           aria-label="Teknik uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgg2">Gözlemci
                                    </button>
                                    <input type="text" id="divgg2" name="gg2" class="form-control" placeholder=""
                                           value="{{$gg2}}"
                                           aria-label="Gözlemci seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgiku2">İ. Uzman
                                    </button>
                                    <input type="text" id="divgiku2" name="ikug2" class="form-control" placeholder=""
                                           value="{{$ikug2}}"
                                           aria-label="İslami uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalgad2">A. Denetçi
                                    </button>
                                    <input type="text" id="divgad2" name="adg2" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Aday denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalsidg2">Değerlendirici
                                    </button>
                                    <input type="text" id="divsidg2" name="sidg2" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Değerlendirici seçiniz">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade{{$tab4show}}{{$tab4}}" id="navs-left-yb">
                              <div class="row g-1 mb-1">
                                <div class="col-sm-2">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="tarihrevyb"
                                             id="tarihrevyb" value="{{$pot->tarihrevyb}}"/>
                                      <label for="tarihrevyb">Yeniden belgelendirme bildirim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-10">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="ybtar"
                                             id="ybtar" value="{{$pot->ybtar}}"/>
                                      <label for="ybtar">Yeniden belgelendirme denetim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row g-1 mb-1">
                                <div class="col-sm-4">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybbd">Başdenetçi
                                    </button>
                                    <input type="text" id="divybbd" name="ybbd" class="form-control" placeholder=""
                                           value="{{$ybbd}}"
                                           aria-label="Başdenetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-8">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybd">Denetçi(ler)
                                    </button>
                                    <input type="text" id="divybd" name="ybd" class="form-control" placeholder=""
                                           value="{{$ybd}}"
                                           aria-label="Denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybtu">T. UZMAN
                                    </button>
                                    <input type="text" id="divybtu" name="ybtu" class="form-control" placeholder=""
                                           value="{{$ybtu}}"
                                           aria-label="Teknik uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybg">Gözlemci
                                    </button>
                                    <input type="text" id="divybg" name="ybg" class="form-control" placeholder=""
                                           value="{{$ybg}}"
                                           aria-label="Gözlemci seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybiku">İ. Uzman
                                    </button>
                                    <input type="text" id="divybiku" name="ikuyb" class="form-control" placeholder=""
                                           value="{{$ikuyb}}"
                                           aria-label="İslami uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalybad">A. Denetçi
                                    </button>
                                    <input type="text" id="divybad" name="adyb" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Aday denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalsidyb">Değerlendirici
                                    </button>
                                    <input type="text" id="divsidyb" name="sidyb" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Değerlendirici seçiniz">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="tab-pane fade{{$tab5show}}{{$tab5}}" id="navs-left-ozel">
                              <div class="row g-1 mb-1">
                                <div class="col-sm-2">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="tarihrevozel"
                                             id="tarihrevozel" value="{{$pot->tarihrevozel}}"/>
                                      <label for="tarihrevozel">Özel Denetim bildirim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-sm-10">
                                  <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" class="form-control" placeholder="DD.MM.YYYY"
                                             name="ozeltar"
                                             id="ozeltar" value="{{$pot->ozeltar}}"/>
                                      <label for="ozeltar">Özel Denetim denetim tarihi</label>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row g-1 mb-1">
                                <div class="col-sm-4">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalotbd">Başdenetçi
                                    </button>
                                    <input type="text" id="divotbd" name="otbd" class="form-control" placeholder=""
                                           value="{{$otbd}}"
                                           aria-label="Başdenetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-8">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalotd">Denetçi(ler)
                                    </button>
                                    <input type="text" id="divotd" name="otd" class="form-control" placeholder=""
                                           value="{{$otd}}"
                                           aria-label="Denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalottu">T. UZMAN
                                    </button>
                                    <input type="text" id="divottu" name="ottu" class="form-control" placeholder=""
                                           value="{{$ottu}}"
                                           aria-label="Teknik uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalotg">Gözlemci
                                    </button>
                                    <input type="text" id="divotg" name="otg" class="form-control" placeholder=""
                                           value="{{$otg}}"
                                           aria-label="Gözlemci seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalotiku">İ. Uzman
                                    </button>
                                    <input type="text" id="divotiku" name="ikuot" class="form-control" placeholder=""
                                           value="{{$ikuot}}"
                                           aria-label="İslami uzman seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-6">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalotad">A. Denetçi
                                    </button>
                                    <input type="text" id="divotad" name="adot" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Aday denetçi seçiniz">
                                  </div>
                                </div>
                                <div class="col-sm-12">
                                  <div class="input-group">
                                    <button class="btn btn-outline-primary" style="width: 135px" type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#myModalsidot">Değerlendirici
                                    </button>
                                    <input type="text" id="divsidot" name="sidot" class="form-control" placeholder=""
                                           value=""
                                           aria-label="Değerlendirici seçiniz">
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="card overflow-hidden mb-4" style="height: 300px;">
                              <h5 class="card-header">Denetçi Kontrol</h5>
                              <div class="card-body" id="vertical-div-scrollbar">
                                <div id="divdenetcikontrol"></div>
                              </div>
                            </div>
                          </div>
                        </div>

                      </div>
                    </div>
                  </div>
                </div>
                {{--                Tabs - Denetim ekibi seçimi bitişi--}}

                @if($iso2200018)
                  <div class="card">
                    <h5 class="card-header">
                      ISO 22000 ek bilgiler
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <div class="row g-4">
{{--                        <div class="col-sm-4">--}}
{{--                          <div class="form-floating form-floating-outline">--}}
{{--                            <div class="form-check mt-3">--}}
{{--                              <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                     id="chb_mysvarmi" {{($pot->yonetimsistemsertifikasi === 1) ? 'checked' : ''}} />--}}
{{--                              <label class="form-check-label" for="chb_mysvarmi">--}}
{{--                                Mevcut yönetim sistemi sertifikanız var mı?--}}
{{--                              </label>--}}
{{--                            </div>--}}
{{--                          </div>--}}
{{--                        </div>--}}
                        <div class="col-sm-4">
                          <div class="form-floating form-floating-outline">
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="haccpcalismasisayisi" id="haccpcalismasisayisi"
                                     class="form-control"
                                     placeholder=""
                                     value="{{$pot->haccpcalismasisayisi}}"/>
                              <label for="haccpcalismasisayisi">22000 HACCP çalışması sayısı</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-4">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="sahasayisi22"
                                   id="sahasayisi22"
                                   class="form-control" placeholder=""
                                   value="{{$pot->sahasayisi22}}"/>
                            <label for="sahasayisi22">22000 saha sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-4">

                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($oicsmiic | $oicsmiic6 | $oicsmiic9 | $oicsmiic171 | $oicsmiic24)
                  <div class="card">
                    <h5 class="card-header">
                      OIC/SMIIC ek bilgiler
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <div class="row g-4">
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="haccpcalismasisayisismiic"
                                   id="haccpcalismasisayisismiic"
                                   class="form-control" placeholder=""
                                   value="{{$pot->haccpcalismasisayisismiic}}"/>
                            <label for="haccpcalismasisayisismiic">SMIIC HACCP çalışması
                              sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="helalurunsayisi" id="helalurunsayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$pot->helalurunsayisi}}"/>
                            <label for="helalurunsayisi">SMIIC ürün sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="sahasayisi22" id="oic_sahasayisi22"
                                   class="form-control" placeholder=""
                                   value="{{$pot->sahasayisi22}}"/>
                            <label for="sahasayisi22">Oic/Smiic saha sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="oicsmiickk" id="oicsmiickk" class="form-control"
                                   placeholder=""
                                   value="{{$pot->oicsmiickk}}"/>
                            <label for="oicsmiickk">Karmaşıklık Kategorisi</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="helalkkn" id="helalkkn" class="form-control"
                                   placeholder=""
                                   value="{{$pot->helalkkn}}"/>
                            <label for="helalkkn">Helal kkn</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="aracsayisi" id="aracsayisi" class="form-control"
                                   placeholder=""
                                   value="{{$pot->aracsayisi}}"/>
                            <label for="aracsayisi">OIC/SMIIC 17-1 için Araç Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="havuzsayisi" id="havuzsayisi" class="form-control"
                                   placeholder="" value="{{$pot->havuzsayisi}}"/>
                            <label for="havuzsayisi">Havuz Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="mutfaksayisi" id="mutfaksayisi" class="form-control"
                                   placeholder="" value="{{$pot->mutfaksayisi}}"/>
                            <label for="mutfaksayisi">Mutfak Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="odasayisi" id="odasayisi" class="form-control"
                                   placeholder="" value="{{$pot->odasayisi}}"/>
                            <label for="odasayisi">Oda Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="hizmetkategorisi" id="hizmetkategorisi" class="form-control"
                                   placeholder="" value="{{$pot->hizmetkategorisi}}"/>
                            <label for="hizmetkategorisi">Hizmet Kategorisi</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($iso50001)
                  <div class="card">
                    <h5 class="card-header">
                      ISO 50001 ek bilgiler
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <div class="row g-4">
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="enyscalisansayisi" id="enyscalisansayisi" class="form-control"
                                   placeholder="" value="{{$pot->enyscalisansayisi}}"/>
                            <label for="enyscalisansayisi">EnYS çalışan</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="yillikenerjituketimi" id="yillikenerjituketimi"
                                   class="form-control"
                                   placeholder="" value="{{$pot->yillikenerjituketimi}}"/>
                            <label for="yillikenerjituketimi">Yıllık tüketim(TEP)</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="enerjikaynaksayisi" id="enerjikaynaksayisi" class="form-control"
                                   placeholder="" value="{{$pot->enerjikaynaksayisi}}"/>
                            <label for="enerjikaynaksayisi">Kull. Enerji Kaynak Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="oeksayisi" id="oeksayisi" class="form-control"
                                   placeholder="" value="{{$pot->oeksayisi}}"/>
                            <label for="oeksayisi">ÖEK Sayısı</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                @if($iso27001)
                  <div class="card">
                    <h5 class="card-header">
                      ISO 27001 ek bilgiler
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <div class="row g-4">
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="bgyscalisansayisi" id="bgyscalisansayisi" class="form-control"
                                   placeholder="" value="{{$pot->bgyscalisansayisi}}"/>
                            <label for="bgyscalisansayisi">BGYS çalışan</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="soarevnotarihi" id="soarevnotarihi" class="form-control"
                                   placeholder="" value="{{$pot->soarevnotarihi}}"/>
                            <label for="soarevnotarihi">SoA Revizyon No/Tarihi</label>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="card">
                  <h5 class="card-header">
                    Azaltma/Arttırma Seçenekleri
                  </h5>
                  <div class="card-datatable text-nowrap">
                    <div class="row g-4">
                      @if($iso900115)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal9001indart">
                                ISO 9001
                              </button>
                              <input type="text" id="totoran9001" name="totoran9001" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 9001 Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($iso1400115)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal14001indart">
                                ISO 14001
                              </button>
                              <input type="text" id="totoran14001" name="totoran14001" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 14001 Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($iso45001)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal45001indart">
                                ISO 45001
                              </button>
                              <input type="text" id="totoran45001" name="totoran45001" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 45001 Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($iso50001)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal50001indart">
                                ISO 50001
                              </button>
                              <input type="text" id="totoran50001" name="totoran50001" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 50001 Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($iso27001)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal27001indart">
                                ISO 27001
                              </button>
                              <input type="text" id="totoran27001" name="totoran27001" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 27001 Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($iso2200018)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal22000indart">
                                ISO 22000
                              </button>
                              <input type="text" id="totoran22000" name="totoran22000" class="form-control"
                                     placeholder="" value="%+-0" aria-label="ISO 22000 arttırım seçenekleri" readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($oicsmiic | $oicsmiic6 | $oicsmiic9 | $oicsmiic171 | $oicsmiic24 | $oicsmiic24)
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modalOicsmiicindart">
                                OIC/SMIIC
                              </button>
                              <input type="text" id="totoranoicsmiic" name="totoranoicsmiic" class="form-control"
                                     placeholder="" value="%+-0" aria-label="OIC/SMIIC Azaltma/Arttırma seçenekleri"
                                     readonly>
                            </div>
                          </div>
                        </div>
                      @endif
                      @if($sistemsay > 1)
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="input-group">
                              <button class="btn btn-outline-primary" type="button"
                                      data-bs-toggle="modal"
                                      data-bs-target="#modal1entegreindart">
                                Entegrasyon Hesabı
                              </button>
                              <input type="text" id="totoranEntegre" name="totoranEntegre" class="form-control"
                                     placeholder="" value="%+-0" aria-label="Entegrasyon indirim seçenekleri"
                                     readonly>
                              <button type="button" class="btn btn-icon btn-success btn-fab demo"
                                      onclick="indartHesaplaEntegreYenile()">
                                <span class="tf-icons mdi mdi-reload mdi-24px"></span>
                              </button>
                            </div>
                          </div>
                        </div>
                      @endif
                    </div>
                    @if($inceleneceksahasayisi === 0)
                      <div class="row g-4 mt-1">
                      <div class="col-sm-4">
                        <div class="input-group input-group-merge">
                          <div class="form-floating form-floating-outline">
                            <input type="text" id="denetimgunazaltilmasi" name="denetimgunazaltilmasi"
                                   class="form-control form-control-sm"
                                   placeholder="" readonly
                                   value="0.0"/>
                            <label for="denetimgunazaltilmasi">Azaltma toplamı</label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group input-group-merge">
                          <div class="form-floating form-floating-outline">
                            <input type="text" id="denetimgunarttirilmasi" name="denetimgunarttirilmasi"
                                   class="form-control form-control-sm"
                                   placeholder="" readonly
                                   value="0.0"/>
                            <label for="denetimgunarttirilmasi">Arttırma toplamı</label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-4">
                        <div class="input-group input-group-merge">
                          <div class="form-floating form-floating-outline">
                            <input type="text" id="denetimentegreindirim" name="denetimentegreindirim"
                                   class="form-control form-control-sm"
                                   placeholder="" readonly
                                   value="0.0"/>
                            <label for="denetimentegreindirim">Entegre indirim toplamı</label>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
                  </div>
                </div>

                <div class="card">
                  <h5 class="card-header">
                    Denetim Gün Hesaplama
                    <span id="sureHesaplaSpinner" class="spinner-border me-1 text-danger" role="status" aria-hidden="true" style="display:none"></span>
                    <button type="button" class="btn btn-xs btn-success btn-fab demo"
                            onclick="hesapla()">
                      <span class="tf-icons mdi mdi-calculator mdi-24px"></span> Hesapla
                    </button>
                    Toplam saha sayısı:  {{$inceleneceksahasayisi}}, Denetlenecek saha sayısı:  {{$inceleneceksahasayisisec}}
                  </h5>
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive text-nowrap">
                      <table class="table">
                        <thead>
                        <tr class="text-nowrap">
                          <th style="max-width: 220px">İlgili Standard</th>
                          <th style="width: 100px" class="text-center">Hes. Baz <br/>Zaman</th>
                          <th style="width: 100px" class="text-center">Az./Art. <br/>Miktarı</th>
                          <th style="width: 100px" class="text-center">Az./Art. <br/>Zaman</th>
                          <th style="width: 100px" class="text-center">Ent. Az. <br/>Miktarı</th>
                          <th style="width: 100px" class="text-center">Kalan</th>
                          <th style="width: 100px" class="text-center">Aşama 1</th>
                          <th style="width: 100px" class="text-center">Aşama 2</th>
                          <th style="width: 100px" class="text-center">Gözetim</th>
                          <th style="width: 100px" class="text-center">Y. Belgel.</th>
                        </tr>
                        </thead>
                        <tbody id="denetimZamanHesaplari" class="table-border-bottom-0">
                        @if($iso900115)
                          <tr>
                            <th scope="row" id="tooltip9001"
                                title="<?= isset($_SESSION["tooltip9001"]) ?? $_SESSION["tooltip9001"]; ?>"
                                data-tooltip=""
                                aria-haspopup="true" class="has-tip tip-bottom radius">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO 9001</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso9001SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <span id="spantip9001"
                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip9001"]) ?? $_SESSION["tooltip9001"]; ?></span>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso9001hamsure" name="iso9001hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso9001indart" type="text"
                                     name="iso9001indart" value="0.0" class="form-control form-control-sm"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso9001azartsure" type="text"
                                     name="iso9001azartsure" value="0.0" class="form-control form-control-sm"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso9001entindart" type="text"
                                     name="iso9001entindart" value="0.0" class="form-control form-control-sm"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso9001kalansure" type="text"
                                     name="iso9001kalansure" value="0.0" style="text-align: right"
                                     class="form-control form-control-sm"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso9001a1sure" class="form-control form-control-sm" type="text"
                                     name="iso9001a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso9001a2sure" class="form-control form-control-sm" type="text"
                                     name="iso9001a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso9001gsure" class="form-control form-control-sm" type="text"
                                     name="iso9001gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso9001ybsure" class="form-control form-control-sm" type="text"
                                     name="iso9001ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($iso1400115)
                          <tr>
                            <th scope="row" id="tooltip14001" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltip14001"]) ?? $_SESSION["tooltip14001"]; ?>">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO 14001</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso14001SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <span id="spantip14001"
                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip14001"]) ?? $_SESSION["tooltip14001"]; ?></span>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso14001hamsure" name="iso14001hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001indart" type="text"
                                     name="iso14001indart" value="0.0" class="form-control form-control-sm"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso14001azartsure" type="text"
                                     name="iso14001azartsure"
                                     value="0.0" class="form-control form-control-sm"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001entindart" type="text"
                                     name="iso14001entindart" value="0.0" class="form-control form-control-sm"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso14001kalansure" type="text"
                                     name="iso14001kalansure"
                                     value="0.0" class="form-control form-control-sm"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001a1sure" class="form-control form-control-sm" type="text"
                                     name="iso14001a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001a2sure" class="form-control form-control-sm" type="text"
                                     name="iso14001a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001gsure" class="form-control form-control-sm" type="text"
                                     name="iso14001gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso14001ybsure" class="form-control form-control-sm" type="text"
                                     name="iso14001ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($iso2200018)
                          <tr>
                            <th scope="row" id="tooltip22000" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltip22000"]) ?? $_SESSION["tooltip22000"]; ?>">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO 22000</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso22000SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <span id="spantip22000"
                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip22000"]) ?? $_SESSION["tooltip22000"]; ?></span>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso22000hamsure" name="iso22000hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000indart" type="text"
                                     name="iso22000indart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso22000azartsure" type="text"
                                     name="iso22000azartsure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000entindart" type="text"
                                     name="iso22000entindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso22000kalansure" type="text"
                                     name="iso22000kalansure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000a1sure" class="form-control form-control-sm" type="text"
                                     name="iso22000a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000a2sure" class="form-control form-control-sm" type="text"
                                     name="iso22000a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000gsure" class="form-control form-control-sm" type="text"
                                     name="iso22000gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso22000ybsure" class="form-control form-control-sm" type="text"
                                     name="iso22000ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($iso45001)
                          <tr>
                            <th scope="row" id="tooltip45001" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltip45001"]) ?? $_SESSION["tooltip45001"]; ?>">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO 45001</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso45001SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <span id="spantip45001"
                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip45001"]) ?? $_SESSION["tooltip45001"]; ?></span>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso45001hamsure" name="iso45001hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>

                            </td>
                            <td>
                              <input id="iso45001indart" type="text"
                                     name="iso45001indart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso45001azartsure" type="text"
                                     name="iso45001azartsure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso45001entindart" type="text"
                                     name="iso45001entindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso45001kalansure" type="text"
                                     name="iso45001kalansure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso45001a1sure" class="form-control form-control-sm" type="text"
                                     name="iso45001a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso45001a2sure" class="form-control form-control-sm" type="text"
                                     name="iso45001a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso45001gsure" class="form-control form-control-sm" type="text"
                                     name="iso45001gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso45001ybsure" class="form-control form-control-sm" type="text"
                                     name="iso45001ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($iso50001)
                          <tr>
                            <th scope="row" id="tooltip50001" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltip50001"]) ?? $_SESSION["tooltip50001"]; ?>">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO 50001</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso50001SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <span id="spantip50001"
                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip50001"]) ?? $_SESSION["tooltip50001"]; ?></span>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso50001hamsure" name="iso50001hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>

                            </td>
                            <td>
                              <input id="iso50001indart" type="text"
                                     name="iso50001indart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso50001azartsure" type="text"
                                     name="iso50001azartsure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso50001entindart" type="text"
                                     name="iso50001entindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso50001kalansure" type="text"
                                     name="iso50001kalansure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso50001a1sure" class="form-control form-control-sm" type="text"
                                     name="iso50001a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso50001a2sure" class="form-control form-control-sm" type="text"
                                     name="iso50001a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso50001gsure" class="form-control form-control-sm" type="text"
                                     name="iso50001gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso50001ybsure" class="form-control form-control-sm" type="text"
                                     name="iso50001ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($iso27001)
                          <tr>
                            <th scope="row" id="tooltip27001" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltip27001"]) ?? $_SESSION["tooltip27001"]; ?>">
                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">ISO/IEC 27001</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="iso27001SureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
{{--                              <span id="spantip27001"--}}
{{--                                    class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip27001"]) ?? $_SESSION["tooltip27001"]; ?></span>--}}
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="iso27001hamsure" name="iso27001hamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>

                            </td>
                            <td>
                              <input id="iso27001indart" type="text"
                                     name="iso27001indart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso27001azartsure" type="text"
                                     name="iso27001azartsure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso27001entindart" type="text"
                                     name="iso27001entindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="iso27001kalansure" type="text"
                                     name="iso27001kalansure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso27001a1sure" class="form-control form-control-sm" type="text"
                                     name="iso27001a1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso27001a2sure" class="form-control form-control-sm" type="text"
                                     name="iso27001a2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso27001gsure" class="form-control form-control-sm" type="text"
                                     name="iso27001gsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="iso27001ybsure" class="form-control form-control-sm" type="text"
                                     name="iso27001ybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($oicsmiic | $oicsmiic6 | $oicsmiic9 | $oicsmiic171 | $oicsmiic24 | $oicsmiic24)
                          <tr>
                            <th scope="row" id="tooltipoicsmiic" class="has-tip" data-tooltip aria-haspopup="true"
                                title="<?= isset($_SESSION["tooltipOicsmiic"]) ?? $_SESSION["tooltipOicsmiic"]; ?>">

                              <div class="input-group">
                                <button class="btn btn-text-dark btn-xs">OIC/SMIIC</button>
                                <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                        onclick="isoOicSmiicSureHesapla()">
                                  <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                </button>
                              </div>
                              <br/>
                              <div id="spantipoicsmiic"
                                   class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltipOicsmiic"]) ?? $_SESSION["tooltipOicsmiic"]; ?></div>
                            </th>
                            <td style="text-align: right">
                              <input type="text" id="oicsmiichamsure" name="oicsmiichamsure" value="0.0"
                                     class="form-control form-control-sm" style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiicindart" type="text"
                                     name="oicsmiicindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="oicsmiicazartsure" type="text"
                                     name="oicsmiicazartsure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiicentindart" type="text"
                                     name="oicsmiicentindart" class="form-control form-control-sm" value="0.0"
                                     style="text-align: right"
                                     readonly/>
                            </td>
                            <td>
                              <input id="oicsmiickalansure" type="text"
                                     name="oicsmiickalansure" class="form-control form-control-sm"
                                     value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiica1sure" class="form-control form-control-sm" type="text"
                                     name="oicsmiica1sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiica2sure" class="form-control form-control-sm" type="text"
                                     name="oicsmiica2sure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiicgsure" class="form-control form-control-sm" type="text"
                                     name="oicsmiicgsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                            <td>
                              <input id="oicsmiicybsure" class="form-control form-control-sm" type="text"
                                     name="oicsmiicybsure" value="0.0"
                                     style="text-align: right" readonly/>
                            </td>
                          </tr>
                        @endif
                        @if($inceleneceksahasayisi > 0)
                          @for($i = 1; $i<=$inceleneceksahasayisi;$i++)
                            <input id="sube<?= $i ?>calsay" type="hidden" value="<?= $sahaa[$i] + $sahab[$i] + $sahac[$i]; ?>"
                                   class="form-control" style="text-align: right" readonly/>

                            @if($iso900115)
                              <tr>
                                <th scope="row" id="tooltip9001{{$i}}" class="has-tip" data-tooltip aria-haspopup="true"
                                    title="<?= isset($_SESSION["tooltip9001{{$i}}"]) ?? $_SESSION["tooltip9001{{$i}}"]; ?>">
                                  <div class="input-group">
                                    <div class="input-group-text form-check mb-0">
                                      <input class="form-check-input m-auto" id="chkSube{{$i}}iso9001" type="checkbox" value="" aria-label="Checkbox for following text input" onclick="subeSurelerEkle()">
                                    </div>
                                    <button class="btn btn-text-dark btn-xs">ISO 9001-Saha {{$i}}</button>
                                    <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                            onclick="iso9001SahaSureHesapla(<?= $inceleneceksahasayisi; ?>)">
                                      <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                    </button>
                                  </div>
                                  <br/>
                                  <span id="spantip9001{{$i}}"
                                        class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip9001{{$i}}"]) ?? $_SESSION["tooltip9001{{$i}}"]; ?></span>
                                </th>
                                <td style="text-align: right">
                                  <input type="text" id="iso9001hamsure{{$i}}" name="iso9001hamsure{{$i}}" value="0.0"
                                         class="form-control form-control-sm" style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso9001indart{{$i}}" type="text"
                                         name="iso9001indart{{$i}}" value="0.0" class="form-control form-control-sm"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso9001azartsure{{$i}}" type="text"
                                         name="iso9001azartsure{{$i}}" value="0.0" class="form-control form-control-sm"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso9001entindart{{$i}}" type="text"
                                         name="iso9001entindart{{$i}}" value="0.0" class="form-control form-control-sm"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso9001kalansure{{$i}}" type="text"
                                         name="iso9001kalansure{{$i}}" value="0.0" style="text-align: right"
                                         class="form-control form-control-sm"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso9001a1sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso9001a1sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso9001a2sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso9001a2sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso9001gsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso9001gsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso9001ybsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso9001ybsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                              </tr>
                            @endif
                            @if($iso1400115)
                              <tr>
                                <th scope="row" id="tooltip14001{{$i}}" class="has-tip" data-tooltip aria-haspopup="true"
                                    title="<?= isset($_SESSION["tooltip14001{{$i}}"]) ?? $_SESSION["tooltip14001{{$i}}"]; ?>">
                                  <div class="input-group">
                                    <div class="input-group-text form-check mb-0">
                                      <input class="form-check-input m-auto" id="chkSube{{$i}}iso14001" type="checkbox" value="" aria-label="Checkbox for following text input" onclick="subeSurelerEkle()">
                                    </div>
                                    <button class="btn btn-text-dark btn-xs">ISO 14001-Saha {{$i}}</button>
                                    <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                            onclick="iso14001SahaSureHesapla(<?= $inceleneceksahasayisi; ?>)">
                                      <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                    </button>
                                  </div>
                                  <br/>
                                  <span id="spantip14001{{$i}}"
                                        class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip14001{{$i}}"]) ?? $_SESSION["tooltip14001{{$i}}"]; ?></span>
                                </th>
                                <td style="text-align: right">
                                  <input type="text" id="iso14001hamsure{{$i}}" name="iso14001hamsure{{$i}}" value="0.0"
                                         class="form-control form-control-sm" style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001indart{{$i}}" type="text"
                                         name="iso14001indart{{$i}}" value="0.0" class="form-control form-control-sm"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso14001azartsure{{$i}}" type="text"
                                         name="iso14001azartsure{{$i}}"
                                         value="0.0" class="form-control form-control-sm"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001entindart{{$i}}" type="text"
                                         name="iso14001entindart{{$i}}" value="0.0" class="form-control form-control-sm"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso14001kalansure{{$i}}" type="text"
                                         name="iso14001kalansure{{$i}}"
                                         value="0.0" class="form-control form-control-sm"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001a1sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso14001a1sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001a2sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso14001a2sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001gsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso14001gsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso14001ybsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso14001ybsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                              </tr>
                            @endif
                            @if($iso45001)
                              <tr>
                                <th scope="row" id="tooltip45001{{$i}}" class="has-tip" data-tooltip aria-haspopup="true"
                                    title="<?= isset($_SESSION["tooltip45001{{$i}}"]) ?? $_SESSION["tooltip45001{{$i}}"]; ?>">
                                  <div class="input-group">
                                    <div class="input-group-text form-check mb-0">
                                      <input class="form-check-input m-auto" id="chkSube{{$i}}iso45001" type="checkbox" value="" aria-label="Checkbox for following text input" onclick="subeSurelerEkle()">
                                    </div>
                                    <button class="btn btn-text-dark btn-xs">ISO 45001-Saha {{$i}}</button>
                                    <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                            onclick="iso45001SahaSureHesapla(<?= $inceleneceksahasayisi; ?>)">
                                      <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                    </button>
                                  </div>
                                  <br/>
                                  <span id="spantip45001{{$i}}"
                                        class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip45001{{$i}}"]) ?? $_SESSION["tooltip45001{{$i}}"]; ?></span>
                                </th>
                                <td style="text-align: right">
                                  <input type="text" id="iso45001hamsure{{$i}}" name="iso45001hamsure{{$i}}" value="0.0"
                                         class="form-control form-control-sm" style="text-align: right" readonly/>

                                </td>
                                <td>
                                  <input id="iso45001indart{{$i}}" type="text"
                                         name="iso45001indart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso45001azartsure{{$i}}" type="text"
                                         name="iso45001azartsure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso45001entindart{{$i}}" type="text"
                                         name="iso45001entindart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso45001kalansure{{$i}}" type="text"
                                         name="iso45001kalansure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso45001a1sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso45001a1sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso45001a2sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso45001a2sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso45001gsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso45001gsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso45001ybsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso45001ybsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                              </tr>
                            @endif
                            @if($iso50001)
                              <tr>
                                <th scope="row" id="tooltip50001{{$i}}" class="has-tip" data-tooltip aria-haspopup="true"
                                    title="<?= isset($_SESSION["tooltip50001{{$i}}"]) ?? $_SESSION["tooltip50001{{$i}}"]; ?>">
                                  <div class="input-group">
                                    <div class="input-group-text form-check mb-0">
                                      <input class="form-check-input m-auto" id="chkSube{{$i}}iso50001" type="checkbox" value="" aria-label="Checkbox for following text input" onclick="subeSurelerEkle()">
                                    </div>
                                    <button class="btn btn-text-dark btn-xs">ISO 50001-Saha {{$i}}</button>
                                    <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                            onclick="iso50001SahaSureHesapla(<?= $inceleneceksahasayisi; ?>)">
                                      <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                    </button>
                                  </div>
                                  <br/>
                                  <span id="spantip50001{{$i}}"
                                        class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip50001{{$i}}"]) ?? $_SESSION["tooltip50001{{$i}}"]; ?></span>
                                </th>
                                <td style="text-align: right">
                                  <input type="text" id="iso50001hamsure{{$i}}" name="iso50001hamsure{{$i}}" value="0.0"
                                         class="form-control form-control-sm" style="text-align: right" readonly/>

                                </td>
                                <td>
                                  <input id="iso50001indart{{$i}}" type="text"
                                         name="iso50001indart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso50001azartsure{{$i}}" type="text"
                                         name="iso50001azartsure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso50001entindart{{$i}}" type="text"
                                         name="iso50001entindart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso50001kalansure{{$i}}" type="text"
                                         name="iso50001kalansure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso50001a1sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso50001a1sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso50001a2sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso50001a2sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso50001gsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso50001gsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso50001ybsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso50001ybsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                              </tr>
                            @endif
                            @if($iso27001)
                              <tr>
                                <th scope="row" id="tooltip27001{{$i}}" class="has-tip" data-tooltip aria-haspopup="true"
                                    title="<?= isset($_SESSION["tooltip27001{{$i}}"]) ?? $_SESSION["tooltip27001{{$i}}"]; ?>">
                                  <div class="input-group">
                                    <div class="input-group-text form-check mb-0">
                                      <input class="form-check-input m-auto" id="chkSube{{$i}}iso27001" type="checkbox" value="" aria-label="Checkbox for following text input" onclick="subeSurelerEkle()">
                                    </div>
                                    <button class="btn btn-text-dark btn-xs">ISO/IEC 27001-Saha {{$i}}</button>
                                    <button type="button" class="btn btn-xs btn-icon btn-success btn-fab demo"
                                            onclick="iso27001SahaSureHesapla(<?= $inceleneceksahasayisi; ?>)">
                                      <span class="tf-icons mdi mdi-reload mdi-14px"></span>
                                    </button>
                                  </div>
                                  <br/>
{{--                                  <span id="spantip27001{{$i}}"--}}
{{--                                        class="form-text text-sm-left text-wrap"><?= isset($_SESSION["tooltip27001{{$i}}"]) ?? $_SESSION["tooltip27001{{$i}}"]; ?></span>--}}
                                </th>
                                <td style="text-align: right">
                                  <input type="text" id="iso27001hamsure{{$i}}" name="iso27001hamsure{{$i}}" value="0.0"
                                         class="form-control form-control-sm" style="text-align: right" readonly/>

                                </td>
                                <td>
                                  <input id="iso27001indart{{$i}}" type="text"
                                         name="iso27001indart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso27001azartsure{{$i}}" type="text"
                                         name="iso27001azartsure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso27001entindart{{$i}}" type="text"
                                         name="iso27001entindart{{$i}}" class="form-control form-control-sm" value="0.0"
                                         style="text-align: right"
                                         readonly/>
                                </td>
                                <td>
                                  <input id="iso27001kalansure{{$i}}" type="text"
                                         name="iso27001kalansure{{$i}}" class="form-control form-control-sm"
                                         value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso27001a1sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso27001a1sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso27001a2sure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso27001a2sure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso27001gsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso27001gsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                                <td>
                                  <input id="iso27001ybsure{{$i}}" class="form-control form-control-sm" type="text"
                                         name="iso27001ybsure{{$i}}" value="0.0"
                                         style="text-align: right" readonly/>
                                </td>
                              </tr>
                            @endif
                          @endfor
                        @endif
                        </tbody>
                        <tfoot>
                        <tr>
                          <td>
                            Toplamlar:
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamhamsuretmp" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                            <input type="text" id="toplamhamsure" name="toplamhamsure" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamindarttmp" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                            <input type="text" id="toplamindart" name="toplamindart" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamazarttmp" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                            <input type="text" id="toplamazart" name="toplamazart" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamentindarttmp" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                            <input type="text" id="toplamentindart" name="toplamentindart" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamkalansuretmp" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                            <input type="text" id="toplamkalansure" name="toplamkalansure" class="form-control form-control-sm" value="0.0"
                                   style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplama1suretmp" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                            <input type="text" id="toplama1sure" name="toplama1sure" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplama2suretmp" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                            <input type="text" id="toplama2sure" name="toplama2sure" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamgsuretmp" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                            <input type="text" id="toplamgsure" name="toplamgsure" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                          </td>
                          <td style="text-align: right">
                            <input type="hidden" id="toplamybsuretmp" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                            <input type="text" id="toplamybsure" name="toplamybsure" class="form-control form-control-sm"
                                   value="0.0" style="text-align: right" readonly/>
                          </td>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>

                <div class="col-12 justify-content-between">
                  <button type="button" class="btn btn-primary btn-next" style="float: right"
                          onclick="denetimSetiHazirla()">
                    <span id="setSetHazirlaSpinner" class="spinner-border me-1" role="status" aria-hidden="true" style="display:none"></span>
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Denetim Paketi Hazırla</span>
                    <i class="mdi mdi-check"></i></button>
                </div>

                <div class="card">
                  <h5 class="card-header justify-content-center">
                    HAZIRLANAN DENETİM PAKETİ
                  </h5>
                  <div class="card-datatable text-wrap">
                    <div class="row g-4">
                      <div class="col-sm-12">
                        <div id='btnAsRaporYazdirLink' style="float:right"></div>
                    </div>
                    </div>
                  </div>
                </div>

                <div class="card">
                  <h5 class="card-header">
                    <div class="col-sm-12 col-lg-12 text-center">
                      <div id="curmonth" hidden=""><?php echo date("m"); ?></div>
                      <div id="curyear" hidden=""><?php echo date("Y"); ?></div>
                      <div id="divcalendarprevyear" class="btn btn-success btn-sm left"
                           onclick="changeCalendarMonth('prev')"><<<<</div>
                      <div id="divcalendarmonth" class="btn btn-danger btn-sm left" onclick="changeCalendarMonth('current')"><?php echo date("m.Y"); ?></div>
                      <div id="divcalendarnextyear" class="btn btn-success btn-sm left"
                           onclick="changeCalendarMonth('next')">>>>></div>
                    </div>
                  </h5>
                  <div class="card-datatable text-wrap">
                    <div class="row g-4">
                      <div class="col-sm-12">
                        <div id="divdenetimtakvimi"></div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
        <div class="modal fade" id="myModaleaNaceKat" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">EA/Nace/Kategori/Teknik alan listesi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                @if($iso900115 | $iso1400115 | $iso45001)
                  <div class="card">
                    <h5 class="card-header">
                      ISO 9001/14001/45001 için EA/Nace kod listesi
                      <div id="diveanace"></div>
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <table id="dt-ea-nace-kodlari" class="dt-ea-nace-kodlari table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th style="width: 15px">9001-Risk Kategorisi</th>
                          <th>14001-Karmaşıklık kategorisi</th>
                          <th>45001-Karmaşıklık kategorisi</th>
                          <th>Ea Kodu</th>
                          <th>Nace Kodu</th>
                          <th>Açıklama</th>
                        </tr>
                        </thead>
                        <tbody id="dt-ea-nace-kodlari-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>9001-Risk Kategorisi</th>
                          <th>14001-Karmaşıklık kategorisi</th>
                          <th>45001-Karmaşıklık kategorisi</th>
                          <th>Ea Kodu</th>
                          <th>Nace Kodu</th>
                          <th>Açıklama</th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                @endif
                @if($iso2200018)
                  <div class="card">
                    <h5 class="card-header">
                      ISO 22000 kategori listesi
                      <div id="div22cat"></div>
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <table id="dt-22000-kategori" class="dt-22000-kategori table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Kategori</th>
                          <th>Başlık</th>
                          <th>Açıklama</th>
                          <th>bb</th>
                          <th>cc</th>
                        </tr>
                        </thead>
                        <tbody id="dt-22000-kategori-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Kategori</th>
                          <th>Başlık</th>
                          <th>Açıklama</th>
                          <th>bb</th>
                          <th>cc</th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                @endif
                @if($oicsmiic | $oicsmiic6 | $oicsmiic9 | $oicsmiic171 | $oicsmiic24 | $oicsmiic24)
                  <div class="card">
                    <h5 class="card-header">
                      OIC/SMIIC kategori listesi
                      <div id="divoiccat"></div>
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <table id="dt-smiic-kategori" class="dt-smiic-kategori table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Kategori</th>
                          <th>Başlık</th>
                          <th>Açıklama</th>
                          <th>Örnekler</th>
                          <th></th>
                          <th></th>
                        </tr>
                        </thead>
                        <tbody id="dt-smiic-kategori-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Kategori</th>
                          <th>Başlık</th>
                          <th>Açıklama</th>
                          <th>Örnekler</th>
                          <th></th>
                          <th></th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                @endif
                @if($iso50001)
                  <div class="card">
                    <h5 class="card-header">
                      ENYS TEKNİK ALAN TABLOSU
                      <div id="div50001cat"></div>
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <table id="dt-50001-kategori" class="dt-50001-kategori table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Teknik Alan</th>
                          <th>AÇIKLAMA</th>
                          <th>Teknik Alan Kodu Grubu</th>
                        </tr>
                        </thead>
                        <tbody id="dt-50001-kategori-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Teknik Alan</th>
                          <th>AÇIKLAMA</th>
                          <th>Teknik Alan Kodu Grubu</th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                @endif
                @if($iso27001)
                  <div class="card">
                    <h5 class="card-header">
                      BGYS KATEGORİ TABLOSU
                      <div id="div27001cat"></div>
                    </h5>
                    <div class="card-datatable text-nowrap">
                      <table id="dt-27001-kategori" class="dt-27001-kategori table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Sektör Grubu</th>
                          <th>Sektör</th>
                          <th>Teknik Alan</th>
                          <th>Teknik Alan Kodu</th>
                          <th>Teknik Alan Kodu Grubu</th>
                        </tr>
                        </thead>
                        <tbody id="dt-27001-kategori-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>Sıra</th>
                          <th>Sektör Grubu</th>
                          <th>Sektör</th>
                          <th>Teknik Alan</th>
                          <th>Teknik Alan Kodu</th>
                          <th>Teknik Alan Kodu Grubu</th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                @endif
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="getDenetimOnerilenBasdenetci()"
                        data-bs-dismiss="modal">Onayla & Kapat
                </button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalbddenetim" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Önerilen başdenetçi</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-wrap">
                    <table id="dt-denetim-onerilen-basdenetci"
                           class="dt-denetim-onerilen-basdenetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Ea Kod</th>
                        <th style="width: 150px">Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-denetim-onerilen-basdenetci-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Ea Kod</th>
                        <th>Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalbdkararu" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Önerilen Karar Üyeleri</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-wrap">
                    <table id="dt-denetim-onerilen-karar-uye"
                           class="dt-denetim-onerilen-karar-uye table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Ea Kod</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-denetim-onerilen-karar-uye-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Ea Kod</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Aşama 1 modallar -->
        <div class="modal fade" id="myModalbd1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable table-responsive-sm pt-0 text-wrap">
                      <table id="dt-asama1-basdenetciler" class="dt-asama1-basdenetciler table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>#</th>
                          <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                          <th>Ea/Nace Kodu</th>
                          <th>22000 Kategori</th>
                          <th>Oic/Smiic Kategori</th>
                          <th>Bgys Teknik Alan Kodu</th>
                          <th>Enys Teknik Alan Kodu</th>
                        </tr>
                        </thead>
                        <tbody id="dt-asama1-basdenetciler-body">

                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>#</th>
                          <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                          <th>Ea/Nace Kodu</th>
                          <th>22000 Kategori</th>
                          <th>Oic/Smiic Kategori</th>
                          <th>Bgys Teknik Alan Kodu</th>
                          <th>Enys Teknik Alan Kodu</th>
                        </tr>
                        </tfoot>
                      </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModald1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <div class="table-responsive text-nowrap">
                      <table id="dt-asama1-denetciler" class="dt-asama1-denetciler table table-bordered">
                        <thead>
                        <tr>
                          <th>#</th>
                          <th>#</th>
                          <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                          <th>Ea/Nace Kodu</th>
                          <th>22000 Kategori</th>
                          <th>Oic/Smiic Kategori</th>
                          <th>Bgys Teknik Alan Kodu</th>
                          <th>Enys Teknik Alan Kodu</th>
                        </tr>
                        </thead>
                        <tbody id="dt-asama1-denetciler-body">
                        </tbody>
                        <tfoot>
                        <tr>
                          <th>#</th>
                          <th>#</th>
                          <th>Adı Soyadı</th>
                          <th>Atandığı sistemler</th>
                          <th>Ea/Nace Kodu</th>
                          <th>22000 Kategori</th>
                          <th>Oic/Smiic Kategori</th>
                          <th>Bgys Teknik Alan Kodu</th>
                          <th>Enys Teknik Alan Kodu</th>
                        </tr>
                        </tfoot>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModaltu1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama1-teknik-uzman" class="dt-asama1-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama1-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalg1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama1-gozlemci" class="dt-asama1-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama1-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModaliku1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama1-iku" class="dt-asama1-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama1-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalad1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama1-aday-denetci" class="dt-asama1-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama1-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsid1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Değerlendirici seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama1-degerlendirici" class="dt-asama1-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama1-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Aşama 2 modallar -->
        <div class="modal fade" id="myModalbd2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-basdenetciler" class="dt-asama2-basdenetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-basdenetciler-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModald2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-denetciler" class="dt-asama2-denetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-denetciler-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModaltu2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-teknik-uzman" class="dt-asama2-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalg2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-gozlemci" class="dt-asama2-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModaliku2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-iku" class="dt-asama2-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalad2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-aday-denetci" class="dt-asama2-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsid2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Değerlendirici seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-asama2-degerlendirici" class="dt-asama2-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-asama2-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Gözetim 1 modallar -->
        <div class="modal fade" id="myModalgbd1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-basdenetciler" class="dt-gozetim1-basdenetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-basdenetciler-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgd1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-denetciler" class="dt-gozetim1-denetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-denetciler-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgtu1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-teknik-uzman" class="dt-gozetim1-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgg1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-gozlemci" class="dt-gozetim1-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgiku1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-iku" class="dt-gozetim1-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgad1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-aday-denetci" class="dt-gozetim1-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsidg1" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim1-degerlendirici" class="dt-gozetim1-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim1-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Gözetim 2 modallar -->
        <div class="modal fade" id="myModalgbd2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-basdenetciler" class="dt-gozetim2-basdenetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-basdenetciler-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgd2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-denetciler" class="dt-gozetim2-denetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-denetciler-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgtu2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-teknik-uzman" class="dt-gozetim2-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgg2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-gozlemci" class="dt-gozetim2-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgiku2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-iku" class="dt-gozetim2-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalgad2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-aday-denetci" class="dt-gozetim2-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsidg2" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Değerlendirici seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-gozetim2-degerlendirici" class="dt-gozetim2-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-gozetim2-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Yeniden Belgelendirme modallar -->
        <div class="modal fade" id="myModalybbd" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-basdenetciler" class="dt-yb-basdenetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-basdenetciler-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalybd" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-denetciler" class="dt-yb-denetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-denetciler-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalybtu" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-teknik-uzman" class="dt-yb-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalybg" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-gozlemci" class="dt-yb-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalybiku" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-iku" class="dt-yb-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalybad" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-aday-denetci" class="dt-yb-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsidyb" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Değerlendirici seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-yb-degerlendirici" class="dt-yb-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-yb-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Yeniden Belgelendirme modallar -->
        <div class="modal fade" id="myModalotbd" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Başdenetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-basdenetciler" class="dt-ot-basdenetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-basdenetciler-body">

                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalotd" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-denetciler" class="dt-ot-denetciler table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-denetciler-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalottu" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Teknik uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-teknik-uzman" class="dt-ot-teknik-uzman table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-teknik-uzman-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalotg" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Gözlemci seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-gozlemci" class="dt-ot-gozlemci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-gozlemci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th><th>Atandığı sistemler</th>
                        <th>Ea/Nace Kodu</th>
                        <th>22000 Kategori</th>
                        <th>Oic/Smiic Kategori</th>
                        <th>Bgys Teknik Alan Kodu</th>
                        <th>Enys Teknik Alan Kodu</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalotiku" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">İslami uzman seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-iku" class="dt-ot-iku table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-iku-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atandığı sistemler</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalotad" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Aday denetçi seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-aday-denetci" class="dt-ot-aday-denetci table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-aday-denetci-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="myModalsidot" data-bs-backdrop="static" tabindex="-1">
          <div class="modal-dialog modal-fullscreen" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Değerlendirici seçiniz...</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="card-datatable text-nowrap">
                    <table id="dt-ot-degerlendirici" class="dt-ot-degerlendirici table table-bordered">
                      <thead>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </thead>
                      <tbody id="dt-ot-degerlendirici-body">
                      </tbody>
                      <tfoot>
                      <tr>
                        <th>#</th>
                        <th>#</th>
                        <th>Adı Soyadı</th>
                        <th>Atanacağı sistem</th>
                      </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Azaltma/Arttırma modallar -->
        <div class="modal fade" id="modal9001indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 9001 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="write9001IndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::write9001IndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modal14001indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 14001 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="write14001IndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::write14001IndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modal45001indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 45001 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="write45001IndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::write45001IndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modal50001indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 50001 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="write50001IndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::write50001IndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        @if($iso27001)
        <div class="modal fade" id="modal27001indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 27001 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="card">
                  <div class="row g-4">
                    <div class="col-sm-12">
                      <table class="table table-bordered table-responsive ">
                        <thead>
                        <tr>
                          <th colspan="2">İş ve Kuruluşla ilişkili faktörlerin tayini (BT dışında)</th>
                        </tr>
                        <tr>
                          <th colspan="2">Toplam:
                            <div id="iskartoplam"></div>
                          </th>
                        </tr>
                        <tr>
                          <th style="width:25%">Kategori</th>
                          <th>Derece</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                          <th>İş türü/türleri ve düzenleyici gereklilikler</th>
                          <td>
                            <div class="form-check">
                              <input type="radio" class="form-check-input" id="isturu1" name="isturu" value="1"
                                     onclick='isFaktorEtkiHesapla()' {{(isset($basvurubgys) && $basvurubgys->isturu === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="isturu1">1 Kuruluş, kritik olmayan iş alanları ve
                                düzenlenmemiş alanlarda çalışmakta<sup>a</sup></label>
                            </div>
                            <div class="form-check">
                              <input type="radio" class="form-check-input" id="isturu2" name="isturu" value="2"
                                     onclick='isFaktorEtkiHesapla()'{{(isset($basvurubgys) && $basvurubgys->isturu === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="isturu2">2 Kuruluşun kritik iş alanlarında çalışan
                                müşterisi var<sup>a</sup></label>
                            </div>
                            <div class="form-check">
                              <input type="radio" class="form-check-input" id="isturu3" name="isturu" value="3"
                                     onclick='isFaktorEtkiHesapla()'{{(isset($basvurubgys) && $basvurubgys->isturu === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="isturu3">3 Kuruluş kritik iş alanlarında
                                çalışmakta<sup>a</sup></label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <th>Prosesler ve görevler</th>
                          <td>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="prosesler" id="prosesler1" value="1"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->prosesler === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="prosesler1"> 1 Standart ve tekrarlayan görevlere
                                sahip standard prosesleri; kuruluşun kontrolünde aynı görevleri yerine getiren birçok
                                fazla personel; birkaç ürün veya hizmet </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="prosesler" id="prosesler2" value="2"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->prosesler === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="prosesler2"> 2 Çok sayıda ürün ve hizmet veren,
                                standard ama tekrarlamayan prosesler </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="prosesler" id="prosesler3" value="3"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->prosesler === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="prosesler3"> 3 Belgelendirme kapsamındaki birçok
                                birimler, yüksek sayıda ürün ve hizmet, karmaşık prosesler (BGYS oldukça karmaşık
                                prosesleri veya nispeten yüksek sayıda veya benzersiz faaliyetleri kapsar) </label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <th>Yönetim sisteminin oluşturulma seviyesi</th>
                          <td>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                     id="ysolusmaseviyesi1" value="1"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="ysolusmaseviyesi1"> 1 BGYS oldukça iyi
                                oluşturulmuştur ve/veya diğer yönetim sistemleri yürürlüktedir. </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                     id="ysolusmaseviyesi2" value="2"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="ysolusmaseviyesi2"> 2 Diğer yönetim sistemlerindeki
                                bazı unsurlar uygulanır, diğerleri değil </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                     id="ysolusmaseviyesi3" value="3"
                                     onclick="isFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="ysolusmaseviyesi3"> 3 Başka hiçbir yönetim sistemi
                                uygulanmıyor, BGYS yeni ve tam oluşturulmamış, </label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td colspan="2"><sup>a</sup> Kritik iş alanları ülke üzerinde ciddi olumsuzluklar
                            oluşturabilecek sağlık, güvenlik, ekonomi, ülke imajı ve devletin fonksiyonel kalmasına
                            gelebilecek risklere yol açacak kritik kamu hizmetlerini etkileyebilecek sektörlerdir.
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                    <div class="col-sm-12">
                      <table class="table table-bordered table-responsive ">
                        <thead>
                        <tr>
                          <th colspan="2" class="text-center">BT alanıyla ilgili faktörler</th>
                        </tr>
                        <tr>
                          <th colspan="2">Toplam:
                            <div id="btkartoplam"></div>
                          </th>
                        </tr>
                        <tr>
                          <th style="width:25%" class="text-center">Kategori</th>
                          <th class="text-center">Derece</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                          <th>BT altyapı karmaşıklığı</th>
                          <td>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi1" value="1"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="btaltyapi1">1 Az ya da çok standardlaştırılmış BT
                                platformları, sunucuları, işletim sistemleri, veri tabanları, ağlar vb. </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi2" value="2"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="btaltyapi2">2 1-3 farklı BT platformu, sunucuları,
                                veri tabanları, ağları </label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi3" value="3"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="btaltyapi3">3 Birçok farklı BT platformu, sunucuları,
                                veri tabanları, ağları </label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <th>Bulut hizmetleri dâhil dış kaynaklara ve tedarikçilere olan bağlılık</th>
                          <td>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak1" value="1"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="diskaynak1"> 1 Dış kaynaklara ya da tedarikçiler az
                                bağımlı olma ya da bağımlı olmama</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak2" value="2"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="diskaynak2"> 2 Tüm kritik iş faaliyetleri olmamak
                                koşuluyla sadece bazılarında dış kaynaklara ya da tedarikçiye olan normal
                                bağımlılık,</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak3" value="3"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="diskaynak3"> 3 Dış kaynaklara ya da tedarikçiye olan
                                fazla bağımlılık, önemli iş faaliyetlerine büyük etki</label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <th>Bilgi sistem gelişimi</th>
                          <td>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                     id="bilgisistemgelisimi1" value="1"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="bilgisistemgelisimi1"> 1 Kuruluş içi sistem/uygulama
                                geliştirme yok veya çok sınırlı</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                     id="bilgisistemgelisimi2" value="2"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 2) ? 'checked' : ''}} />
                              <label class="form-check-label" for="bilgisistemgelisimi2"> 2 Bazı önemli iş amaçları için
                                kuruluş içi veya dış kaynaklı sistem/uygulama geliştirme</label>
                            </div>
                            <div class="form-check">
                              <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                     id="bilgisistemgelisimi3" value="3"
                                     onclick="btFaktorEtkiHesapla()" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 3) ? 'checked' : ''}} />
                              <label class="form-check-label" for="bilgisistemgelisimi3"> 3 Önemli iş amaçları için
                                kuruluş içi ya da dış kaynaklı kapsamlı sistem/uygulama geliştirme</label>
                            </div>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="bgysFaktorEtkiHesapla()"
                        data-bs-dismiss="modal">Hesapla & Kapat
                </button>
              </div>
            </div>
          </div>
        </div>
        @endif
        <div class="modal fade" id="modal22000indart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">ISO 22000 Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="write22000IndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::write22000IndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modalOicsmiicindart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">OIC/SMIIC Azaltma/Arttırma Oranları</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="writeSmiicIndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::writeSmiicIndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>
        <div class="modal fade" id="modal1entegreindart" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h4 class="modal-title" id="modalScrollableTitle">Yönetim Sistemlerinin Entegrasyon Düzeyi
                  Bilgileri</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="writeEntegreIndArt-form" onSubmit="return false">
                  {{ csrf_field() }}
                  <div class="card">
                    <div class="row g-4">
                      {{\App\Http\Controllers\Planlama\Plan::writeEntegreIndArt()}}
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </div>
          </div>
        </div>

        <div class="modal modal-top fade" id="myModalSucces" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-4 mt-2 text-wrap">
                    <div id="formkaydetsonucsuccess"></div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </form>
          </div>
        </div>
        <div class="modal modal-top fade" id="myModalError" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col mb-4 mt-2 text-wrap">
                    <div id="formkaydetsonucerror"></div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kapat</button>
              </div>
            </form>
          </div>
        </div>

      </div>
    </div>
  </div>
@endsection
