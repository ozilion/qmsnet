<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Roles;
use Illuminate\Support\Facades\DB;

class AccessRoles extends Controller
{

  public function index()
  {

//    $roles = DB::table("roles")->get();
    $roles = Roles::all();
    return view('content.apps.app-access-roles', ['roles' => $roles]);
  }
}
