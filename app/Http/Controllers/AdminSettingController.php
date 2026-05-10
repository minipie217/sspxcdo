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

        $emailTemplates = \App\Models\EmailTemplate::orderBy('id')->get();
        $homeLayout = app(\App\Services\HomepageLayoutService::class)->read();
        $paymentAccounts = \App\Models\PaymentAccount::orderBy('sort_order')->get();

        return view('admin.settings.index', compact('groups', 'emailTemplates', 'homeLayout', 'paymentAccounts'));
    }

    public function updateEmailTemplate(Request $request, \App\Models\EmailTemplate $template)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        $template->update([
            'subject' => $request->subject,
            'body'    => $request->body,
        ]);

        return back()
            ->with('success', "Email template \"{$template->label}\" saved.")
            ->with('tab', 'emails');
    }

    public function update(Request $request)
    {
        $this->ensureHomepageSettings();

        $request->validate([
            'settings'         => 'required|array',
            'settings.*'       => 'nullable|string|max:2000',
            'site_logo'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'hero_background'  => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'home_layout_json' => 'nullable|string|max:5000',
            'tab'              => 'nullable|string',
            'qr_codes'         => 'nullable|array',
            'qr_codes.*'       => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
        ]);

        // Save all text settings
        foreach ($request->input('settings', []) as $key => $value) {
            Setting::set($key, $value);
        }

        // Validate and save homepage layout JSON
        if ($request->filled('home_layout_json')) {
            $decoded = json_decode($request->input('home_layout_json'), true);
            $allowed = array_keys($this->homepageLayoutService->availableSections());
            $keys    = collect($decoded['sections'] ?? [])->pluck('key')->filter()->all();

            if (! is_array($decoded) || ! isset($decoded['sections']) || array_diff($keys, $allowed)) {
                return back()
                    ->withErrors(['home_layout_json' => 'Home layout JSON must contain valid section keys only.'])
                    ->withInput()
                    ->with('tab', 'homepage');
            }

            $this->homepageLayoutService->write($decoded);
        }

        // Site logo upload — overwrites existing
        if ($request->hasFile('site_logo')) {
            $old = Setting::get('site_logo');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('site_logo')->store('logos', 'public');
            Setting::set('site_logo', $path);
        }

        // Hero background upload — overwrites existing
        if ($request->hasFile('hero_background')) {
            $old = Setting::get('homepage_hero_background');
            if ($old) Storage::disk('public')->delete($old);
            $path = $request->file('hero_background')->store('homepage', 'public');
            Setting::set('homepage_hero_background', $path);
        }

        // QR code uploads — overwrites existing per key
        foreach ($request->file('qr_codes', []) as $key => $file) {
            $old = Setting::get($key);
            if ($old) Storage::disk('public')->delete($old);
            $path = $file->store('qr_codes', 'public');
            Setting::set($key, $path);
        }

        return back()
            ->with('success', 'Settings updated.')
            ->with('tab', $request->tab ?? 'general');
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
        ];
    }

    public function deleteQr(string $key)
    {
        // Validate key is a known QR setting
        $allowed = [
            'bdo_qr_code', 'bpi_qr_code', 'metrobank_qr_code',
            'unionbank_qr_code', 'gcash_qr_code', 'maya_qr_code', 'other_qr_code',
        ];

        if (! in_array($key, $allowed)) {
            abort(404);
        }

        $path = Setting::get($key);

        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::set($key, null);
        }

        return back()
            ->with('success', 'QR code removed.')
            ->with('tab', 'payment');
    }

    public function deleteLogo()
    {
        $path = Setting::get('site_logo');

        if ($path) {
            Storage::disk('public')->delete($path);
            Setting::set('site_logo', null);
        }

        return back()
            ->with('success', 'Logo removed.')
            ->with('tab', 'general');
    }
}
