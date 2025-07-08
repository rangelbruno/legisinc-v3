<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Show user profile
     */
    public function profile()
    {
        return view('user.profile');
    }

    /**
     * Update user's last access
     */
    public function updateLastAccess(Request $request)
    {
        if (auth()->check()) {
            auth()->user()->atualizarUltimoAcesso();
        }
        
        return response()->json(['success' => true]);
    }
}