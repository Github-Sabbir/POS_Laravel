<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $r)
    {
        $data = $r->validate([
            'login_id' => 'required|string|max:120',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['login_id' => $data['login_id'], 'password' => $data['password'], 'active' => true], $r->boolean('remember'))) {
            $r->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['login_id' => 'Invalid Login ID or password, or the account is inactive.'])->onlyInput('login_id');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
