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
        return [
            'bdo' => [
                'label'          => 'BDO',
                'account_name'   => Setting::get('bdo_account_name'),
                'account_number' => Setting::get('bdo_account_number'),
            ],
            'bpi' => [
                'label'          => 'BPI',
                'account_name'   => Setting::get('bpi_account_name'),
                'account_number' => Setting::get('bpi_account_number'),
            ],
            'metrobank' => [
                'label'          => 'Metrobank',
                'account_name'   => Setting::get('metrobank_account_name'),
                'account_number' => Setting::get('metrobank_account_number'),
            ],
            'unionbank' => [
                'label'          => 'UnionBank',
                'account_name'   => Setting::get('unionbank_account_name'),
                'account_number' => Setting::get('unionbank_account_number'),
            ],
            'gcash' => [
                'label'  => 'GCash',
                'name'   => Setting::get('gcash_name'),
                'number' => Setting::get('gcash_number'),
            ],
            'maya' => [
                'label'  => 'Maya',
                'name'   => Setting::get('maya_name'),
                'number' => Setting::get('maya_number'),
            ],
            'other' => [
                'label'   => Setting::get('other_payment_label'),
                'details' => Setting::get('other_payment_details'),
            ],
        ];
    }
}