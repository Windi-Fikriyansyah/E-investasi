<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;


class ProdukController extends Controller
{
    public function index()
    {
        $categories = DB::table('kategori')->get();
        $products = DB::table('produk')
            ->select('produk.*', 'produk.kategori as category_name')
            ->get();
        $userVip = Auth::check() ? (Auth::user()->keanggotaan ?? 'VIP 0') : 'VIP 0';
        $userVipLevel = (int) str_replace('VIP ', '', $userVip);

        return view('produk.index', compact('categories', 'products', 'userVipLevel'));
    }

    public function invest(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu'
            ], 401);
        }


        $productId = $request->product_id;
        $userId = Auth::id();

        // Dapatkan data produk
        $product = DB::table('produk')->where('id', $productId)->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        // Dapatkan user
        $user = User::find($userId);
        $isFirstInvestment = DB::table('transaksi')
            ->where('user_id', $userId)
            ->count() === 0;

        // Cek balance
        if ($user->balance < $product->harga) {
            return response()->json([
                'success' => false,
                'message' => 'Saldo tidak mencukupi'
            ], 400);
        }

        // Mulai transaksi database
        DB::beginTransaction();

        try {
            // Kurangi balance
            $user->balance -= $product->harga;
            $user->save();

            // Catat transaksi
            DB::table('transaksi')->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'amount' => $product->harga,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            if ($isFirstInvestment) {
                $this->distributeFirstInvestmentCommission($user, $product->harga);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Investasi berhasil!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function distributeFirstInvestmentCommission(User $user, $investmentAmount)
    {
        if (!empty($user->referred_by)) {
            $uplineLevel1 = User::find($user->referred_by);

            if ($uplineLevel1) {
                $commissionLevel1 = $investmentAmount * 0.20; // 30%
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
}
