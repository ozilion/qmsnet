<?php

namespace App\Http\Controllers\Planlama;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Nonconformity;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
  /**
   * Display report options.
   *
   * @return \Illuminate\View\View
   */
  public function index()
  {
    $standards = Standard::where('is_active', true)->get();

    return view('rapor.reports', compact('standards'));
  }

  /**
   * Generate a report based on specified criteria.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\View\View
   */
  public function generate(Request $request)
  {
    // Validate input
    $validated = $request->validate([
      'report_type' => 'required|in:audit_summary,nonconformity_analysis,audit_history',
      'standard_id' => 'nullable|exists:standards,id',
      'date_from' => 'nullable|date',
      'date_to' => 'nullable|date|after_or_equal:date_from',
    ]);

    $reportType = $request->report_type;
    $standardId = $request->standard_id;
    $dateFrom = $request->date_from;
    $dateTo = $request->date_to;

    // Prepare report data based on type
    switch ($reportType) {
      case 'audit_summary':
        $reportData = $this->generateAuditSummary($standardId, $dateFrom, $dateTo);
        break;
      case 'nonconformity_analysis':
        $reportData = $this->generateNonconformityAnalysis($standardId, $dateFrom, $dateTo);
        break;
      case 'audit_history':
        $reportData = $this->generateAuditHistory($standardId, $dateFrom, $dateTo);
        break;
      default:
        return redirect()->route('reports.index')
          ->with('error', 'Invalid report type specified.');
    }

    return view('rapor.reports.report-view', [
      'reportType' => $reportType,
      'reportData' => $reportData,
      'filters' => [
        'standardId' => $standardId,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
      ],
    ]);
  }

  /**
   * Generate audit summary report.
   *
   * @param  int|null  $standardId
   * @param  string|null  $dateFrom
   * @param  string|null  $dateTo
   * @return array
   */
  private function generateAuditSummary($standardId = null, $dateFrom = null, $dateTo = null)
  {
    $query = Audit::where('user_id', Auth::id());

    // Apply filters
    if ($standardId) {
      $query->whereHas('standardRevision', function ($q) use ($standardId) {
        $q->where('standard_id', $standardId);
      });
    }

    if ($dateFrom) {
      $query->where('audit_date', '>=', $dateFrom);
    }

    if ($dateTo) {
      $query->where('audit_date', '<=', $dateTo);
    }

    // Get audits with related data
    $audits = $query->with(['standardRevision.standard', 'responses', 'nonconformities'])->get();

    // Prepare summary data
    $totalAudits = $audits->count();
    $auditsByType = $audits->groupBy('audit_type')->map->count();
    $auditsByStatus = $audits->groupBy('status')->map->count();

    $nonconformitiesByAudit = [];
    foreach ($audits as $audit) {
      $nonconformitiesByAudit[$audit->id] = [
        'companyName' => $audit->company_name,
        'date' => $audit->audit_date->format('Y-m-d'),
        'major' => $audit->nonconformities->where('severity', 'major')->count(),
        'minor' => $audit->nonconformities->where('severity', 'minor')->count(),
      ];
    }

    return [
      'totalAudits' => $totalAudits,
      'auditsByType' => $auditsByType,
      'auditsByStatus' => $auditsByStatus,
      'nonconformitiesByAudit' => $nonconformitiesByAudit,
    ];
  }

  /**
   * Generate nonconformity analysis report.
   *
   * @param  int|null  $standardId
   * @param  string|null  $dateFrom
   * @param  string|null  $dateTo
   * @return array
   */
  private function generateNonconformityAnalysis($standardId = null, $dateFrom = null, $dateTo = null)
  {
    $query = Nonconformity::whereHas('audit', function ($q) {
      $q->where('user_id', Auth::id());
    });

    // Apply filters to audit relationship
    if ($standardId || $dateFrom || $dateTo) {
      $query->whereHas('audit', function ($q) use ($standardId, $dateFrom, $dateTo) {
        if ($standardId) {
          $q->whereHas('standardRevision', function ($sq) use ($standardId) {
            $sq->where('standard_id', $standardId);
          });
        }

        if ($dateFrom) {
          $q->where('audit_date', '>=', $dateFrom);
        }

        if ($dateTo) {
          $q->where('audit_date', '<=', $dateTo);
        }
      });
    }

    // Get nonconformities with related data
    $nonconformities = $query->with(['audit', 'standardSection.standardRevision.standard'])->get();

    // Prepare analysis data
    $totalNonconformities = $nonconformities->count();
    $nonconformitiesBySeverity = $nonconformities->groupBy('severity')->map->count();
    $nonconformitiesByStatus = $nonconformities->groupBy('status')->map->count();

    // Group by standard sections
    $nonconformitiesBySection = [];
    foreach ($nonconformities as $nc) {
      $sectionId = $nc->standardSection->id;
      $sectionName = $nc->standardSection->clause_number . ' ' . $nc->standardSection->clause_title;

      if (!isset($nonconformitiesBySection[$sectionName])) {
        $nonconformitiesBySection[$sectionName] = [
          'count' => 0,
          'major' => 0,
          'minor' => 0,
        ];
      }

      $nonconformitiesBySection[$sectionName]['count']++;
      if ($nc->severity === 'major') {
        $nonconformitiesBySection[$sectionName]['major']++;
      } else {
        $nonconformitiesBySection[$sectionName]['minor']++;
      }
    }

    return [
      'totalNonconformities' => $totalNonconformities,
      'nonconformitiesBySeverity' => $nonconformitiesBySeverity,
      'nonconformitiesByStatus' => $nonconformitiesByStatus,
      'nonconformitiesBySection' => $nonconformitiesBySection,
    ];
  }

  /**
   * Generate audit history report.
   *
   * @param  int|null  $standardId
   * @param  string|null  $dateFrom
   * @param  string|null  $dateTo
   * @return array
   */
  private function generateAuditHistory($standardId = null, $dateFrom = null, $dateTo = null)
  {
    $query = Audit::where('user_id', Auth::id());

    // Apply filters
    if ($standardId) {
      $query->whereHas('standardRevision', function ($q) use ($standardId) {
        $q->where('standard_id', $standardId);
      });
    }

    if ($dateFrom) {
      $query->where('audit_date', '>=', $dateFrom);
    }

    if ($dateTo) {
      $query->where('audit_date', '<=', $dateTo);
    }

    // Get audits with related data
    $audits = $query->with(['standardRevision.standard', 'responses', 'nonconformities'])
      ->orderBy('audit_date', 'desc')
      ->get();

    $auditHistoryData = [];
    foreach ($audits as $audit) {
      $auditHistoryData[] = [
        'id' => $audit->id,
        'company_name' => $audit->company_name,
        'standard' => $audit->standardRevision->standard->code,
        'audit_date' => $audit->audit_date->format('Y-m-d'),
        'audit_type' => $audit->audit_type,
        'status' => $audit->status,
        'questions_total' => $this->calculateTotalQuestions($audit),
        'questions_answered' => $audit->responses->count(),
        'nonconformities' => [
          'major' => $audit->nonconformities->where('severity', 'major')->count(),
          'minor' => $audit->nonconformities->where('severity', 'minor')->count(),
          'open' => $audit->nonconformities->where('status', 'open')->count(),
          'closed' => $audit->nonconformities->where('status', 'closed')->count(),
        ],
      ];
    }

    return [
      'audits' => $auditHistoryData,
    ];
  }

  /**
   * Calculate total questions for an audit.
   *
   * @param  \App\Models\Audit  $audit
   * @return int
   */
  private function calculateTotalQuestions($audit)
  {
    $totalQuestions = 0;

    if ($audit->standardRevision && $audit->standardRevision->standardSections) {
      foreach ($audit->standardRevision->standardSections as $section) {
        if ($section->questions) {
          $totalQuestions += $section->questions->count();
        }
      }
    }

    return $totalQuestions;
  }
}
