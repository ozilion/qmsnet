<?php

use App\Http\Controllers\api\ChecklistController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LocationController;
use App\Models\User;
use App\Notifications\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/api/login', function (Request $request) {
  $user = User::where('email', $request->email)->first();

  if (! $user || ! Hash::check($request->password, $user->password)) {
    return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
  }

  $token = $user->createToken('token-name')->plainTextToken;

  return response()->json(['token' => $token], 200);
});

// API Routes
// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
  // Auth
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::get('/profile', [AuthController::class, 'profile']);

  // Standards
  Route::get('/standards', [ChecklistController::class, 'getStandards']);
  Route::get('/standards/revisions/{id}', [ChecklistController::class, 'getStandardRevision']);

  // Audits
  Route::get('/audits', [ChecklistController::class, 'getAudits']);
  Route::get('/audits/{id}', [ChecklistController::class, 'getAudit']);
  Route::post('/audits', [ChecklistController::class, 'createAudit']);
  Route::put('/audits/{id}', [ChecklistController::class, 'updateAudit']);

  // Sync
  Route::post('/audits/{id}/sync-responses', [ChecklistController::class, 'syncResponses']);
  Route::post('/audits/{id}/sync-nonconformities', [ChecklistController::class, 'syncNonconformities']);
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/send-notification', function (Request $request) {
  $user = User::find($request->user_id);
  $user->notify(new PushNotification($request->message));

  return response()->json(['status' => 'Notification sent!']);
});

Route::post('/location', [LocationController::class, 'store']);
Route::get('/location', [LocationController::class, 'store']);
Route::post('/track', [LocationController::class, 'getTrack']);
Route::get('/track', [LocationController::class, 'getTrack']);
