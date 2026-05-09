<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\HomepageLayoutService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingController extends Controller
{
    public function __construct(
        private SettingService $settingService,
        private HomepageLayoutService $homepageLayoutService,
    ) {}

    public function index()
    {
        $this->ensureHomepageSettings();

        $groups = Setting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        $homeLayout = $this->homepageLayoutService->read();

        return view('admin.settings.index', compact('groups', 'homeLayout'));
    }

    public function update(Request $request)
    {
        $this->ensureHomepageSettings();

        $data = $request->validate([
            'settings'   => 'required|array',
            'settings.*' => 'nullable|string|max:2000',
            'site_logo'         => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'hero_background'   => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'home_layout_json'   => 'nullable|string|max:5000',
            'tab'        => 'nullable|string',
        ]);

        if ($request->filled('home_layout_json')) {
            $decoded = json_decode($request->input('home_layout_json'), true);
            $allowed = array_keys($this->homepageLayoutService->availableSections());

            $keys = collect($decoded['sections'] ?? [])->pluck('key')->filter()->all();
            if (! is_array($decoded) || ! isset($decoded['sections']) || array_diff($keys, $allowed)) {
                return back()
                    ->withErrors(['home_layout_json' => 'Home layout JSON must contain valid section keys only.'])
                    ->withInput()
                    ->with('tab', 'homepage');
            }

            $this->homepageLayoutService->write($decoded);
        }

        // Handle text settings — skip internal keys
        $skip = ['remove_logo', 'remove_hero_background'];
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

        if ($request->input('settings.remove_hero_background') === '1') {
            $old = Setting::get('homepage_hero_background');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('homepage_hero_background', null);
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

        if ($request->hasFile('hero_background')) {
            $old = Setting::get('homepage_hero_background');
            if ($old) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('hero_background')->store('homepage', 'public');
            Setting::set('homepage_hero_background', $path);
        }

        return back()->with('success', 'Settings updated.')->with('tab', $request->tab ?? 'general');
    }

    private function ensureHomepageSettings(): void
    {
        $now = now();

        foreach ($this->homepageSettingDefaults() as $setting) {
            Setting::query()->firstOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'label' => $setting['label'],
                    'group' => 'homepage',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    private function homepageSettingDefaults(): array
    {
        return [
            ['key' => 'homepage_hero_background', 'value' => null, 'label' => 'Hero Background Image'],
            ['key' => 'homepage_badge', 'value' => 'Raffles, sponsors, tickets, and payments in one flow', 'label' => 'Hero Badge'],
            ['key' => 'homepage_hero_title', 'value' => 'Build a raffle page that sells trust first.', 'label' => 'Hero Title'],
            ['key' => 'homepage_hero_body', 'value' => 'Give sponsors a clear public experience, guide them into ticket selection, and keep the admin side organized from launch to draw day.', 'label' => 'Hero Body'],
            ['key' => 'homepage_primary_cta', 'value' => 'View active raffles', 'label' => 'Primary Button Text'],
            ['key' => 'homepage_secondary_cta', 'value' => 'Become a sponsor', 'label' => 'Secondary Button Text'],
            ['key' => 'homepage_feature_intro', 'value' => 'Built in sections', 'label' => 'Features Eyebrow'],
            ['key' => 'homepage_feature_heading', 'value' => 'Every part of the homepage has a clear job.', 'label' => 'Features Heading'],
            ['key' => 'homepage_feature_one_title', 'value' => 'Public confidence', 'label' => 'Feature 1 Title'],
            ['key' => 'homepage_feature_one_body', 'value' => 'Lead with prize clarity, draw timing, ticket counts, and direct routes into active raffles.', 'label' => 'Feature 1 Body'],
            ['key' => 'homepage_feature_two_title', 'value' => 'Sponsor conversion', 'label' => 'Feature 2 Title'],
            ['key' => 'homepage_feature_two_body', 'value' => 'Make registration and ticket reservation feel like one connected campaign journey.', 'label' => 'Feature 2 Body'],
            ['key' => 'homepage_feature_three_title', 'value' => 'Admin momentum', 'label' => 'Feature 3 Title'],
            ['key' => 'homepage_feature_three_body', 'value' => 'Surface the operational pieces that matter: raffles, payments, availability, and status.', 'label' => 'Feature 3 Body'],
            ['key' => 'homepage_workflow_heading', 'value' => 'From first visit to confirmed ticket.', 'label' => 'Workflow Heading'],
            ['key' => 'homepage_workflow_body', 'value' => 'The homepage frames the app like a real product, then moves visitors toward the actions your Laravel routes already support.', 'label' => 'Workflow Body'],
            ['key' => 'homepage_sections_heading', 'value' => 'A Shopify-style rhythm without copying Shopify.', 'label' => 'Sections Heading'],
            ['key' => 'homepage_sections_body', 'value' => 'Large bands, focused messages, strong calls to action, and repeated visual blocks give the page a commercial feel while keeping the content specific to raffle management.', 'label' => 'Sections Body'],
            ['key' => 'homepage_final_cta_heading', 'value' => 'Ready to send visitors into the raffle flow?', 'label' => 'Final CTA Heading'],
            ['key' => 'homepage_final_cta_body', 'value' => 'Use the homepage as the front door for sponsors while admins keep running raffles from the dashboard.', 'label' => 'Final CTA Body'],
            ['key' => 'homepage_stat_tickets', 'value' => '2,500', 'label' => 'Hero Tickets Stat'],
            ['key' => 'homepage_stat_sold', 'value' => '1,842', 'label' => 'Hero Sold Stat'],
            ['key' => 'homepage_stat_price', 'value' => 'P100', 'label' => 'Hero Price Stat'],
        ];
    }
}
