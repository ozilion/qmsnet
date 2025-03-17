@extends('layouts/layoutMaster')

@section('title', ' Upload Standard Revision ')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
@endsection

@section('content')
  <div class="container">
    <div class="row mb-4">
      <div class="col-md-12">
        <h1>Upload Standard Revision</h1>
        <h4>{{ $standard->code }} - {{ $standard->name }}</h4>
      </div>
    </div>

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card">
      <div class="card-header">
        <h5>Revision Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('standards.storeRevision', $standard) }}" method="POST" enctype="multipart/form-data">
          @csrf

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="revision_number" class="form-label">Revision Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('revision_number') is-invalid @enderror"
                     id="revision_number" name="revision_number" value="{{ old('revision_number') }}" required>
              @error('revision_number')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="revision_date" class="form-label">Revision Date <span class="text-danger">*</span></label>
              <input type="date" class="form-control @error('revision_date') is-invalid @enderror"
                     id="revision_date" name="revision_date" value="{{ old('revision_date', date('Y-m-d')) }}" required>
              @error('revision_date')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="revision_notes" class="form-label">Revision Notes</label>
            <textarea class="form-control @error('revision_notes') is-invalid @enderror"
                      id="revision_notes" name="revision_notes" rows="3">{{ old('revision_notes') }}</textarea>
            @error('revision_notes')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="docx_file" class="form-label">DOCX File <span class="text-danger">*</span></label>
            <input type="file" class="form-control @error('docx_file') is-invalid @enderror"
                   id="docx_file" name="docx_file" required accept=".docx">
            @error('docx_file')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <div class="form-text">
              Upload a DOCX file containing the standard requirements. The file should follow the standard format with requirements organized in sections.
            </div>
          </div>

          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input @error('is_current') is-invalid @enderror"
                   id="is_current" name="is_current" value="1" {{ old('is_current') ? 'checked' : '' }}>
            <label class="form-check-label" for="is_current">
              Set as current revision
            </label>
            @error('is_current')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex justify-content-between">
            <a href="{{ route('standards.show', $standard) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Upload Revision</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
