<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Permissions;
use Illuminate\Http\Request;

class AccessPermission extends Controller
{
  public function index()
  {
    $permissions = Permissions::all();
    return view('content.apps.app-access-permission', ['permissions' => $permissions]);
  }
}
