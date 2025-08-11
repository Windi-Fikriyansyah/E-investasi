<?php
// app/Helpers/RouteHelper.php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RouteHelper
{
    public static function getPageTitle()
    {
        $routeName = Route::currentRouteName();

        $titles = [
            'dashboard' => 'Dashboard',
            'user.dashboard' => 'Beranda',
            'admin.dashboard' => 'Admin Dashboard',
            'produk.index' => 'Investasi',
            'pesanan' => 'Pesanan',
            'bonus.index' => 'Tim Saya',
            'forum.index' => 'Forum',
            'profile.index' => 'Profil',
            'profile.edit' => 'Edit Profil',
            'profile.ubahpassword' => 'Ubah Password',
            'deposit.index' => 'Deposit',
            'deposit.riwayat' => 'Riwayat Deposit',
            'withdrawal.index' => 'Penarikan',
            'withdrawal.history' => 'Riwayat Penarikan',
            'bank.index' => 'Rekening Bank',
            'referral.index' => 'Referral',
            'tentang.index' => 'Tentang Kami',
            'vip.rules' => 'Aturan VIP',
            // Tambahkan route dan judul lainnya sesuai kebutuhan
        ];

        return $titles[$routeName] ?? 'WealthGrowth';
    }
}
