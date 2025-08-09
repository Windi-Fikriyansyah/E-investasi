<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $referralCode = $request->query('ref');

        return view('auth.register', [
            'prefilledReferralCode' => $referralCode
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $messages = [
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.max' => 'Nomor telepon maksimal 20 karakter.',
            'phone.unique' => 'Nomor telepon sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'referral_code.exists' => 'Kode referral tidak ditemukan.',
        ];

        $request->validate([
            'phone' => ['required', 'max:20', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'referral_code' => ['nullable', 'string', 'exists:users,referral_code']
        ], $messages);

        $userData = [
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'referral_code' => Str::random(8),
            'role' => 'user', // default role
            'balance' => 0, // default balance
            'status' => 'active',
        ];

        $referralCode = $request->referral_code;
        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
            if ($referrer) {
                $userData['referred_by'] = $referrer->id;
                // Tambahkan bonus atau logika referral di sini jika perlu
                // Contoh: $referrer->increment('balance', 10000);
            }
        }

        $user = User::create($userData);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
