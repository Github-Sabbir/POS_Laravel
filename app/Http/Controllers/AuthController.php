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
            'identifier' => 'required|string|max:190',
            'password' => 'required|string',
        ]);

        $identifier = trim($data['identifier']);
        $credentials = ['password' => $data['password'], 'active' => true];

        // Login ID is preferred, while email remains supported for existing/staff accounts.
        $user = \App\Models\User::where('login_id', $identifier)->first()
            ?: \App\Models\User::where('email', $identifier)->first();

        if ($user) {
            $loginCredentials = [
                'password' => $data['password'],
                'active' => true,
            ];
            $loginCredentials[$user->login_id ? 'login_id' : 'email'] = $user->login_id ?: $user->email;

            if (Auth::attempt($loginCredentials, $r->boolean('remember'))) {
                $r->session()->regenerate();
                return redirect()->intended(route('dashboard'));
            }
        }

        return back()->withErrors(['identifier' => 'Invalid Login ID/email or inactive account.'])->onlyInput('identifier');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
