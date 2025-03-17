@extends('layouts/layoutMaster')

@section('title', ' Raporlama ')

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
        <h1>Create New Audit</h1>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Audit Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('checklists.store') }}" method="POST">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="standard_revision_id" class="form-label">Standard <span class="text-danger">*</span></label>
              <select class="form-select @error('standard_revision_id') is-invalid @enderror"
                      id="standard_revision_id" name="standard_revision_id" required>
                <option value="">Select a standard</option>
                @foreach($standards as $standard)
                  @if($standard->currentRevision)
                    <option value="{{ $standard->currentRevision->id }}" {{ old('standard_revision_id') == $standard->currentRevision->id ? 'selected' : '' }}>
                      {{ $standard->code }} ({{ $standard->version }}) - {{ $standard->currentRevision->revision_number }}
                    </option>
                  @endif
                @endforeach
              </select>
              @error('standard_revision_id')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="audit_type" class="form-label">Audit Type <span class="text-danger">*</span></label>
              <select class="form-select @error('audit_type') is-invalid @enderror"
                      id="audit_type" name="audit_type" required>
                <option value="">Select audit type</option>
                <option value="Initial" {{ old('audit_type') == 'Initial' ? 'selected' : '' }}>Initial</option>
                <option value="Surveillance" {{ old('audit_type') == 'Surveillance' ? 'selected' : '' }}>Surveillance</option>
                <option value="Recertification" {{ old('audit_type') == 'Recertification' ? 'selected' : '' }}>Recertification</option>
                <option value="Special" {{ old('audit_type') == 'Special' ? 'selected' : '' }}>Special</option>
              </select>
              @error('audit_type')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-8">
              <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                     id="company_name" name="company_name" value="{{ old('company_name') }}" required>
              @error('company_name')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-4">
              <label for="audit_date" class="form-label">Audit Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('audit_date') is-invalid @enderror"
                     id="audit_date" name="audit_date" value="{{ old('audit_date', date('Y-m-d')) }}" required>
              @error('audit_date')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="plan_no" class="form-label">Plan Number (Optional)</label>
            <select class="form-select @error('plan_no') is-invalid @enderror" id="plan_no" name="plan_no">
              <option value="">Select a plan (optional)</option>
              @foreach($plans as $plan)
                @php
                  $firma = \App\Models\Basvuru::where('planno', $plan->planno)->first();
                  $firmaAdi = $firma ? $firma->firmaadi : '-';
                @endphp
                <option value="{{ $plan->planno }}" {{ old('plan_no') == $plan->planno ? 'selected' : '' }}>
                  Plan #{{ $plan->planno }} - {{ $firmaAdi }}
                </option>
              @endforeach
            </select>
            @error('plan_no')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-between">
            <a href="{{ route('checklists.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Create Audit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
