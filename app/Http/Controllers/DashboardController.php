<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Standard;
use App\Models\Audit;
use App\Models\Nonconformity;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
  /**
   * Display the dashboard.
   *
   * @return \Illuminate\View\View
   */
  public function index()
  {
    // Get counts for dashboard widgets
    $standardsCount = Standard::count();
    $auditsCount = Audit::where('user_id', Auth::id())->count();
    $inProgressCount = Audit::where('user_id', Auth::id())
      ->where('status', 'in_progress')
      ->count();
    $nonconformitiesCount = Nonconformity::whereHas('audit', function($query) {
      $query->where('user_id', Auth::id());
    })
      ->where('status', 'open')
      ->count();

    // Get recent audits
    $recentAudits = Audit::where('user_id', Auth::id())
      ->with('standardRevision.standard')
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get();

    // Get open nonconformities
    $openNonconformities = Nonconformity::whereHas('audit', function($query) {
      $query->where('user_id', Auth::id());
    })
      ->with(['audit', 'standardSection.standardRevision.standard'])
      ->where('status', 'open')
      ->orderBy('created_at', 'desc')
      ->take(5)
      ->get();

    return view('rapor.index', compact(
      'standardsCount',
      'auditsCount',
      'inProgressCount',
      'nonconformitiesCount',
      'recentAudits',
      'openNonconformities'
    ));
  }
}
