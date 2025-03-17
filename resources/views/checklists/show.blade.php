@extends('layouts/layoutMaster')

@section('title', ' Audit Checklist Details ')

@section('vendor-style')
  <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}"/>
@endsection

@section('vendor-script')
  <script src="{{ asset('assets/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
  <script src="{{asset('assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
@endsection

@section('styles')
  <style>
    .section-card {
      margin-bottom: 1.5rem;
      border-left: 4px solid #0d6efd;
    }
    .nonconformity-card {
      border-left: 4px solid #dc3545;
    }
    .question-card {
      margin-bottom: 1rem;
      border: 1px solid #dee2e6;
      border-radius: 0.25rem;
    }
    .compliant-yes {
      color: #198754;
      font-weight: bold;
    }
    .compliant-no {
      color: #dc3545;
      font-weight: bold;
    }
    .compliant-na {
      color: #6c757d;
      font-style: italic;
    }
    .evidence-box {
      background-color: #f8f9fa;
      border-left: 3px solid #0d6efd;
      padding: 0.5rem 1rem;
      margin-top: 0.5rem;
    }
    .nav-pills .nav-link.active {
      background-color: #0d6efd;
    }
    .print-only {
      display: none;
    }
    @media print {
      .no-print {
        display: none !important;
      }
      .print-only {
        display: block !important;
      }
      .card {
        border: 1px solid #ddd !important;
        break-inside: avoid;
      }
      .container-fluid {
        width: 100% !important;
        padding: 0 !important;
      }
    }
    .dropdown-item-submenu {
      position: relative;
    }

    .dropdown-item-submenu .dropdown-menu {
      top: 0;
      left: 100%;
      margin-top: -1px;
    }

    .dropdown-item-submenu:hover .dropdown-menu {
      display: block;
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Audit Checklist: {{ $audit->company_name }}</h1>
      <div class="no-print">
        <div class="btn-group">
          <a href="{{ route('checklists.edit', $audit) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Checklist
          </a>
          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li class="dropdown-item-submenu">
              <a class="dropdown-item" href="#" data-bs-toggle="dropdown">
                <i class="fas fa-file-export"></i> Export to DOCX
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'asama1']) }}">
                    I. Aşama
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'asama2']) }}">
                    II. Aşama
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'gozetim1']) }}">
                    I. Gözetim
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'gozetim2']) }}">
                    II. Gözetim
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'ybtar']) }}">
                    Yeniden Belgelendirme
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'ozeltar']) }}">
                    Özel Tetkik
                  </a>
                </li>
              </ul>
            </li>
            <li>
              <button class="dropdown-item" onclick="window.print()">
                <i class="fas fa-print"></i> Print Checklist
              </button>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteAuditModal">
                <i class="fas fa-trash"></i> Delete Audit
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteAuditModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Audit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete this audit for <strong>{{ $audit->company_name }}</strong>?</p>
            <p class="text-danger">This action cannot be undone.</p>
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

    <!-- Print Header -->
    <div class="print-only mb-4">
      <div class="text-center">
        <h2>ISO Standard Audit Checklist</h2>
        <h3>{{ $audit->standardRevision->standard->code }} {{ $audit->standardRevision->standard->version }}</h3>
        <p>Generated on: {{ date('d/m/Y') }}</p>
      </div>
    </div>

    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-xl-2 no-print">
        <div class="card mb-4">
          <div class="card-header">
            <h5 class="mb-0">Audit Info</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <strong>Company:</strong>
              <div>{{ $audit->company_name }}</div>
            </div>
            <div class="mb-3">
              <strong>Standard:</strong>
              <div>{{ $audit->standardRevision->standard->code }} {{ $audit->standardRevision->standard->version }}</div>
            </div>
            <div class="mb-3">
              <strong>Audit Type:</strong>
              <div>{{ $audit->audit_type }}</div>
            </div>
            <div class="mb-3">
              <strong>Date:</strong>
              <div>{{ $audit->audit_date->format('d/m/Y') }}</div>
            </div>
            <div class="mb-3">
              <strong>Status:</strong>
              <div>
                @if($audit->status == 'draft')
                  <span class="badge bg-secondary">Draft</span>
                @elseif($audit->status == 'in_progress')
                  <span class="badge bg-primary">In Progress</span>
                @elseif($audit->status == 'completed')
                  <span class="badge bg-success">Completed</span>
                @elseif($audit->status == 'approved')
                  <span class="badge bg-info">Approved</span>
                @endif
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h5 class="mb-0">Sections</h5>
          </div>
          <div class="card-body p-0">
            <div class="nav flex-column nav-pills" id="sections-tab" role="tablist">
              @foreach($audit->standardRevision->standardSections as $section)
                <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                   id="section-{{ $section->id }}-tab"
                   data-bs-toggle="pill"
                   href="#section-{{ $section->id }}"
                   role="tab"
                   aria-controls="section-{{ $section->id }}"
                   aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                  <div class="d-flex align-items-center">
                    <div class="flex-grow-1">{{ $section->clause_number }} {{ $section->clause_title }}</div>

                    @php
                      $nonconformity = $audit->nonconformities->where('standard_section_id', $section->id)->first();
                    @endphp

                    @if($nonconformity)
                      <i class="fas fa-exclamation-triangle text-danger ms-1"
                         title="{{ ucfirst($nonconformity->severity) }} Nonconformity"></i>
                    @endif
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-9 col-xl-10">
        <!-- Audit Summary for Print -->
        <div class="card mb-4 print-only">
          <div class="card-header">
            <h5>Audit Information</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-6">
                <table class="table table-borderless">
                  <tr>
                    <th style="width: 150px;">Company:</th>
                    <td>{{ $audit->company_name }}</td>
                  </tr>
                  <tr>
                    <th>Standard:</th>
                    <td>{{ $audit->standardRevision->standard->code }} {{ $audit->standardRevision->standard->version }}</td>
                  </tr>
                  <tr>
                    <th>Audit Type:</th>
                    <td>{{ $audit->audit_type }}</td>
                  </tr>
                </table>
              </div>
              <div class="col-6">
                <table class="table table-borderless">
                  <tr>
                    <th style="width: 150px;">Audit Date:</th>
                    <td>{{ $audit->audit_date->format('d/m/Y') }}</td>
                  </tr>
                  <tr>
                    <th>Auditor:</th>
                    <td>{{ $audit->user->name }}</td>
                  </tr>
                  <tr>
                    <th>Status:</th>
                    <td>{{ ucfirst($audit->status) }}</td>
                  </tr>
                </table>
              </div>
            </div>
          </div>
        </div>

        <div class="tab-content" id="sectionsTabContent">
          @foreach($audit->standardRevision->standardSections as $section)
            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                 id="section-{{ $section->id }}"
                 role="tabpanel"
                 aria-labelledby="section-{{ $section->id }}-tab">

              <div class="card section-card mb-4">
                <div class="card-header">
                  <h5>{{ $section->clause_number }} {{ $section->clause_title }}</h5>
                </div>
                <div class="card-body">
                  @if($section->description)
                    <div class="mb-3 p-3 bg-light rounded">
                      {{ $section->description }}
                    </div>
                  @endif

                  @if($section->questions && $section->questions->count() > 0)
                    @foreach($section->questions as $question)
                      @php
                        $response = $audit->responses->where('question_id', $question->id)->first();
                      @endphp

                      <div class="card question-card">
                        <div class="card-body">
                          <h6 class="card-title">{{ $question->question_text }}</h6>

                          <div class="mt-3">
                            <strong>Compliance Status:</strong>
                            @if($response)
                              @if($response->is_compliant === true)
                                <span class="badge bg-success">Compliant</span>
                              @elseif($response->is_compliant === false)
                                <span class="badge bg-danger">Non-Compliant</span>
                              @else
                                <span class="badge bg-secondary">N/A</span>
                              @endif
                            @else
                              <span class="badge bg-secondary">Not Assessed</span>
                            @endif
                          </div>

                          @if($response && (!empty($response->response_text) || !empty($response->evidence)))
                            <div class="mt-3">
                              @if(!empty($response->response_text))
                                <div>
                                  <strong>Notes:</strong>
                                  <p>{{ $response->response_text }}</p>
                                </div>
                              @endif

                              @if(!empty($response->evidence))
                                <div class="evidence-box">
                                  <strong>Evidence:</strong>
                                  <p class="mb-0">{{ $response->evidence }}</p>
                                </div>
                              @endif
                            </div>
                          @endif
                        </div>
                      </div>
                    @endforeach
                  @else
                    <div class="alert alert-info">
                      No questions defined for this section.
                    </div>
                  @endif
                </div>
              </div>

              @php
                $nonconformity = $audit->nonconformities->where('standard_section_id', $section->id)->first();
              @endphp

              @if($nonconformity)
                <div class="card nonconformity-card mb-4">
                  <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                      <i class="fas fa-exclamation-triangle me-2"></i>
                      {{ ucfirst($nonconformity->severity) }} Nonconformity
                    </h5>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <strong>Description:</strong>
                      <p>{{ $nonconformity->description }}</p>
                    </div>

                    @if(!empty($nonconformity->correction))
                      <div class="mb-3">
                        <strong>Correction:</strong>
                        <p>{{ $nonconformity->correction }}</p>
                      </div>
                    @endif

                    @if(!empty($nonconformity->corrective_action))
                      <div class="mb-3">
                        <strong>Corrective Action:</strong>
                        <p>{{ $nonconformity->corrective_action }}</p>
                      </div>
                    @endif

                    <div class="row">
                      <div class="col-md-6">
                        <strong>Status:</strong>
                        <span class="badge {{ $nonconformity->status === 'closed' ? 'bg-success' : 'bg-warning' }}">
                          {{ ucfirst($nonconformity->status) }}
                        </span>
                      </div>

                      @if($nonconformity->due_date)
                        <div class="col-md-6">
                          <strong>Due Date:</strong>
                          <span>{{ $nonconformity->due_date->format('d/m/Y') }}</span>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              @endif

              <div class="d-flex justify-content-between mb-4 no-print">
                <button type="button" class="btn btn-outline-primary section-nav"
                        data-direction="prev" data-current="{{ $section->id }}">
                  <i class="fas fa-arrow-left"></i> Previous Section
                </button>
                <button type="button" class="btn btn-outline-primary section-nav"
                        data-direction="next" data-current="{{ $section->id }}">
                  Next Section <i class="fas fa-arrow-right"></i>
                </button>
              </div>
            </div>
          @endforeach
        </div>

        <!-- Nonconformity Summary -->
        <div class="card mb-4">
          <div class="card-header">
            <h5>Nonconformity Summary</h5>
          </div>
          <div class="card-body">
            @php
              $majorCount = $audit->nonconformities->where('severity', 'major')->count();
              $minorCount = $audit->nonconformities->where('severity', 'minor')->count();
              $totalNonconformities = $majorCount + $minorCount;
            @endphp

            @if($totalNonconformities > 0)
              <div class="row">
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h3 class="mb-0">{{ $totalNonconformities }}</h3>
                      <p class="mb-0">Total Nonconformities</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h3 class="mb-0 text-danger">{{ $majorCount }}</h3>
                      <p class="mb-0">Major</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <h3 class="mb-0 text-warning">{{ $minorCount }}</h3>
                      <p class="mb-0">Minor</p>
                    </div>
                  </div>
                </div>
              </div>

              @if($totalNonconformities > 0)
                <div class="table-responsive mt-4">
                  <table class="table table-bordered">
                    <thead>
                    <tr>
                      <th>Section</th>
                      <th>Severity</th>
                      <th>Description</th>
                      <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($audit->nonconformities as $nonconformity)
                      <tr>
                        <td>
                          @php
                            $section = $audit->standardRevision->standardSections->where('id', $nonconformity->standard_section_id)->first();
                          @endphp
                          {{ $section ? $section->clause_number . ' ' . $section->clause_title : 'Unknown Section' }}
                        </td>
                        <td>
                            <span class="badge {{ $nonconformity->severity === 'major' ? 'bg-danger' : 'bg-warning' }}">
                              {{ ucfirst($nonconformity->severity) }}
                            </span>
                        </td>
                        <td>{{ Str::limit($nonconformity->description, 100) }}</td>
                        <td>
                            <span class="badge {{ $nonconformity->status === 'closed' ? 'bg-success' : 'bg-warning' }}">
                              {{ ucfirst($nonconformity->status) }}
                            </span>
                        </td>
                      </tr>
                    @endforeach
                    </tbody>
                  </table>
                </div>
              @endif
            @else
              <div class="text-center py-4">
                <div class="mb-3">
                  <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                </div>
                <h4>No Nonconformities Found</h4>
                <p class="text-muted">All audit requirements appear to be met.</p>
              </div>
            @endif
          </div>
        </div>

        <!-- Signature Section -->
        <div class="card mb-4">
          <div class="card-header">
            <h5>Signatures</h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label">Lead Auditor</label>
                <div class="p-3 border-bottom" style="min-height: 60px;">
                  @if($audit->status == 'approved')
                    <p>Electronically signed by {{ $audit->user->name }}</p>
                  @endif
                </div>
                <small>{{ $audit->user->name }}</small>
              </div>
              <div class="col-md-6 mb-4">
                <label class="form-label">Date</label>
                <div class="p-3 border-bottom" style="min-height: 60px;">
                  @if($audit->status == 'approved')
                    <p>{{ now()->format('d/m/Y') }}</p>
                  @endif
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 mb-4">
                <label class="form-label">Company Representative</label>
                <div class="p-3 border-bottom" style="min-height: 60px;"></div>
              </div>
              <div class="col-md-6 mb-4">
                <label class="form-label">Date</label>
                <div class="p-3 border-bottom" style="min-height: 60px;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script>
    // Enable navigation between sections
    document.querySelectorAll('.section-nav').forEach(button => {
      button.addEventListener('click', function() {
        const direction = this.dataset.direction;
        const currentId = this.dataset.current;
        const tabs = document.querySelectorAll('.nav-link');

        // Find current tab index
        let currentIndex = -1;
        tabs.forEach((tab, index) => {
          if (tab.id === `section-${currentId}-tab`) {
            currentIndex = index;
          }
        });

        if (currentIndex !== -1) {
          let targetIndex;

          if (direction === 'next') {
            targetIndex = (currentIndex + 1) % tabs.length;
          } else {
            targetIndex = (currentIndex - 1 + tabs.length) % tabs.length;
          }

          // Trigger click on target tab
          tabs[targetIndex].click();

          // Scroll to top of section
          window.scrollTo(0, 0);
        }
      });
    });
    document.addEventListener('DOMContentLoaded', function() {
      const submenuTriggers = document.querySelectorAll('.dropdown-item-submenu > a');

      submenuTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          const parent = this.parentNode;
          const submenu = parent.querySelector('.dropdown-menu');

          if (submenu) {
            if (submenu.style.display === 'block') {
              submenu.style.display = 'none';
            } else {
              submenu.style.display = 'block';
            }
          }
        });
      });
    });
  </script>
@endsection
