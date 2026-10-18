<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class FactoryResetController extends Controller
{
    public function reset(Request $request)
    {
        if ($request->user()?->role !== 'admin') {
            abort(403);
        }

        Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);

        $publicStorage = storage_path('app/public');
        if (File::isDirectory($publicStorage)) {
            foreach (File::directories($publicStorage) as $directory) {
                File::deleteDirectory($directory);
            }
        }

        Artisan::call('optimize:clear');
        Artisan::call('storage:link');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Factory reset completed. Default Admin and Cashier accounts are ready.');
    }
}
