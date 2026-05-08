<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function __construct(private SettingService $settingService) {}

    public function index()
    {
        $groups = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:500',
            'site_logo'         => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'tab'        => 'nullable|string',
        ]);

        // Handle text settings — skip internal keys
        $skip = ['remove_logo'];
        foreach ($request->input('settings', []) as $key => $value) {
            if (in_array($key, $skip)) continue;
            Setting::set($key, $value);
        }

        // Handle logo removal
        if ($request->input('settings.remove_logo') === '1') {
            $old = Setting::get('site_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('site_logo', null);
        }

        // Handle logo upload separately
        if ($request->hasFile('site_logo')) {
            // Delete old logo if exists
            $old = Setting::get('site_logo');
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::set('site_logo', $path);
        }

        return back()->with('success', 'Settings updated.')->with('tab', $request->tab ?? 'general');
    }
}