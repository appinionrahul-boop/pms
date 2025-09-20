<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('layouts.navbars.auth.change-password'); // create this view
    }

    public function update(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password'     => 'required|string|min:8|confirmed',
    ]);

    $user = Auth::user();

    // Check current password
    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Your current password is incorrect']);
    }

    // Update password
    $user->password = Hash::make($request->new_password);
    $user->save();

    // ✅ Force logout after password change
    Auth::logout();

    // ✅ Invalidate session
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')->with('success', 'Password updated successfully. Please log in again.');
}
}
