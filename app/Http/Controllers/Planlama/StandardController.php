<?php

namespace App\Http\Controllers\Planlama;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DB;
use App\Models\Standard;
use App\Models\StandardRevision;
use App\Services\DocxParserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class StandardController extends Controller
{
  protected $docxParser;

  public function __construct(DocxParserService $docxParser)
  {
    $this->docxParser = $docxParser;
  }

  /**
   * Display a listing of the standards.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $standards = Standard::with('currentRevision')->get();
    return view('standards.index', compact('standards'));
  }

  /**
   * Show the form for creating a new standard.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('standards.create');
  }

  /**
   * Store a newly created standard in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'code' => 'required|unique:standards,code',
      'name' => 'required',
      'version' => 'required',
      'description' => 'nullable',
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    $standard = Standard::create($request->all());

    return redirect()->route('standards.index')
      ->with('success', 'Standard created successfully.');
  }

  /**
   * Display the specified standard.
   *
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function show(Standard $standard)
  {
    $standard->load('revisions');
    return view('standards.show', compact('standard'));
  }

  /**
   * Show the form for editing the specified standard.
   *
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function edit(Standard $standard)
  {
    return view('standards.edit', compact('standard'));
  }

  /**
   * Update the specified standard in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Standard $standard)
  {
    $validator = Validator::make($request->all(), [
      'code' => 'required|unique:standards,code,' . $standard->id,
      'name' => 'required',
      'version' => 'required',
      'description' => 'nullable',
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    $standard->update($request->all());

    return redirect()->route('standards.index')
      ->with('success', 'Standard updated successfully.');
  }

  /**
   * Remove the specified standard from storage.
   *
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function destroy(Standard $standard)
  {
    // Check if there are any audits using this standard's revisions
    $hasAudits = DB::table('audits')
      ->join('standard_revisions', 'audits.standard_revision_id', '=', 'standard_revisions.id')
      ->where('standard_revisions.standard_id', $standard->id)
      ->exists();

    if ($hasAudits) {
      return redirect()->route('standards.index')
        ->with('error', 'This standard cannot be deleted because it is being used in one or more audits.');
    }

    $standard->delete();

    return redirect()->route('standards.index')
      ->with('success', 'Standard deleted successfully.');
  }

  /**
   * Show the form for uploading a new standard revision.
   *
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function uploadRevision(Standard $standard)
  {
    return view('standards.upload_revision', compact('standard'));
  }

  /**
   * Store a new standard revision.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Standard  $standard
   * @return \Illuminate\Http\Response
   */
  public function storeRevision(Request $request, Standard $standard)
  {
    $validator = Validator::make($request->all(), [
      'revision_number' => 'required',
      'revision_date' => 'required|date',
      'revision_notes' => 'nullable',
      'docx_file' => 'required|file|mimes:docx',
      'is_current' => 'boolean',
    ]);

    if ($validator->fails()) {
      return back()->withErrors($validator)->withInput();
    }

    // Store the uploaded file
    $filePath = $request->file('docx_file')->store('standard_revisions');

    // Update existing revisions if this is set as current
    if ($request->has('is_current') && $request->is_current) {
      StandardRevision::where('standard_id', $standard->id)
        ->update(['is_current' => false]);
    }

    // Create new revision
    $revision = new StandardRevision([
      'standard_id' => $standard->id,
      'revision_number' => $request->revision_number,
      'revision_date' => $request->revision_date,
      'revision_notes' => $request->revision_notes,
      'docx_file_path' => $filePath,
      'is_current' => $request->has('is_current') ? true : false,
    ]);

    $revision->save();

    // Parse the DOCX file
    $fullPath = Storage::path($filePath);
    $parseResult = $this->docxParser->parseDocx($fullPath, $revision);

    if (!$parseResult) {
      return back()->with('error', 'Failed to parse the DOCX file.');
    }

    return redirect()->route('standards.show', $standard)
      ->with('success', 'Standard revision uploaded and parsed successfully.');
  }

  /**
   * Set a specific revision as the current one for a standard
   *
   * @param Standard $standard
   * @param StandardRevision $revision
   * @return \Illuminate\Http\RedirectResponse
   */
  public function setCurrentRevision(Standard $standard, StandardRevision $revision)
  {
    // Ensure the revision belongs to this standard
    if ($revision->standard_id !== $standard->id) {
      return back()->with('error', 'This revision does not belong to the specified standard.');
    }

    // Update all revisions to set is_current to false
    StandardRevision::where('standard_id', $standard->id)
      ->update(['is_current' => false]);

    // Set the selected revision as current
    $revision->update(['is_current' => true]);

    return back()->with('success', 'Revision set as current successfully.');
  }
}
