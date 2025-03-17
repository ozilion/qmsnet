@extends('layouts/layoutMaster')

@section('title', 'Roles - Apps')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>

<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
@endsection

@section('page-script')
<script src="{{asset('assets/js/app-access-roles.js')}}"></script>
<script src="{{asset('assets/js/modal-add-role.js')}}"></script>
@endsection

@section('content')
<h4 class="mb-1">Roles List</h4>
<p class="mb-4">A role provided access to predefined menus and features so that depending on assigned role an administrator can have access to what user needs.</p>
<!-- Role cards -->
<div class="row g-4">
  {{-- Loop through each role --}}
  @foreach($roles as $role)
    <div class="col-xl-4 col-lg-6 col-md-6">
      <div class="card">
        <div class="card-body">
          {{-- 1) Top row: "Total X users" & avatar group --}}
          <div class="d-flex justify-content-between mb-2">
            {{-- If $role->user_count or similar is available, otherwise placeholder --}}
            <p>Total XXXX users</p>

            {{-- Avatars group (demo placeholders here);
                 If you have real user data, loop that or skip entirely --}}
            <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                  title="Example User A" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/5.png') }}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                  title="Example User B" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/12.png') }}" alt="Avatar">
              </li>
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top"
                  title="Example User C" class="avatar pull-up">
                <img class="rounded-circle" src="{{ asset('assets/img/avatars/6.png') }}" alt="Avatar">
              </li>
              <li class="avatar">
                <span class="avatar-initial rounded-circle pull-up bg-lighter text-body"
                      data-bs-toggle="tooltip" data-bs-placement="bottom" title="3 more">+3
                </span>
              </li>
            </ul>
          </div>

          {{-- 2) Bottom row: role heading & copy icon --}}
          <div class="d-flex justify-content-between align-items-end">
            <div class="role-heading">
              {{-- role name from DB --}}
              <h4 class="mb-1 text-body">{{ $role->name }}</h4>
              {{-- "Edit Role" could open a modal with role details --}}
              <a href="javascript:;" data-bs-toggle="modal"
                 data-bs-target="#addRoleModal" class="role-edit-modal">
                <span>Edit Role</span>
              </a>
            </div>
            <a href="javascript:void(0);" class="text-muted">
              <i class="mdi mdi-content-copy mdi-20px"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  @endforeach

  {{-- Last "Add Role" Card --}}
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card h-100">
      <div class="row h-100">
        <div class="col-5">
          <div class="d-flex align-items-end h-100 justify-content-center">
            <img src="{{ asset('assets/img/illustrations/add-new-role-illustration.png') }}"
                 class="img-fluid" alt="Image" width="70">
          </div>
        </div>
        <div class="col-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <button data-bs-target="#addRoleModal" data-bs-toggle="modal"
                    class="btn btn-primary mb-3 text-nowrap add-new-role">
              Add Role
            </button>
            <p class="mb-0">Add role, if it does not exist</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-4">

  <h4 class="fw-medium mb-1 mt-5">Total users with their roles</h4>
  <p class="mb-0 mt-1">Find all of your company’s administrator accounts and their associate roles.</p>

  <div class="col-12">
    <!-- Role Table -->
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-users table">
          <thead class="table-light">
            <tr>
              <th></th>
              <th></th>
              <th>User</th>
              <th>Role</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <!--/ Role Table -->
  </div>
</div>
<!--/ Role cards -->

<!-- Add Role Modal -->
@include('_partials/_modals/modal-add-role')
<!-- / Add Role Modal -->
@endsection
