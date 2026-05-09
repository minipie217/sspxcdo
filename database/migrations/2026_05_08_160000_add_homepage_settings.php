<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('settings')->insertOrIgnore([
            [
                'key' => 'homepage_hero_background',
                'value' => null,
                'label' => 'Hero Background Image',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_badge',
                'value' => 'Raffles, sponsors, tickets, and payments in one flow',
                'label' => 'Hero Badge',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_hero_title',
                'value' => 'Build a raffle page that sells trust first.',
                'label' => 'Hero Title',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_hero_body',
                'value' => 'Give sponsors a clear public experience, guide them into ticket selection, and keep the admin side organized from launch to draw day.',
                'label' => 'Hero Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_primary_cta',
                'value' => 'View active raffles',
                'label' => 'Primary Button Text',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_secondary_cta',
                'value' => 'Become a sponsor',
                'label' => 'Secondary Button Text',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_heading',
                'value' => 'Every part of the homepage has a clear job.',
                'label' => 'Features Heading',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_one_title',
                'value' => 'Public confidence',
                'label' => 'Feature 1 Title',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_one_body',
                'value' => 'Lead with prize clarity, draw timing, ticket counts, and direct routes into active raffles.',
                'label' => 'Feature 1 Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_two_title',
                'value' => 'Sponsor conversion',
                'label' => 'Feature 2 Title',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_two_body',
                'value' => 'Make registration and ticket reservation feel like one connected campaign journey.',
                'label' => 'Feature 2 Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_three_title',
                'value' => 'Admin momentum',
                'label' => 'Feature 3 Title',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_three_body',
                'value' => 'Surface the operational pieces that matter: raffles, payments, availability, and status.',
                'label' => 'Feature 3 Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_feature_intro',
                'value' => 'Built in sections',
                'label' => 'Features Eyebrow',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_workflow_heading',
                'value' => 'From first visit to confirmed ticket.',
                'label' => 'Workflow Heading',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_workflow_body',
                'value' => 'The homepage frames the app like a real product, then moves visitors toward the actions your Laravel routes already support.',
                'label' => 'Workflow Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_sections_heading',
                'value' => 'A Shopify-style rhythm without copying Shopify.',
                'label' => 'Sections Heading',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_sections_body',
                'value' => 'Large bands, focused messages, strong calls to action, and repeated visual blocks give the page a commercial feel while keeping the content specific to raffle management.',
                'label' => 'Sections Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_final_cta_heading',
                'value' => 'Ready to send visitors into the raffle flow?',
                'label' => 'Final CTA Heading',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_final_cta_body',
                'value' => 'Use the homepage as the front door for sponsors while admins keep running raffles from the dashboard.',
                'label' => 'Final CTA Body',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_stat_tickets',
                'value' => '2,500',
                'label' => 'Hero Tickets Stat',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_stat_sold',
                'value' => '1,842',
                'label' => 'Hero Sold Stat',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'homepage_stat_price',
                'value' => 'P100',
                'label' => 'Hero Price Stat',
                'group' => 'homepage',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('group', 'homepage')->delete();
    }
};
