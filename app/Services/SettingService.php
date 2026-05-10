<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Collection;

class SettingService
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }

    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
    }

    public function getGroup(string $group): Collection
    {
        return Setting::where('group', $group)->get();
    }

    public function updateGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            Setting::where('key', $key)
                ->where('group', $group)
                ->update(['value' => $value]);
        }
    }

    public function reservationMinutes(): int
    {
        return (int) Setting::get('reservation_minutes', 30);
    }

    public function paymentInstructions(): array
    {
        return \App\Models\PaymentAccount::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($account) => [
                'type'           => $account->type,
                'label'          => $account->label,
                'account_name'   => $account->account_name,
                'account_number' => $account->account_number,
                'qr_code'        => $account->qr_code,
                'icon'           => $account->typeIcon(),
            ])
            ->toArray();
    }

    public function maxTicketsPerSponsor(): int
    {
        return (int) Setting::get('max_tickets_per_sponsor', 5);
    }
}