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
      <div class="col-md-6">
        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary mb-3">
          <i class="fas fa-arrow-left"></i> Back to Reports
        </a>
        <h1>
          @if($reportType == 'audit_summary')
            Audit Summary Report
          @elseif($reportType == 'nonconformity_analysis')
            Nonconformity Analysis
          @elseif($reportType == 'audit_history')
            Audit History
          @else
            Report
          @endif
        </h1>
      </div>
      <div class="col-md-6 text-md-end">
        <button class="btn btn-primary" onclick="window.print()">
          <i class="fas fa-print"></i> Print Report
        </button>
        <a href="{{ route('reports.index') }}" class="btn btn-outline-primary ms-2">
          <i class="fas fa-sync"></i> New Report
        </a>
      </div>
    </div>

    <div class="card mb-4">
      <div class="card-header">
        <h5>Report Parameters</h5>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <strong>Report Type:</strong>
            <p>
              @if($reportType == 'audit_summary')
                Audit Summary Report
              @elseif($reportType == 'nonconformity_analysis')
                Nonconformity Analysis
              @elseif($reportType == 'audit_history')
                Audit History
              @else
                {{ $reportType }}
              @endif
            </p>
          </div>
          <div class="col-md-4">
            <strong>Date Range:</strong>
            <p>
              @if(!empty($filters['dateFrom']) && !empty($filters['dateTo']))
                {{ $filters['dateFrom'] }} to {{ $filters['dateTo'] }}
              @elseif(!empty($filters['dateFrom']))
                From {{ $filters['dateFrom'] }}
              @elseif(!empty($filters['dateTo']))
                Until {{ $filters['dateTo'] }}
              @else
                All dates
              @endif
            </p>
          </div>
          <div class="col-md-4">
            <strong>Standard:</strong>
            <p>
              @if(!empty($filters['standardId']))
                {{ App\Models\Standard::find($filters['standardId'])->code ?? 'Unknown' }}
              @else
                All standards
              @endif
            </p>
          </div>
        </div>
      </div>
    </div>

    @if($reportType == 'audit_summary')
      <!-- Audit Summary Report -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5>Audit Overview</h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-4 mb-3">
                  <div class="display-4 text-primary">{{ $reportData['totalAudits'] }}</div>
                  <div>Total Audits</div>
                </div>
                <div class="col-md-8">
                  <canvas id="auditTypeChart" width="100%" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5>Audit Status</h5>
            </div>
            <div class="card-body">
              <canvas id="auditStatusChart" width="100%" height="200"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h5>Nonconformities by Audit</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Company</th>
                <th>Date</th>
                <th>Major NCs</th>
                <th>Minor NCs</th>
                <th>Total NCs</th>
              </tr>
              </thead>
              <tbody>
              @foreach($reportData['nonconformitiesByAudit'] as $auditId => $auditData)
                <tr>
                  <td>{{ $auditData['companyName'] }}</td>
                  <td>{{ $auditData['date'] }}</td>
                  <td>{{ $auditData['major'] }}</td>
                  <td>{{ $auditData['minor'] }}</td>
                  <td>{{ $auditData['major'] + $auditData['minor'] }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    @elseif($reportType == 'nonconformity_analysis')
      <!-- Nonconformity Analysis Report -->
      <div class="row">
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5>Nonconformity Overview</h5>
            </div>
            <div class="card-body">
              <div class="row text-center">
                <div class="col-md-4 mb-3">
                  <div class="display-4 text-danger">{{ $reportData['totalNonconformities'] }}</div>
                  <div>Total Nonconformities</div>
                </div>
                <div class="col-md-8">
                  <canvas id="ncSeverityChart" width="100%" height="200"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-6 mb-4">
          <div class="card h-100">
            <div class="card-header">
              <h5>Nonconformity Status</h5>
            </div>
            <div class="card-body">
              <canvas id="ncStatusChart" width="100%" height="200"></canvas>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-header">
          <h5>Nonconformities by Standard Section</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Section</th>
                <th>Total NCs</th>
                <th>Major</th>
                <th>Minor</th>
                <th>Percentage</th>
              </tr>
              </thead>
              <tbody>
              @foreach($reportData['nonconformitiesBySection'] as $section => $data)
                <tr>
                  <td>{{ $section }}</td>
                  <td>{{ $data['count'] }}</td>
                  <td>{{ $data['major'] }}</td>
                  <td>{{ $data['minor'] }}</td>
                  <td>
                    @if($reportData['totalNonconformities'] > 0)
                      {{ number_format(($data['count'] / $reportData['totalNonconformities']) * 100, 1) }}%
                    @else
                      0%
                    @endif
                  </td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>

    @elseif($reportType == 'audit_history')
      <!-- Audit History Report -->
      <div class="card mb-4">
        <div class="card-header">
          <h5>Audit History</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
              <tr>
                <th>Company</th>
                <th>Standard</th>
                <th>Date</th>
                <th>Type</th>
                <th>Status</th>
                <th>Questions</th>
                <th>Major NCs</th>
                <th>Minor NCs</th>
              </tr>
              </thead>
              <tbody>
              @foreach($reportData['audits'] as $audit)
                <tr>
                  <td>{{ $audit['company_name'] }}</td>
                  <td>{{ $audit['standard'] }}</td>
                  <td>{{ $audit['audit_date'] }}</td>
                  <td>{{ $audit['audit_type'] }}</td>
                  <td>
                                        <span class="badge bg-{{
                                            $audit['status'] == 'draft' ? 'secondary' :
                                            ($audit['status'] == 'in_progress' ? 'primary' :
                                            ($audit['status'] == 'completed' ? 'success' : 'info'))
                                        }}">
                                            {{ ucfirst($audit['status']) }}
                                        </span>
                  </td>
                  <td>{{ $audit['questions_answered'] }}/{{ $audit['questions_total'] }}</td>
                  <td>{{ $audit['nonconformities']['major'] }}</td>
                  <td>{{ $audit['nonconformities']['minor'] }}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection

