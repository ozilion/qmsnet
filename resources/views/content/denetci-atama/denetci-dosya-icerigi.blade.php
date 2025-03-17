@extends('layouts/layoutMaster')

@section('title', 'Denetçi Dosya İçeriği')

@section('vendor-style')
{{--  <link rel="stylesheet" href="{{asset('assets/vendor/libs/dropzone/dropzone.css')}}"/>--}}
  <link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
@endsection

@section('vendor-script')
{{--  <script src="{{asset('assets/vendor/libs/dropzone/dropzone.js')}}"></script>--}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/auditor-denetci-dosyasi.js')}}"></script>
  <script src="{{asset('assets/js/cards-actions.js')}}"></script>
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    {{--  <span class="text-muted fw-light">Forms /</span> File upload--}}
  </h4>

  <div class="row">
    <!-- Multi  -->
    <div class="col-12">
      <div class="card">
        <h5 class="card-header">Denetçi Dosya İçeriği Kontrolü</h5>
        <div class="card-body">
          <form id="denetciEkleForm" onSubmit="return false">
            {{ csrf_field() }}
            {{-- GÖZDEN GEÇİRELECEK KONULAR --}}
            <div class="col-sm-12">
              <div class="card card-action mb-4">
                <div class="card-header">
                  <div class="card-action-title text-center"></div>
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
                    <input type="hidden" name="uid" id="uid" class="form-control"
                           placeholder="" value="{{(isset($auditor) && !is_null($auditor->id)) ? $auditor->id : ''}}"/>
                    <div class="row g-4">
                      <div class="col-sm-12">
                        <div class="form-floating form-floating-outline">
                          <input type="text" name="name" id="name" class="form-control"
                                 placeholder=""
                                 value=""/>
                          <label for="name">Adı Soyadı</label>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-floating form-floating-outline">
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="basdenetcivaryok"
                                   name="basdenetci" {{(isset($auditor) && $auditor->basdenetci === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="basdenetcivaryok">
                              Başdenetçi
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="denetcivaryok"
                                   name="denetci" {{(isset($auditor) && $auditor->denetci === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="denetcivaryok">
                              Denetçi
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="adaydenetcivaryok"
                                   name="adaydenetci" {{(isset($auditor) && $auditor->adaydenetci === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="adaydenetcivaryok">
                              Aday Denetçi
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="teknikuzmanvaryok"
                                   name="teknikuzman" {{(isset($auditor) && $auditor->teknikuzman === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="teknikuzmanvaryok">
                              Teknik Uzman
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="ikuvaryok"
                                   name="iku" {{(isset($auditor) && $auditor->iku === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="ikuvaryok">
                              İslami Konular Uzmanı
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="teknikgozdengecirenvaryok"
                                   name="teknikgozdengeciren" {{(isset($auditor) && $auditor->teknikgozdengeciren === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="teknikgozdengecirenvaryok">
                              Teknik Gözden Geçiren
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="kararvericivaryok"
                                   name="kararverici" {{(isset($auditor) && $auditor->kararverici === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="kararvericivaryok">
                              Karar Verici
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-floating form-floating-outline">
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="belgelendirmemuduruvaryok"
                                   name="belgelendirmemuduru" {{(isset($auditor) && $auditor->belgelendirmemuduru === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="fbelgelendirmemuduruvaryok">
                              Belgelendirme Müdürü
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="belgelendirmesorumlusuvaryok"
                                   name="belgelendirmesorumlusu" {{(isset($auditor) && $auditor->belgelendirmesorumlusu === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="belgelendirmesorumlusuvaryok">
                              Belgelendirme Sorumlusu
                            </label>
                          </div>
                          <div class="form-check-inline mt-3">
                            <input class="form-check-input" type="checkbox" value="1"
                                   id="planlamasorumlusuvaryok"
                                   name="planlamasorumlusu" {{(isset($auditor) && $auditor->planlamasorumlusu === 1) ? 'checked' : ''}} />
                            <label class="form-check-label" for="planlamasorumlusuvaryok">
                              Planlama Sorumlusu
                            </label>
                          </div>
                        </div>
                      </div>
                      <div class="col-sm-12">
                        <button class="btn btn-primary btn-next btn-submit">Kaydet</button>
                      </div>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            {{-- GÖZDEN GEÇİRELECEK KONULAR --}}
          </form>
          <form id="denetciDosyaIcerigiForm" onSubmit="return false" style="{{ session('showDosyaForm') ? '' : 'display:none;' }}">
            {{ csrf_field() }}

            {{-- GÖZDEN GEÇİRELECEK KONULAR --}}
            <div class="col-sm-12">
              <div class="card card-action mb-4">
                <div class="card-header">
                  <div class="card-action-title text-center"></div>
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

                    <table class="table table-bordered table-hover">
                      <thead>
                      <tr>
                        <th scope="col">Kayıt Adı</th>
                        <th scope="col" style="width: 5%" class="text-center">Kontrol</th>
                        <th scope="col" style="width: 15%" class="text-center">Güncelleme Tarihi</th>
                        <th scope="col" style="width: 40%" class="text-center">Açıklama</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr>
                        <td>1. Özgeçmiş/CV</td>
                        <td class="text-center"><input type="radio" name="karargga" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="CV"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama1" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>2. Diploma</td>
                        <td class="text-center"><input type="radio" name="kararggb" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Diploma"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama2" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>3. Eğitim Sertifika veya Kayıtları (Başdenetçi/Denetçi Eğitimi*)</td>
                        <td class="text-center"><input type="radio" name="kararggc" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Egitim"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama3" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>4. İş Tecrübesine Ait Kayıtlar/İş Referans Yazıları</td>
                        <td class="text-center"><input type="radio" name="kararggd" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Referans"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama4" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>5. Denetim Tecrübesine Ait Kayıtlar/Audit Log</td>
                        <td class="text-center"><input type="radio" name="karargge" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Audit Log"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama5" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>6. Tarafsızlık ve Gizlilik Taahhüdü</td>
                        <td class="text-center"><input type="radio" name="kararggf" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Gizlilik Tahhudu"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama6" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>7. Personel Oryantasyon Formu</td>
                        <td class="text-center"><input type="radio" name="kararggg" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Egitim"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama7" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>8. Denetçi Sınavları</td>
                        <td class="text-center"><input type="radio" name="kararggh" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Sinav"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama8" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>9. Başdenetçi/Denetci Saha Gözlem Formu</td>
                        <td class="text-center"><input type="radio" name="kararggi" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Gozlem"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama9" class="form-control" placeholder="Açıklama girin"/></td>
                      </tr>
                      <tr>
                        <td>10. Personel Atama Formu</td>
                        <td class="text-center"><input type="radio" name="kararggj" value="u"
                                                       onclick="denetciDosyaIcerikKontrolu(10)"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama10" class="form-control" placeholder="Açıklama girin"/>
                        </td>
                      </tr>
                      <tr>
                        <td>11. Sözleşme</td>
                        <td class="text-center"><input type="radio" name="kararggk" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Sozlesme"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama11" class="form-control" placeholder="Açıklama girin"/>
                        </td>
                      </tr>
                      <tr>
                        <td>12. İmza Beyanı Formu</td>
                        <td class="text-center"><input type="radio" name="kararggl" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Imza"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama12" class="form-control" placeholder="Açıklama girin"/>
                        </td>
                      </tr>
                      <tr>
                        <td>13. Personel Performans Değerlendirme Formu</td>
                        <td class="text-center"><input type="radio" name="kararggm" value="u" data-bs-toggle="offcanvas"
                                                       data-upload-altklasor="Performans"
                                                       data-bs-target="#offcanvasDenetcidosyasiUpload"
                                                       aria-controls="offcanvasDenetcidosyasiUpload"/></td>
                        <td class="text-center"></td>
                        <td><input type="text" name="aciklama13" class="form-control" placeholder="Açıklama girin"/>
                        </td>
                      </tr>
                      </tbody>
                    </table>
                    <br />
                    <span class="helper-text">* İslami Konular Uzmanı ve Teknik Uzman için aranmaz.</span>
                  </div>
                </div>
              </div>
            </div>
            {{-- GÖZDEN GEÇİRELECEK KONULAR --}}
          </form>
          @include('_partials/_offcanvas/offcanvas-denetci-dosyasi-upload')
        </div>
      </div>
    </div>
    <!-- Multi  -->
  </div>
@endsection
