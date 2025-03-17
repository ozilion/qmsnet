@extends('layouts.layoutMaster')

@section('title', '['.$pno.'] '.$asama . " başvuru bilgileri düzenleme")

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}"/>
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/plan-basvuru-form-wizard-validation.js')}}"></script>
@endsection

@section('content')
  <!-- Toast with Animation -->
  <div class="bs-toast toast toast-ex animate__animated my-2 " role="alert" aria-live="assertive" aria-atomic="true"
       data-bs-delay="5000">
    <div class="toast-header">
      <i class="mdi mdi-home me-2 "></i>
      <div class="me-auto fw-medium">Bilgilendirme</div>
      <small class="text-muted"></small>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div id="toast-body" class="toast-body">

    </div>
  </div>
  <!--/ Toast with Animation -->
  <div class="row">
    <div class="col-12">
      <h5>[{{$pno}}] {{$basvuru[0]->firmaadi}}</h5>
    </div>
    <div class="row gy-4 mb-4">
      <div class="col-xl-12">
        <div class="card">
          <div
            class="card-header sticky-element bg-info d-flex justify-content-sm-between align-items-sm-center flex-column flex-sm-row">
            <h5 class="card-title mb-sm-0 me-2"></h5>
            @include('_partials/planlama-menu', ['pno' => $pno])
          </div>
          <div class="card-body">
            <!-- Validation Wizard -->
            <div class="col-12 mb-4">
              <small class="text-light fw-medium">Başvuru bilgileri düzenleme</small>
              <div id="basvuru-wizard-validation" class="bs-stepper mt-2">
                <div class="bs-stepper-header">
                  <div class="step" data-target="#company-details-validation">
                    <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
                      <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                      <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Kuruluş Bilgileri</span>
                <span class="bs-stepper-subtitle">Genel Bilgiler</span>
              </span>
            </span>
                    </button>
                  </div>
                  <div class="line mt-lg-n4 mb-lg-3"></div>
                  <div class="step" data-target="#personal-info-validation">
                    <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
                      <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                      <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Personel Bilgileri</span>
                <span class="bs-stepper-subtitle">Denetim süresini etkileyen verileri girin</span>
              </span>
            </span>
                    </button>
                  </div>
                  <div class="line mt-lg-n4 mb-lg-3"></div>
                  <div class="step" data-target="#social-links-validation">
                    <button type="button" class="step-trigger flex-lg-wrap gap-lg-2 px-lg-0">
                      <span class="bs-stepper-circle"><i class="mdi mdi-check"></i></span>
                      <span class="bs-stepper-label ms-lg-0">
              <span class="d-flex flex-column gap-1 text-lg-center">
                <span class="bs-stepper-title">Hesaplamaya esas bilgiler</span>
                <span class="bs-stepper-subtitle">Denetim süresini etkileyen veriler</span>
              </span>
            </span>
                    </button>
                  </div>
                </div>
                <div class="bs-stepper-content">
                  <form id="basvuru-wizard-validation-form" onSubmit="return false">
                    {{ csrf_field() }}
                    <input type="hidden" id="formBasvuruRoute" value="{{route('sbasvuru')}}">
                    <input type="hidden" id="planno" name="planno" value="{{$pno}}">
                    <input type="hidden" id="enyseffectiveemployee" name="enyseffectiveemployee" value="">
                    <input type="hidden" id="bgyseffectiveemployee" name="bgyseffectiveemployee" value="">
                    <!-- Account Details -->
                    <div id="company-details-validation" class="content">
                      <div class="content-header mb-3">
                        <h6 class="mb-0">Kuruluş Bilgileri</h6>
                        <small>Kuruluşa ait genel bilgileri giriniz.</small>
                      </div>
                      <div class="row g-4">
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="firmaadi" id="formValidationFirmaadi" class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->firmaadi}}"/>
                            <label for="formValidationFirmaadi">Kuruluş Adı</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="firmaadresi" id="formValidationFirmaadi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->firmaadresi}}"/>
                            <label for="formValidationFirmaadi">Kuruluş Merkez Adresi</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationIlce" name="milce" class="form-control"
                                     placeholder=""
                                     value="{{$basvuru[0]->milce}}"/>
                              <label for="formValidationIlce">İlçe</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationSehir" name="msehir" class="form-control"
                                     placeholder=""
                                     value="{{$basvuru[0]->msehir}}"/>
                              <label for="formValidationSehir">Şehir</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationFirmatelefon" name="firmatelefon"
                                     class="form-control"
                                     placeholder="" value="{{$basvuru[0]->firmatelefon}}"/>
                              <label for="formValidationFirmatelefon">Telefon</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationFaks" name="firmafaks" class="form-control"
                                     placeholder=""
                                     value="{{$basvuru[0]->firmafaks}}"/>
                              <label for="formValidationFaks">Faks</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationFirmaeposta" name="firmaeposta" class="form-control"
                                     placeholder="" value="{{$basvuru[0]->firmaeposta}}"/>
                              <label for="formValidationFirmaeposta">E-Posta</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="input-group input-group-merge">
                            <div class="form-floating form-floating-outline">
                              <input type="text" id="formValidationFirmaweb" name="firmaweb" class="form-control"
                                     placeholder=""
                                     value="{{$basvuru[0]->firmaweb}}"/>
                              <label for="formValidationFirmaweb">Web Adresi</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso900115varyok"
                                     name="iso900115varyok" {{($basvuru[0]->iso900115varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="iso900115varyok">
                                ISO 9001:2015
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso1400115varyok"
                                     name="iso1400115varyok" {{($basvuru[0]->iso1400115varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="iso1400115varyok">
                                ISO 14001:2015
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso4500118varyok"
                                     name="iso4500118varyok" {{($basvuru[0]->iso4500118varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="iso4500118varyok">
                                ISO 45001:2018
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso2200018varyok"
                                     name="iso2200018varyok" {{($basvuru[0]->iso2200018varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="iso2200018varyok">
                                ISO 22000:2018
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso27001varyok"
                                     name="iso27001varyok" {{($basvuru[0]->iso27001varyok === 1) ? 'checked' : ''}} onclick="iso27001ekformAc()" />
                              <label class="form-check-label" for="iso27001varyok">
                                ISO 27001:2022
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="iso5000118varyok"
                                     name="iso5000118varyok"
                                     {{($basvuru[0]->iso5000118varyok === 1) ? 'checked' : ''}} onclick="iso50001ekformAc()"/>
                              <label class="form-check-label" for="iso5000118varyok">
                                ISO 50001:2018
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1" id="helalvaryok"
                                     name="helalvaryok" {{($basvuru[0]->helalvaryok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="helalvaryok">
                                OIC/SMIIC 1:2019
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="oicsmiik6varyok"
                                     name="oicsmiik6varyok" {{($basvuru[0]->oicsmiik6varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="oicsmiik6varyok">
                                OIC/SMIIC 6:2019
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="oicsmiik9varyok"
                                     name="oicsmiik9varyok" {{($basvuru[0]->oicsmiik9varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="oicsmiik9varyok">
                                OIC/SMIIC 9:2019
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="oicsmiik171varyok"
                                     name="oicsmiik171varyok" {{($basvuru[0]->oicsmiik171varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="oicsmiik171varyok">
                                OIC/SMIIC 17-1:2020
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="oicsmiik23varyok"
                                     name="oicsmiik23varyok" {{($basvuru[0]->oicsmiik23varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="oicsmiik23varyok">
                                OIC/SMIIC 23:2022
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="1"
                                     id="oicsmiik24varyok"
                                     name="oicsmiik24varyok" {{($basvuru[0]->oicsmiik24varyok === 1) ? 'checked' : ''}} />
                              <label class="form-check-label" for="oicsmiik24varyok">
                                OIC/SMIIC 24:2020
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="input-group input-group-merge">
                        <span class="input-group-text" id="formValidationDigersistemlerneler"><i
                            class="mdi mdi-file"></i></span>
                            <input type="text" class="form-control" placeholder="Diğer..." aria-label="Search..."
                                   aria-describedby="formValidationDigersistemlerneler" id="digersistemlerneler"
                                   name="digersistemlerneler"
                                   value="{{$basvuru[0]->digersistemlerneler}}"/>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsam" name="belgelendirmekapsami"
                              placeholder="">{{$basvuru[0]->belgelendirmekapsami}}</textarea>
                            <label for="formValidationKapsam">Kapsam</label>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsaming" name="belgelendirmekapsamiing"
                              placeholder="">{{$basvuru[0]->belgelendirmekapsamiing}}</textarea>
                            <label for="formValidationKapsaming">Scope</label>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="harictutulanmaddeler"
                              name="harictutulanmaddeler"
                              placeholder="">{{$basvuru[0]->harictutulanmaddeler}}</textarea>
                            <label for="harictutulanmaddeler">ISO 9001 için uygulanabilir olmayan madde varsa
                              belirtiniz.</label>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="diskaynakliprosesler" name="diskaynakliprosesler"
                              placeholder="">{{$basvuru[0]->diskaynakliprosesler}}</textarea>
                            <label for="diskaynakliprosesler">Dış kaynaklı hale getirilmiş proses/ler varsa bilgi
                              veriniz.</label>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                            <div class="card">
                              <div class="card-header">
                                Prosesler, işlemler, teknik kaynaklar ve fonksiyonlar ile ilgili bilgi veriniz.
                              </div>
                              <div class="card-body">
                                <div class="row g-4">
                                  <div class="col-sm-6">
                                    <div class="form-floating form-floating-outline">
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="basitproses"
                                               name="basitproses" {{($basvuru[0]->basitproses === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="basitproses">
                                          Basit Proses
                                        </label>
                                      </div>
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="calismaalanikucuk"
                                               name="calismaalanikucuk" {{($basvuru[0]->calismaalanikucuk === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="calismaalanikucuk">
                                          Çalışma alanı küçük
                                        </label>
                                      </div>
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="prosesdusukrisk"
                                               name="prosesdusukrisk" {{($basvuru[0]->prosesdusukrisk === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="prosesdusukrisk">
                                          Proseslerde düşük risk
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-sm-6">
                                    <div class="form-floating form-floating-outline">
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="karisikproses"
                                               name="karisikproses" {{($basvuru[0]->karisikproses === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="karisikproses">
                                          Karışık proses
                                        </label>
                                      </div>
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1" id="calismaalanibuyuk"
                                               name="calismaalanibuyuk" {{($basvuru[0]->calismaalanibuyuk === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="calismaalanibuyuk">
                                          Büyük çalışma alanı
                                        </label>
                                      </div>
                                      <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="1"
                                               id="otomasyonkullanimi"
                                               name="otomasyonkullanimi" {{($basvuru[0]->otomasyonkullanimi === 1) ? 'checked' : ''}} />
                                        <label class="form-check-label" for="otomasyonkullanimi">
                                          Otomasyon kullanımı
                                        </label>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="yukumluolunanmevzuatlar" name="yukumluolunanmevzuatlar"
                              placeholder="">{{$basvuru[0]->yukumluolunanmevzuatlar}}</textarea>
                            <label for="yukumluolunanmevzuatlar">Uymak zorunda olduğunuz yasal yükümlülüklerle ilgili
                              bilgi
                              veriniz.</label>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="kullanilandanismanlikhizmeti"
                              name="kullanilandanismanlikhizmeti"
                              placeholder="">{{$basvuru[0]->kullanilandanismanlikhizmeti}}</textarea>
                            <label for="kullanilandanismanlikhizmeti">Eğer kullanıldıysa danışmanlık hizmetleri hakkında
                              bilgi
                              veriniz (Kuruluş ve danışman adı).</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <small class="text-light fw-medium d-block">Mevcut yönetim sistemi sertifikanız var
                              mı?</small>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" id="yonetimsistemsertifikasi1"
                                     name="yonetimsistemsertifikasi"
                                     value="1" {{($basvuru[0]->yonetimsistemsertifikasi) ? 'checked' : ''}} />
                              <label class="form-check-label" for="yonetimsistemsertifikasi1">Evet</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" id="yonetimsistemsertifikasi2"
                                     name="yonetimsistemsertifikasi"
                                     value="0" {{(!$basvuru[0]->yonetimsistemsertifikasi) ? 'checked' : ''}} />
                              <label class="form-check-label" for="yonetimsistemsertifikasi2">Hayır</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="yonetimsistemisuresi" id="yonetimsistemisuresi"
                                   class="form-control"
                                   placeholder="3+ YIL" value="{{$basvuru[0]->yonetimsistemisuresi}}"/>
                            <label for="yonetimsistemisuresi">Varsa yönetim sistemi ne kadar süredir
                              uygulanmaktadır?</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="haccpcalismasisayisi" id="haccpcalismasisayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->haccpcalismasisayisi}}"/>
                            <label for="haccpcalismasisayisi">22000 HACCP çalışması sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="haccpcalismasisayisismiic" id="haccpcalismasisayisismiic"
                                   class="form-control" placeholder=""
                                   value="{{$basvuru[0]->haccpcalismasisayisismiic}}"/>
                            <label for="haccpcalismasisayisismiic">SMIIC HACCP çalışması sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="helalurunsayisi" id="helalurunsayisi" class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->helalurunsayisi}}"/>
                            <label for="helalurunsayisi">SMIIC ürün sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="sahasayisi22" id="sahasayisi22" class="form-control" placeholder=""
                                   value="{{$basvuru[0]->sahasayisi22}}"/>
                            <label for="sahasayisi22">ISO 22000/Helal Belgelendirme Ziyaret edilecek saha sayısı<br>Merkez ofis hariç</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <select id="oicsmiickk" name="oicsmiickk" class="selectpicker w-100"
                                    data-style="btn-default">
                              <option value="">Karmaşıklık Kategorisi Seçiniz...</option>
                              <optgroup label="Çok Yüksek">
                                <option value="4-1"<?= ($basvuru[0]->oicsmiickk == "4-1") ? " selected" : "" ?>>
                                  Başka yerde sınıflandırılmamış kimyasallar
                                </option>
                                <option value="4-2"<?= ($basvuru[0]->oicsmiickk == "4-2") ? " selected" : "" ?>>
                                  Farmasötik ürünler
                                </option>
                                <option value="4-3"<?= ($basvuru[0]->oicsmiickk == "4-3") ? " selected" : "" ?>>
                                  Işlenmiş et ürünleri
                                </option>
                                <option value="4-4"<?= ($basvuru[0]->oicsmiickk == "4-4") ? " selected" : "" ?>>
                                  Genetiği değiştirilmiş ürünleri
                                </option>
                                <option value="4-5"<?= ($basvuru[0]->oicsmiickk == "4-5") ? " selected" : "" ?>>
                                  Gıda katkıda maddeleri
                                </option>
                                <option value="4-6"<?= ($basvuru[0]->oicsmiickk == "4-6") ? " selected" : "" ?>>
                                  Biyokültürler
                                </option>
                                <option value="4-7"<?= ($basvuru[0]->oicsmiickk == "4-7") ? " selected" : "" ?>>
                                  Kozmetik ürünler
                                </option>
                                <option value="4-8"<?= ($basvuru[0]->oicsmiickk == "4-8") ? " selected" : "" ?>>
                                  Işlem yardımcıları
                                </option>
                                <option value="4-9"<?= ($basvuru[0]->oicsmiickk == "4-9") ? " selected" : "" ?>>
                                  Mikro-organizmalar
                                </option>
                              </optgroup>
                              <optgroup label="Yüksek">
                                <option value="3-1"<?= ($basvuru[0]->oicsmiickk == "3-1") ? " selected" : "" ?>>
                                  Kesimhane ve tavukçuluk
                                </option>
                                <option value="3-2"<?= ($basvuru[0]->oicsmiickk == "3-2") ? " selected" : "" ?>>
                                  Peynir ürünleri
                                </option>
                                <option value="3-3"<?= ($basvuru[0]->oicsmiickk == "3-3") ? " selected" : "" ?>>
                                  Bisküvi çeşitleri
                                </option>
                                <option value="3-4"<?= ($basvuru[0]->oicsmiickk == "3-4") ? " selected" : "" ?>>
                                  Yağ
                                </option>
                                <option value="3-5"<?= ($basvuru[0]->oicsmiickk == "3-5") ? " selected" : "" ?>>
                                  İçecekler
                                </option>
                                <option value="3-6"<?= ($basvuru[0]->oicsmiickk == "3-6") ? " selected" : "" ?>>
                                  Oteller
                                </option>
                                <option value="3-7"<?= ($basvuru[0]->oicsmiickk == "3-7") ? " selected" : "" ?>>
                                  Restoranlar
                                </option>
                                <option value="3-8"<?= ($basvuru[0]->oicsmiickk == "3-8") ? " selected" : "" ?>>
                                  Beslenme takviyeleri
                                </option>
                                <option value="3-9"<?= ($basvuru[0]->oicsmiickk == "3-9") ? " selected" : "" ?>>
                                  Temizlik maddeleri
                                </option>
                                <option value="3-10"<?= ($basvuru[0]->oicsmiickk == "3-10") ? " selected" : "" ?>>
                                  Ambalajlama malzemeleri
                                </option>
                                <option value="3-11"<?= ($basvuru[0]->oicsmiickk == "3-11") ? " selected" : "" ?>>
                                  Tekstil ürünleri
                                </option>
                                <option value="3-12"<?= ($basvuru[0]->oicsmiickk == "3-12") ? " selected" : "" ?>>
                                  İslami finans
                                </option>
                              </optgroup>
                              <optgroup label="Orta">
                                <option value="2-1"<?= ($basvuru[0]->oicsmiickk == "2-1") ? " selected" : "" ?>>
                                  Süt ürünleri
                                </option>
                                <option value="2-2"<?= ($basvuru[0]->oicsmiickk == "2-2") ? " selected" : "" ?>>
                                  Balık ürünleri
                                </option>
                                <option value="2-3"<?= ($basvuru[0]->oicsmiickk == "2-3") ? " selected" : "" ?>>
                                  Yumurta ürünleri
                                </option>
                                <option value="2-4"<?= ($basvuru[0]->oicsmiickk == "2-4") ? " selected" : "" ?>>
                                  Arıcılık
                                </option>
                                <option value="2-5"<?= ($basvuru[0]->oicsmiickk == "2-5") ? " selected" : "" ?>>
                                  Baharatlar
                                </option>
                                <option value="2-6"<?= ($basvuru[0]->oicsmiickk == "2-6") ? " selected" : "" ?>>
                                  Bahçe ürünleri
                                </option>
                                <option value="2-7"<?= ($basvuru[0]->oicsmiickk == "2-7") ? " selected" : "" ?>>
                                  Korunmuş meyveler
                                </option>
                                <option value="2-8"<?= ($basvuru[0]->oicsmiickk == "2-8") ? " selected" : "" ?>>
                                  Korunmuş bitkiler
                                </option>
                                <option value="2-9"<?= ($basvuru[0]->oicsmiickk == "2-9") ? " selected" : "" ?>>
                                  Konserve ürünler
                                </option>
                                <option value="2-10"<?= ($basvuru[0]->oicsmiickk == "2-10") ? " selected" : "" ?>>
                                  Makarna
                                </option>
                                <option value="2-11"<?= ($basvuru[0]->oicsmiickk == "2-11") ? " selected" : "" ?>>
                                  Şeker
                                </option>
                                <option value="2-12"<?= ($basvuru[0]->oicsmiickk == "2-12") ? " selected" : "" ?>>
                                  Hayvan yemi
                                </option>
                                <option value="2-13"<?= ($basvuru[0]->oicsmiickk == "2-13") ? " selected" : "" ?>>
                                  Balık yemi
                                </option>
                                <option value="2-14"<?= ($basvuru[0]->oicsmiickk == "2-14") ? " selected" : "" ?>>
                                  Su tedariki
                                </option>
                                <option value="2-15"<?= ($basvuru[0]->oicsmiickk == "2-15") ? " selected" : "" ?>>
                                  Ürünlerin geliştirilmesi
                                </option>
                                <option value="2-16"<?= ($basvuru[0]->oicsmiickk == "2-16") ? " selected" : "" ?>>
                                  Proses ve ekipman
                                </option>
                                <option value="2-17"<?= ($basvuru[0]->oicsmiickk == "2-17") ? " selected" : "" ?>>
                                  Veterinerlik hizmetleri
                                </option>
                                <option value="2-18"<?= ($basvuru[0]->oicsmiickk == "2-18") ? " selected" : "" ?>>
                                  Proses ekipmanı
                                </option>
                                <option value="2-19"<?= ($basvuru[0]->oicsmiickk == "2-19") ? " selected" : "" ?>>
                                  Otomatlar
                                </option>
                                <option value="2-20"<?= ($basvuru[0]->oicsmiickk == "2-20") ? " selected" : "" ?>>
                                  Deri ürünleri
                                </option>
                                <option value="2-21"<?= ($basvuru[0]->oicsmiickk == "2-21") ? " selected" : "" ?>>
                                  Unlu Mamuller(YAŞ PASTA, KAHKE, POĞAÇA, SİMİT, KATMER, KURU
                                  PASTA VB.)
                                </option>
                              </optgroup>
                              <optgroup label="Düşük">
                                <option value="1-1"<?= ($basvuru[0]->oicsmiickk == "1-1") ? " selected" : "" ?>>
                                  Balık
                                </option>
                                <option value="1-2"<?= ($basvuru[0]->oicsmiickk == "1-2") ? " selected" : "" ?>>
                                  Yumurta üretimi
                                </option>
                                <option value="1-3"<?= ($basvuru[0]->oicsmiickk == "1-3") ? " selected" : "" ?>>
                                  Süt üretimi
                                </option>
                                <option value="1-4"<?= ($basvuru[0]->oicsmiickk == "1-4") ? " selected" : "" ?>>
                                  Balıkçılık
                                </option>
                                <option value="1-5"<?= ($basvuru[0]->oicsmiickk == "1-5") ? " selected" : "" ?>>
                                  Avcılık
                                </option>
                                <option value="1-6"<?= ($basvuru[0]->oicsmiickk == "1-6") ? " selected" : "" ?>>
                                  Tuzakta yakalama
                                </option>
                                <option value="1-7"<?= ($basvuru[0]->oicsmiickk == "1-7") ? " selected" : "" ?>>
                                  Meyveler
                                </option>
                                <option value="1-8"<?= ($basvuru[0]->oicsmiickk == "1-8") ? " selected" : "" ?>>
                                  Sebzeler
                                </option>
                                <option value="1-9"<?= ($basvuru[0]->oicsmiickk == "1-9") ? " selected" : "" ?>>
                                  Tahıl
                                </option>
                                <option value="1-10"<?= ($basvuru[0]->oicsmiickk == "1-10") ? " selected" : "" ?>>
                                  Taze meyve ve taze meyve suları
                                </option>
                                <option value="1-11"<?= ($basvuru[0]->oicsmiickk == "1-11") ? " selected" : "" ?>>
                                  İçme suyu
                                </option>
                                <option value="1-12"<?= ($basvuru[0]->oicsmiickk == "1-12") ? " selected" : "" ?>>
                                  Un
                                </option>
                                <option value="1-13"<?= ($basvuru[0]->oicsmiickk == "1-13") ? " selected" : "" ?>>
                                  Tuz
                                </option>
                                <option value="1-14"<?= ($basvuru[0]->oicsmiickk == "1-14") ? " selected" : "" ?>>
                                  Perakende satış mağazaları
                                </option>
                                <option value="1-15"<?= ($basvuru[0]->oicsmiickk == "1-15") ? " selected" : "" ?>>
                                  Dükkânlar
                                </option>
                                <option value="1-16"<?= ($basvuru[0]->oicsmiickk == "1-16") ? " selected" : "" ?>>
                                  Toptan satıcılar
                                </option>
                                <option value="1-17"<?= ($basvuru[0]->oicsmiickk == "1-17") ? " selected" : "" ?>>
                                  Nakliye ve depolama
                                </option>
                                <option value="1-18"<?= ($basvuru[0]->oicsmiickk == "1-18") ? " selected" : "" ?>>
                                  Nişasta ve Şurupları
                                </option>
                              </optgroup>
                            </select>
                            <label for="oicsmiickk">OIC/SMIIC Karmaşıklık Kategorisi</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="hammadeler" id="hammadeler" class="form-control" placeholder=""
                                   value="{{$basvuru[0]->hammadeler}}"/>
                            <label for="hammadeler">Hammadeler</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="helalkkn" id="helalkkn" class="form-control" placeholder=""
                                   value="{{$basvuru[0]->helalkkn}}"/>
                            <label for="helalkkn">Helal kkn</label>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="aracsayisi" id="aracsayisi" class="form-control" placeholder=""
                                   value="{{$basvuru[0]->aracsayisi}}"/>
                            <label for="aracsayisi">OIC/SMIIC 17-1 için Araç Sayısı</label>
                          </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                          <button class="btn btn-outline-secondary btn-prev" disabled><i
                              class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">Geri</span>
                          </button>
                          <button class="btn btn-primary btn-next"><span
                              class="align-middle d-sm-inline-block d-none me-sm-1">İleri</span>
                            <i class="mdi mdi-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                    <!-- Personal Info -->
                    <div id="personal-info-validation" class="content">
                      <div class="content-header mb-3">
                        <h6 class="mb-0">Personel Bilgileri</h6>
                        <small>Denetim süresini etkileyen verileri girin</small>
                      </div>
                      <div class="row g-4">
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="beyazyakacalisansayisi" id="beyazyakacalisansayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->beyazyakacalisansayisi}}"/>
                            <label for="beyazyakacalisansayisi">Beyaz Yaka</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="tamzamanlicalisansayisi" id="tamzamanlicalisansayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->tamzamanlicalisansayisi}}"/>
                            <label for="tamzamanlicalisansayisi">Tam zamanlı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="yarizamanlicalisansayisi" id="yarizamanlicalisansayisi"
                                   class="form-control" placeholder=""
                                   value="{{$basvuru[0]->yarizamanlicalisansayisi}}"/>
                            <label for="yarizamanlicalisansayisi">Yarı zamanlı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="taseroncalisansayisi" id="taseroncalisansayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->taseroncalisansayisi}}"/>
                            <label for="taseroncalisansayisi">Taşeron</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="gecicicalisansayisi" id="gecicicalisansayisi" class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->gecicicalisansayisi}}"/>
                            <label for="gecicicalisansayisi">Geçici</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="vasifsizcalisansayisi" id="vasifsizcalisansayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->vasifsizcalisansayisi}}"/>
                            <label for="vasifsizcalisansayisi">Vasıfsız</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="toplamcalisansayisi" id="toplamcalisansayisi" class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->toplamcalisansayisi}}"/>
                            <label for="toplamcalisansayisi">Efektif toplam çalışan</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="vardiyalicalisansayisi" id="vardiyalicalisansayisi"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->vardiyalicalisansayisi}}"/>
                            <label for="vardiyalicalisansayisi">Vardiya 1</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="vardiyalicalisansayisi1" id="vardiyalicalisansayisi1"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->vardiyalicalisansayisi1}}"/>
                            <label for="vardiyalicalisansayisi1">Vardiya 2</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="vardiyalicalisansayisi2" id="vardiyalicalisansayisi2"
                                   class="form-control"
                                   placeholder=""
                                   value="{{$basvuru[0]->vardiyalicalisansayisi2}}"/>
                            <label for="vardiyalicalisansayisi2">Vardiya 3</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <small class="text-light fw-medium d-block">Tüm vardiyalarda aynı iş mi yapılıyor?</small>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" id="tumvardayni1" name="tumvardayni"
                                     value="1" {{($basvuru[0]->tumvardayni) ? 'checked' : ''}} />
                              <label class="form-check-label" for="tumvardayni1">Evet</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" id="tumvardayni2" name="tumvardayni"
                                     value="0" {{(!$basvuru[0]->tumvardayni) ? 'checked' : ''}} />
                              <label class="form-check-label" for="tumvardayni2">Hayır</label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="ayniisiyapansayisi" id="ayniisiyapansayisi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->ayniisiyapansayisi}}"/>
                            <label for="ayniisiyapansayisi">Aynı İşi Yapan Çalışan Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="soarevnotarihi" id="soarevnotarihi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->soarevnotarihi}}"/>
                            <label for="soarevnotarihi">SoA Revizyon No/Tarihi</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="bgyscalisansayisi" id="bgyscalisansayisi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->bgyscalisansayisi}}"/>
                            <label for="bgyscalisansayisi">BGYS çalışan</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="havuzsayisi" id="havuzsayisi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->havuzsayisi}}"/>
                            <label for="havuzsayisi">Havuz Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="mutfaksayisi" id="mutfaksayisi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->mutfaksayisi}}"/>
                            <label for="mutfaksayisi">Mutfak Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="odasayisi" id="odasayisi" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->odasayisi}}"/>
                            <label for="odasayisi">Oda Sayısı</label>
                          </div>
                        </div>
                        <div class="col-sm-2">
                          <div class="form-floating form-floating-outline">
                            <small class="text-light fw-medium d-block">Hizmet Kategorisi</small>
                            <div class="form-check form-check-inline mt-3">
                              <input class="form-check-input" type="radio" id="hizmetkategorisia"
                                     name="hizmetkategorisi"
                                     value="A" {{($basvuru[0]->hizmetkategorisi === 'A') ? 'checked' : ''}} />
                              <label class="form-check-label" for="hizmetkategorisia">A</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" id="hizmetkategorisib"
                                     name="hizmetkategorisi"
                                     value="B" {{($basvuru[0]->hizmetkategorisi === 'B') ? 'checked' : ''}} />
                              <label class="form-check-label" for="hizmetkategorisib">B</label>
                            </div>
                            <div class="form-check form-check-inline">
                              <input class="form-check-input" type="radio" id="hizmetkategorisic"
                                     name="hizmetkategorisi"
                                     value="C" {{($basvuru[0]->hizmetkategorisi === 'C') ? 'checked' : ''}} />
                              <label class="form-check-label" for="hizmetkategorisic">C</label>
                            </div>
                          </div>
                        </div>

                        {{-- ISO 50001 GÖZDEN GEÇİRELECEK KONULAR --}}
                        <div id="divenysekform" class="col-sm-12 {{($basvuru[0]->iso5000118varyok === 1) ? '' : ' hide'}}">
                          <div class="card card-action mb-4">
                            <div class="card-header">
                              <div class="card-action-title text-center">EnYS Belgelendirme Basvuru Ek Kontroller</div>
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
                                <div class="row g-4 mb-4">
                                  <div class="col-sm-4">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="yillikenerjituketimi" id="yillikenerjituketimi"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->yillikenerjituketimi}}"/>
                                      <label for="yillikenerjituketimi">Yıllık tüketim(TEP)</label>
                                    </div>
                                  </div>
                                  <div class="col-sm-4">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="enerjikaynaksayisi" id="enerjikaynaksayisi"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->enerjikaynaksayisi}}"/>
                                      <label for="enerjikaynaksayisi">Kull. Enerji Tipi Sayısı</label>
                                    </div>
                                  </div>
                                  <div class="col-sm-4">
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="oeksayisi" id="oeksayisi" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->oeksayisi}}"/>
                                      <label for="oeksayisi">ÖEK Sayısı</label>
                                    </div>
                                  </div>
                                </div>
                                <div class="row g-4">
                                  <div class="col-sm-12">
                                    <table class="table table-bordered table-hover table-sm">
                                      <tr>
                                        <td rowspan="7" style="align-content: center">EnYS Personeli Sayısı</td>
                                        <td>1.	Üst yönetim<br>
                                          (Örneğin. Genel Müdür, Yönetim Kurulu Üyesi - Birden fazla kişi olması durumunda sadece EnYS sorumluları)
                                        </td>
                                        <td><input type="text" name="enyscalisanust" id="enyscalisanust" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanust) ? $basvuruenys->enyscalisanust : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td>2.	Enerji yönetimi ekibi</td>
                                        <td><input type="text" name="enyscalisanekip" id="enyscalisanekip" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanekip) ? $basvuruenys->enyscalisanekip : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td>3.	Enerji performansını etkileyen büyük değişikliklerden sorumlu kişi/kişiler<br>
                                          (Ör. Birim/Departman yöneticisi)
                                        </td>
                                        <td><input type="text" name="enyscalisanperf" id="enyscalisanperf" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanperf) ? $basvuruenys->enyscalisanperf : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td>4.	EnYS’ nin etkinliği ile ilgili sorumluluk üstlenen kişi/kişiler<br>
                                          (Ör. Birim/Departman yöneticisi, idari personel, montaj personeli vb.)
                                        </td>
                                        <td><input type="text" name="enyscalisanetkin" id="enyscalisanetkin" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanetkin) ? $basvuruenys->enyscalisanetkin : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td>5.	Enerji performansı iyileştirme faaliyetlerini geliştirmekten, uygulamaktan veya sürdürmekten sorumlu kişi/kişiler<br>
                                          (Ör. Tasarım, Ar-Ge, Ür-Ge)
                                        </td>
                                        <td><input type="text" name="enyscalisanarge" id="enyscalisanarge" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanarge) ? $basvuruenys->enyscalisanarge : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td>6.	Önemli enerji kullanımlarından sorumlu kişi/kişiler<br>
                                          (Ör. Makina operatörü gibi önemli enerji kullanımını etkileyen personel)
                                        </td>
                                        <td><input type="text" name="enyscalisanoek" id="enyscalisanoek" class="form-control"
                                                   placeholder="" value="{{isset($basvuruenys->enyscalisanoek) ? $basvuruenys->enyscalisanoek : ""}}"/></td>
                                      </tr>
                                      <tr>
                                        <td style="text-align: right">EnYS Personel Sayısı
                                        </td>
                                        <td><input type="text" name="enyscalisansayisi" id="enyscalisansayisi" class="form-control"
                                                   placeholder="" value="{{$basvuru[0]->enyscalisansayisi}}"/></td>
                                      </tr>
                                    </table>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        {{-- ISO 50001 GÖZDEN GEÇİRELECEK KONULAR --}}

                        {{-- ISO 27001 Azaltma/Arttırma Oranları --}}
                        <div id="divbgysekform" class="col-sm-12 {{($basvuru[0]->iso27001varyok === 1) ? '' : ' hide'}}">
                          <div class="card card-action mb-4">
                            <div class="card-header">
                              <div class="card-action-title text-center">ISO 27001 Azaltma/Arttırma Oranları</div>
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
                                            <input type="radio" class="form-check-input" id="isturu1" name="isturu" value="1" {{(isset($basvurubgys) && $basvurubgys->isturu === 1) ? 'checked' : ''}} />
                                            <label class="form-check-label" for="isturu1">1 Kuruluş, kritik olmayan iş alanları ve
                                              düzenlenmemiş alanlarda çalışmakta<sup>a</sup></label>
                                          </div>
                                          <div class="form-check">
                                            <input type="radio" class="form-check-input" id="isturu2" name="isturu" value="2" {{(isset($basvurubgys) && $basvurubgys->isturu === 2) ? 'checked' : ''}} />
                                            <label class="form-check-label" for="isturu2">2 Kuruluşun kritik iş alanlarında çalışan
                                              müşterisi var<sup>a</sup></label>
                                          </div>
                                          <div class="form-check">
                                            <input type="radio" class="form-check-input" id="isturu3" name="isturu" value="3" {{(isset($basvurubgys) && $basvurubgys->isturu === 3) ? 'checked' : ''}} />
                                            <label class="form-check-label" for="isturu3">3 Kuruluş kritik iş alanlarında
                                              çalışmakta<sup>a</sup></label>
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <th>Prosesler ve görevler</th>
                                        <td>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prosesler" id="prosesler1" value="1" {{(isset($basvurubgys) && $basvurubgys->prosesler === 1) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="prosesler1"> 1 Standart ve tekrarlayan görevlere
                                              sahip standard prosesleri; kuruluşun kontrolünde aynı görevleri yerine getiren birçok
                                              fazla personel; birkaç ürün veya hizmet </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prosesler" id="prosesler2" value="2" {{(isset($basvurubgys) && $basvurubgys->prosesler === 2) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="prosesler2"> 2 Çok sayıda ürün ve hizmet veren,
                                              standard ama tekrarlamayan prosesler </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="prosesler" id="prosesler3" value="3" {{(isset($basvurubgys) && $basvurubgys->prosesler === 3) ? 'checked' : ''}}/>
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
                                                   id="ysolusmaseviyesi1" value="1" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 1) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="ysolusmaseviyesi1"> 1 BGYS oldukça iyi
                                              oluşturulmuştur ve/veya diğer yönetim sistemleri yürürlüktedir. </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                                   id="ysolusmaseviyesi2" value="2" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 2) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="ysolusmaseviyesi2"> 2 Diğer yönetim sistemlerindeki
                                              bazı unsurlar uygulanır, diğerleri değil </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                                   id="ysolusmaseviyesi3" value="3" {{(isset($basvurubgys) && $basvurubgys->ysolusmaseviyesi === 3) ? 'checked' : ''}}/>
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
                                            <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi1" value="1" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 1) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="btaltyapi1">1 Az ya da çok standardlaştırılmış BT
                                              platformları, sunucuları, işletim sistemleri, veri tabanları, ağlar vb. </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi2" value="2" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 2) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="btaltyapi2">2 1-3 farklı BT platformu, sunucuları,
                                              veri tabanları, ağları </label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi3" value="3" {{(isset($basvurubgys) && $basvurubgys->btaltyapi === 3) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="btaltyapi3">3 Birçok farklı BT platformu, sunucuları,
                                              veri tabanları, ağları </label>
                                          </div>
                                        </td>
                                      </tr>
                                      <tr>
                                        <th>Bulut hizmetleri dâhil dış kaynaklara ve tedarikçilere olan bağlılık</th>
                                        <td>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak1" value="1" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 1) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="diskaynak1"> 1 Dış kaynaklara ya da tedarikçiler az
                                              bağımlı olma ya da bağımlı olmama</label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak2" value="2" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 2) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="diskaynak2"> 2 Tüm kritik iş faaliyetleri olmamak
                                              koşuluyla sadece bazılarında dış kaynaklara ya da tedarikçiye olan normal
                                              bağımlılık,</label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak3" value="3" {{(isset($basvurubgys) && $basvurubgys->diskaynak === 3) ? 'checked' : ''}}/>
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
                                                   id="bilgisistemgelisimi1" value="1" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 1) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="bilgisistemgelisimi1"> 1 Kuruluş içi sistem/uygulama
                                              geliştirme yok veya çok sınırlı</label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                                   id="bilgisistemgelisimi2" value="2" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 2) ? 'checked' : ''}}/>
                                            <label class="form-check-label" for="bilgisistemgelisimi2"> 2 Bazı önemli iş amaçları için
                                              kuruluş içi veya dış kaynaklı sistem/uygulama geliştirme</label>
                                          </div>
                                          <div class="form-check">
                                            <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                                   id="bilgisistemgelisimi3" value="3" {{(isset($basvurubgys) && $basvurubgys->bilgisistemgelisimi === 3) ? 'checked' : ''}}/>
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
                          </div>
                        </div>
                        {{-- ISO 27001 Azaltma/Arttırma Oranları --}}

                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                            <div class="table-responsive text-nowrap">
                              <table class="table">
                                <thead>
                                <tr>
                                  <th colspan="6" class="text-center">
                                    ISO 9001, ISO 14001 ve Helal belgelendirme başvurularında
                                    doldurulacaktır.<br/>
                                    <span class="text-muted">Kapsam dahilinde tekrarlayan ve basit görevleri olan personel sayısı/Kayda değer sayıda aynı işi yapan personel sayısı</span>

                                  </th>
                                </tr>
                                <tr>
                                  <th>Sıra No</th>
                                  <th>Faaliyet Adı</th>
                                  <th>Çalışan sayısı
                                    (aynı işi yapan çalışan sayısı)
                                  </th>
                                  <th>Sıra No</th>
                                  <th>Faaliyet Adı</th>
                                  <th>Çalışan sayısı
                                    (aynı işi yapan çalışan sayısı)
                                  </th>
                                </tr>
                                </thead>
                                <tbody class="table-border-bottom-0">
                                <tr>
                                  <td>1</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi1" id="faaliyetadi1" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi1}}"/>
                                      <label for="faaliyetadi1"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay1" id="faaliyetadicalsay1"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay1}}"/>
                                      <label for="faaliyetadicalsay1"></label>
                                    </div>
                                  </td>
                                  <td>4</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi4" id="faaliyetadi4" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi4}}"/>
                                      <label for="faaliyetadi4"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay4" id="faaliyetadicalsay4"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay4}}"/>
                                      <label for="faaliyetadicalsay4"></label>
                                    </div>
                                  </td>
                                </tr>
                                <tr>
                                  <td>2</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi2" id="faaliyetadi2" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi2}}"/>
                                      <label for="faaliyetadi2"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay2" id="faaliyetadicalsay2"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay2}}"/>
                                      <label for="faaliyetadicalsay2"></label>
                                    </div>
                                  </td>
                                  <td>5</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi5" id="faaliyetadi5" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi5}}"/>
                                      <label for="faaliyetadi5"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay5" id="faaliyetadicalsay5"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay5}}"/>
                                      <label for="faaliyetadicalsay5"></label>
                                    </div>
                                  </td>
                                </tr>
                                <tr>
                                  <td>3</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi3" id="faaliyetadi3" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi3}}"/>
                                      <label for="faaliyetadi3"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay3" id="faaliyetadicalsay3"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay3}}"/>
                                      <label for="faaliyetadicalsay3"></label>
                                    </div>
                                  </td>
                                  <td>6</td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadi6" id="faaliyetadi6" class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadi6}}"/>
                                      <label for="faaliyetadi6"></label>
                                    </div>
                                  </td>
                                  <td>
                                    <div class="form-floating form-floating-outline">
                                      <input type="text" name="faaliyetadicalsay6" id="faaliyetadicalsay6"
                                             class="form-control"
                                             placeholder="" value="{{$basvuru[0]->faaliyetadicalsay1}}"/>
                                      <label for="faaliyetadicalsay6"></label>
                                    </div>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-12 d-flex justify-content-between">
                          <button class="btn btn-outline-secondary btn-prev"><i
                              class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">Geri</span>
                          </button>
                          <button class="btn btn-primary btn-next"><span
                              class="align-middle d-sm-inline-block d-none me-sm-1">İleri</span>
                            <i class="mdi mdi-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                    <!-- Saha bilgileri / entegrasyon düzeyi -->
                    <div id="social-links-validation" class="content">
                      <div class="content-header mb-3">
                        <h6 class="mb-0">Çok Sahalı Kuruluş Bilgileri</h6>
                        <small>Enegrasyon düzeyleri</small>
                      </div>
                      <div class="row g-4">
                        <div class="col-sm-12">
                          <div class="form-floating form-floating-outline">
                            <div class="table-responsive text-nowrap">
                              <table class="table table-responsive" id="dynamic-table">
                                <thead>
                                <tr>
                                  <th style="width: 5%">Saha Sıra No</th>
                                  <th style="width: 60%">Saha Adresi</th>
                                  <th style="width: 15%" colspan="3">Saha Çalışan Sayısı<br>(vardiyalara
                                    göre yazınız)
                                  </th>
                                  <th style="width: 15%">Ana Faaliyet Konusu
                                  </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>
                                    <input type="text" name="subeadresia" id="subeadresia" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subeadresia}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardaa" id="subevardaa" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardaa}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardab" id="subevardab" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardab}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardac" id="subevardac" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardac}}"/>
                                  </td>
                                  <td>
                                    <input type="text" name="subefaaliyeta" id="subefaaliyeta" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subefaaliyeta}}"/>
                                  </td>
                                </tr>
                                <tr>
                                  <td>2</td>
                                  <td>
                                    <input type="text" name="subeadresib" id="subeadresib" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subeadresib}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardba" id="subevardba" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardba}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardbb" id="subevardbb" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardbb}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardbc" id="subevardbc" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardbc}}"/>
                                  </td>
                                  <td>
                                    <input type="text" name="subefaaliyetb" id="subefaaliyetb" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subefaaliyetb}}"/>
                                  </td>
                                </tr>
                                <tr>
                                  <td>3</td>
                                  <td>
                                    <input type="text" name="subeadresic" id="subeadresic" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subeadresic}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardca" id="subevardca" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardca}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardcb" id="subevardcb" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardcb}}"/>
                                  </td>
                                  <td style="width: 5%">
                                    <input type="text" name="subevardcc" id="subevardcc" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subevardcc}}"/>
                                  </td>
                                  <td>
                                    <input type="text" name="subefaaliyetc" id="subefaaliyetc" class="form-control"
                                           placeholder="" value="{{$basvuru[0]->subefaaliyetc}}"/>
                                  </td>
                                </tr>
                                </tbody>
                              </table>
                              <button type="button" class="btn btn-primary" id="add-row-btn">Satır Ekle</button>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-12">
                          <small class="text-light fw-semibold d-block">Yönetim Sistemlerinin Entegrasyon Düzeyi
                            Bilgileri</small>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="ygg"
                                     name="ygg" {{($basvuru[0]->ygg == "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="ygg">
                                YGG entegre yaklaşım
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="icdenetim"
                                     name="icdenetim" {{($basvuru[0]->icdenetim === "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="icdenetim">
                                İç denetim
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="politikahedefler"
                                     name="politikahedefler" {{($basvuru[0]->politikahedefler === "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="politikahedefler">
                                Politika ve Hedefler
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="prosesentegre"
                                     name="prosesentegre" {{($basvuru[0]->prosesentegre == "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="prosesentegre">
                                Prosesler
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="entegredokumantasyon"
                                     name="entegredokumantasyon" {{($basvuru[0]->entegredokumantasyon === "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="entegredokumantasyon">
                                Entegre dokümantasyon
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="duzelticifaaliyet"
                                     name="duzelticifaaliyet" {{($basvuru[0]->duzelticifaaliyet === "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="duzelticifaaliyet">
                                Düzeltici faaliyetler
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-3">
                          <div class="form-floating form-floating-outline">
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="riskyonetimyaklasimi"
                                     name="riskyonetimyaklasimi" {{($basvuru[0]->riskyonetimyaklasimi == "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="riskyonetimyaklasimi">
                                Risk yönetimi
                              </label>
                            </div>
                            <div class="form-check mt-3">
                              <input class="form-check-input" type="checkbox" value="12.5" id="yondessor"
                                     name="yondessor" {{($basvuru[0]->yondessor === "12.5") ? 'checked' : ''}} />
                              <label class="form-check-label" for="yondessor">
                                Yönetim desteği
                              </label>
                            </div>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="yonetimtemsilcisiadi" id="yonetimtemsilcisiadi"
                                   class="form-control"
                                   placeholder="" value="{{$basvuru[0]->yonetimtemsilcisiadi}}"/>
                            <label for="yonetimtemsilcisiadi">Müşteri Kuruluş Yetkili Temsilcisi Ad / Soyad</label>
                          </div>
                        </div>
                        <div class="col-sm-6">
                          <div class="form-floating form-floating-outline">
                            <input type="text" name="danisman" id="danisman" class="form-control"
                                   placeholder="" value="{{$basvuru[0]->danisman}}"/>
                            <label for="danisman">Kimden</label>
                          </div>
                        </div>
                        <div class="col-12">
                          <div class="row g-4">
                            <div class="col-sm-12">
                              <div class="form-floating form-floating-outline">
                                <small class="text-light fw-medium d-block">Başvurulan kapsam akreditasyon dâhilinde mi?</small>
                                <div class="form-check form-check-inline mt-3">
                                  <input class="form-check-input" type="radio" id="akreditasyonEvet" name="akreditasyonKapsam" value="1" />
                                  <label class="form-check-label" for="akreditasyonEvet">Evet</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" id="akreditasyonHayir" name="akreditasyonKapsam" value="0" />
                                  <label class="form-check-label" for="akreditasyonHayir">Hayır</label>
                                </div>
                                <span class="helper-text">* Hayır, ise bilgi verilir.</span>
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-floating form-floating-outline">
                                <small class="text-light fw-medium d-block">Uygun EA/NACE kodunda/kategoride-alt kategoride/teknik alan-teknolojik alanda görevlendirilecek denetim/karar personeli var mı?</small>
                                <div class="form-check form-check-inline mt-3">
                                  <input class="form-check-input" type="radio" id="naceEvet" name="naceKodPersonel" value="1" />
                                  <label class="form-check-label" for="naceEvet">Evet</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" id="naceHayir" name="naceKodPersonel" value="0" />
                                  <label class="form-check-label" for="naceHayir">Hayır</label>
                                </div>
                                <span class="helper-text">* Hayır, ise teklif verilmez.</span>
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-floating form-floating-outline">
                                <small class="text-light fw-medium d-block">Mevsimsel/Sezonluk üretim var mı? (GGYS ve Helal için)</small>
                                <div class="form-check form-check-inline mt-3">
                                  <input class="form-check-input" type="radio" id="mevsimselEvet" name="mevsimselUretim" value="1" />
                                  <label class="form-check-label" for="mevsimselEvet">Evet</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" id="mevsimselHayir" name="mevsimselUretim" value="0" />
                                  <label class="form-check-label" for="mevsimselHayir">Hayır</label>
                                </div>
                                <span class="helper-text">* Evet, ise bilgi verilir.</span>
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-floating form-floating-outline">
                                <small class="text-light fw-medium d-block">Başvuru kabul edildi mi?</small>
                                <div class="form-check form-check-inline mt-3">
                                  <input class="form-check-input" type="radio" id="basvuruEvet" name="basvuruKabul" value="1" />
                                  <label class="form-check-label" for="basvuruEvet">Evet</label>
                                </div>
                                <div class="form-check form-check-inline">
                                  <input class="form-check-input" type="radio" id="basvuruHayir" name="basvuruKabul" value="0" />
                                  <label class="form-check-label" for="basvuruHayir">Hayır</label>
                                </div>
                                <span class="helper-text">* Hayır, ise gerekçeleri ile bilgi verilir.</span>
                              </div>
                            </div>
                            <div class="col-sm-12">
                              <div class="form-floating form-floating-outline">
                            <textarea class="form-control h-px-100" id="notlar"
                              name="notlar"
                              placeholder="">{{$basvuru[0]->notlar}}</textarea>
                                <label for="notlar">Notlar</label>
                              </div>
                            </div>
                          </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                          <button class="btn btn-outline-secondary btn-prev"><i
                              class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                            <span class="align-middle d-sm-inline-block d-none">Geri</span>
                          </button>
                          <button class="btn btn-primary btn-next btn-submit">Kaydet</button>
                        </div>
                      </div>
                    </div>
                  </form>
                  <div class="modal modal-top fade" id="myModalSucces" tabindex="-1">
                    <div class="modal-dialog">
                      <form class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Başvurusu</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col mb-4 mt-2">
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
                    <div class="modal-dialog">
                      <form class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="modalTopTitle">Sistem Belgelendirme Başvurusu</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="row">
                            <div class="col mb-4 mt-2">
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
            <!-- /Validation Wizard -->
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
