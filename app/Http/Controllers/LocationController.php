<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;

class LocationController extends BaseController
{
  // app/Http/Controllers/LocationController.php
  public function store(Request $request) {
//    var_dump($request);
    $request->validate([
      'phone_number' => 'required|string',
      'latitude' => 'required|numeric',
      'longitude' => 'required|numeric',
      'work_start' => 'nullable|date_format:H:i',
      'work_end' => 'nullable|date_format:H:i',
    ]);

    Location::create([
      'phone_number' => $request->phone_number,
      'latitude' => $request->latitude,
      'longitude' => $request->longitude,
      'work_start' => $request->work_start,
      'work_end' => $request->work_end,
    ]);

    return response()->json(['message' => 'Location and work hours stored successfully.']);
  }

  public function getTrackData(Request $request)
  {
    // Validate phone parameter
    $validated = $request->validate([
      'phone' => 'required|string',
    ]);

    $phone = $validated['phone'];

    // Retrieve vehicle associated with the phone number
    $vehicle = Location::where('phone_number', $phone)->first();

    if (!$vehicle) {
      return response()->json([
        'error' => 'Vehicle not found for the provided phone number.',
      ], 404);
    }

    // Example Data (replace with real database queries)
    $start = [
      'lat' => $vehicle->latitude,
      'lng' => $vehicle->longitude,
    ];

    $end = [
      'lat' => $vehicle->latitude,
      'lng' => $vehicle->longitude,
    ];

    // Assuming route_points is stored as a JSON column in your database
    $routePoints = json_decode($vehicle->route_points, true);

    return response()->json([
      'start' => $start,
      'end' => $end,
      'route' => $routePoints,
    ]);
  }

  public function getTrack(Request $request)
  {
    $request->validate(['phone_number' => 'required|string']);

    $locations = Location::where('phone_number', $request->phone_number)
      ->whereDate('created_at', Carbon::today())
      ->get(['latitude', 'longitude', 'created_at']);

    return response()->json($locations);
  }

}
