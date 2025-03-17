@extends('layouts/layoutMaster')

@section('title', '[' . $pno . '] ' . $asama . " | " . $plan->firmaadi)

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/flatpickr/flatpickr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}"/>
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/jquery-sticky/jquery-sticky.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-datepicker/bootstrap-datepicker.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/plan-tables-datatables-planlar.js')}}"></script>
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
@endsection

@section('content')
  <?php
  $pot = $plan;

//  $pot = array_merge($pot0, $pot1);

  $ilkyayin = "";
  $yayintarihi = "";
  $gecerliliktarihi = "";
  $bittarihi = "";

  $asama1 = "";
  $asama1 = $pot->asama1;
  $asama2 = $pot->asama2;
  $gozetim1 = $pot->gozetim1;
  $gozetim2 = $pot->gozetim2;
  $ybtar = $pot->ybtar;
  $ozeltar = $pot->ozeltar;

  $asama = trim($asama);
  if ($asama == "") $asama = "ilkplan";

  $soarevnodate = $pot->soarevnotarihi;
  $ea = $pot->eakodu;
  $nace = $pot->nacekodu;
  $kat = str_replace("@", "", $pot->kategori22);
  $oickat = str_replace("ß", "", $pot->kategorioic);
  $enysteknikalan = str_replace("Æ", "", $pot->teknikalanenys);
  $bgkat = str_replace("€", "", $pot->kategoribgys);

  $kyssistemler = \App\Helpers\Helpers::getSistemler($pot);
  $oicsistemler = \App\Helpers\Helpers::getOicSistemler($pot);

  if ($kyssistemler !== "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
  }
  if ($kyssistemler === "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $oicsistemler;
  }
  if ($kyssistemler !== "" && $oicsistemler === "") {
    $belgelendirileceksistemler = $kyssistemler;
  }
  if ($kyssistemler !== "" && $oicsistemler === "") {
    $belgelendirileceksistemler = $kyssistemler;
  }

  $ilkyayin = (count($cert) > 0) ? date_create_from_format("Y-m-d", $cert[0]->ilkyayin) : date_create_from_format("Y-m-d", date("Y-m-d"));
  $ilkyayin = ($ilkyayin != "") ? date_format($ilkyayin, "d.m.Y") : date("d.m.Y");

  $yayintarihi = (count($cert) > 0)  ? date_create_from_format("Y-m-d", $cert[0]->yayintarihi) : date_create_from_format("Y-m-d", date("Y-m-d"));
  $yayintarihi = ($yayintarihi != "") ? date_format($yayintarihi, "d.m.Y") : date("d.m.Y");

  $gecerliliktarihi = (count($cert) > 0)  ? date_create_from_format("Y-m-d", $cert[0]->gecerliliktarihi) : date_create_from_format("Y-m-d", date("Y-m-d", strtotime('+1 year -1 day')));
  $gecerliliktarihi = ($gecerliliktarihi != "") ? date_format($gecerliliktarihi, "d.m.Y") : date("d.m.Y", strtotime('+1 year -1 day'));

  $bitistarihi = (count($cert) > 0)  ? date_create_from_format("Y-m-d", $cert[0]->bitistarihi) : date_create_from_format("Y-m-d", date("Y-m-d", strtotime('+3 years -1 day')));
  $bitistarihi = ($bitistarihi != "") ? date_format($bitistarihi, "d.m.Y") : date("d.m.Y", strtotime('+3 years -1 day'));

  $sertikod = (count($cert) > 0)  ? substr($cert[0]->belgeno, 0, 1) : "";
  $sertino = (count($cert) > 0)  ? substr($cert[0]->belgeno, 1, strlen($cert[0]->belgeno)) : "";

  ?>
  <div class="row">
    <div class="col-12 text-danger">
      <h5>[{{$pno}}] {{$pot->firmaadi}}</h5>
      {{$pot->belgelendirmekapsami}}
    </div>


    <div class="row gy-4 mb-4">
      <div class="col-xl-12">
        <form id="sertifika-form" onSubmit="return false">
          {{ csrf_field() }}
          <div class="card">
            <div
              class="card-header sticky-element bg-info d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
              <h5 class="card-title mb-sm-0 me-2">{{$belgelendirileceksistemler}}</h5>
              @include('_partials/planlama-menu', ['pno' => $pno])
            </div>
            <div class="card-body">
              <input type="hidden" id="formSertifikaRoute" value="{{route('planSertifika')}}">
              <input type="hidden" id="formSertifikaKaydetRoute" value="{{route('planSertifikaKaydet')}}">
              <input type="hidden" id="planno" name="planno" class="form-control" value="{{$pno}}">
              <div class="row g-4">
                <div class="col-sm-12">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="firmaadi" name="firmaadi"
                             class="form-control"
                             placeholder=""
                             value="{{$pot->firmaadi}}"/>
                      <label for="firmaadi">Firma adı</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="firmaadresi" name="firmaadresi"
                             class="form-control"
                             placeholder=""
                             value="{{$pot->firmaadresi}}"/>
                      <label for="firmaadresi">Firma adresi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gizlikat" name="categories" class="form-control"
                             value="<?php echo $kat; ?>"/>
                      <label for="gizlikat">22000 Kategori/Alt Kategori</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gizlioickat" name="oiccategories" class="form-control"
                             value="<?php echo $oickat; ?>"/>
                      <label for="gizlioickat">Helal Kategori/Alt Kategori</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="soarevnotarihi" name="soarevnotarihi" class="form-control"
                             value="<?php echo $soarevnodate; ?>"/>
                      <label for="soarevnotarihi">SoA Rev No&Tarihi</label>
                    </div>
                  </div>
                </div>

                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsam" name="belgelendirmekapsami"
                              placeholder="">{{$pot->belgelendirmekapsami}}</textarea>
                    <label for="formValidationKapsam">Kapsam</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="belgelendirmekapsamiing" name="belgelendirmekapsamiing"
                              placeholder="">{{$pot->belgelendirmekapsamiing}}</textarea>
                    <label for="belgelendirmekapsamiing">Kapsam</label>
                  </div>
                </div>

                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 9001:2015" id="formValidationIso900115varyok"
                             name="stdadi" onclick='$("#certkodu").val("Q")' />
                      <label class="form-check-label" for="formValidationIso900115varyok">
                        ISO 9001:2015
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 14001:2015" id="formValidationIso1400115varyok"
                             name="stdadi" onclick='$("#certkodu").val("E")' />
                      <label class="form-check-label" for="formValidationIso1400115varyok">
                        ISO 14001:2015
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 45001:2018" id="formValidationIso4500118varyok"
                             name="stdadi" onclick='$("#certkodu").val("O")' />
                      <label class="form-check-label" for="formValidationIso4500118varyok">
                        ISO 45001:2018
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 22000:2018" id="formValidationIso2200018varyok"
                             name="stdadi" onclick='$("#certkodu").val("F")' />
                      <label class="form-check-label" for="formValidationIso2200018varyok">
                        ISO 22000:2018
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 27001:2022" id="formValidationIso27001varyok"
                             name="stdadi" onclick='$("#certkodu").val("I")' />
                      <label class="form-check-label" for="formValidationIso27001varyok">
                        ISO 27001:2022
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="ISO 50001:2018" id="formValidationIso5000118varyok"
                             name="stdadi" onclick='$("#certkodu").val("En")' />
                      <label class="form-check-label" for="formValidationIso5000118varyok">
                        ISO 50001:2018
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 1:2019" id="formValidationHelalvaryok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationHelalvaryok">
                        OIC/SMIIC 1:2019
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 6:2019" id="formValidationOicsmiik6varyok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationOicsmiik6varyok">
                        OIC/SMIIC 6:2019
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 9:2019" id="formValidationOicsmiik9varyok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationOicsmiik9varyok">
                        OIC/SMIIC 9:2019
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 17-1:2020" id="formValidationOicsmiik171varyok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationOicsmiik171varyok">
                        OIC/SMIIC 17-1:2020
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 23:2022" id="formValidationOicsmiik23varyok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationOicsmiik23varyok">
                        OIC/SMIIC 23:2022
                      </label>
                    </div>
                    <div class="form-check mt-1">
                      <input class="form-check-input" type="radio" value="OIC/SMIIC 24:2020" id="formValidationOicsmiik24varyok"
                             name="stdadi" onclick='$("#certkodu").val("H")' />
                      <label class="form-check-label" for="formValidationOicsmiik24varyok">
                        OIC/SMIIC 24:2020
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12 mt-1">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check form-check-inline mt-1">
                      <input class="form-check-input" type="radio" value="ISO 10002:2018" id="formValidationISO100022018varyok"
                             name="stdadi" onclick='$("#certkodu").val("C")' />
                      <label class="form-check-label" for="formValidationISO100022018varyok">
                        ISO 10002:2018
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" value="HACCP" id="formValidationHACCPvaryok"
                             name="stdadi" onclick='$("#certkodu").val("HC")' />
                      <label class="form-check-label" for="formValidationHACCPvaryok">
                        HACCP
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" value="GDP" id="formValidationGDPvaryok"
                             name="stdadi" onclick='$("#certkodu").val("GDP")' />
                      <label class="form-check-label" for="formValidationGDPvaryok">
                        GDP
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" value="GMP" id="formValidationGMPvaryok"
                             name="stdadi" onclick='$("#certkodu").val("GMP")' />
                      <label class="form-check-label" for="formValidationGMPvaryok">
                        GMP
                      </label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" value="FSSC 22000:2011" id="formValidationFSSCvaryok"
                             name="stdadi" onclick='$("#certkodu").val("FS")' />
                      <label class="form-check-label" for="formValidationFSSCvaryok">
                        FSSC 22000:2011
                      </label>
                    </div>
                  </div>
                </div>

                <div class="col-sm-2 mt-1">
                  <label>OIC/SMIIC 9:2019 Türü</label>
                </div>
                <div class="col-sm-10 mt-1">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipa" name="smiic9tip" value="A">
                      <label class="form-check-label" for="smiic9tipa">A</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipa96" name="smiic9tip" value="A96">
                      <label class="form-check-label" for="smiic9tipa96">A (9+6)</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipb" name="smiic9tip" value="B">
                      <label class="form-check-label" for="smiic9tipb">B</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipb96" name="smiic9tip" value="B96">
                      <label class="form-check-label" for="smiic9tipb96">B (9+6)</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipc" name="smiic9tip" value="C">
                      <label class="form-check-label" for="smiic9tipc">C</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input type="radio" class="form-check-input" id="smiic9tipc96" name="smiic9tip" value="C96">
                      <label class="form-check-label" for="smiic9tipc96">C (9+6)</label>
                    </div>
                  </div>
                </div>

