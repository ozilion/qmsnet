@extends('layouts.layoutMaster')

@section('title', __('Periodic Site Monitoring'))

@section('vendor-style')
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}"/>
  <link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}"/>
@endsection

@section('vendor-script')
  <script src="{{asset('assets/vendor/libs/moment/moment.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.colVis.min.js"></script>
  <script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/cleavejs/cleave.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
  <script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
@endsection

@section('page-script')
  <script src="{{asset('assets/js/auditor-denetci-dosyasi.js')}}"></script>
  <script src="{{asset('assets/js/modal-edit-user.js')}}"></script>
  <script src="{{asset('assets/js/modal-enable-otp.js')}}"></script>
  <script src="{{asset('assets/js/app-user-view.js')}}"></script>
  <script src="{{asset('assets/js/app-user-view-security.js')}}"></script>
@endsection

@section('content')
  <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">{{__('User')}} / {{__('View')}} /</span> {{__('Periodic Site Monitoring')}}
  </h4>
  <div class="row">
    <input type="hidden" id="uid" value="{{$user->id}}">

    <!-- User Content -->
    <div class="col-xl-12 col-lg-12 col-md-12 order-0 order-md-1">
      <!-- User Tabs -->
      <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link" href="{{route('user-view-account', ["id" => $user->id])}}"><i
              class="mdi mdi-account-outline mdi-20px me-1"></i>{{__('Application')}}</a></li>
        <li class="nav-item"><a class="nav-link" href="{{url('app/user/audit/log', ["uid" => $user->id])}}"><i
              class="mdi mdi-lock-open-outline mdi-20px me-1"></i>{{__('Audit Log')}}</a></li>
        <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i
              class="mdi mdi-bookmark-outline mdi-20px me-1"></i>{{__('Periodic Site Monitoring')}}</a></li>
      </ul>
      <!--/ User Tabs -->

      <!-- Site Monitoring -->
      <div class="card mb-4">
        <h5 class="card-header">{{$user ? $user->name : "Violet Mendoza"}} / {{__('Periodic Site Monitoring')}}</h5>
        <div class="table-responsive">
          <table class="table table-bordered user-site-monitoring display" id="siteMonitoringTable" style="width:100%; border-color: #1a202c">
            <thead>
            <!-- 1. Satır: Üst Başlıklar -->
            <tr>
              <td rowspan="2" class="text-center align-middle">SIRA NO</td>
              <td colspan="2" class="text-center align-middle">9001</td>
              <td colspan="2" class="text-center align-middle">14001</td>
              <td colspan="2" class="text-center align-middle">45001</td>
              <td colspan="2" class="text-center align-middle">22000</td>
              <td rowspan="2" class="text-center align-middle">50001</td>
              <td rowspan="2" class="text-center align-middle">27001</td>
              <td rowspan="2" class="text-center align-middle">OIC/SMIIC</td>
            </tr>
            <!-- 2. Satır: Alt Başlıklar -->
            <tr>
              <!-- 9001 Alt Başlıkları -->
              <td class="text-center align-middle">KRİTİK</td>
              <td class="text-center align-middle">KRİTİK OLMAYAN</td>
              <!-- 14001 Alt Başlıkları -->
              <td class="text-center align-middle">KRİTİK</td>
              <td class="text-center align-middle">KRİTİK OLMAYAN</td>
              <!-- 45001 Alt Başlıkları -->
              <td class="text-center align-middle">KRİTİK</td>
              <td class="text-center align-middle">KRİTİK OLMAYAN</td>
              <!-- 22000 Alt Başlıkları -->
              <td class="text-center align-middle">YÜKSEK</td>
              <td class="text-center align-middle">ORTA</td>
              <!-- 50001, 27001 ve OIC/SMIIC için alt başlık yok -->
            </tr>
            </thead>
            <tbody>
            <!-- Veri satırları DataTables veya sunucu taraflı dinamik olarak eklenecektir -->
            </tbody>
          </table>

        </div>
      </div>
      <!--/ Site Monitoring -->
    </div>
    <!--/ User Content -->
  </div>

  <!-- Modals -->
  @include('_partials._modals.modal-edit-user')
  @include('_partials._modals.modal-enable-otp')
  @include('_partials._modals.modal-upgrade-plan')
  <!-- /Modals -->

@endsection
