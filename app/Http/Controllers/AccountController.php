<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function show(): View
    {
        $user = Auth::user();
        $savedCoupons = $user->savedCoupons()
            ->orderByPivot('created_at', 'desc')
            ->get();

        return view('account.show', compact('user', 'savedCoupons'));
    }

    public function addresses(): View
    {
        $user = Auth::user();
        $addresses = $user->addresses()->get();

        return view('account.addresses', compact('user', 'addresses'));
    }
}
