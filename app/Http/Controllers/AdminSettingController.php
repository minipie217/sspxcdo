<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;

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
        ]);

        foreach ($data['settings'] as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Settings updated.');
    }
}