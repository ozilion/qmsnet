@extends('layouts/layoutMaster')

@section('title', ' Edit Standard ')

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
        <h1>Edit Standard</h1>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h5>Standard Details</h5>
      </div>
      <div class="card-body">
        <form action="{{ route('standards.update', $standard) }}" method="POST">
          @csrf
          @method('PUT')

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="code" class="form-label">Standard Code <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('code') is-invalid @enderror"
                     id="code" name="code" value="{{ old('code', $standard->code) }}" required>
              @error('code')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="col-md-6">
              <label for="version" class="form-label">Version <span class="text-danger">*</span></label>
              <input type="text" class="form-control @error('version') is-invalid @enderror"
                     id="version" name="version" value="{{ old('version', $standard->version) }}" required>
              @error('version')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="mb-3">
            <label for="name" class="form-label">Standard Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name', $standard->name) }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description" rows="3">{{ old('description', $standard->description) }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <div class="form-check">
              <input class="form-check-input @error('is_active') is-invalid @enderror" type="checkbox"
                     id="is_active" name="is_active" value="1" {{ old('is_active', $standard->is_active) ? 'checked' : '' }}>
              <label class="form-check-label" for="is_active">
                Active Standard
              </label>
              @error('is_active')
              <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <a href="{{ route('standards.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Update Standard</button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
