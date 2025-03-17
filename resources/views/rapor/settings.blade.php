@extends('layouts/layoutMaster')

@section('title', ' Dashboard - Ayarlar ')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
@endsection

@section('page-script')
{{--  <script src="{{ asset('assets/js/audit-plan.js') }}"></script>--}}
@endsection

@section('content')
  <div class="container">
    <div class="row mb-4">
      <div class="col-md-12">
        <h1>Settings</h1>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="row">
      <div class="col-md-4 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">User Profile</h5>
          </div>
          <div class="card-body text-center">
            <div class="mb-3">
              <i class="fas fa-user-circle" style="font-size: 64px; color: #6c757d;"></i>
            </div>
            <h5>{{ $user->name }}</h5>
            <p class="text-muted">{{ $user->email }}</p>
            <p class="mb-1">Role: {{ ucfirst($user->role) }}</p>
            <p class="mb-0">Member since: {{ $user->created_at->format('M Y') }}</p>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Application Settings</h5>
          </div>
          <div class="card-body">
            <form action="{{ route('settings.update') }}" method="POST">
              @csrf

              <h6 class="mb-3">Synchronization</h6>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="auto_sync" name="auto_sync"
                    {{ $settings && $settings->auto_sync ? 'checked' : '' }}>
                  <label class="form-check-label" for="auto_sync">
                    Auto Synchronization
                  </label>
                </div>
                <div class="form-text">
                  Automatically sync data when connected to the internet.
                </div>
              </div>

              <div class="mb-4">
                <label for="sync_frequency" class="form-label">Sync Frequency (minutes)</label>
                <select class="form-select" id="sync_frequency" name="sync_frequency">
                  <option value="5" {{ $settings && $settings->sync_frequency == 5 ? 'selected' : '' }}>Every 5 minutes</option>
                  <option value="15" {{ !$settings || $settings->sync_frequency == 15 ? 'selected' : '' }}>Every 15 minutes</option>
                  <option value="30" {{ $settings && $settings->sync_frequency == 30 ? 'selected' : '' }}>Every 30 minutes</option>
                  <option value="60" {{ $settings && $settings->sync_frequency == 60 ? 'selected' : '' }}>Every hour</option>
                </select>
              </div>

              <hr class="my-4">

              <h6 class="mb-3">Appearance</h6>
              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode"
                    {{ $settings && $settings->dark_mode ? 'checked' : '' }}>
                  <label class="form-check-label" for="dark_mode">
                    Dark Mode
                  </label>
                </div>
              </div>

              <hr class="my-4">

              <h6 class="mb-3">Notifications</h6>
              <div class="mb-3">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="notification_email" name="notification_email"
                    {{ !$settings || $settings->notification_email ? 'checked' : '' }}>
                  <label class="form-check-label" for="notification_email">
                    Email Notifications
                  </label>
                </div>
              </div>

              <div class="mb-4">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="notification_app" name="notification_app"
                    {{ !$settings || $settings->notification_app ? 'checked' : '' }}>
                  <label class="form-check-label" for="notification_app">
                    In-App Notifications
                  </label>
                </div>
              </div>

              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Save Settings</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