{{--                <div class="col-sm-2 mt-1">--}}
{{--                  <label>Akredite</label>--}}
{{--                </div>--}}
{{--                <div class="col-sm-10 mt-1">--}}
{{--                  <div class="form-floating form-floating-outline">--}}
{{--                      <div class="form-check form-check-inline">--}}
{{--                        <input type="radio" class="form-check-input" id="onayli" name="akredite" value="yes"--}}
{{--                               checked>--}}
{{--                        <label class="form-check-label" for="onayli">Var</label>--}}
{{--                      </div>--}}
{{--                      <div class="form-check form-check-inline">--}}
{{--                        <input type="radio" class="form-check-input" id="onaysiz" name="akredite" value="no">--}}
{{--                        <label class="form-check-label" for="onaysiz">Yok</label>--}}
{{--                      </div>--}}
{{--                  </div>--}}
{{--                </div>--}}

                <div class="col-sm-2 mt-1">
                  <label>OIC/SMIIC 17-1:2020</label>
                </div>
                <div class="col-sm-10 mt-1">
                  <div class="form-floating form-floating-outline">
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" id="smiic171" name="smiic17" value="1" checked>
                        <label class="form-check-label" for="smiic171">1</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" id="smiic172" name="smiic17" value="2">
                        <label class="form-check-label" for="smiic172">2</label>
                      </div>
                      <div class="form-check form-check-inline">
                        <input type="radio" class="form-check-input" id="smiic173" name="smiic17" value="3">
                        <label class="form-check-label" for="smiic173">3</label>
                      </div>
                  </div>
                </div>

                <div class="col-sm-12 mt-2">
                  <div class="input-group">
                    <label class="btn btn-outline-primary">Sertifika No </label>
                    <input type="text" id="certkodu" name="certkodu" class="form-control col-1" placeholder=""
                           value="{{$sertikod}}" aria-label="" style="max-width: 100px">
                    <input type="text" id="certno" name="certno" class="form-control col-11" placeholder=""
                           value="{{($sertino == "") ? date("dmy") : $sertino}}" aria-label="">
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="ilkyayin"
                             id="ilkyayin" value="{{$ilkyayin}}"/>
                      <label for="ilkyayin">İlk Yayın tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="bitistarihi"
                             id="bitistarihi" value="{{$bitistarihi}}"/>
                      <label for="bitistarihi">Bitiş tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="yayintarihi"
                             id="yayintarihi" value="{{$yayintarihi}}"/>
                      <label for="yayintarihi">Yayın tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="number" class="form-control" placeholder="0" name="gecerliliktarihi"
                             id="gecerliliktarihi" value="{{$gecerliliktarihi}}"/>
                      <label for="gecerliliktarihi">Geçerlilik tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="number" class="form-control" min="0" name="certrevizyonno"
                             id="certrevizyonno" value="0"/>
                      <label for="certrevizyonno">Revizyon No</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 mt-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="certrevtarihi"
                             id="certrevtarihi" value=""/>
                      <label for="certrevtarihi">Sertifika Revizyon tarihi</label>
                    </div>
                  </div>
                </div>



                <div class="col-12 justify-content-between">
                  <button type="button" class="btn btn-primary btn-next" style="float: right"
                          onclick="sertifikaKaydet()">
                    <span id="setSetHazirlaSpinner" class="spinner-border me-1" role="status" aria-hidden="true" style="display:none"></span>
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Kaydet</span>
                    <i class="mdi mdi-check"></i></button>&nbsp;&nbsp;&nbsp;
                  <button type="button" class="btn btn-success btn-next" style="float: right"
                          onclick="sertifikaHazirla()">
                    <span id="setSetHazirlaSpinner" class="spinner-border me-1" role="status" aria-hidden="true" style="display:none"></span>
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Hazırla</span>
                    <i class="mdi mdi-check"></i></button>
                </div>

                <div class="card">
                  <h5 class="card-header justify-content-center">
                    SONUÇ
                  </h5>
                  <div class="card-datatable text-wrap">
                    <div class="row g-4">
                      <div class="col-sm-12">
                        <div id='btnAsRaporYazdirLink' style="float:left"></div>
                        <div id='btnAsRaporKaydetLink' style="float:left"></div>
                    </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        </form>

        <div class="modal modal-top fade" id="myModalSucces" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <form class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Sertifika</h5>
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
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Sertifika</h5>
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
