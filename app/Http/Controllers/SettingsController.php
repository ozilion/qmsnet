<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserSetting;

class SettingsController extends Controller
{
  /**
   * Display user settings.
   *
   * @return \Illuminate\View\View
   */
  public function index()
  {
    $user = Auth::user();
    $settings = UserSetting::where('user_id', $user->id)->first();

    return view('settings.index', compact('user', 'settings'));
  }

  /**
   * Update user settings.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request)
  {
    $validated = $request->validate([
      'auto_sync' => 'nullable|boolean',
      'sync_frequency' => 'nullable|integer|min:5|max:60',
      'dark_mode' => 'nullable|boolean',
      'notification_email' => 'nullable|boolean',
      'notification_app' => 'nullable|boolean',
    ]);

    $user = Auth::user();

    // Find or create settings
    $settings = UserSetting::firstOrNew(['user_id' => $user->id]);

    // Update settings
    $settings->auto_sync = $request->has('auto_sync');
    $settings->sync_frequency = $request->input('sync_frequency', 15);
    $settings->dark_mode = $request->has('dark_mode');
    $settings->notification_email = $request->has('notification_email');
    $settings->notification_app = $request->has('notification_app');

    $settings->save();

    return redirect()->route('rapor.settings')
      ->with('success', 'Settings updated successfully.');
  }
}
