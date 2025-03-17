@extends('layouts.layoutMaster')

@section('title', 'Yeni Başvuru Kayıt')

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}"/>
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/plan-basvuru-form-wizard-validation.js')}}"></script>
  <script src="{{asset('assets/js/forms-qmsnet.js')}}"></script>
@endsection

@section('content')
  <!-- Toast with Animation -->
  <div class="bs-toast toast toast-ex animate__animated my-2 " role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
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
      <h4 class="py-3 mb-4"><span class="text-muted fw-light"><a href="{{url('/planlama/dashboards-plan')}}"> Planlama</a> /</span> Yeni Başvuru Kayıt</h4>
    </div>
<?php
//  var_dump($basvuru);
  ?>
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
            <input type="hidden" id="enyseffectiveemployee" name="enyseffectiveemployee" value="">
            <input type="hidden" id="bgyseffectiveemployee" name="bgyseffectiveemployee" value="">
            <!-- Account Details -->
            <div id="company-details-validation" class="content">
              <div class="content-header mb-3">
                <h6 class="mb-0">Kuruluş Bilgileri</h6>
                <small>Kuruluşa ait genel bilgileri giriniz.</small>
              </div>
              <div class="row g-4">
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <select id="planno" name="planno" class="selectpicker w-100"
                            data-style="btn-default">
                      <option value="" disabled>Denetim plan no seçiniz...
                      </option>
                      <?php

                      $sorgu = "select planno from basvuru order by planno desc limit 1";
                      $sonplan = Illuminate\Support\Facades\DB::select($sorgu)[0]->planno;
                      $sonplan = str_pad(intval($sonplan) + 1, 4, '0', STR_PAD_LEFT);
                      echo '<option value="' . $sonplan . '" selected>' . $sonplan . '</option>';

                      echo '<optgroup label="Mevcut başvurular">';
                      $sorgu = "select planno, firmaadi from basvuru order by planno desc";
                      $sonuc = Illuminate\Support\Facades\DB::select($sorgu);
                      foreach ($sonuc as $ret) {
                        $sec = '';
//                        if ($planlar[0]->planno == $ret->planno) $sec = ' selected="selected"';
                        echo '<option value="' . $ret->planno . '"' . $sec . '>' . str_pad($ret->planno,4,"0",STR_PAD_LEFT) . ' ' . $ret->firmaadi . '</option>';
                      }
                      echo '</optgroup>';

                      ?>
                    </select>
                    <label for="planno">Planno</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="firmaadi" id="formValidationFirmaadi" class="form-control" placeholder=""
                           value=""/>
                    <label for="formValidationFirmaadi">Kuruluş Adı</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="firmaadresi" id="formValidationFirmaadi" class="form-control"
                           value=""/>
                    <label for="formValidationFirmaadi">Kuruluş Merkez Adresi</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationIlce" name="milce" class="form-control" placeholder=""
                             value=""/>
                      <label for="formValidationIlce">İlçe</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationSehir" name="msehir" class="form-control" placeholder=""
                             value=""/>
                      <label for="formValidationSehir">Şehir</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationFirmatelefon" name="firmatelefon" class="form-control" placeholder=""
                             value=""/>
                      <label for="formValidationFirmatelefon">Telefon</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationFaks" name="firmafaks" class="form-control" placeholder=""
                             value=""/>
                      <label for="formValidationFaks">Faks</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationFirmaeposta" name="firmaeposta" class="form-control"
                             placeholder="" value=""/>
                      <label for="formValidationFirmaeposta">E-Posta</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline">
                      <input type="text" id="formValidationFirmaweb" name="firmaweb" class="form-control" placeholder=""
                             value=""/>
                      <label for="formValidationFirmaweb">Web Adresi</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationIso900115varyok"
                             name="iso900115varyok"  />
                      <label class="form-check-label" for="formValidationIso900115varyok">
                        ISO 9001:2015
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationIso1400115varyok"
                             name="iso1400115varyok"  />
                      <label class="form-check-label" for="formValidationIso1400115varyok">
                        ISO 14001:2015
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationIso4500118varyok"
                             name="iso4500118varyok"  />
                      <label class="form-check-label" for="formValidationIso4500118varyok">
                        ISO 45001:2018
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationIso2200018varyok"
                             name="iso2200018varyok"  />
                      <label class="form-check-label" for="formValidationIso2200018varyok">
                        ISO 22000:2018
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="iso27001varyok"
                             name="iso27001varyok" onclick="iso27001ekformAc()" />
                      <label class="form-check-label" for="iso27001varyok">
                        ISO 27001:2022
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="iso5000118varyok"
                             name="iso5000118varyok" onclick="iso50001ekformAc()" />
                      <label class="form-check-label" for="iso5000118varyok">
                        ISO 50001:2018
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationHelalvaryok"
                             name="helalvaryok" />
                      <label class="form-check-label" for="formValidationHelalvaryok">
                        OIC/SMIIC 1:2019
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationOicsmiik6varyok"
                             name="oicsmiik6varyok" />
                      <label class="form-check-label" for="formValidationOicsmiik6varyok">
                        OIC/SMIIC 6:2019
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationOicsmiik9varyok"
                             name="oicsmiik9varyok"  />
                      <label class="form-check-label" for="formValidationOicsmiik9varyok">
                        OIC/SMIIC 9:2019
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationOicsmiik171varyok"
                             name="oicsmiik171varyok"  />
                      <label class="form-check-label" for="formValidationOicsmiik171varyok">
                        OIC/SMIIC 17-1:2020
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationOicsmiik23varyok"
                             name="oicsmiik23varyok"  />
                      <label class="form-check-label" for="formValidationOicsmiik23varyok">
                        OIC/SMIIC 23:2022
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="1" id="formValidationOicsmiik24varyok"
                             name="oicsmiik24varyok"  />
                      <label class="form-check-label" for="formValidationOicsmiik24varyok">
                        OIC/SMIIC 24:2020
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="input-group input-group-merge">
                    <span class="input-group-text" id="formValidationDigersistemlerneler"><i class="mdi mdi-file"></i></span>
                    <input type="text" class="form-control" placeholder="Diğer..." aria-label="Search..."
                           aria-describedby="formValidationDigersistemlerneler" name="digersistemlerneler"
                           value=""/>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsam" name="belgelendirmekapsami"
                              placeholder=""></textarea>
                    <label for="formValidationKapsam">Kapsam</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="formValidationKapsaming" name="belgelendirmekapsamiing"
                              placeholder=""></textarea>
                    <label for="formValidationKapsaming">Scope</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="harictutulanmaddeler"
                              name="harictutulanmaddeler"
                              placeholder=""></textarea>
                    <label for="harictutulanmaddeler">ISO 9001 için uygulanabilir olmayan madde varsa
                      belirtiniz.</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="diskaynakliprosesler" name="diskaynakliprosesler"
                              placeholder=""></textarea>
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
                                       name="basitproses"  />
                                <label class="form-check-label" for="basitproses">
                                  Basit Proses
                                </label>
                              </div>
                              <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="calismaalanikucuk"
                                       name="calismaalanikucuk"  />
                                <label class="form-check-label" for="calismaalanikucuk">
                                  Çalışma alanı küçük
                                </label>
                              </div>
                              <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="prosesdusukrisk"
                                       name="prosesdusukrisk"  />
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
                                       name="karisikproses"  />
                                <label class="form-check-label" for="karisikproses">
                                  Karışık proses
                                </label>
                              </div>
                              <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="calismaalanibuyuk"
                                       name="calismaalanibuyuk"  />
                                <label class="form-check-label" for="calismaalanibuyuk">
                                  Büyük çalışma alanı
                                </label>
                              </div>
                              <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="otomasyonkullanimi"
                                       name="otomasyonkullanimi"  />
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
                              placeholder=""></textarea>
                    <label for="yukumluolunanmevzuatlar">Uymak zorunda olduğunuz yasal yükümlülüklerle ilgili bilgi
                      veriniz.</label>
                  </div>
                </div>
                <div class="col-sm-12">
                  <div class="form-floating form-floating-outline">
                    <textarea class="form-control h-px-100" id="kullanilandanismanlikhizmeti"
                              name="kullanilandanismanlikhizmeti"
                              placeholder=""></textarea>
                    <label for="kullanilandanismanlikhizmeti">Eğer kullanıldıysa danışmanlık hizmetleri hakkında bilgi
                      veriniz (Kuruluş ve danışman adı).</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">Mevcut yönetim sistemi sertifikanız var mı?</small>
                    <div class="form-check form-check-inline mt-3">
                      <input class="form-check-input" type="radio" id="yonetimsistemsertifikasi1"
                             name="yonetimsistemsertifikasi"
                             value="1"  />
                      <label class="form-check-label" for="yonetimsistemsertifikasi1">Evet</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="yonetimsistemsertifikasi2"
                             name="yonetimsistemsertifikasi"
                             value="0"  />
                      <label class="form-check-label" for="yonetimsistemsertifikasi2">Hayır</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="yonetimsistemisuresi" id="yonetimsistemisuresi" class="form-control"
                           placeholder="3+ YIL" value=""/>
                    <label for="yonetimsistemisuresi">Varsa yönetim sistemi ne kadar süredir uygulanmaktadır?</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="haccpcalismasisayisi" id="haccpcalismasisayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="haccpcalismasisayisi">22000 HACCP çalışması sayısı</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="haccpcalismasisayisismiic" id="haccpcalismasisayisismiic"
                           class="form-control" placeholder=""
                           value=""/>
                    <label for="haccpcalismasisayisismiic">SMIIC HACCP çalışması sayısı</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="helalurunsayisi" id="helalurunsayisi" class="form-control" placeholder=""
                           value=""/>
                    <label for="helalurunsayisi">SMIIC ürün sayısı</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="sahasayisi22" id="sahasayisi22" class="form-control" placeholder=""
                           value="0"/>
                    <label for="sahasayisi22">ISO 22000/Helal Belgelendirme Ziyaret edilecek saha sayısı<br>Merkez ofis hariç</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <select id="oicsmiickk" name="oicsmiickk" class="selectpicker w-100" data-style="btn-default">
                      <option value="">Karmaşıklık Kategorisi Seçiniz...</option>
                      <optgroup label="Çok Yüksek">
                        <option value="4-1">
                          Başka yerde sınıflandırılmamış kimyasallar
                        </option>
                        <option value="4-2">
                          Farmasötik ürünler
                        </option>
                        <option value="4-3">
                          Işlenmiş et ürünleri
                        </option>
                        <option value="4-4">
                          Genetiği değiştirilmiş ürünleri
                        </option>
                        <option value="4-5">
                          Gıda katkıda maddeleri
                        </option>
                        <option value="4-6">
                          Biyokültürler
                        </option>
                        <option value="4-7">
                          Kozmetik ürünler
                        </option>
                        <option value="4-8">
                          Işlem yardımcıları
                        </option>
                        <option value="4-9">
                          Mikro-organizmalar
                        </option>
                      </optgroup>
                      <optgroup label="Yüksek">
                        <option value="3-1">
                          Kesimhane ve tavukçuluk
                        </option>
                        <option value="3-2">
                          Peynir ürünleri
                        </option>
                        <option value="3-3">
                          Bisküvi çeşitleri
                        </option>
                        <option value="3-4">
                          Yağ
                        </option>
                        <option value="3-5">
                          İçecekler
                        </option>
                        <option value="3-6">
                          Oteller
                        </option>
                        <option value="3-7">
                          Restoranlar
                        </option>
                        <option value="3-8">
                          Beslenme takviyeleri
                        </option>
                        <option value="3-9">
                          Temizlik maddeleri
                        </option>
                        <option value="3-10">
                          Ambalajlama malzemeleri
                        </option>
                        <option value="3-11">
                          Tekstil ürünleri
                        </option>
                        <option value="3-12">
                          İslami finans
                        </option>
                      </optgroup>
                      <optgroup label="Orta">
                        <option value="2-1">
                          Süt ürünleri
                        </option>
                        <option value="2-2">
                          Balık ürünleri
                        </option>
                        <option value="2-3">
                          Yumurta ürünleri
                        </option>
                        <option value="2-4">
                          Arıcılık
                        </option>
                        <option value="2-5">
                          Baharatlar
                        </option>
                        <option value="2-6">
                          Bahçe ürünleri
                        </option>
                        <option value="2-7">
                          Korunmuş meyveler
                        </option>
                        <option value="2-8">
                          Korunmuş bitkiler
                        </option>
                        <option value="2-9">
                          Konserve ürünler
                        </option>
                        <option value="2-10">
                          Makarna
                        </option>
                        <option value="2-11">
                          Şeker
                        </option>
                        <option value="2-12">
                          Hayvan yemi
                        </option>
                        <option value="2-13">
                          Balık yemi
                        </option>
                        <option value="2-14">
                          Su tedariki
                        </option>
                        <option value="2-15">
                          Ürünlerin geliştirilmesi
                        </option>
                        <option value="2-16">
                          Proses ve ekipman
                        </option>
                        <option value="2-17">
                          Veterinerlik hizmetleri
                        </option>
                        <option value="2-18">
                          Proses ekipmanı
                        </option>
                        <option value="2-19">
                          Otomatlar
                        </option>
                        <option value="2-20">
                          Deri ürünleri
                        </option>
                        <option value="2-21">
                          Unlu Mamuller(YAŞ PASTA, KAHKE, POĞAÇA, SİMİT, KATMER, KURU
                          PASTA VB.)
                        </option>
                      </optgroup>
                      <optgroup label="Düşük">
                        <option value="1-1">
                          Balık
                        </option>
                        <option value="1-2">
                          Yumurta üretimi
                        </option>
                        <option value="1-3">
                          Süt üretimi
                        </option>
                        <option value="1-4">
                          Balıkçılık
                        </option>
                        <option value="1-5">
                          Avcılık
                        </option>
                        <option value="1-6">
                          Tuzakta yakalama
                        </option>
                        <option value="1-7">
                          Meyveler
                        </option>
                        <option value="1-8">
                          Sebzeler
                        </option>
                        <option value="1-9">
                          Tahıl
                        </option>
                        <option value="1-10">
                          Taze meyve ve taze meyve suları
                        </option>
                        <option value="1-11">
                          İçme suyu
                        </option>
                        <option value="1-12">
                          Un
                        </option>
                        <option value="1-13">
                          Tuz
                        </option>
                        <option value="1-14">
                          Perakende satış mağazaları
                        </option>
                        <option value="1-15">
                          Dükkânlar
                        </option>
                        <option value="1-16">
                          Toptan satıcılar
                        </option>
                        <option value="1-17">
                          Nakliye ve depolama
                        </option>
                        <option value="1-18">
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
                           value=""/>
                    <label for="hammadeler">Hammadeler</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="helalkkn" id="helalkkn" class="form-control" placeholder=""
                           value=""/>
                    <label for="helalkkn">Helal kkn</label>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="aracsayisi" id="aracsayisi" class="form-control" placeholder=""
                           value=""/>
                    <label for="aracsayisi">OIC/SMIIC 17-1 için Araç Sayısı</label>
                  </div>
                </div>

                <div class="col-12 d-flex justify-content-between">
                  <button class="btn btn-outline-secondary btn-prev" disabled><i
                      class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Geri</span>
                  </button>
                  <button class="btn btn-primary btn-next"><span class="align-middle d-sm-inline-block d-none me-sm-1">İleri</span>
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
                           value=""/>
                    <label for="beyazyakacalisansayisi">Beyaz Yaka</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="tamzamanlicalisansayisi" id="tamzamanlicalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="tamzamanlicalisansayisi">Tam zamanlı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="yarizamanlicalisansayisi" id="yarizamanlicalisansayisi"
                           class="form-control" placeholder=""
                           value=""/>
                    <label for="yarizamanlicalisansayisi">Yarı zamanlı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="taseroncalisansayisi" id="taseroncalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="taseroncalisansayisi">Taşeron</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="gecicicalisansayisi" id="gecicicalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="gecicicalisansayisi">Geçici</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="vasifsizcalisansayisi" id="vasifsizcalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="vasifsizcalisansayisi">Vasıfsız</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="toplamcalisansayisi" id="toplamcalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="toplamcalisansayisi">Efektif toplam çalışan</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="vardiyalicalisansayisi" id="vardiyalicalisansayisi" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="vardiyalicalisansayisi">Vardiya 1</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="vardiyalicalisansayisi1" id="vardiyalicalisansayisi1" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="vardiyalicalisansayisi1">Vardiya 2</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="vardiyalicalisansayisi2" id="vardiyalicalisansayisi2" class="form-control"
                           placeholder=""
                           value=""/>
                    <label for="vardiyalicalisansayisi2">Vardiya 3</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">Tüm vardiyalarda aynı iş mi yapılıyor?</small>
                    <div class="form-check form-check-inline mt-3">
                      <input class="form-check-input" type="radio" id="tumvardayni1" name="tumvardayni"
                             value="1" />
                      <label class="form-check-label" for="tumvardayni1">Evet</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="tumvardayni2" name="tumvardayni"
                             value="0" />
                      <label class="form-check-label" for="tumvardayni2">Hayır</label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="ayniisiyapansayisi" id="ayniisiyapansayisi" class="form-control"
                           placeholder="" value=""/>
                    <label for="ayniisiyapansayisi">Aynı İşi Yapan Çalışan Sayısı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="soarevnotarihi" id="soarevnotarihi" class="form-control"
                           placeholder="" value=""/>
                    <label for="soarevnotarihi">SoA Revizyon No/Tarihi</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="bgyscalisansayisi" id="bgyscalisansayisi" class="form-control"
                           placeholder="" value=""/>
                    <label for="bgyscalisansayisi">BGYS çalışan</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="havuzsayisi" id="havuzsayisi" class="form-control"
                           placeholder="" value=""/>
                    <label for="havuzsayisi">Havuz Sayısı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="mutfaksayisi" id="mutfaksayisi" class="form-control"
                           placeholder="" value=""/>
                    <label for="mutfaksayisi">Mutfak Sayısı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="odasayisi" id="odasayisi" class="form-control"
                           placeholder="" value=""/>
                    <label for="odasayisi">Oda Sayısı</label>
                  </div>
                </div>
                <div class="col-sm-2">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">Hizmet Kategorisi</small>
                    <div class="form-check form-check-inline mt-3">
                      <input class="form-check-input" type="radio" id="hizmetkategorisia" name="hizmetkategorisi"
                             value="A"  />
                      <label class="form-check-label" for="hizmetkategorisia">A</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="hizmetkategorisib" name="hizmetkategorisi"
                             value="B" />
                      <label class="form-check-label" for="hizmetkategorisib">B</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="radio" id="hizmetkategorisic" name="hizmetkategorisi"
                             value="C"  />
                      <label class="form-check-label" for="hizmetkategorisic">C</label>
                    </div>
                  </div>
                </div>

                {{-- ISO 50001 GÖZDEN GEÇİRELECEK KONULAR --}}
                <div id="divenysekform" class="col-sm-12 hide">
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
                                     placeholder="" value=""/>
                              <label for="yillikenerjituketimi">Yıllık tüketim(TEP)</label>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="enerjikaynaksayisi" id="enerjikaynaksayisi"
                                     class="form-control"
                                     placeholder="" value=""/>
                              <label for="enerjikaynaksayisi">Kull. Enerji Tipi Sayısı</label>
                            </div>
                          </div>
                          <div class="col-sm-4">
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="oeksayisi" id="oeksayisi" class="form-control"
                                     placeholder="" value=""/>
                              <label for="oeksayisi">ÖEK Sayısı</label>
                            </div>
                          </div>
                        </div>
                        <div class="row g-4">
                          <div class="col-sm-12">
                            <table class="table table-bordered table-hover table-sm">
                              <tr>
                                <td rowspan="7" style="align-content: center">EnYS efektif personeli sayısı</td>
                                <td>1.	Üst yönetim<br>
                                  (Örneğin. Genel Müdür, Yönetim Kurulu Üyesi - Birden fazla kişi olması durumunda sadece EnYS sorumluları)
                                </td>
                                <td><input type="text" name="enyscalisanust" id="enyscalisanust" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td>2.	Enerji yönetimi ekibi</td>
                                <td><input type="text" name="enyscalisanekip" id="enyscalisanekip" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td>3.	Enerji performansını etkileyen büyük değişikliklerden sorumlu kişi/kişiler<br>
                                  (Ör. Birim/Departman yöneticisi)
                                </td>
                                <td><input type="text" name="enyscalisanperf" id="enyscalisanperf" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td>4.	EnYS’ nin etkinliği ile ilgili sorumluluk üstlenen kişi/kişiler<br>
                                  (Ör. Birim/Departman yöneticisi, idari personel, montaj personeli vb.)
                                </td>
                                <td><input type="text" name="enyscalisanetkin" id="enyscalisanetkin" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td>5.	Enerji performansı iyileştirme faaliyetlerini geliştirmekten, uygulamaktan veya sürdürmekten sorumlu kişi/kişiler<br>
                                  (Ör. Tasarım, Ar-Ge, Ür-Ge)
                                </td>
                                <td><input type="text" name="enyscalisanarge" id="enyscalisanarge" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td>6.	Önemli enerji kullanımlarından sorumlu kişi/kişiler<br>
                                  (Ör. Makina operatörü gibi önemli enerji kullanımını etkileyen personel)
                                </td>
                                <td><input type="text" name="enyscalisanoek" id="enyscalisanoek" class="form-control"
                                           placeholder="" value=""/></td>
                              </tr>
                              <tr>
                                <td style="text-align: right">EnYS Efektif personel sayısı<br>
                                  (Aliment tarafından doldurulacak)
                                </td>
                                <td><input type="text" name="enyscalisansayisi" id="enyscalisansayisi" class="form-control"
                                           placeholder="" value=""/></td>
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
                <div id="divbgysekform" class="col-sm-12 hide">
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
                                    <input type="radio" class="form-check-input" id="isturu1" name="isturu" value="1" />
                                    <label class="form-check-label" for="isturu1">1 Kuruluş, kritik olmayan iş alanları ve
                                      düzenlenmemiş alanlarda çalışmakta<sup>a</sup></label>
                                  </div>
                                  <div class="form-check">
                                    <input type="radio" class="form-check-input" id="isturu2" name="isturu" value="2" />
                                    <label class="form-check-label" for="isturu2">2 Kuruluşun kritik iş alanlarında çalışan
                                      müşterisi var<sup>a</sup></label>
                                  </div>
                                  <div class="form-check">
                                    <input type="radio" class="form-check-input" id="isturu3" name="isturu" value="3" />
                                    <label class="form-check-label" for="isturu3">3 Kuruluş kritik iş alanlarında
                                      çalışmakta<sup>a</sup></label>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <th>Prosesler ve görevler</th>
                                <td>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prosesler" id="prosesler1" value="1" />
                                    <label class="form-check-label" for="prosesler1"> 1 Standart ve tekrarlayan görevlere
                                      sahip standard prosesleri; kuruluşun kontrolünde aynı görevleri yerine getiren birçok
                                      fazla personel; birkaç ürün veya hizmet </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prosesler" id="prosesler2" value="2" />
                                    <label class="form-check-label" for="prosesler2"> 2 Çok sayıda ürün ve hizmet veren,
                                      standard ama tekrarlamayan prosesler </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="prosesler" id="prosesler3" value="3" />
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
                                           id="ysolusmaseviyesi1" value="1" />
                                    <label class="form-check-label" for="ysolusmaseviyesi1"> 1 BGYS oldukça iyi
                                      oluşturulmuştur ve/veya diğer yönetim sistemleri yürürlüktedir. </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                           id="ysolusmaseviyesi2" value="2" />
                                    <label class="form-check-label" for="ysolusmaseviyesi2"> 2 Diğer yönetim sistemlerindeki
                                      bazı unsurlar uygulanır, diğerleri değil </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ysolusmaseviyesi"
                                           id="ysolusmaseviyesi3" value="3" />
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
                                    <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi1" value="1" />
                                    <label class="form-check-label" for="btaltyapi1">1 Az ya da çok standardlaştırılmış BT
                                      platformları, sunucuları, işletim sistemleri, veri tabanları, ağlar vb. </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi2" value="2" />
                                    <label class="form-check-label" for="btaltyapi2">2 1-3 farklı BT platformu, sunucuları,
                                      veri tabanları, ağları </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="btaltyapi" id="btaltyapi3" value="3" />
                                    <label class="form-check-label" for="btaltyapi3">3 Birçok farklı BT platformu, sunucuları,
                                      veri tabanları, ağları </label>
                                  </div>
                                </td>
                              </tr>
                              <tr>
                                <th>Bulut hizmetleri dâhil dış kaynaklara ve tedarikçilere olan bağlılık</th>
                                <td>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak1" value="1" />
                                    <label class="form-check-label" for="diskaynak1"> 1 Dış kaynaklara ya da tedarikçiler az
                                      bağımlı olma ya da bağımlı olmama</label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak2" value="2" />
                                    <label class="form-check-label" for="diskaynak2"> 2 Tüm kritik iş faaliyetleri olmamak
                                      koşuluyla sadece bazılarında dış kaynaklara ya da tedarikçiye olan normal
                                      bağımlılık,</label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="diskaynak" id="diskaynak3" value="3" />
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
                                           id="bilgisistemgelisimi1" value="1" />
                                    <label class="form-check-label" for="bilgisistemgelisimi1"> 1 Kuruluş içi sistem/uygulama
                                      geliştirme yok veya çok sınırlı</label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                           id="bilgisistemgelisimi2" value="2" />
                                    <label class="form-check-label" for="bilgisistemgelisimi2"> 2 Bazı önemli iş amaçları için
                                      kuruluş içi veya dış kaynaklı sistem/uygulama geliştirme</label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="bilgisistemgelisimi"
                                           id="bilgisistemgelisimi3" value="3" />
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
                                     value=""/>
                              <label for="faaliyetadi1"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay1" id="faaliyetadicalsay1" class="form-control"
                                     value=""/>
                              <label for="faaliyetadicalsay1"></label>
                            </div>
                          </td>
                          <td>4</td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadi4" id="faaliyetadi4" class="form-control"
                                     value=""/>
                              <label for="faaliyetadi4"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay4" id="faaliyetadicalsay4" class="form-control"
                                     value=""/>
                              <label for="faaliyetadicalsay4"></label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadi2" id="faaliyetadi2" class="form-control"
                                     value=""/>
                              <label for="faaliyetadi2"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay2" id="faaliyetadicalsay2" class="form-control"
                                     value=""/>
                              <label for="faaliyetadicalsay2"></label>
                            </div>
                          </td>
                          <td>5</td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadi5" id="faaliyetadi5" class="form-control"
                                     value=""/>
                              <label for="faaliyetadi5"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay5" id="faaliyetadicalsay5" class="form-control"
                                     value=""/>
                              <label for="faaliyetadicalsay5"></label>
                            </div>
                          </td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadi3" id="faaliyetadi3" class="form-control"
                                     value=""/>
                              <label for="faaliyetadi3"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay3" id="faaliyetadicalsay3" class="form-control"
                                     value=""/>
                              <label for="faaliyetadicalsay3"></label>
                            </div>
                          </td>
                          <td>6</td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadi6" id="faaliyetadi6" class="form-control"
                                     value=""/>
                              <label for="faaliyetadi6"></label>
                            </div>
                          </td>
                          <td>
                            <div class="form-floating form-floating-outline">
                              <input type="text" name="faaliyetadicalsay6" id="faaliyetadicalsay6" class="form-control"
                                     value=""/>
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
                  <button class="btn btn-outline-secondary btn-prev"><i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
                    <span class="align-middle d-sm-inline-block d-none">Geri</span>
                  </button>
                  <button class="btn btn-primary btn-next"><span class="align-middle d-sm-inline-block d-none me-sm-1">İleri</span>
                    <i class="mdi mdi-arrow-right"></i></button>
                </div>
              </div>
            </div>
            <!-- Social Links -->
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
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardaa" id="subevardaa" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardab" id="subevardab" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardac" id="subevardac" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td>
                            <input type="text" name="subefaaliyeta" id="subefaaliyeta" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                        </tr>
                        <tr>
                          <td>2</td>
                          <td>
                            <input type="text" name="subeadresib" id="subeadresib" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardba" id="subevardba" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardbb" id="subevardbb" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardbc" id="subevardbc" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td>
                            <input type="text" name="subefaaliyetb" id="subefaaliyetb" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                        </tr>
                        <tr>
                          <td>3</td>
                          <td>
                            <input type="text" name="subeadresic" id="subeadresic" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardca" id="subevardca" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardcb" id="subevardcb" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td style="width: 5%">
                            <input type="text" name="subevardcc" id="subevardcc" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                          <td>
                            <input type="text" name="subefaaliyetc" id="subefaaliyetc" class="form-control"
                                   placeholder="" value=""/>
                          </td>
                        </tr>
                        </tbody>
                      </table>
                      <button type="button" class="btn btn-primary" id="add-row-btn">Satır Ekle</button>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-semibold d-block">Yönetim Sistemlerinin Entegrasyon Düzeyi
                      Bilgileri</small>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="ygg"
                             name="ygg"  />
                      <label class="form-check-label" for="ygg">
                        YGG entegre yaklaşım
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="icdenetim"
                             name="icdenetim"  />
                      <label class="form-check-label" for="icdenetim">
                        İç denetim
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">&nbsp;</small>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="politikahedefler"
                             name="politikahedefler" />
                      <label class="form-check-label" for="politikahedefler">
                        Politika ve Hedefler
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="prosesentegre"
                             name="prosesentegre" />
                      <label class="form-check-label" for="prosesentegre">
                        Prosesler
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">&nbsp;</small>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="entegredokumantasyon"
                             name="entegredokumantasyon"  />
                      <label class="form-check-label" for="entegredokumantasyon">
                        Entegre dokümantasyon
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="duzelticifaaliyet"
                             name="duzelticifaaliyet"  />
                      <label class="form-check-label" for="duzelticifaaliyet">
                        Düzeltici faaliyetler
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="form-floating form-floating-outline">
                    <small class="text-light fw-medium d-block">&nbsp;</small>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="riskyonetimyaklasimi"
                             name="riskyonetimyaklasimi" />
                      <label class="form-check-label" for="riskyonetimyaklasimi">
                        Risk yönetimi
                      </label>
                    </div>
                    <div class="form-check mt-3">
                      <input class="form-check-input" type="checkbox" value="12.5" id="yondessor"
                             name="yondessor"  />
                      <label class="form-check-label" for="yondessor">
                        Yönetim desteği
                      </label>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="yonetimtemsilcisiadi" id="yonetimtemsilcisiadi" class="form-control"
                           value=""/>
                    <label for="yonetimtemsilcisiadi">Müşteri Kuruluş Yetkili Temsilcisi Ad / Soyad</label>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="form-floating form-floating-outline">
                    <input type="text" name="danisman" id="danisman" class="form-control"
                           value=""/>
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
                                      placeholder=""></textarea>
                        <label for="notlar">Notlar</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-12 d-flex justify-content-between">
                  <button class="btn btn-outline-secondary btn-prev"><i class="mdi mdi-arrow-left me-sm-1 me-0"></i>
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
@endsection
