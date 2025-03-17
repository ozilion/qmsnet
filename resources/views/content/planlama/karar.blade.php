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
{{--  <script src="{{asset('assets/js/plan-tables-datatables-planlar.js')}}"></script>--}}
  <script src="{{asset('assets/js/plan-hesaplamalar-planlar.js')}}"></script>
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
  <script src="{{asset('assets/js/cards-actions.js')}}"></script>
@endsection

@section('content')
  <?php
  $pot = $plan;

//  $pot = array_merge($pot0, $pot1);


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

  $soarevnodate = $pot->soarevnotarihi;
  $ea = $pot->eakodu;
  $nace = $pot->nacekodu;
  $kat = str_replace("@", "", $pot->kategori22);
  $oickat = str_replace("ß", "", $pot->kategorioic);
  $enysteknikalan = str_replace("Æ", "", $pot->teknikalanenys);
  $bgkat = str_replace("€", "", $pot->kategoribgys);
  $helalvarmi = 0;

  $kyssistemler = \App\Helpers\Helpers::getSistemler($pot);
  $oicsistemler = \App\Helpers\Helpers::getOicSistemler($pot);

  if ($kyssistemler !== "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $kyssistemler . ", " . $oicsistemler;
  }
  if ($kyssistemler === "" && $oicsistemler !== "") {
    $belgelendirileceksistemler = $oicsistemler;
    $helalvarmi = 1;
  }
  if ($kyssistemler !== "" && $oicsistemler === "") {
    $belgelendirileceksistemler = $kyssistemler;
  }

  $cevrim = $pot->belgecevrimi;
  $cevrim = ($cevrim == "") ? "1" : $cevrim;

  $kurul0 = mb_substr($pot->firmaadi, 0, 10, 'UTF-8');
  $bitistarihi = date_create_from_format("Y-m-d", $pot->bitistarihi);
  $bitistarihi = ($bitistarihi != "") ? date_format($bitistarihi, "d.m.Y") : "Sertifika kaydı yok...";

  $de = "<div class='table-responsive text-nowrap'><table class='table table-hover table-bordered' style='width: 100%;'><th>#</th><th>BD</th><th>D</th><th>TU</th><th>G</th><th>İKU</th>";
  $de .= "<tr><td style='width: 7%;'>Aşama 1</td><td style='width: 15%;'>" . $pot->bd1 . "</td><td style='width: 28%;'>" . $pot->d1 . "</td><td style='width: 15%;'>" . $pot->tu1 . "</td><td style='width: 20%;'>" . $pot->g1 . "</td><td style='width: 15%;'>" . $pot->iku1 . "</td></tr>";
  $de .= "<tr><td>Aşama 2</td><td>" . $pot->bd2 . "</td><td>" . $pot->d2 . "</td><td>" . $pot->tu2 . "</td><td>" . $pot->g2 . "</td><td>" . $pot->iku2 . "</td></tr>";
  $de .= "<tr><td>Gözetim 1</td><td>" . $pot->gbd1 . "</td><td>" . $pot->gd1 . "</td><td>" . $pot->gtu1 . "</td><td>" . $pot->gg1 . "</td><td>" . $pot->ikug1 . "</td></tr>";
  $de .= "<tr><td>Gözetim 2</td><td>" . $pot->gbd2 . "</td><td>" . $pot->gd2 . "</td><td>" . $pot->gtu2 . "</td><td>" . $pot->gg2 . "</td><td>" . $pot->ikug2 . "</td></tr>";
  $de .= "<tr><td>Yen. Belg.</td><td>" . $pot->ybbd . "</td><td>" . $pot->ybd . "</td><td>" . $pot->ybtu . "</td><td>" . $pot->ybg . "</td><td>" . $pot->ikuyb . "</td></tr>";
  $de .= "<tr><td>Özel</td><td>" . $pot->otbd . "</td><td>" . $pot->otd . "</td><td>" . $pot->ottu . "</td><td>" . $pot->otg . "</td><td>" . $pot->ikuot . "</td></tr>";
  $de .= "</table></div>";

  $eanacekodlariuye2iku = "";
  $eanacekodlariuye3iku = "";
  if ($karar->uyeikuadi != "") {
    if ($karar->uye2adi == "") {
      $karar->uye2adi = $karar->uyeikuadi;
      $eanacekodlariuye2iku = "İslami Konular";
    } else if ($karar->uye3adi == "") {
      $karar->uye3adi = $karar->uyeikuadi;
      $eanacekodlariuye3iku = "İslami Konular";
    }
    $karar->uyeikuadi = "";
  }

  $toplampuan = isset($kararbd->toplampuan) ? $kararbd->toplampuan : 0;
  $ortalamapuan = isset($kararbd->ortalamapuan) ? $kararbd->ortalamapuan : 0;


  ?>
  <div class="row">
    <div class="col-12 text-danger">
      <h5>[{{$pno}}] {{$pot->firmaadi}}</h5>
      {{$pot->belgelendirmekapsami}}
    </div>

    <div class="row gy-4 mb-4">
      <div class="col-xl-12">
        <form id="karar-form" onSubmit="return false">
          {{ csrf_field() }}
          <div class="card">
            <div
              class="card-header sticky-element bg-info d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
              <h5 class="card-title mb-sm-0 me-2">{{$belgelendirileceksistemler}}</h5>
              @include('_partials/planlama-menu', ['pno' => $pno])
            </div>
            <div class="card-body">
              <input type="hidden" id="formKararRoute" value="{{route('plankarar')}}">
              <input type="hidden" id="planno" name="planno" class="form-control" value="{{$pno}}">
              <input type="hidden" id="helalvarmi" name="helalvarmi" class="form-control" value="{{$helalvarmi}}">
              <input type="hidden" id="teklifno" name="teklifno" class="form-control" value="{{$pno}}">
              <input type="hidden" id="asama" name="asama" class="form-control" value="{{$asama}}">
              <input type="hidden" id="eanacekodlariuy2eiku" name="eanacekodlariuye2iku" class="form-control"
                     value="{{$eanacekodlariuye2iku}}">
              <input type="hidden" id="eanacekodlariuye3iku" name="eanacekodlariuye3iku" class="form-control"
                     value="{{$eanacekodlariuye3iku}}">

              <div class="row g-4">
                @can('karar')
                  <div class="col-12 justify-content-between">
                    <button type="button" class="btn btn-primary btn-next" style="float: right"
                            onclick="kararHazirla()">
                    <span id="setSetHazirlaSpinner" class="spinner-border me-1" role="status" aria-hidden="true"
                          style="display:none"></span>
                      <span class="align-middle d-sm-inline-block d-none me-sm-1">Kaydet</span>
                      <i class="mdi mdi-check"></i></button>
                  </div>
                @endcan

                <div class="col-sm-2">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" class="form-control" placeholder="DD.MM.YYYY" name="degerlendirmekarartarih"
                             id="degerlendirmekarartarih" value="{{$karar->degerlendirmekarartarih}}"/>
                      <label for="degerlendirmekarartarih">Belgelendirme Karar Tarihi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" id="belgelendirileceksistemler"
                           name="belgelendirileceksistemler"
                           class="form-control" placeholder=""
                           value="{{$belgelendirileceksistemler}}"/>
                    <label for="belgelendirileceksistemler">Belgelendirilen Sistemler</label>
                  </div>
                </div>
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

                {{-- EA/KATEGORİ/TEKNİK ALAN --}}
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gizliea" name="eakodu" class="form-control" value="<?php echo $ea; ?>"/>
                      <label for="gizliea">Ea Kodu</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gizlinace" name="firmanacekodu" class="form-control"
                             value="<?php echo $nace; ?>"/>
                      <label for="gizlinace">Nace Kodu</label>
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
                      <input type="text" id="gizlienysta" name="enysteknikalan" class="form-control"
                             value="<?php echo $enysteknikalan; ?>"/>
                      <label for="gizlienysta">EnYs Teknik Alan</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="gizlibgys" name="bgcategories" class="form-control"
                             value="<?php echo $bgkat; ?>"/>
                      <label for="gizlibgys">BGYS Teknik Alan</label>
                    </div>
                  </div>
                </div>
                {{-- EA/KATEGORİ/TEKNİK ALAN --}}

                {{-- KAPSAM --}}
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsam" name="belgelendirmekapsami"
                              placeholder="">{{$pot->belgelendirmekapsami}}</textarea>
                    <label for="formValidationKapsam">Kapsam</label>
                  </div>
                </div>
                {{-- KAPSAM --}}

                {{-- GÖZDEN GEÇİRELECEK KONULAR --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">Gözden Geçirme</div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        <table class="table table-bordered">
                          <thead>
                          <tr>
                            <th scope="col">Gözden Geçirilecek Konular</th>
                            <th scope="col" style="width: 100px" class="text-center">Uygun</th>
                            <th scope="col" style="width: 100px" class="text-center">Uygun Değil</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>1. Kuruluş unvanı ve adres</td>
                            <td class="text-center"><input type="radio" name="karargga"
                                                           value="u" {{(isset($karargg->karargga) && $karargg->karargga === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="karargga"
                                                           value="ud" {{(isset($karargg->karargga) && $karargg->karargga === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>2. Belgelendirme kapsamı</td>
                            <td class="text-center"><input type="radio" name="kararggb"
                                                           value="u" {{(isset($karargg->kararggb) && $karargg->kararggb === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggb"
                                                           value="ud" {{(isset($karargg->kararggb) && $karargg->kararggb === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>3. Denetim Adam/Gün sayısı</td>
                            <td class="text-center"><input type="radio" name="kararggc"
                                                           value="u" {{(isset($karargg->kararggc) && $karargg->kararggc === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggc"
                                                           value="ud" {{(isset($karargg->kararggc) && $karargg->kararggc === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>4. Denetim ekibi ilgili EA/NACE & Kategoride/Alt Kategori & Teknik Alanda atanmış</td>
                            <td class="text-center"><input type="radio" name="kararggd"
                                                           value="u" {{(isset($karargg->kararggd) && $karargg->kararggd === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggd"
                                                           value="ud" {{(isset($karargg->kararggd) && $karargg->kararggd === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>5. Gerekli ise Teknik Uzman kullanılmış</td>
                            <td class="text-center"><input type="radio" name="karargge"
                                                           value="u" {{(isset($karargg->karargge) && $karargg->karargge === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="karargge"
                                                           value="ud" {{(isset($karargg->karargge) && $karargg->karargge === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>6. Denetim ekibi referans standardlarda atanmış</td>
                            <td class="text-center"><input type="radio" name="kararggf"
                                                           value="u" {{(isset($karargg->kararggf) && $karargg->kararggf === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggf"
                                                           value="ud" {{(isset($karargg->kararggf) && $karargg->kararggf === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>7. Denetim ekibi tarafsızlık beyanı imzalamış</td>
                            <td class="text-center"><input type="radio" name="kararggg"
                                                           value="u" {{(isset($karargg->kararggg) && $karargg->kararggg === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggg"
                                                           value="ud" {{(isset($karargg->kararggg) && $karargg->kararggg === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>8. Birden fazla saha var ise gerekli sayıda saha görülmüş</td>
                            <td class="text-center"><input type="radio" name="kararggh"
                                                           value="u" {{(isset($karargg->kararggh) && $karargg->kararggh === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggh"
                                                           value="ud" {{(isset($karargg->kararggh) && $karargg->kararggh === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>9. Denetim Planı</td>
                            <td class="text-center"><input type="radio" name="kararggi"
                                                           value="u" {{(isset($karargg->kararggi) && $karargg->kararggi === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggi"
                                                           value="ud" {{(isset($karargg->kararggi) && $karargg->kararggi === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>10. Kod/Kategor/Teknik Alan Denetçisi veya Teknik Uzmanın incelemesi gereken standard
                              maddelerine dikkat edilmiş
                            </td>
                            <td class="text-center"><input type="radio" name="kararggj"
                                                           value="u" {{(isset($karargg->kararggj) && $karargg->kararggj === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggj"
                                                           value="ud" {{(isset($karargg->kararggj) && $karargg->kararggj === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>11. Raporlardaki uygunsuzluk sayısı ve tanımları tutarlı</td>
                            <td class="text-center"><input type="radio" name="kararggk"
                                                           value="u" {{(isset($karargg->kararggk) && $karargg->kararggk === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggk"
                                                           value="ud" {{(isset($karargg->kararggk) && $karargg->kararggk === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>12. Varsa, uygunsuzluklar ve kapama kanıtları</td>
                            <td class="text-center"><input type="radio" name="kararggl"
                                                           value="u" {{(isset($karargg->kararggl) && $karargg->kararggl === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggl"
                                                           value="ud" {{(isset($karargg->kararggl) && $karargg->kararggl === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>13. Enerji performans iyileştirmesinin sürekli olup olmadığının gösterilmesi ilişkin
                              kanıt
                            </td>
                            <td class="text-center"><input type="radio" name="kararggm"
                                                           value="u" {{(isset($karargg->kararggm) && $karargg->kararggm === 'u') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararggm"
                                                           value="ud" {{(isset($karargg->kararggm) && $karargg->kararggm === 'ud') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- GÖZDEN GEÇİRELECEK KONULAR --}}

                {{-- KARAR --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">Karar</div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        <div class="row g-4">
                          <div class="col-sm-6">
                            <div class="form-floating form-floating-outline">
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgeduruma" name="belgedurum"
                                       value="devam" {{($karar->belgedurum === 'devam') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgeduruma">Belgelendirme verilebilir</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumb" name="belgedurum"
                                       value="aski" {{(!$karar->belgedurum === 'aski') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumb">Belgelendirme askıya
                                  alınabilir</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumg" name="belgedurum"
                                       value="genis" {{(!$karar->belgedurum === 'genis') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumg">Belgelendirme kapsamı
                                  genişletilebilir</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumyb" name="belgedurum"
                                       value="ybver" {{(!$karar->belgedurum === 'ybver') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumyb">Yeniden belgelendirme
                                  verilebilir</label>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="form-floating form-floating-outline">
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumbv" name="belgedurum"
                                       value="verilemez" {{(!$karar->belgedurum === 'verilemez') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumbv">Belgelendirme verilemez</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumc" name="belgedurum"
                                       value="askiindir" {{(!$karar->belgedurum === 'askiindir') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumc">Belgelendirme askıdan
                                  indirilebilir</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumdar" name="belgedurum"
                                       value="daralt" {{(!$karar->belgedurum === 'daralt') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumdar">Belgelendirme kapsamı
                                  daraltılabilir</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="belgedurumd" name="belgedurum"
                                       value="iptal" {{(!$karar->belgedurum === 'iptal') ? 'checked' : ''}} />
                                <label class="form-check-label" for="belgedurumd">Belgelendirme geri çekilebilir</label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- KARAR --}}

                {{-- AÇIKLAMA --}}
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="kararaciklama" name="kararaciklama"
                              placeholder="">{{$karar->kararaciklama}}</textarea>
                    <label for="kararaciklama">Açıklama</label>
                  </div>
                </div>
                {{-- AÇIKLAMA --}}

                {{-- Başdenetçinin Değerlendirilmesi --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">Başdenetçinin Değerlendirilmesi</div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        <table class="table table-bordered">
                          <thead>
                          <tr>
                            <th colspan="2" scope="col" class="text-center">Değerlendirme Kriterleri</th>
                            <th scope="col" class="text-center" style="width: 100px">İyi<br>(2)</th>
                            <th scope="col" class="text-center" style="width: 100px">Kısmen iyi<br>(1)</th>
                            <th scope="col" class="text-center" style="width: 100px">Kötü<br>(0)</th>
                          </tr>
                          </thead>
                          <tbody>
                          <tr>
                            <td>1</td>
                            <td>Denetim Planının uygunluğu</td>
                            <td class="text-center"><input type="radio" name="kararbda" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbda) && $kararbd->kararbda === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbda" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbda) && $kararbd->kararbda === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbda" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbda) && $kararbd->kararbda === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>Denetim Raporunun uygunluğu</td>
                            <td class="text-center"><input type="radio" name="kararbdb" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdb) && $kararbd->kararbdb === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdb" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdb) && $kararbd->kararbdb === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdb" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdb) && $kararbd->kararbdb === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>Denetim Raporunda objektif kanıtları ifade etme becerisi</td>
                            <td class="text-center"><input type="radio" name="kararbdc" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdc) && $kararbd->kararbdc === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdc" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdc) && $kararbd->kararbdc === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdc" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdc) && $kararbd->kararbdc === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>Tespit edilen uygunsuzlukların yeterliliğini değerlendirme etkinliği</td>
                            <td class="text-center"><input type="radio" name="kararbdd" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdd) && $kararbd->kararbdd === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdd" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdd) && $kararbd->kararbdd === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdd" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdd) && $kararbd->kararbdd === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>Belgelendirme kapsamı dahilinde belirlenen proseslerin uygunluğu/yeterliliğini
                              değerlendirme etkinliği
                            </td>
                            <td class="text-center"><input type="radio" name="kararbde" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbde) && $kararbd->kararbde === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbde" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbde) && $kararbd->kararbde === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbde" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbde) && $kararbd->kararbde === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>6</td>
                            <td>Belgelendirme kapsamı dahilinde yasal mevzuatın değerlendirme etkinliği</td>
                            <td class="text-center"><input type="radio" name="kararbdf" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdf) && $kararbd->kararbdf === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdf" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdf) && $kararbd->kararbdf === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdf" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdf) && $kararbd->kararbdf === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>7</td>
                            <td>Hariç tutma/uygulanabilir olmayan maddelerin haklı gerekçelerini değerlendirme
                              etkinliği
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdg" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdg) && $kararbd->kararbdg === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdg" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdg) && $kararbd->kararbdg === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdg" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdg) && $kararbd->kararbdg === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>8</td>
                            <td>İç denetim, YGG, hedef… vb. yeterliliğini değerlendirme etkinliği</td>
                            <td class="text-center"><input type="radio" name="kararbdh" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdh) && $kararbd->kararbdh === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdh" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdh) && $kararbd->kararbdh === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdh" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdh) && $kararbd->kararbdh === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>9</td>
                            <td>Bağlam ve ilgili taraflar ile kapsam yeterliliğini değerlendirme etkinliği</td>
                            <td class="text-center"><input type="radio" name="kararbdi" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdi) && $kararbd->kararbdi === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdi" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdi) && $kararbd->kararbdi === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdi" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdi) && $kararbd->kararbdi === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td>10</td>
                            <td>Risk ve fırsatların yeterliliğini değerlendirme etkinliği</td>
                            <td class="text-center"><input type="radio" name="kararbdj" value="2"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdj) && $kararbd->kararbdj === '2') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdj" value="1"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdj) && $kararbd->kararbdj === '1') ? 'checked' : ''}} />
                            </td>
                            <td class="text-center"><input type="radio" name="kararbdj" value="0"
                                                           onchange="recalcScore()" {{(isset($kararbd->kararbdj) && $kararbd->kararbdj === '0') ? 'checked' : ''}} />
                            </td>
                          </tr>
                          <tr>
                            <td colspan="2"><span class="float-end" style="margin-right: 1px">Toplam Puan</span></td>
                            <td colspan="3"><input type="text" id="toplampuan" name="toplampuan" class="form-control"
                                                   placeholder="" value="{{$toplampuan}}"/></td>
                          </tr>
                          <tr>
                            <td colspan="2"><span class="float-end" style="margin-right: 1px">Ortalama Puan</span></td>
                            <td colspan="3"><input type="text" id="ortalamapuan" name="ortalamapuan"
                                                   class="form-control" placeholder="" value="{{$ortalamapuan}}"/></td>
                          </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- Başdenetçinin Değerlendirilmesi --}}

                {{-- Tarafsızlık ve Gizlilik Beyanı --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">Tarafsızlık ve Gizlilik Beyanı</div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        <span class="fw-bold">Yukarıda adı geçen müşteri kuruluşla;</span>
                        <br>
                        - Son 2 yıl içerisinde herhangi bir ticari ya da çıkar ilişkim olmadığını ve danışman olarak
                        müşteride bulunmadığımı,<br>
                        - Gelecek 2 yıl içerisinde herhangi bir ticari ya da çıkar ilişkim olmayacağını ve danışman
                        olarak müşteri kuruluşta bulunmayacağımı,<br>
                        - Aliment’ i çıkar çatışmasına sokabilecek herhangi bir durumdan haberdar olduğunda, bu durumu
                        derhal Aliment’ e bildireceğimi,<br>
                        - Belgelendirme faaliyetleri sırasında elde ettiğim tüm bilgiler için gizlilik ilkesine uygun
                        davranacağımı<br>
                        beyan ve taahhüt ederim.
                      </div>
                    </div>
                  </div>
                </div>
                {{-- Tarafsızlık ve Gizlilik Beyanı --}}

                {{-- KOMİTE ÜYELERİ --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">
                        <span class="note needsclick">* Kutucuğu işaretleyerek <span class="fw-medium">Tarafsızlık ve Gizlilik Beyanını</span> onaylayın.</span></div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        <div class="row gy-4 mb-4">
                          <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkKararaonerilendenetci" type="checkbox"
                                         value="1" aria-label="Checkbox for following text input" checked>
                                </div>
                                <input type="text" id="kararaonerilendenetci" name="kararaonerilendenetci"
                                       class="form-control"
                                       value="<?php echo $pot->kararaonerilendenetci; ?>"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkuye1adi" name="chkuye1adi"
                                         type="checkbox" value="1"
                                         {{(isset($kararo->uye1adi) && $kararo->uye1adi === 1) ? 'checked' : ''}} aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" id="uye1adi" name="uye1adi" class="form-control"
                                       value="<?php echo $karar->uye1adi; ?>"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkuye2adi" name="chkuye2adi"
                                         type="checkbox" value="1"
                                         {{(isset($kararo->uye2adi) && $kararo->uye2adi === 1) ? 'checked' : ''}} aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" id="uye2adi" name="uye2adi" class="form-control"
                                       value="<?php echo $karar->uye2adi; ?>"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkuye3adi" name="chkuye3adi"
                                         type="checkbox" value="1"
                                         {{(isset($kararo->uye3adi) && $kararo->uye3adi === 1) ? 'checked' : ''}} aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" id="uye3adi" name="uye3adi" class="form-control"
                                       value="<?php echo $karar->uye3adi; ?>"/>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-6">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkuyeikuadi" name="chkuyeikuadi"
                                         type="checkbox" value="1"
                                         {{(isset($kararo->uyeikuadi) && $kararo->uyeikuadi === 1) ? 'checked' : ''}} aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" id="uyeikuadi" name="uyeikuadi" class="form-control"
                                       value="<?php echo $karar->uyeikuadi; ?>"/>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- KOMİTE ÜYELERİ --}}

                @can('bgmuduru')
                {{-- Belgelendirmenin Sürdürülebilirlik Kararı --}}
                <div id="divbgmuduronay" class="col-sm-12 {{($asama === "g1karar" || $asama === "g2karar") ? '' : 'hide'}}">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">*Bu alan, sadece gözetim denetimleri sonrasında,
                        gereken durumlarda doldurulacaktır.
                      </div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        Belgelendirmenin Sürdürülebilirlik Kararı<br>
                        <div class="row g-4">
                          <div class="col-sm-12">
                            <div class="form-floating form-floating-outline">
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="bskarara" name="bskarar"
                                       value="u" {{($karar->bskarar === 'u') ? 'checked' : ''}} />
                                <label class="form-check-label" for="bskarara">Uygun</label>
                              </div>
                              <div class="form-check form-check">
                                <input class="form-check-input" type="radio" id="bskararb" name="bskarar"
                                       value="ud" {{(!$karar->bskarar === 'ud') ? 'checked' : ''}} />
                                <label class="form-check-label" for="bskararb">Uygun değil</label>
                              </div>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="input-group input-group-merge">
                              <div class="input-group">
                                <div class="input-group-text form-check mb-0">
                                  <input class="form-check-input m-auto" id="chkbgmudur" name="chkbgmudur"
                                         type="checkbox" value="1" aria-label="Checkbox for following text input">
                                </div>
                                <input type="text" id="bgmuduronayi" name="bgmuduronayi" class="form-control"
                                       value="Belgelendirme Müdürü Onay" readonly/>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                {{-- Belgelendirmenin Sürdürülebilirlik Kararı --}}
                @endcan

                {{-- DENETİM EKİBİ --}}
                <div class="col-sm-12">
                  <div class="card card-action mb-4">
                    <div class="card-header">
                      <div class="card-action-title text-center">Denetim ekibi</div>
                      <div class="card-action-element">
                        <ul class="list-inline mb-0">
                          <li class="list-inline-item">
                            <a href="javascript:void(0);" class="card-collapsible"><i
                                class="tf-icons mdi mdi-chevron-up"></i></a>
                          </li>
                        </ul>
                      </div>
                    </div>
                    <div class="collapse show">
                      <div class="card-body">
                        @php
                          echo $de;
                        @endphp
                      </div>
                    </div>
                  </div>
                </div>
                {{-- DENETİM EKİBİ --}}

                @can('karar')
                <div class="col-12 justify-content-between">
                  <button type="button" class="btn btn-primary btn-next" style="float: right"
                          onclick="kararHazirla()">
                    <span id="setSetHazirlaSpinner" class="spinner-border me-1" role="status" aria-hidden="true"
                          style="display:none"></span>
                    <span class="align-middle d-sm-inline-block d-none me-sm-1">Kaydet</span>
                    <i class="mdi mdi-check"></i></button>
                </div>
                @endcan

                <div class="card">
                  <h5 class="card-header justify-content-center">
                    SONUÇ
                  </h5>
                  <div class="card-datatable text-wrap">
                    <div class="row g-4">
                      <div class="col-sm-12">
                        <div id='btnAsRaporYazdirLink'></div>
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
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Karar</h5>
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
                <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Karar</h5>
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
