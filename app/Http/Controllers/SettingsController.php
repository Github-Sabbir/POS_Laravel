<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index', ['settings' => Setting::pluck('value', 'key')]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'shop_name' => 'required|string|max:120',
            'currency_symbol' => 'required|string|max:10',
            'receipt_width' => 'required|in:58mm,80mm',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        foreach (['shop_name', 'currency_symbol', 'receipt_width'] as $key) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key]]);
        }

        if ($request->hasFile('logo')) {
            $old = Setting::where('key', 'logo_path')->value('value');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('logo')->store('settings', 'public');
            Setting::updateOrCreate(['key' => 'logo_path'], ['value' => $path]);
        }

        return back()->with('success', 'Settings saved successfully.');
    }

    public function removeLogo()
    {
        $path = Setting::where('key', 'logo_path')->value('value');
        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::where('key', 'logo_path')->delete();
        }
        return back()->with('success', 'Logo removed.');
    }
}
