@extends('layouts/layoutMaster')

@section('title', ' Dashboard - Raporlar ')

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
        <h1>Generate Report</h1>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Report Options</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('reports.generate') }}" method="GET">
          <div class="mb-3">
            <label for="report_type" class="form-label">Report Type <span class="text-danger">*</span></label>
            <select class="form-select" id="report_type" name="report_type" required>
              <option value="">Select a report type</option>
              <option value="audit_summary">Audit Summary</option>
              <option value="nonconformity_analysis">Nonconformity Analysis</option>
              <option value="audit_history">Audit History</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="standard_id" class="form-label">Standard (Optional)</label>
            <select class="form-select" id="standard_id" name="standard_id">
              <option value="">All Standards</option>
              @foreach($standards as $standard)
                <option value="{{ $standard->id }}">{{ $standard->code }} ({{ $standard->version }})</option>
              @endforeach
            </select>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="date_from" class="form-label">Date From (Optional)</label>
              <input type="date" class="form-control" id="date_from" name="date_from">
            </div>

            <div class="col-md-6">
              <label for="date_to" class="form-label">Date To (Optional)</label>
              <input type="date" class="form-control" id="date_to" name="date_to">
            </div>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Generate Report</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
