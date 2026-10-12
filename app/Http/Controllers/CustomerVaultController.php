<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\CustomerPaymentMethod;
use Illuminate\Http\Request;

class CustomerVaultController extends Controller
{
    public function index()
    {
        CustomerAddress::checkAndCreateTable();
        CustomerPaymentMethod::checkAndCreateTable();

        $userId = auth()->id();
        $addresses = CustomerAddress::where('user_id', $userId)->orderByDesc('is_default')->get();
        $paymentMethods = CustomerPaymentMethod::where('user_id', $userId)->orderByDesc('is_default')->get();

        return view('vault.index', compact('addresses', 'paymentMethods'));
    }

    // Address Store
    public function storeAddress(Request $request)
    {
        CustomerAddress::checkAndCreateTable();

        $validated = $request->validate([
            'title' => 'required|string|max:50',
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line1' => 'required|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'required|string|max:20',
            'country' => 'nullable|string|max:100',
            'is_default' => 'nullable|boolean',
        ]);

        $userId = auth()->id();
        $isDefault = $request->boolean('is_default');

        if ($isDefault || CustomerAddress::where('user_id', $userId)->count() === 0) {
            CustomerAddress::where('user_id', $userId)->update(['is_default' => false]);
            $isDefault = true;
        }

        CustomerAddress::create([
            'user_id' => $userId,
            'title' => $validated['title'],
            'recipient_name' => $validated['recipient_name'],
            'phone' => $validated['phone'],
            'address_line1' => $validated['address_line1'],
            'address_line2' => $validated['address_line2'] ?? null,
            'city' => $validated['city'],
            'state' => $validated['state'],
            'zip_code' => $validated['zip_code'],
            'country' => $validated['country'] ?? 'India',
            'is_default' => $isDefault,
        ]);

        return back()->with('success', 'New delivery address added successfully.');
    }

    public function setDefaultAddress(CustomerAddress $address)
    {
        if ($address->user_id != auth()->id()) abort(403);

        CustomerAddress::where('user_id', auth()->id())->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default delivery address updated.');
    }

    public function destroyAddress(CustomerAddress $address)
    {
        if ($address->user_id != auth()->id()) abort(403);
        $address->delete();

        return back()->with('success', 'Address removed from your address book.');
    }

    // Payment Method Store (Masked simulation)
    public function storePaymentMethod(Request $request)
    {
        CustomerPaymentMethod::checkAndCreateTable();

        $request->validate([
            'method_type' => 'required|in:card,upi',
            'card_number' => 'nullable|required_if:method_type,card|string',
            'holder_name' => 'nullable|required_if:method_type,card|string',
            'expiry' => 'nullable|required_if:method_type,card|string',
            'upi_id' => 'nullable|required_if:method_type,upi|string',
        ]);

        $userId = auth()->id();
        $isDefault = CustomerPaymentMethod::where('user_id', $userId)->count() === 0;

        if ($request->method_type === 'card') {
            $cleanedCard = str_replace(' ', '', $request->card_number);
            $lastFour = substr($cleanedCard, -4);
            $brand = 'Visa';
            if (str_starts_with($cleanedCard, '5')) $brand = 'Mastercard';
            elseif (str_starts_with($cleanedCard, '6') || str_starts_with($cleanedCard, '8')) $brand = 'RuPay';
            elseif (str_starts_with($cleanedCard, '3')) $brand = 'Amex';

            CustomerPaymentMethod::create([
                'user_id' => $userId,
                'method_type' => 'card',
                'card_brand' => $brand,
                'last_four' => $lastFour ?: '1234',
                'holder_name' => $request->holder_name,
                'expiry_month' => substr($request->expiry, 0, 2) ?: '12',
                'expiry_year' => substr($request->expiry, 3, 2) ?: '28',
                'is_default' => $isDefault,
            ]);
        } else {
            CustomerPaymentMethod::create([
                'user_id' => $userId,
                'method_type' => 'upi',
                'upi_id' => $request->upi_id,
                'is_default' => $isDefault,
            ]);
        }

        return back()->with('success', 'Payment shortcut safely saved to your vault.');
    }

    public function setDefaultPaymentMethod(CustomerPaymentMethod $method)
    {
        if ($method->user_id != auth()->id()) abort(403);

        CustomerPaymentMethod::where('user_id', auth()->id())->update(['is_default' => false]);
        $method->update(['is_default' => true]);

        return back()->with('success', 'Default payment method updated.');
    }

    public function destroyPaymentMethod(CustomerPaymentMethod $method)
    {
        if ($method->user_id != auth()->id()) abort(403);
        $method->delete();

        return back()->with('success', 'Payment method removed from your vault.');
    }
}
