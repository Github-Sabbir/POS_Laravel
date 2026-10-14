<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        return view('users.profile', ['user' => $request->user(), 'isAdminView' => false]);
    }

    public function adminShow(Request $request, User $user)
    {
        abort_unless($request->user()->role === 'admin', 403);
        return view('users.profile', ['user' => $user, 'isAdminView' => true]);
    }

}
