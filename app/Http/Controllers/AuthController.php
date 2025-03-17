<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    $request->validate([
      'name' => 'required|string|max:255',
      'email' => 'required|string|email|max:255|unique:users',
      'password' => 'required|string|min:6|confirmed',
    ]);

    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    $token = $user->createToken('authToken')->accessToken;

    return response()->json(['token' => $token, 'user' => $user], 201);
  }

  public function login(Request $request)
  {
    $loginData = $request->validate([
      'email' => 'required|string|email',
      'password' => 'required|string',
    ]);

    if (!Auth::attempt($loginData)) {
      return response()->json(['message' => 'Invalid Credentials'], 401);
    }

    $user = Auth::user();
    if (!$user->isDenetciActive()) {
      auth()->logout();
      return response()->json(['message' => 'Hesabınız aktif değil. Lütfen yetkili ile iletişime geçin.'], 400);
    }
    $token = $user->createToken('authToken')->accessToken;

    return response()->json(['token' => $token, 'user' => $user], 200);
  }
}
