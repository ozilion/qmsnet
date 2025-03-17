<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Planlama\Plan;
use App\Models\Auditor;
use App\Models\Denetciler;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserViewAccount extends Controller
{
  public function index(Request $request)
  {
    return view('content.apps.app-user-view-account');
  }

}
