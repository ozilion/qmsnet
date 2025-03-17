@extends('layouts/layoutMaster')

@section('title', ' Standard Details ')

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
      <div class="col-md-6">
        <h1>{{ $standard->code }} - {{ $standard->name }}</h1>
      </div>
      <div class="col-md-6 text-end">
        <div class="btn-group" role="group">
          <a href="{{ route('standards.edit', $standard) }}" class="btn btn-primary">
            <i class="fas fa-edit"></i> Edit Standard
          </a>
          <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="visually-hidden">Toggle Dropdown</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('standards.uploadRevision', $standard) }}">
                <i class="fas fa-upload"></i> Upload New Revision
              </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#deleteStandardModal">
                <i class="fas fa-trash"></i> Delete Standard
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Delete Modal -->
    <div class="modal fade" id="deleteStandardModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Delete Standard</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to delete <strong>{{ $standard->code }} - {{ $standard->name }}</strong>?</p>
            <p class="text-danger">This action cannot be undone. All revisions, sections, and questions will also be deleted.</p>
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

    <div class="row">
      <div class="col-md-4">
        <div class="card mb-4">
          <div class="card-header">
            <h5>Standard Details</h5>
          </div>
          <div class="card-body">
            <dl class="row">
              <dt class="col-sm-4">Code:</dt>
              <dd class="col-sm-8">{{ $standard->code }}</dd>

              <dt class="col-sm-4">Name:</dt>
              <dd class="col-sm-8">{{ $standard->name }}</dd>

              <dt class="col-sm-4">Version:</dt>
              <dd class="col-sm-8">{{ $standard->version }}</dd>

              <dt class="col-sm-4">Status:</dt>
              <dd class="col-sm-8">
                <span class="badge {{ $standard->is_active ? 'bg-success' : 'bg-danger' }}">
                  {{ $standard->is_active ? 'Active' : 'Inactive' }}
                </span>
              </dd>

              <dt class="col-sm-4">Created:</dt>
              <dd class="col-sm-8">{{ $standard->created_at->format('d/m/Y') }}</dd>

              <dt class="col-sm-4">Updated:</dt>
              <dd class="col-sm-8">{{ $standard->updated_at->format('d/m/Y') }}</dd>
            </dl>

            @if($standard->description)
              <hr>
              <h6>Description:</h6>
              <p>{{ $standard->description }}</p>
            @endif
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Standard Revisions</h5>
            <a href="{{ route('standards.uploadRevision', $standard) }}" class="btn btn-sm btn-primary">
              <i class="fas fa-upload"></i> Upload New Revision
            </a>
          </div>
          <div class="card-body">
            @if(count($standard->revisions) > 0)
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                  <tr>
                    <th>Revision #</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Sections</th>
                    <th>Questions</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                  @foreach($standard->revisions as $revision)
                    <tr>
                      <td>{{ $revision->revision_number }}</td>
                      <td>{{ $revision->revision_date->format('d/m/Y') }}</td>
                      <td>
                        @if($revision->is_current)
                          <span class="badge bg-success">Current</span>
                        @else
                          <span class="badge bg-secondary">Previous</span>
                        @endif
                      </td>
                      <td>{{ $revision->standardSections->count() }}</td>
                      <td>
                        @php
                          $questionCount = 0;
                          foreach($revision->standardSections as $section) {
                            $questionCount += $section->questions->count();
                          }
                        @endphp
                        {{ $questionCount }}
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          @if(!$revision->is_current)
                            <form action="{{ route('standards.set_current_revision', ['standard' => $standard, 'revision' => $revision]) }}" method="POST">
                              @csrf
                              <button type="submit" class="btn btn-sm btn-success">
                                <i class="fas fa-check"></i> Set as Current
                              </button>
                            </form>
                          @endif

                          <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#revisionDetailsModal{{ $revision->id }}">
                            <i class="fas fa-eye"></i>
                          </button>
                        </div>

                        <!-- Revision Details Modal -->
                        <div class="modal fade" id="revisionDetailsModal{{ $revision->id }}" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title">Revision #{{ $revision->revision_number }} Details</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="mb-4">
                                  <h6>General Information</h6>
                                  <dl class="row">
                                    <dt class="col-sm-4">Revision Number:</dt>
                                    <dd class="col-sm-8">{{ $revision->revision_number }}</dd>

                                    <dt class="col-sm-4">Revision Date:</dt>
                                    <dd class="col-sm-8">{{ $revision->revision_date->format('d/m/Y') }}</dd>

                                    <dt class="col-sm-4">Status:</dt>
                                    <dd class="col-sm-8">
                                      <span class="badge {{ $revision->is_current ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $revision->is_current ? 'Current' : 'Previous' }}
                                      </span>
                                    </dd>

                                    <dt class="col-sm-4">Created:</dt>
                                    <dd class="col-sm-8">{{ $revision->created_at->format('d/m/Y') }}</dd>
                                  </dl>
                                </div>

                                @if($revision->revision_notes)
                                  <div class="mb-4">
                                    <h6>Revision Notes</h6>
                                    <p>{{ $revision->revision_notes }}</p>
                                  </div>
                                @endif

                                <div class="mb-4">
                                  <h6>Sections</h6>
                                  @if($revision->standardSections->count() > 0)
                                    <div class="accordion" id="sectionsAccordion{{ $revision->id }}">
                                      @foreach($revision->standardSections as $section)
                                        <div class="accordion-item">
                                          <h2 class="accordion-header" id="heading{{ $section->id }}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#collapse{{ $section->id }}" aria-expanded="false"
                                                    aria-controls="collapse{{ $section->id }}">
                                              {{ $section->clause_number }} {{ $section->clause_title }}
                                              <span class="badge bg-primary ms-2">{{ $section->questions->count() }} questions</span>
                                            </button>
                                          </h2>
                                          <div id="collapse{{ $section->id }}" class="accordion-collapse collapse"
                                               aria-labelledby="heading{{ $section->id }}"
                                               data-bs-parent="#sectionsAccordion{{ $revision->id }}">
                                            <div class="accordion-body">
                                              @if($section->description)
                                                <p>{{ $section->description }}</p>
                                              @endif

                                              @if($section->questions->count() > 0)
                                                <ol>
                                                  @foreach($section->questions as $question)
                                                    <li class="mb-2">{{ $question->question_text }}</li>
                                                  @endforeach
                                                </ol>
                                              @else
                                                <p class="text-muted">No questions defined for this section.</p>
                                              @endif
                                            </div>
                                          </div>
                                        </div>
                                      @endforeach
                                    </div>
                                  @else
                                    <p class="text-muted">No sections found for this revision.</p>
                                  @endif
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <h4>No Revisions Found</h4>
                <p class="text-muted">Upload a revision to this standard to get started</p>
                <a href="{{ route('standards.uploadRevision', $standard) }}" class="btn btn-primary">
                  <i class="fas fa-upload"></i> Upload Revision
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
