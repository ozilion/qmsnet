<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Standard;
use App\Models\StandardRevision;
use App\Models\Audit;
use App\Models\Response;
use App\Models\Nonconformity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
  /**
   * Get all standards with their current revisions.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStandards()
  {
    $standards = Standard::where('is_active', true)
      ->with('currentRevision')
      ->get();

    return response()->json([
      'success' => true,
      'data' => $standards
    ]);
  }

  /**
   * Get a specific standard revision with all its sections and questions.
   *
   * @param int $revisionId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getStandardRevision($revisionId)
  {
    $revision = StandardRevision::with([
      'standard',
      'commonSections',
      'standardSections.questions'
    ])->findOrFail($revisionId);

    return response()->json([
      'success' => true,
      'data' => $revision
    ]);
  }

  /**
   * Get audits for the authenticated user.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAudits()
  {
    $audits = Audit::where('user_id', Auth::id())
      ->with('standardRevision.standard')
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json([
      'success' => true,
      'data' => $audits
    ]);
  }

  /**
   * Get a specific audit with all its data.
   *
   * @param int $auditId
   * @return \Illuminate\Http\JsonResponse
   */
  public function getAudit($auditId)
  {
    $audit = Audit::where('id', $auditId)
      ->where('user_id', Auth::id())
      ->with([
        'standardRevision.standard',
        'standardRevision.commonSections',
        'standardRevision.standardSections.questions',
        'responses',
        'nonconformities'
      ])
      ->firstOrFail();

    return response()->json([
      'success' => true,
      'data' => $audit
    ]);
  }

  /**
   * Create a new audit.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function createAudit(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'standard_revision_id' => 'required|exists:standard_revisions,id',
      'plan_no' => 'nullable|exists:planlar,planno',
      'audit_type' => 'required|in:Initial,Surveillance,Recertification,Special',
      'company_name' => 'required',
      'audit_date' => 'required|date',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

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

    return response()->json([
      'success' => true,
      'message' => 'Audit created successfully',
      'data' => $audit
    ]);
  }

  /**
   * Update an existing audit.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $auditId
   * @return \Illuminate\Http\JsonResponse
   */
  public function updateAudit(Request $request, $auditId)
  {
    $audit = Audit::where('id', $auditId)
      ->where('user_id', Auth::id())
      ->firstOrFail();

    $validator = Validator::make($request->all(), [
      'company_name' => 'required',
      'audit_date' => 'required|date',
      'status' => 'required|in:draft,in_progress,completed,approved',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    $audit->update([
      'company_name' => $request->company_name,
      'audit_date' => $request->audit_date,
      'status' => $request->status,
    ]);

    return response()->json([
      'success' => true,
      'message' => 'Audit updated successfully',
      'data' => $audit
    ]);
  }

  /**
   * Sync responses for an audit.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $auditId
   * @return \Illuminate\Http\JsonResponse
   */
  public function syncResponses(Request $request, $auditId)
  {
    $audit = Audit::where('id', $auditId)
      ->where('user_id', Auth::id())
      ->firstOrFail();

    $validator = Validator::make($request->all(), [
      'responses' => 'required|array',
      'responses.*.uuid' => 'required|string',
      'responses.*.question_id' => 'required|exists:questions,id',
      'responses.*.response_text' => 'nullable|string',
      'responses.*.is_compliant' => 'nullable|boolean',
      'responses.*.evidence' => 'nullable|string',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    $syncedResponses = [];

    foreach ($request->responses as $responseData) {
      $response = Response::updateOrCreate(
        [
          'uuid' => $responseData['uuid'],
          'audit_id' => $audit->id,
        ],
        [
          'question_id' => $responseData['question_id'],
          'response_text' => $responseData['response_text'] ?? null,
          'is_compliant' => $responseData['is_compliant'] ?? null,
          'evidence' => $responseData['evidence'] ?? null,
          'is_synced' => true,
          'sync_timestamp' => now(),
        ]
      );

      $syncedResponses[] = $response;
    }

    return response()->json([
      'success' => true,
      'message' => 'Responses synced successfully',
      'data' => $syncedResponses
    ]);
  }

  /**
   * Sync nonconformities for an audit.
   *
   * @param \Illuminate\Http\Request $request
   * @param int $auditId
   * @return \Illuminate\Http\JsonResponse
   */
  public function syncNonconformities(Request $request, $auditId)
  {
    $audit = Audit::where('id', $auditId)
      ->where('user_id', Auth::id())
      ->firstOrFail();

    $validator = Validator::make($request->all(), [
      'nonconformities' => 'required|array',
      'nonconformities.*.uuid' => 'required|string',
      'nonconformities.*.standard_section_id' => 'required|exists:standard_sections,id',
      'nonconformities.*.description' => 'required|string',
      'nonconformities.*.severity' => 'required|in:major,minor',
      'nonconformities.*.correction' => 'nullable|string',
      'nonconformities.*.corrective_action' => 'nullable|string',
      'nonconformities.*.due_date' => 'nullable|date',
      'nonconformities.*.status' => 'required|in:open,closed',
    ]);

    if ($validator->fails()) {
      return response()->json([
        'success' => false,
        'errors' => $validator->errors()
      ], 422);
    }

    $syncedNonconformities = [];

    foreach ($request->nonconformities as $ncData) {
      $nonconformity = Nonconformity::updateOrCreate(
        [
          'uuid' => $ncData['uuid'],
          'audit_id' => $audit->id,
        ],
        [
          'standard_section_id' => $ncData['standard_section_id'],
          'description' => $ncData['description'],
          'severity' => $ncData['severity'],
          'correction' => $ncData['correction'] ?? null,
          'corrective_action' => $ncData['corrective_action'] ?? null,
          'due_date' => $ncData['due_date'] ?? null,
          'status' => $ncData['status'],
          'is_synced' => true,
          'sync_timestamp' => now(),
        ]
      );

      $syncedNonconformities[] = $nonconformity;
    }

    return response()->json([
      'success' => true,
      'message' => 'Nonconformities synced successfully',
      'data' => $syncedNonconformities
    ]);
  }
}
