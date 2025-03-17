@extends('layouts/layoutMaster')

@section('title', ' Dashboard ')

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
        <h1>Dashboard</h1>
      </div>
    </div>

    <div class="row">
      <!-- Quick Stats -->
      <div class="col-md-3 mb-4">
        <div class="card border-primary h-100">
          <div class="card-body text-center">
            <div class="display-4 text-primary mb-3">
              <i class="fas fa-file-alt"></i>
            </div>
            <h5 class="card-title">Standards</h5>
            <p class="card-text display-5 fw-bold">{{ \App\Models\Standard::count() }}</p>
            <a href="{{ route('standards.index') }}" class="btn btn-sm btn-primary">Manage Standards</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card border-success h-100">
          <div class="card-body text-center">
            <div class="display-4 text-success mb-3">
              <i class="fas fa-tasks"></i>
            </div>
            <h5 class="card-title">Audits</h5>
            <p class="card-text display-5 fw-bold">{{ \App\Models\Audit::where('user_id', Auth::id())->count() }}</p>
            <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-success">View Audits</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card border-warning h-100">
          <div class="card-body text-center">
            <div class="display-4 text-warning mb-3">
              <i class="fas fa-spinner"></i>
            </div>
            <h5 class="card-title">In Progress</h5>
            <p class="card-text display-5 fw-bold">{{ \App\Models\Audit::where('user_id', Auth::id())->where('status', 'in_progress')->count() }}</p>
            <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-warning">Continue Audits</a>
          </div>
        </div>
      </div>

      <div class="col-md-3 mb-4">
        <div class="card border-danger h-100">
          <div class="card-body text-center">
            <div class="display-4 text-danger mb-3">
              <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h5 class="card-title">Nonconformities</h5>
            <p class="card-text display-5 fw-bold">{{ \App\Models\Nonconformity::whereHas('audit', function($query) { $query->where('user_id', Auth::id()); })->where('status', 'open')->count() }}</p>
            <a href="#" class="btn btn-sm btn-danger">View Issues</a>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Recent Audits -->
      <div class="col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Audits</h5>
            <a href="{{ route('checklists.index') }}" class="btn btn-sm btn-primary">View All</a>
          </div>
          <div class="card-body">
            @php
              $recentAudits = \App\Models\Audit::where('user_id', Auth::id())
                  ->with('standardRevision.standard')
                  ->orderBy('created_at', 'desc')
                  ->take(5)
                  ->get();
            @endphp

            @if($recentAudits->count() > 0)
              <div class="list-group">
                @foreach($recentAudits as $audit)
                  <a href="{{ route('checklists.edit', $audit) }}" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                      <h6 class="mb-1">{{ $audit->company_name }}</h6>
                      <small>{{ $audit->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                      <div>
                        <small class="text-muted">
                          {{ $audit->standardRevision->standard->code ?? 'N/A' }} | {{ $audit->audit_type }}
                        </small>
                      </div>
                      <span class="badge bg-{{
                                            $audit->status == 'draft' ? 'secondary' :
                                            ($audit->status == 'in_progress' ? 'primary' :
                                            ($audit->status == 'completed' ? 'success' : 'info'))
                                        }}">
                                            {{ ucfirst($audit->status) }}
                                        </span>
                    </div>
                  </a>
                @endforeach
              </div>
            @else
              <div class="text-center py-4">
                <i class="fas fa-clipboard-list text-muted" style="font-size: 48px;"></i>
                <p class="mt-3">No audits found</p>
                <a href="{{ route('checklists.create') }}" class="btn btn-primary">
                  <i class="fas fa-plus"></i> Create New Audit
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Open Nonconformities -->
      <div class="col-md-6 mb-4">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Open Nonconformities</h5>
            <a href="#" class="btn btn-sm btn-danger">View All</a>
          </div>
          <div class="card-body">
            @php
              $openNCs = \App\Models\Nonconformity::whereHas('audit', function($query) {
                      $query->where('user_id', Auth::id());
                  })
                  ->with(['audit', 'standardSection.standardRevision.standard'])
                  ->where('status', 'open')
                  ->orderBy('created_at', 'desc')
                  ->take(5)
                  ->get();
            @endphp

            @if($openNCs->count() > 0)
              <div class="list-group">
                @foreach($openNCs as $nc)
                  <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                      <h6 class="mb-1">
                        {{ $nc->standardSection->standardRevision->standard->code ?? 'N/A' }} -
                        {{ $nc->standardSection->clauseNumber }} {{ $nc->standardSection->clauseTitle }}
                      </h6>
                      <span class="badge bg-{{ $nc->severity == 'major' ? 'danger' : 'warning' }}">
                                            {{ ucfirst($nc->severity) }}
                                        </span>
                    </div>
                    <p class="mb-1 text-muted small">{{ Str::limit($nc->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                      <small>
                        {{ $nc->audit->company_name }}
                        @if($nc->dueDate)
                          <span class="text-danger ms-2">
                                                Due: {{ $nc->dueDate->format('d/m/Y') }}
                                            </span>
                        @endif
                      </small>
                      <a href="{{ route('checklists.edit', $nc->audit) }}" class="btn btn-sm btn-outline-primary">
                        View
                      </a>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-4">
                <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                <p class="mt-3">No open nonconformities</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Quick Actions -->
      <div class="col-md-12 mb-4">
        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-3 mb-3 mb-md-0">
                <a href="{{ route('checklists.create') }}" class="btn btn-lg btn-outline-primary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                  <i class="fas fa-plus-circle mb-3" style="font-size: 2rem;"></i>
                  New Audit
                </a>
              </div>
              <div class="col-md-3 mb-3 mb-md-0">
                <a href="{{ route('standards.create') }}" class="btn btn-lg btn-outline-success w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                  <i class="fas fa-file-upload mb-3" style="font-size: 2rem;"></i>
                  Add Standard
                </a>
              </div>
              <!-- Düğme bağlantılarını güncelleyin -->
              <div class="col-md-3 mb-3 mb-md-0">
                <a href="{{ route('reports.index') }}" class="btn btn-lg btn-outline-info w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                  <i class="fas fa-chart-bar mb-3" style="font-size: 2rem;"></i>
                  Generate Report
                </a>
              </div>
              <div class="col-md-3">
                <a href="{{ route('settings.index') }}" class="btn btn-lg btn-outline-secondary w-100 h-100 d-flex flex-column justify-content-center align-items-center p-4">
                  <i class="fas fa-cog mb-3" style="font-size: 2rem;"></i>
                  Settings
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
