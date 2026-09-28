<?php namespace App\Http\Controllers;
use Illuminate\Http\Request; use Illuminate\Support\Facades\Auth; use Illuminate\Support\Facades\RateLimiter;
class AuthController extends Controller {
 public function show(){return view('auth.login');}
 public function login(Request $r){$data=$r->validate(['email'=>'required|email','password'=>'required|string']); if(Auth::attempt(['email'=>$data['email'],'password'=>$data['password'],'active'=>true],$r->boolean('remember'))){$r->session()->regenerate();return redirect()->intended(route('dashboard'));} return back()->withErrors(['email'=>'Invalid credentials or inactive account.'])->onlyInput('email');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect()->route('login');}
}
