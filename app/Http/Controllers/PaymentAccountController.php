<?php

namespace App\Http\Controllers;

use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentAccountController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'type'           => 'required|in:bdo,bpi,metrobank,unionbank,gcash,maya,other',
            'label'          => 'required|string|max:255',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'qr_code'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'is_active'      => 'boolean',
        ]);

        $data = $request->only(['type', 'label', 'account_name', 'account_number', 'is_active']);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['sort_order'] = PaymentAccount::max('sort_order') + 1;

        if ($request->hasFile('qr_code')) {
            $data['qr_code'] = $request->file('qr_code')->store('qr_codes', 'public');
        }

        PaymentAccount::create($data);

        return back()
            ->with('success', 'Payment account added.')
            ->with('tab', 'payment');
    }

    public function update(Request $request, PaymentAccount $paymentAccount)
    {
        $request->validate([
            'type'           => 'required|in:bdo,bpi,metrobank,unionbank,gcash,maya,other',
            'label'          => 'required|string|max:255',
            'account_name'   => 'nullable|string|max:255',
            'account_number' => 'nullable|string|max:255',
            'qr_code'        => 'nullable|file|mimes:jpg,jpeg,png,svg|max:2048',
            'is_active'      => 'boolean',
        ]);

        $data = $request->only(['type', 'label', 'account_name', 'account_number']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('qr_code')) {
            if ($paymentAccount->qr_code) {
                Storage::disk('public')->delete($paymentAccount->qr_code);
            }
            $data['qr_code'] = $request->file('qr_code')->store('qr_codes', 'public');
        }

        $paymentAccount->update($data);

        return back()
            ->with('success', 'Payment account updated.')
            ->with('tab', 'payment');
    }

    public function destroy(PaymentAccount $paymentAccount)
    {
        if ($paymentAccount->qr_code) {
            Storage::disk('public')->delete($paymentAccount->qr_code);
        }

        $paymentAccount->delete();

        return back()
            ->with('success', 'Payment account removed.')
            ->with('tab', 'payment');
    }

    public function deleteQr(PaymentAccount $paymentAccount)
    {
        if ($paymentAccount->qr_code) {
            Storage::disk('public')->delete($paymentAccount->qr_code);
            $paymentAccount->update(['qr_code' => null]);
        }

        return back()
            ->with('success', 'QR code removed.')
            ->with('tab', 'payment');
    }
}