<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserApiController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'User API']);
    }

    public function login(Request $request)
    {
        return response()->json(['message' => 'Login endpoint']);
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logout endpoint']);
    }

    public function register(Request $request)
    {
        return response()->json(['message' => 'Register endpoint']);
    }

    public function getUsers()
    {
        return response()->json(['users' => []]);
    }

    public function getUser($id)
    {
        return response()->json(['user' => ['id' => $id]]);
    }

    public function createUser(Request $request)
    {
        return response()->json(['message' => 'User created']);
    }

    public function updateUser(Request $request, $id)
    {
        return response()->json(['message' => 'User updated']);
    }

    public function deleteUser($id)
    {
        return response()->json(['message' => 'User deleted']);
    }

    public function authStatus()
    {
        return response()->json(['authenticated' => auth()->check()]);
    }

    public function healthCheck()
    {
        return response()->json(['status' => 'ok']);
    }

    public function autoLogin(Request $request)
    {
        return response()->json(['message' => 'Auto login']);
    }
}
