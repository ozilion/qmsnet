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
    <div class="row justify-content-between mb-4">
      <div class="col-md-6">
        <h1>Standards</h1>
      </div>
      <div class="col-md-6 text-md-end">
        <a href="{{ route('standards.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> Add New Standard
        </a>
      </div>
    </div>

    @if (session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <div class="card">
      <div class="card-header">
        <h5>Available Standards</h5>
      </div>
      <div class="card-body">
        @if(count($standards) > 0)
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Code</th>
                <th>Name</th>
                <th>Version</th>
                <th>Current Revision</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              @foreach($standards as $standard)
                <tr>
                  <td>{{ $standard->code }}</td>
                  <td>{{ $standard->name }}</td>
                  <td>{{ $standard->version }}</td>
                  <td>
                    @if($standard->currentRevision)
                      {{ $standard->currentRevision->revision_number }}
                      ({{ $standard->currentRevision->revision_date->format('d/m/Y') }})
                    @else
                      <span class="badge bg-warning">No Revision</span>
                    @endif
                  </td>
                  <td>
                    @if($standard->is_active)
                      <span class="badge bg-success">Active</span>
                    @else
                      <span class="badge bg-secondary">Inactive</span>
                    @endif
                  </td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('standards.show', $standard) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('standards.edit', $standard) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="{{ route('standards.uploadRevision', $standard) }}" class="btn btn-sm btn-success">
                        <i class="fas fa-file-upload"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-danger"
                              data-bs-toggle="modal" data-bs-target="#deleteModal{{ $standard->id }}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $standard->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Delete Standard</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to delete <strong>{{ $standard->code }}</strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('standards.destroy', $standard) }}" method="POST">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="text-center py-4">
            <div class="mb-3">
              <i class="fas fa-file-alt text-muted" style="font-size: 64px;"></i>
            </div>
            <h4>No Standards Found</h4>
            <p class="text-muted">Start by adding a new standard</p>
            <a href="{{ route('standards.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Add New Standard
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
