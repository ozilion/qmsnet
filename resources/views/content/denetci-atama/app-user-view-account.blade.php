@extends('layouts/layoutMaster')

@section('title', 'User View ' . ($user ? '- ' . $user->name : ''))

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/toastr/toastr.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-user-view.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<script src="{{asset('assets/vendor/libs/toastr/toastr.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/auditor-denetci-dosyasi.js')}}"></script>
<script src="{{asset('assets/js/modal-edit-user.js')}}"></script>
<script src="{{asset('assets/js/app-user-view.js')}}"></script>
<script src="{{asset('assets/js/app-user-view-account.js')}}"></script>
@endsection

@section('styles')
  <style>
    /* Thumbnail ve önizleme için CSS */
    .file-preview-container {
      display: flex;
      flex-wrap: wrap;
      margin: 0 -7.5px; /* Negatif margin for the grid */
    }

    .file-preview-item {
      padding: 7.5px;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .file-preview-item:hover {
      transform: translateY(-3px);
    }

    .file-preview-icon {
      border-radius: 4px 4px 0 0;
      border: 1px solid #e0e0e0;
      border-bottom: none;
      background-color: #f8f9fa;
      height: 140px !important;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }

    .file-preview-icon img {
      max-width: 100% !important;
      max-height: 100% !important;
      width: auto !important;
      height: auto !important;
      display: block !important;
    }

    .file-preview-icon i {
      font-size: 36px;
    }

    .file-preview-info {
      border: 1px solid #e0e0e0;
      border-top: none;
      border-radius: 0 0 4px 4px;
      padding: 10px;
      background-color: #fff;
    }

    .file-preview-name {
      font-size: 13px;
      font-weight: 500;
      margin-bottom: 4px;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .file-preview-size {
      font-size: 12px;
      color: #6c757d;
      margin-bottom: 8px;
    }

    .file-preview-action {
      text-align: center;
    }

    .file-preview-download {
      font-size: 12px;
      padding: 3px 8px;
    }

    /* Modal içinde tam ekran resim görüntüleme için stiller */
    .fullscreen-image-container {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.9);
      z-index: 1050;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .fullscreen-close-btn {
      position: absolute;
      top: 15px;
      right: 15px;
      z-index: 1051;
      color: white;
      background-color: rgba(0, 0, 0, 0.5);
      border: none;
      border-radius: 50%;
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
    }

    .fullscreen-filename {
      position: absolute;
      bottom: 15px;
      left: 0;
      right: 0;
      text-align: center;
      color: white;
      padding: 8px;
      background-color: rgba(0, 0, 0, 0.5);
      font-size: 14px;
    }

    /* Dark mode uyumluluğu */
    html[data-theme="dark"] .file-preview-icon {
      background-color: #283144;
      border-color: #444564;
    }

    html[data-theme="dark"] .file-preview-info {
      background-color: #2b3547;
      border-color: #444564;
    }

    /* Tablo içindeki butonlar için stil */
    .table .btn-fab {
      margin: 0 2px;
    }

    /* Tablo içindeki butonların yan yana görünmesi için stiller */
    .table td.text-center .btn-fab {
      display: inline-block;
      vertical-align: middle;
    }

    /* Butonlar arasındaki boşluğu ayarla */
    .table td.text-center .btn-fab + .btn-fab {
      margin-left: 4px;
    }

    /* Butonların tablonun içinde düzgün görünmesi için */
    .table td.text-center {
      white-space: nowrap;
    }

    /* Tablo buton hücreleri için yeni stil */
    .button-cell {
      min-width: 130px;
      padding: 8px !important;
    }

    /* Butonları içeren flex container */
    .button-cell .d-flex {
      gap: 8px;
    }

    /* Buton boyutlarını standardize etmek için */
    .button-cell .btn {
      width: 38px;
      height: 38px;
      padding: 0;
      display: flex;
      align-items: center;
      justify-content: center;
    }

  </style>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">{{__('User')}} / {{__('View')}} /</span> {{__('Account')}}
</h4>
{{--{{print_r($user->denetci->uid)}}--}}
<div class="row">
  <!-- User Sidebar -->
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <!-- User Card -->
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class=" d-flex align-items-center flex-column">
            <img class="img-fluid rounded mb-3 mt-4" src="{{asset('assets/img/avatars/10.png')}}" height="120" width="120" alt="User avatar" />
            <div class="user-info text-center">
              <h4>{{$user ? $user->name : "No Name"}}</h4>
              <span class="badge bg-label-danger rounded-pill">{{$user ? $user->role : "User"}}</span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-wrap my-2 py-3">
{{--          <div class="d-flex align-items-center me-4 mt-3 gap-3">--}}
{{--            <div class="avatar">--}}
{{--              <div class="avatar-initial bg-label-primary rounded">--}}
{{--                <i class='mdi mdi-check mdi-24px'></i>--}}
{{--              </div>--}}
{{--            </div>--}}
{{--            <div>--}}
{{--              <h4 class="mb-0">{{$user ? $user->toplamdenetim : "0"}}</h4>--}}
{{--              <span>Toplam Denetim</span>--}}
{{--            </div>--}}
{{--          </div>--}}
          <div class="d-flex align-items-center mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class='mdi mdi-star-outline mdi-24px'></i>
              </div>
            </div>
            <div>
              {!! $user->sistemler !!}
            </div>
          </div>
        </div>
        <h5 class="pb-3 border-bottom mb-3">Detaylar</h5>
        <div class="info-container">
          <ul class="list-unstyled mb-4">
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Kullanıcı adı:</span>
              <span>{{$user->email}}</span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">E-Posta:</span>
              <span>{{$user->email}}</span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Durum:</span>
              <span class="badge bg-label-success rounded-pill">{{$user->denetci->is_active == 1 ? "Aktif" : "Pasif"}}</span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Rol:</span>
              <span>{{$user->role}}</span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Kuruluş:</span>
              <span>{{$user->kurulus}}</span>
            </li>
          </ul>
          <div class="d-flex justify-content-center">
{{--            <a href="javascript:;" class="btn btn-primary me-3" data-bs-target="#editUser" data-bs-toggle="modal">Edit</a>--}}
{{--            <a href="javascript:;" class="btn btn-outline-danger suspend-user">Suspended</a>--}}
          </div>
        </div>
      </div>
    </div>
    <!-- /User Card -->
  </div>
  <!--/ User Sidebar -->

  <!-- User Content -->
  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
    <!-- User Tabs -->
    <ul class="nav nav-tabs mb-3">
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="mdi mdi-account-outline mdi-20px me-1"></i>{{__('Application')}}</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/audit/log', ["uid" => $user->id])}}"><i class="mdi mdi-lock-open-outline mdi-20px me-1"></i>{{__('Audit Log')}}</a></li>
      <li class="nav-item"><a class="nav-link" href="{{url('app/user/periodic/site/monitoring', ["uid" => $user->id])}}"><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>{{__('Periodic Site Monitoring')}}</a></li>
      <li class="nav-item"><a class="nav-link" href=""><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>Baş Denetçi Değerlendirme</a></li>
      <li class="nav-item"><a class="nav-link" href=""><i class="mdi mdi-bookmark-outline mdi-20px me-1"></i>Müşteri Değerlendirme</a></li>
    </ul>
    <!--/ User Tabs -->


    <!-- Project table -->
    <div class="card">
      <h5 class="card-header">Denetçi Dosya İçeriği Kontrolü</h5>
      <div class="card-body">
        <form id="denetciEkleForm" onSubmit="return false">
          {{ csrf_field() }}
          <input type="hidden" name="name" id="name" class="form-control"
                 placeholder=""
                 value="{{$user ? $user->name : ""}}"/>
          <input type="hidden" name="uid" id="uid" class="form-control"
                 placeholder=""
                 value="{{$user ? $user->id : ""}}"/>
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
        <form id="denetciDosyaIcerigiForm" onSubmit="return false">
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
                      <th scope="col" style="width: 10%" class="text-center">Kontrol</th>
                      <th scope="col" style="width: 15%" class="text-center">Güncelleme Tarihi</th>
                      <th scope="col" style="width: 40%" class="text-center">Açıklama</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                      <td>1. Özgeçmiş/CV</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="karargga" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="CV"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="CV">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama1" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>2. Diploma</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggb" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Diploma"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Diploma">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama2" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>3. Eğitim Sertifika veya Kayıtları (Başdenetçi/Denetçi Eğitimi*)</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggc" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Egitim"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Egitim">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama3" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>4. İş Tecrübesine Ait Kayıtlar/İş Referans Yazıları</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggd" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Referans"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Referans">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama4" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>5. Denetim Tecrübesine Ait Kayıtlar/Audit Log</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="karargge" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Audit Log"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Audit Log">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama5" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>6. Tarafsızlık ve Gizlilik Taahhüdü</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggf" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Gizlilik Tahhudu"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Gizlilik Tahhudu">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama6" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>7. Personel Oryantasyon Formu</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggg" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Egitim"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Egitim">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama7" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>8. Denetçi Sınavları</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggh" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Sinav"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Sinav">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama8" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>9. Başdenetçi/Denetci Saha Gözlem Formu</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggi" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Gozlem"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Gozlem">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama9" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>10. Personel Atama Formu</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggj" value="u"
                                  onclick="denetciDosyaIcerikKontrolu({{$user ? $user->id : ''}})">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Atama">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama10" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>11. Sözleşme</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggk" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Sozlesme"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Sozlesme">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama11" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>12. İmza Beyanı Formu</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggl" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Imza"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Imza">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama12" class="form-control" placeholder="Açıklama girin"/></td>
                    </tr>
                    <tr>
                      <td>13. Personel Performans Değerlendirme Formu</td>
                      <td class="text-center button-cell">
                        <div class="d-flex justify-content-center align-items-center">
                          <button type="button" class="btn btn-icon btn-primary btn-sm me-2 btn-icerik-form" name="kararggm" value="u"
                                  data-bs-toggle="offcanvas" data-upload-altklasor="Performans"
                                  data-bs-target="#offcanvasDenetcidosyasiUpload"
                                  aria-controls="offcanvasDenetcidosyasiUpload">
                            <span class="tf-icons mdi mdi-exclamation mdi-20px"></span>
                          </button>
                          <button type="button" class="btn btn-icon btn-info btn-sm"
                                  data-bs-toggle="modal" data-bs-target="#filePreviewModal"
                                  data-folder-name="{{$user ? $user->name : ''}}" data-subfolder-name="Performans">
                            <span class="tf-icons mdi mdi-eye-outline mdi-20px"></span>
                          </button>
                        </div>
                      </td>
                      <td class="text-center"></td>
                      <td><input type="text" name="aciklama13" class="form-control" placeholder="Açıklama girin"/></td>
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
      </div>
    </div>
    <!-- /Project table -->
  </div>
  <!--/ User Content -->
</div>


@include('_partials/_offcanvas/offcanvas-denetci-dosyasi-upload')
@include('_partials/_modals/modal-file-preview')
@include('_partials/_modals/modal-edit-user')
@include('_partials/_modals/modal-upgrade-plan')
<!-- /Modal -->
@endsection
