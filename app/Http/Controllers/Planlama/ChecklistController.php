<?php

namespace App\Http\Controllers\Planlama;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use App\Models\Nonconformity;
use App\Models\Response;
use App\Models\Standard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChecklistController extends Controller
{
  /**
   * Display a listing of audits.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $audits = Audit::where('user_id', Auth::id())
      ->with('standardRevision.standard')
      ->orderBy('created_at', 'desc')
      ->get();

    return view('checklists.index', compact('audits'));
  }

  /**
   * Show the form for creating a new audit checklist.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $standards = Standard::where('is_active', true)
      ->with(['currentRevision'])
      ->get();

    // Get plan info if integrated with existing system
    $plans = \App\Models\Planlar::where('kid', Auth::user()->kurulusid)
      ->orderBy('planno', 'desc')
      ->get();

    return view('checklists.create', compact('standards', 'plans'));
  }

  /**
   * Store a newly created audit in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'standard_revision_id' => 'required|exists:standard_revisions,id',
      'plan_no' => 'nullable|exists:planlar,planno',
      'audit_type' => 'required|in:Initial,Surveillance,Recertification,Special',
      'company_name' => 'required',
      'audit_date' => 'required|date',
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    // Create a new audit
    $audit = Audit::create([
      'uuid' => (string) Str::uuid(),
      'user_id' => Auth::id(),
      'standard_revision_id' => $request->standard_revision_id,
      'plan_no' => $request->plan_no,
      'audit_type' => $request->audit_type,
      'company_name' => $request->company_name,
      'audit_date' => $request->audit_date,
      'status' => 'draft',
    ]);

    return redirect()->route('checklists.edit', $audit)
      ->with('success', 'Audit checklist created successfully. You can now fill in the details.');
  }

  /**
   * Display the specified audit with its checklist.
   *
   * @param  \App\Models\Audit  $audit
   * @return \Illuminate\Http\Response
   */
  public function show(Audit $audit)
  {
    // Ensure the user owns this audit
//    if ($audit->user_id !== Auth::id() && !Auth::user()->hasRole('superadmin')) {
//      return abort(403, 'Unauthorized action.');
//    }

    $audit->load([
      'standardRevision.standard',
      'standardRevision.standardSections.questions',
      'responses',
      'nonconformities'
    ]);

    return view('checklists.show', compact('audit'));
  }

  /**
   * Show the form for editing the specified audit checklist.
   *
   * @param  \App\Models\Audit  $audit
   * @return \Illuminate\Http\Response
   */
  public function edit(Audit $audit)
  {
    // Ensure the user owns this audit
//    if ($audit->user_id !== Auth::id() && !Auth::user()->hasRole('superadmin')) {
//      return abort(403, 'Unauthorized action.');
//    }

    $audit->load([
      'standardRevision.standard',
      'standardRevision.commonSections',
      'standardRevision.standardSections.questions',
      'responses',
      'nonconformities'
    ]);

    // Group responses by question ID for easy access in the form
    $responses = $audit->responses->keyBy('question_id');

    return view('checklists.edit', compact('audit', 'responses'));
  }

  /**
   * Update the specified audit checklist in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Audit  $audit
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Audit $audit)
  {
    // Ensure the user owns this audit
    if ($audit->user_id !== Auth::id() && !Auth::user()->hasRole('superadmin')) {
      return abort(403, 'Unauthorized action.');
    }

    // Validate basic audit info
    $validator = Validator::make($request->all(), [
      'company_name' => 'required',
      'audit_date' => 'required|date',
      'status' => 'required|in:draft,in_progress,completed,approved',
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    // Update audit info
    $audit->update([
      'company_name' => $request->company_name,
      'audit_date' => $request->audit_date,
      'status' => $request->status,
    ]);

    // Process responses for each question
    if ($request->has('responses')) {
      foreach ($request->responses as $questionId => $responseData) {
        $this->updateOrCreateResponse($audit, $questionId, $responseData);
      }
    }

    // Process nonconformities
    if ($request->has('nonconformities')) {
      foreach ($request->nonconformities as $sectionId => $ncData) {
        $this->updateOrCreateNonconformity($audit, $sectionId, $ncData);
      }
    }

    return back()->with('success', 'Audit checklist updated successfully.');
  }

  /**
   * Update or create a response for a question
   *
   * @param  \App\Models\Audit  $audit
   * @param  int  $questionId
   * @param  array  $data
   * @return void
   */
  protected function updateOrCreateResponse(Audit $audit, $questionId, $data)
  {
    // Find existing response or create a new one
    $response = Response::updateOrCreate(
      [
        'audit_id' => $audit->id,
        'question_id' => $questionId,
      ],
      [
        'uuid' => (string) Str::uuid(),
        'response_text' => $data['text'] ?? null,
        'is_compliant' => isset($data['is_compliant']) ? ($data['is_compliant'] === 'yes') : null,
        'evidence' => $data['evidence'] ?? null,
        'is_synced' => true,
        'sync_timestamp' => now(),
      ]
    );
  }

  /**
   * Update or create a nonconformity for a section
   *
   * @param  \App\Models\Audit  $audit
   * @param  int  $sectionId
   * @param  array  $data
   * @return void
   */
  protected function updateOrCreateNonconformity(Audit $audit, $sectionId, $data)
  {
    // If description is empty, no nonconformity to record
    if (empty($data['description'])) {
      return;
    }

    // Find existing nonconformity or create a new one
    $nonconformity = Nonconformity::updateOrCreate(
      [
        'audit_id' => $audit->id,
        'standard_section_id' => $sectionId,
      ],
      [
        'uuid' => (string) Str::uuid(),
        'description' => $data['description'],
        'severity' => $data['severity'] ?? 'minor',
        'correction' => $data['correction'] ?? null,
        'corrective_action' => $data['corrective_action'] ?? null,
        'due_date' => !empty($data['due_date']) ? $data['due_date'] : null,
        'status' => $data['status'] ?? 'open',
        'is_synced' => true,
        'sync_timestamp' => now(),
      ]
    );
  }

  /**
   * Export the completed checklist to DOCX.
   *
   * @param  \App\Models\Audit  $audit
   * @param  string|null  $asama
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function exportDocx(Audit $audit, $asama = null)
  {
    // Ensure the user has access to this audit
    if ($audit->user_id !== Auth::id() && !Auth::user()->hasRole('superadmin')) {
      return abort(403, 'Unauthorized action.');
    }

    // Load all related data
    $audit->load([
      'standardRevision.standard',
      'standardRevision.commonSections',
      'standardRevision.standardSections.questions',
      'responses',
      'nonconformities',
      'user'
    ]);

    // Determine audit stage from request if not provided
    if ($asama === null && request()->has('asama')) {
      $asama = request()->get('asama');
    }

    try {
      // Generate the DOCX file
      $exporter = new \App\Services\ChecklistExportService();
      $filePath = $exporter->exportToDocx($audit, $asama);

      // Download the file
      return response()->download($filePath, 'AFR.09 Denetim Raporu R0.docx')
        ->deleteFileAfterSend(true);
    } catch (\Exception $e) {
      // Log the error
      Log::error('Error exporting audit checklist: ' . $e->getMessage());

      // Return back with error message
      return back()->with('error', 'Failed to export the audit checklist: ' . $e->getMessage());
    }
  }

  /**
   * Remove the specified audit from storage.
   *
   * @param  \App\Models\Audit  $audit
   * @return \Illuminate\Http\Response
   */
  public function destroy(Audit $audit)
  {
    // Ensure the user owns this audit
    if ($audit->user_id !== Auth::id() && !Auth::user()->hasRole('superadmin')) {
      return abort(403, 'Unauthorized action.');
    }

    $audit->delete();

    return redirect()->route('checklists.index')
      ->with('success', 'Audit checklist deleted successfully.');
  }
}
