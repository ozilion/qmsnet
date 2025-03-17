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

@section('styles')
  <style>
    .section-card {
      margin-bottom: 1.5rem;
      border-left: 4px solid #0d6efd;
    }
    .nonconformity-badge {
      position: absolute;
      top: 0;
      right: 0;
      transform: translate(50%, -50%);
    }
    .question-card {
      margin-bottom: 1rem;
      transition: all 0.3s;
    }
    .question-card:hover {
      box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    }
    .nav-pills .nav-link.active {
      background-color: #0d6efd;
    }
  </style>
@endsection

@section('content')
  <div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1>Edit Audit: {{ $audit->company_name }}</h1>
      <div>
        <div class="btn-group">
          <button type="button" class="btn btn-primary" onclick="document.getElementById('audit-form').submit()">
            <i class="fas fa-save"></i> Save Changes
          </button>
          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <!-- Replace the existing export link in edit.blade.php -->
            <li>
              <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#exportOptionsModal">
                <i class="fas fa-file-export"></i> Export to DOCX
              </a>
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

    <!-- Export modal -->
    <div class="modal fade" id="exportOptionsModal" tabindex="-1" aria-labelledby="exportOptionsModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exportOptionsModalLabel">Select Export Format</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="list-group">
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'asama1']) }}" class="list-group-item list-group-item-action">I. ASAMA</a>
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'asama2']) }}" class="list-group-item list-group-item-action">II. ASAMA</a>
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'gozetim1']) }}" class="list-group-item list-group-item-action">I. GOZETIM</a>
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'gozetim2']) }}" class="list-group-item list-group-item-action">II. GOZETIM</a>
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'ybtar']) }}" class="list-group-item list-group-item-action">YenidenBelgelendirme</a>
              <a href="{{ route('checklists.export', ['audit' => $audit, 'asama' => 'ozeltar']) }}" class="list-group-item list-group-item-action">OZEL TETKIK</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-3 col-xl-2">
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
              <div>{{ $audit->standardRevision->standard->code }}</div>
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
                <select class="form-select form-select-sm" id="status" name="status" form="audit-form">
                  <option value="draft" {{ $audit->status == 'draft' ? 'selected' : '' }}>Draft</option>
                  <option value="in_progress" {{ $audit->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                  <option value="completed" {{ $audit->status == 'completed' ? 'selected' : '' }}>Completed</option>
                  <option value="approved" {{ $audit->status == 'approved' ? 'selected' : '' }}>Approved</option>
                </select>
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
                    <div class="flex-grow-1">{{ $section->clause_number }} {{ $section->clauseTitle }}
                    </div>
                    @php
                      $hasResponse = false;
                      $hasNonconformity = false;

                      // Check if this section has any responses
                      if ($audit->responses && $section->questions) {
                          foreach ($section->questions as $question) {
                              if ($audit->responses->where('question_id', $question->id)->count() > 0) {
                                  $hasResponse = true;
                                  break;
                              }
                          }
                      }

                      // Check if this section has any nonconformities
                      if ($audit->nonconformities) {
                          $hasNonconformity = $audit->nonconformities->where('standard_section_id', $section->id)->count() > 0;
                      }
                    @endphp

                    @if($hasResponse)
                      <i class="fas fa-check-circle text-success ms-2"></i>
                    @endif

                    @if($hasNonconformity)
                      <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
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
        @if(session("error"))
        {{session("error")}}
        @endif
        <form id="audit-form" action="{{ route('checklists.update', $audit) }}" method="POST">
          @csrf
          @method('PUT')

          <input type="hidden" name="company_name" value="{{ $audit->company_name }}">
          <input type="hidden" name="audit_date" value="{{ $audit->audit_date->format('Y-m-d') }}">

          <div class="tab-content" id="sectionsTabContent">
            @foreach($audit->standardRevision->standardSections as $section)
              <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                   id="section-{{ $section->id }}"
                   role="tabpanel"
                   aria-labelledby="section-{{ $section->id }}-tab">

                <div class="card section-card mb-4">
                  <div class="card-header">
                    <h5>{{ $section->clause_number }} {{ $section->clauseTitle }}</h5>
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
                          $response = $responses->get($question->id);
                        @endphp

                        <div class="card question-card mb-2">
                          <div class="card-body">
                            <h6 class="card-title">{{ $question->item_number }} : {{ $question->question_text }}</h6>

                            <div class="row mt-3">
                              <div class="col-md-4">
                                <label class="form-label">Compliance Status:</label>
                                <div class="btn-group w-100" role="group">
                                  <input type="radio" class="btn-check" name="responses[{{ $question->id }}][is_compliant]"
                                         id="compliant_yes_{{ $question->id }}" value="yes"
                                    {{ $response && $response->isCompliant === true ? 'checked' : '' }}>
                                  <label class="btn btn-outline-success" for="compliant_yes_{{ $question->id }}">
                                    Compliant
                                  </label>

                                  <input type="radio" class="btn-check" name="responses[{{ $question->id }}][is_compliant]"
                                         id="compliant_no_{{ $question->id }}" value="no"
                                    {{ $response && $response->isCompliant === false ? 'checked' : '' }}>
                                  <label class="btn btn-outline-danger" for="compliant_no_{{ $question->id }}">
                                    Non-Compliant
                                  </label>

                                  <input type="radio" class="btn-check" name="responses[{{ $question->id }}][is_compliant]"
                                         id="compliant_na_{{ $question->id }}" value=""
                                    {{ !$response || $response->isCompliant === null ? 'checked' : '' }}>
                                  <label class="btn btn-outline-secondary" for="compliant_na_{{ $question->id }}">
                                    N/A
                                  </label>
                                </div>
                              </div>

                              <div class="col-md-8">
                                <div class="mb-3">
                                  <label for="response_text_{{ $question->id }}" class="form-label">Notes:</label>
                                  <textarea class="form-control"
                                            id="response_text_{{ $question->id }}"
                                            name="responses[{{ $question->id }}][text]"
                                            rows="2">{{ $response ? $response->responseText : '' }}</textarea>
                                </div>

                                <div class="mb-0">
                                  <label for="evidence_{{ $question->id }}" class="form-label">Evidence:</label>
                                  <input type="text" class="form-control"
                                         id="evidence_{{ $question->id }}"
                                         name="responses[{{ $question->id }}][evidence]"
                                         value="{{ $response ? $response->evidence : '' }}">
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    @else
                      <div class="alert alert-info">
                        No questions defined for this section.
                      </div>
                    @endif
                  </div>
                  <div class="card-footer">
                    @php
                      $nonconformity = $audit->nonconformities ?
                          $audit->nonconformities->where('standard_section_id', $section->id)->first() : null;
                    @endphp

                    <button type="button" class="btn {{ $nonconformity ? 'btn-warning' : 'btn-outline-danger' }}"
                            data-bs-toggle="modal" data-bs-target="#nonconformityModal{{ $section->id }}">
                      <i class="fas {{ $nonconformity ? 'fa-edit' : 'fa-exclamation-triangle' }}"></i>
                      {{ $nonconformity ? 'Edit Nonconformity' : 'Add Nonconformity' }}
                    </button>

                    <!-- Nonconformity Modal -->
                    <div class="modal fade" id="nonconformityModal{{ $section->id }}" tabindex="-1" aria-hidden="true">
                      <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">{{ $nonconformity ? 'Edit' : 'Add' }} Nonconformity</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <input type="hidden"
                                   name="nonconformities[{{ $section->id }}][id]"
                                   value="{{ $nonconformity ? $nonconformity->id : '' }}">

                            <div class="mb-3">
                              <label class="form-label">Severity:</label>
                              <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check"
                                       name="nonconformities[{{ $section->id }}][severity]"
                                       id="severity_minor_{{ $section->id }}" value="minor"
                                  {{ !$nonconformity || $nonconformity->severity === 'minor' ? 'checked' : '' }}>
                                <label class="btn btn-outline-warning" for="severity_minor_{{ $section->id }}">
                                  Minor
                                </label>

                                <input type="radio" class="btn-check"
                                       name="nonconformities[{{ $section->id }}][severity]"
                                       id="severity_major_{{ $section->id }}" value="major"
                                  {{ $nonconformity && $nonconformity->severity === 'major' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger" for="severity_major_{{ $section->id }}">
                                  Major
                                </label>
                              </div>
                            </div>

                            <div class="mb-3">
                              <label for="description_{{ $section->id }}" class="form-label">Description:</label>
                              <textarea class="form-control"
                                        id="description_{{ $section->id }}"
                                        name="nonconformities[{{ $section->id }}][description]"
                                        rows="3" required>{{ $nonconformity ? $nonconformity->description : '' }}</textarea>
                              <div class="form-text">Describe the nonconformity in detail.</div>
                            </div>

                            <div class="mb-3">
                              <label for="correction_{{ $section->id }}" class="form-label">Correction:</label>
                              <textarea class="form-control"
                                        id="correction_{{ $section->id }}"
                                        name="nonconformities[{{ $section->id }}][correction]"
                                        rows="2">{{ $nonconformity ? $nonconformity->correction : '' }}</textarea>
                              <div class="form-text">Immediate action to correct the nonconformity.</div>
                            </div>

                            <div class="mb-3">
                              <label for="corrective_action_{{ $section->id }}" class="form-label">Corrective Action:</label>
                              <textarea class="form-control"
                                        id="corrective_action_{{ $section->id }}"
                                        name="nonconformities[{{ $section->id }}][corrective_action]"
                                        rows="2">{{ $nonconformity ? $nonconformity->correctiveAction : '' }}</textarea>
                              <div class="form-text">Action to prevent recurrence.</div>
                            </div>

                            <div class="mb-3">
                              <label for="due_date_{{ $section->id }}" class="form-label">Due Date:</label>
                              <input type="date" class="form-control"
                                     id="due_date_{{ $section->id }}"
                                     name="nonconformities[{{ $section->id }}][due_date]"
                                     value="{{ $nonconformity && $nonconformity->dueDate ? $nonconformity->dueDate->format('Y-m-d') : '' }}">
                            </div>

                            <div class="mb-3">
                              <label class="form-label">Status:</label>
                              <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check"
                                       name="nonconformities[{{ $section->id }}][status]"
                                       id="status_open_{{ $section->id }}" value="open"
                                  {{ !$nonconformity || $nonconformity->status === 'open' ? 'checked' : '' }}>
                                <label class="btn btn-outline-danger" for="status_open_{{ $section->id }}">
                                  Open
                                </label>

                                <input type="radio" class="btn-check"
                                       name="nonconformities[{{ $section->id }}][status]"
                                       id="status_closed_{{ $section->id }}" value="closed"
                                  {{ $nonconformity && $nonconformity->status === 'closed' ? 'checked' : '' }}>
                                <label class="btn btn-outline-success" for="status_closed_{{ $section->id }}">
                                  Closed
                                </label>
                              </div>
                            </div>
                          </div>
                          <div class="modal-footer">
                            @if($nonconformity)
                              <button type="button" class="btn btn-outline-danger me-auto"
                                      onclick="document.getElementById('delete_nc_{{ $section->id }}').value='1';"
                                      data-bs-dismiss="modal">
                                Delete
                              </button>
                              <input type="hidden" id="delete_nc_{{ $section->id }}"
                                     name="nonconformities[{{ $section->id }}][delete]" value="0">
                            @endif
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Save</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-between mb-4">
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
        </form>
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

    // Auto-save functionality
    let autoSaveTimeout;
    const autoSaveDelay = 30000; // 30 seconds

    function setupAutoSave() {
      const formInputs = document.querySelectorAll('#audit-form input, #audit-form textarea, #audit-form select');

      formInputs.forEach(input => {
        input.addEventListener('change', () => {
          clearTimeout(autoSaveTimeout);

          // Show saving indicator
          const savingIndicator = document.createElement('div');
          savingIndicator.id = 'saving-indicator';
          savingIndicator.innerHTML = '<div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Auto-saving...';
          savingIndicator.style.position = 'fixed';
          savingIndicator.style.bottom = '20px';
          savingIndicator.style.right = '20px';
          savingIndicator.style.padding = '10px 20px';
          savingIndicator.style.backgroundColor = 'white';
          savingIndicator.style.border = '1px solid #ddd';
          savingIndicator.style.borderRadius = '4px';
          savingIndicator.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
          savingIndicator.style.zIndex = '1050';

          // Remove existing indicator if present
          const existingIndicator = document.getElementById('saving-indicator');
          if (existingIndicator) {
            existingIndicator.remove();
          }

          document.body.appendChild(savingIndicator);

          autoSaveTimeout = setTimeout(() => {
            // Submit the form via AJAX
            const form = document.getElementById('audit-form');
            const formData = new FormData(form);

            fetch(form.action, {
              method: 'POST',
              body: formData,
              headers: {
                'X-Requested-With': 'XMLHttpRequest'
              }
            })
              .then(response => response.json())
              .then(data => {
                // Update indicator to show saved
                savingIndicator.innerHTML = '<i class="fas fa-check text-success me-2"></i> Saved';

                // Remove indicator after 3 seconds
                setTimeout(() => {
                  savingIndicator.remove();
                }, 3000);
              })
              .catch(error => {
                savingIndicator.innerHTML = '<i class="fas fa-exclamation-triangle text-danger me-2"></i> Error saving';
                savingIndicator.style.backgroundColor = '#fff3f3';

                // Remove indicator after 5 seconds
                setTimeout(() => {
                  savingIndicator.remove();
                }, 5000);
              });
          }, autoSaveDelay);
        });
      });
    }

    // Initialize auto-save when page loads
    document.addEventListener('DOMContentLoaded', setupAutoSave);
  </script>
@endsection
