<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;


class DashboardController extends Controller
{
    public function index()
    {
        $categories = DB::table('kategori')->get();
        $products = DB::table('produk')
            ->select('produk.*', 'produk.kategori as category_name')
            ->get();
        $userVip = Auth::check() ? (Auth::user()->keanggotaan ?? 'VIP 0') : 'VIP 0';
        $userVipLevel = (int) str_replace('VIP ', '', $userVip);

        return view('dashboard', compact('categories', 'products', 'userVipLevel'));
    }

    public function rules()
    {
        return view('vip.index');
    }

    public function invest(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login terlebih dahulu');
        }

        $productId = $request->product_id;
        $userId = Auth::id();

        // Dapatkan data produk
        $product = DB::table('produk')->where('id', $productId)->first();

        if (!$product) {
            return back()->with('error', 'Produk tidak ditemukan');
        }

        // Dapatkan user
        $user = User::find($userId);
        $isFirstInvestment = DB::table('transaksi')
            ->where('user_id', $userId)
            ->count() === 0;

        // Cek VIP level
        $productVip = $product->vip ?? 'VIP 0';
        $productVipLevel = (int) str_replace('VIP ', '', $productVip);
        $userVipLevel = (int) str_replace('VIP ', '', $user->keanggotaan ?? 'VIP 0');



        // Cek balance
        if ($user->balance < $product->harga) {
            return back()->with('error', 'Saldo tidak mencukupi');
        }

        // Kurangi balance
        $user->balance -= $product->harga;

        // Update VIP berdasarkan total investasi kumulatif
        $totalInvestasi = DB::table('transaksi')
            ->where('user_id', $userId)
            ->sum('amount') + $product->harga; // Tambahkan investasi saat ini

        $newVipLevel = $this->calculateVipLevel($totalInvestasi);
        $currentVipLevel = (int) str_replace('VIP ', '', $user->keanggotaan ?? 'VIP 0');

        // Update VIP jika level baru lebih tinggi
        if ($newVipLevel > $currentVipLevel) {
            $user->keanggotaan = 'VIP ' . $newVipLevel;
        }
        // Atau jika produk VIP lebih tinggi dari user VIP saat ini
        elseif ($productVipLevel > $currentVipLevel) {
            $user->keanggotaan = $productVip;
        }

        $user->save();

        // Catat transaksi
        DB::table('transaksi')->insert([
            'user_id' => $userId,
            'product_id' => $productId,
            'amount' => $product->harga,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($isFirstInvestment) {
            $this->distributeFirstInvestmentCommission($user, $product->harga);
        }

        return back()->with('success', 'Investasi berhasil!');
    }

    private function distributeFirstInvestmentCommission(User $user, $investmentAmount)
    {
        if (!empty($user->referred_by)) {
            $uplineLevel1 = User::find($user->referred_by);

            if ($uplineLevel1) {
                $commissionLevel1 = $investmentAmount * 0.30; // 30%
                $uplineLevel1->balance += $commissionLevel1;
                $uplineLevel1->save();

                // Catat komisi
                DB::table('komisi')->insert([
                    'user_id' => $uplineLevel1->id,
                    'downline_id' => $user->id,
                    'amount' => $commissionLevel1,
                    'level' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Dapatkan upline level 2 (sponsor dari upline level 1)
                if (!empty($uplineLevel1->referred_by)) {
                    $uplineLevel2 = User::find($uplineLevel1->referred_by);

                    if ($uplineLevel2) {
                        $commissionLevel2 = $investmentAmount * 0.03; // 3%
                        $uplineLevel2->balance += $commissionLevel2;
                        $uplineLevel2->save();

                        // Catat komisi
                        DB::table('komisi')->insert([
                            'user_id' => $uplineLevel2->id,
                            'downline_id' => $user->id,
                            'amount' => $commissionLevel2,
                            'level' => 2,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);

                        // Dapatkan upline level 3 (sponsor dari upline level 2)
                        if (!empty($uplineLevel2->referred_by)) {
                            $uplineLevel3 = User::find($uplineLevel2->referred_by);

                            if ($uplineLevel3) {
                                $commissionLevel3 = $investmentAmount * 0.01; // 1%
                                $uplineLevel3->balance += $commissionLevel3;
                                $uplineLevel3->save();

                                // Catat komisi
                                DB::table('komisi')->insert([
                                    'user_id' => $uplineLevel3->id,
                                    'downline_id' => $user->id,
                                    'amount' => $commissionLevel3,
                                    'level' => 3,
                                    'created_at' => now(),
                                    'updated_at' => now()
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
    private function calculateVipLevel($totalInvestasi)
    {
        if ($totalInvestasi >= 52000000) {
            return 6;
        } elseif ($totalInvestasi >= 15000000) {
            return 5;
        } elseif ($totalInvestasi >= 10000000) {
            return 4;
        } elseif ($totalInvestasi >= 3200000) {
            return 3;
        } elseif ($totalInvestasi >= 420000) {
            return 2;
        } elseif ($totalInvestasi >= 50000) {
            return 1;
        } else {
            return 0;
        }
    }
}
