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
        <h1>Audit Checklists</h1>
      </div>
      <div class="col-md-6 text-md-end">
        <a href="{{ route('checklists.create') }}" class="btn btn-primary">
          <i class="fas fa-plus"></i> New Audit
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
        <h5>Your Audits</h5>
      </div>
      <div class="card-body">
        @if(count($audits) > 0)
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Company</th>
                <th>Standard</th>
                <th>Type</th>
                <th>Date</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th>Actions</th>
              </tr>
              </thead>
              <tbody>
              @foreach($audits as $audit)
                <tr>
                  <td>{{ $audit->company_name }}</td>
                  <td>
                    @if($audit->standardRevision && $audit->standardRevision->standard)
                      {{ $audit->standardRevision->standard->code }}
                    @else
                      N/A
                    @endif
                  </td>
                  <td>{{ $audit->audit_type }}</td>
                  <td>{{ $audit->audit_date->format('d/m/Y') }}</td>
                  <td>
                    @if($audit->status == 'draft')
                      <span class="badge bg-secondary">Draft</span>
                    @elseif($audit->status == 'in_progress')
                      <span class="badge bg-primary">In Progress</span>
                    @elseif($audit->status == 'completed')
                      <span class="badge bg-success">Completed</span>
                    @elseif($audit->status == 'approved')
                      <span class="badge bg-info">Approved</span>
                    @endif
                  </td>
                  <td>{{ $audit->updated_at->diffForHumans() }}</td>
                  <td>
                    <div class="btn-group" role="group">
                      <a href="{{ route('checklists.edit', $audit) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="{{ route('checklists.show', $audit) }}" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="{{ route('checklists.export', $audit) }}" class="btn btn-sm btn-success" title="Export">
                        <i class="fas fa-file-export"></i>
                      </a>
                      <button type="button" class="btn btn-sm btn-danger"
                              data-bs-toggle="modal" data-bs-target="#deleteModal{{ $audit->id }}">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>

                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal{{ $audit->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Delete Audit</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            Are you sure you want to delete this audit for <strong>{{ $audit->company_name }}</strong>?
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <form action="{{ route('checklists.destroy', $audit) }}" method="POST">
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
              <i class="fas fa-clipboard-list text-muted" style="font-size: 64px;"></i>
            </div>
            <h4>No Audits Found</h4>
            <p class="text-muted">Start by creating a new audit</p>
            <a href="{{ route('checklists.create') }}" class="btn btn-primary">
              <i class="fas fa-plus"></i> Create New Audit
            </a>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection
