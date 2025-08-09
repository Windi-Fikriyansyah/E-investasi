<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Display the user's profile form.
     */

    public function index()
    {
        $userId = Auth::id();

        // Level 1 Team (direct referrals)
        $level1Users = DB::table('users')
            ->where('referred_by', $userId)
            ->select('id')
            ->get()
            ->pluck('id');

        $level1Count = count($level1Users);

        $level1Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->where('users.referred_by', $userId)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 1 dari tabel komisi
        $level1Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 1)
            ->sum('amount');

        // Level 2 Team (referrals of your direct referrals)
        $level2Users = DB::table('users')
            ->whereIn('referred_by', $level1Users)
            ->select('id')
            ->get()
            ->pluck('id');

        $level2Count = count($level2Users);

        $level2Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->whereIn('users.referred_by', $level1Users)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 2 dari tabel komisi
        $level2Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 2)
            ->sum('amount');

        // Level 3 Team
        $level3Users = DB::table('users')
            ->whereIn('referred_by', $level2Users)
            ->select('id')
            ->get()
            ->pluck('id');

        $level3Count = count($level3Users);

        $level3Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->whereIn('users.referred_by', $level2Users)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 3 dari tabel komisi
        $level3Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 3)
            ->sum('amount');

        $referralCount = $level1Count;

        return view('bonus.index', compact(
            'referralCount',
            'level1Count',
            'level1Investors',
            'level1Commission',
            'level2Count',
            'level2Investors',
            'level2Commission',
            'level3Count',
            'level3Investors',
            'level3Commission'
        ));
    }
    public function bonus()
    {
        $userId = Auth::id();

        // Level 1 Team (direct referrals)
        $level1Users = DB::table('users')
            ->where('referred_by', $userId)
            ->select('id')
            ->get()
            ->pluck('id');

        $level1Count = count($level1Users);

        $level1Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->where('users.referred_by', $userId)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 1 dari tabel komisi
        $level1Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 1)
            ->sum('amount');

        // Level 2 Team (referrals of your direct referrals)
        $level2Users = DB::table('users')
            ->whereIn('referred_by', $level1Users)
            ->select('id')
            ->get()
            ->pluck('id');

        $level2Count = count($level2Users);

        $level2Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->whereIn('users.referred_by', $level1Users)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 2 dari tabel komisi
        $level2Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 2)
            ->sum('amount');

        // Level 3 Team
        $level3Users = DB::table('users')
            ->whereIn('referred_by', $level2Users)
            ->select('id')
            ->get()
            ->pluck('id');

        $level3Count = count($level3Users);

        $level3Investors = DB::table('users')
            ->join('transaksi', 'users.id', '=', 'transaksi.user_id')
            ->whereIn('users.referred_by', $level2Users)
            ->distinct('users.id')
            ->count('users.id');

        // Hitung komisi level 3 dari tabel komisi
        $level3Commission = DB::table('komisi')
            ->where('user_id', $userId)
            ->where('level', 3)
            ->sum('amount');

        $referralCount = $level1Count;

        $claimedBonuses = DB::table('claimed_bonuses')
            ->where('user_id', $userId)
            ->pluck('milestone')
            ->toArray();

        $totalClaimedBonuses = DB::table('claimed_bonuses')
            ->where('user_id', $userId)
            ->sum('amount');

        return view('tim.index', compact(
            'referralCount',
            'level1Count',
            'level1Investors',
            'level1Commission',
            'level2Count',
            'level2Investors',
            'level2Commission',
            'level3Count',
            'level3Investors',
            'level3Commission',
            'claimedBonuses',
            'totalClaimedBonuses'
        ));
    }



    public function claimBonus(Request $request)
    {
        $userId = Auth::id();
        $milestone = $request->input('milestone');

        // Tentukan bonus berdasarkan milestone
        $bonusAmounts = [
            'starter' => 30000,
            'bronze' => 100000,
            'silver' => 150000,
            'gold' => 400000,
            'platinum' => 850000
        ];

        $amount = $bonusAmounts[$milestone] ?? 0;

        // Cek apakah bonus sudah di-claim sebelumnya
        $alreadyClaimed = DB::table('claimed_bonuses')
            ->where('user_id', $userId)
            ->where('milestone', $milestone)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json([
                'success' => false,
                'message' => 'Bonus ini sudah di-claim sebelumnya'
            ]);
        }

        if ($amount > 0) {
            try {
                DB::transaction(function () use ($userId, $milestone, $amount) {
                    // Tambahkan ke balance user
                    DB::table('users')
                        ->where('id', $userId)
                        ->increment('balance', $amount);

                    // Catat di claimed_bonuses
                    DB::table('claimed_bonuses')->insert([
                        'user_id' => $userId,
                        'milestone' => $milestone,
                        'amount' => $amount,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);

                    // Catat transaksi
                    DB::table('transactions_agen')->insert([
                        'user_id' => $userId,
                        'amount' => $amount,
                        'type' => 'milestone_bonus',
                        'description' => 'Bonus Milestone ' . ucfirst($milestone),
                        'status' => 'completed',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                });

                return response()->json([
                    'success' => true,
                    'message' => 'Bonus berhasil di-claim!'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal claim bonus: ' . $e->getMessage()
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Milestone tidak valid'
        ]);
    }
}