@section('scripts')
  @if($reportType == 'audit_summary' || $reportType == 'nonconformity_analysis')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        @if($reportType == 'audit_summary')
        // Audit Type Chart
        const auditTypeCtx = document.getElementById('auditTypeChart').getContext('2d');
        const auditTypeChart = new Chart(auditTypeCtx, {
          type: 'pie',
          data: {
            labels: [
              @foreach($reportData['auditsByType'] as $type => $count)
                '{{ ucfirst($type) }}',
              @endforeach
            ],
            datasets: [{
              data: [
                @foreach($reportData['auditsByType'] as $count)
                  {{ $count }},
                @endforeach
              ],
              backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b'
              ],
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right',
              },
              title: {
                display: true,
                text: 'Audits by Type'
              }
            }
          }
        });

        // Audit Status Chart
        const auditStatusCtx = document.getElementById('auditStatusChart').getContext('2d');
        const auditStatusChart = new Chart(auditStatusCtx, {
          type: 'doughnut',
          data: {
            labels: [
              @foreach($reportData['auditsByStatus'] as $status => $count)
                '{{ ucfirst($status) }}',
              @endforeach
            ],
            datasets: [{
              data: [
                @foreach($reportData['auditsByStatus'] as $count)
                  {{ $count }},
                @endforeach
              ],
              backgroundColor: [
                '#6c757d',  // draft - secondary
                '#007bff',  // in_progress - primary
                '#28a745',  // completed - success
                '#6f42c1',  // approved - purple
              ],
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right',
              },
              title: {
                display: true,
                text: 'Audits by Status'
              }
            }
          }
        });
        @endif

        @if($reportType == 'nonconformity_analysis')
        // NC Severity Chart
        const ncSeverityCtx = document.getElementById('ncSeverityChart').getContext('2d');
        const ncSeverityChart = new Chart(ncSeverityCtx, {
          type: 'pie',
          data: {
            labels: [
              @foreach($reportData['nonconformitiesBySeverity'] as $severity => $count)
                '{{ ucfirst($severity) }}',
              @endforeach
            ],
            datasets: [{
              data: [
                @foreach($reportData['nonconformitiesBySeverity'] as $count)
                  {{ $count }},
                @endforeach
              ],
              backgroundColor: [
                '#e74a3b',  // major - danger
                '#f6c23e',  // minor - warning
              ],
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right',
              },
              title: {
                display: true,
                text: 'Nonconformities by Severity'
              }
            }
          }
        });

        // NC Status Chart
        const ncStatusCtx = document.getElementById('ncStatusChart').getContext('2d');
        const ncStatusChart = new Chart(ncStatusCtx, {
          type: 'doughnut',
          data: {
            labels: [
              @foreach($reportData['nonconformitiesByStatus'] as $status => $count)
                '{{ ucfirst($status) }}',
              @endforeach
            ],
            datasets: [{
              data: [
                @foreach($reportData['nonconformitiesByStatus'] as $count)
                  {{ $count }},
                @endforeach
              ],
              backgroundColor: [
                '#e74a3b',  // open - danger
                '#28a745',  // closed - success
              ],
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: {
                position: 'right',
              },
              title: {
                display: true,
                text: 'Nonconformities by Status'
              }
            }
          }
        });
        @endif
      });
    </script>
  @endif
@endsection
