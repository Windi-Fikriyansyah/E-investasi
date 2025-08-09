<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            $transactions = DB::table('transaksi')
                ->join('produk', 'transaksi.product_id', '=', 'produk.id')
                ->select(
                    'transaksi.*',
                    'produk.nama_produk as product_name',
                    'produk.harga as product_price',
                    'produk.durasi',
                    'produk.vip',
                    'produk.total_pendapatan'
                )
                ->where('transaksi.user_id', $user->id)
                ->orderByDesc('transaksi.created_at')
                ->get()
                ->map(function ($item) {
                    // Add lowercase status for CSS classes
                    $item->status_class = strtolower($item->status);

                    $createdAt = Carbon::parse($item->created_at);
                    $endDate = $createdAt->copy()->addDays($item->durasi);
                    $now = Carbon::now();

                    // Calculate daily return amount
                    $item->daily_return = $item->total_pendapatan / $item->durasi;

                    // Calculate full days elapsed since creation (24 jam per hari)
                    $fullDaysElapsed = $createdAt->diffInDays($now);
                    $item->days_elapsed = max(0, min($fullDaysElapsed, $item->durasi));

                    // Calculate available claims (how many days can be claimed)
                    // Hanya bisa claim setelah 24 jam dari waktu pembelian
                    $item->available_claims = max(0, min($fullDaysElapsed, $item->durasi));

                    // Get already claimed days from claims table
                    $claimedDays = DB::table('transaksi_claims')
                        ->where('transaksi_id', $item->id)
                        ->count();

                    $item->claimed_days = $claimedDays;
                    $item->remaining_claims = max(0, $item->available_claims - $claimedDays);

                    // Check if can claim (has unclaimed available days)
                    $item->can_claim = ($item->status != 'completed' && $item->remaining_claims > 0);

                    // Calculate remaining days until completion
                    $item->days_remaining = max(0, $now->diffInDays($endDate, false));

                    // Mark as completed if all days have been elapsed
                    if ($now->gte($endDate) && $item->status != 'completed') {
                        $item->can_complete = true;
                    }

                    // Add end date for reference
                    $item->end_date = $endDate;

                    // Calculate total claimed amount
                    $item->total_claimed = $claimedDays * $item->daily_return;

                    // Calculate pending claim amount
                    $item->pending_claim_amount = $item->remaining_claims * $item->daily_return;

                    // Calculate next claim time
                    if ($claimedDays < $item->durasi) {
                        $nextClaimTime = $createdAt->copy()->addDays($claimedDays + 1);
                        $item->next_claim_time = $nextClaimTime->format('d M Y H:i');
                        $item->can_claim_now = $now->gte($nextClaimTime);
                    } else {
                        $item->next_claim_time = null;
                        $item->can_claim_now = false;
                    }

                    return $item;
                });

            return view('riwayat_transaksi.index', compact('transactions'));
        } catch (\Exception $e) {
            \Log::error('Error in TransaksiController@index: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memuat data transaksi');
        }
    }

    public function claim(Request $request, $id)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu'])
                    : redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
            }

            // Get transaction with product details
            $transaction = DB::table('transaksi')
                ->join('produk', 'transaksi.product_id', '=', 'produk.id')
                ->select(
                    'transaksi.*',
                    'produk.total_pendapatan',
                    'produk.durasi',
                    'produk.nama_produk as product_name'
                )
                ->where('transaksi.id', $id)
                ->where('transaksi.user_id', $user->id)
                ->first();

            if (!$transaction) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Transaksi tidak ditemukan'])
                    : redirect()->back()->with('error', 'Transaksi tidak ditemukan');
            }

            // Check if transaction is already completed
            if ($transaction->status == 'completed') {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Transaksi sudah selesai'])
                    : redirect()->back()->with('error', 'Transaksi sudah selesai');
            }

            $createdAt = Carbon::parse($transaction->created_at);
            $now = Carbon::now();

            // Calculate full days elapsed (24 jam per hari)
            $fullDaysElapsed = $createdAt->diffInDays($now);
            $availableClaims = max(0, min($fullDaysElapsed, $transaction->durasi));

            // Get already claimed days
            $claimedDays = DB::table('transaksi_claims')
                ->where('transaksi_id', $id)
                ->count();

            $remainingClaims = max(0, $availableClaims - $claimedDays);

            // Check if next claim is available (harus sudah lewat 24 jam dari claim terakhir)
            $nextClaimTime = $createdAt->copy()->addDays($claimedDays + 1);
            if ($now->lt($nextClaimTime)) {
                $waitTime = $now->diffForHumans($nextClaimTime, true);
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Anda bisa claim lagi dalam ' . $waitTime])
                    : redirect()->back()->with('error', 'Anda bisa claim lagi dalam ' . $waitTime);
            }

            if ($remainingClaims <= 0) {
                return $request->ajax()
                    ? response()->json(['success' => false, 'message' => 'Tidak ada hari yang bisa diklaim saat ini'])
                    : redirect()->back()->with('error', 'Tidak ada hari yang bisa diklaim saat ini');
            }

            // Calculate daily return
            $dailyReturn = $transaction->total_pendapatan / $transaction->durasi;
            $totalClaimAmount = $remainingClaims * $dailyReturn;

            // Start database transaction
            DB::beginTransaction();

            // Update user balance
            $balanceUpdated = DB::table('users')
                ->where('id', $user->id)
                ->increment('balance', $totalClaimAmount);

            if (!$balanceUpdated) {
                throw new \Exception('Gagal mengupdate saldo user');
            }

            // Record claims for each available day
            $claimData = [];
            for ($i = 0; $i < $remainingClaims; $i++) {
                $claimDay = $claimedDays + $i + 1;
                $claimData[] = [
                    'transaksi_id' => $id,
                    'user_id' => $user->id,
                    'day_number' => $claimDay,
                    'claim_amount' => $dailyReturn,
                    'claim_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            DB::table('transaksi_claims')->insert($claimData);

            // Update transaction total claimed amount
            $newTotalClaimed = ($claimedDays + $remainingClaims) * $dailyReturn;
            DB::table('transaksi')
                ->where('id', $id)
                ->update([
                    'total_claimed' => $newTotalClaimed,
                    'updated_at' => now()
                ]);

            // Check if all days have been claimed (complete the transaction)
            $endDate = $createdAt->copy()->addDays($transaction->durasi);
            if (($claimedDays + $remainingClaims) >= $transaction->durasi && $now->gte($endDate)) {
                DB::table('transaksi')
                    ->where('id', $id)
                    ->update([
                        'status' => 'completed',
                        'return_amount' => $transaction->total_pendapatan,
                        'updated_at' => now()
                    ]);
            }

            DB::commit();

            $message = 'Berhasil mengklaim ' . $remainingClaims . ' hari! Saldo ditambahkan Rp ' . number_format($totalClaimAmount, 0, ',', '.');

            return $request->ajax()
                ? response()->json([
                    'success' => true,
                    'message' => $message,
                    'claimed_days' => $remainingClaims,
                    'claim_amount' => $totalClaimAmount
                ])
                : redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in TransaksiController@claim: ' . $e->getMessage());

            return $request->ajax()
                ? response()->json(['success' => false, 'message' => 'Gagal mengklaim: ' . $e->getMessage()])
                : redirect()->back()->with('error', 'Gagal mengklaim: ' . $e->getMessage());
        }
    }

    /**
     * Get transaction statistics for user
     */
    public function getStats()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $stats = DB::table('transaksi')
                ->join('produk', 'transaksi.product_id', '=', 'produk.id')
                ->where('transaksi.user_id', $user->id)
                ->selectRaw('
                    COUNT(*) as total_transactions,
                    COUNT(CASE WHEN transaksi.status = "completed" THEN 1 END) as completed_count,
                    COUNT(CASE WHEN transaksi.status IN ("aktif", "pending") THEN 1 END) as active_count,
                    SUM(CASE WHEN transaksi.status = "completed" THEN COALESCE(transaksi.return_amount, produk.total_pendapatan) ELSE 0 END) as total_earned,
                    SUM(CASE WHEN transaksi.status IN ("aktif", "pending") THEN transaksi.amount ELSE 0 END) as active_investment
                ')
                ->first();

            // Calculate claimable transactions (have pending daily claims)
            $claimableTransactions = DB::table('transaksi')
                ->join('produk', 'transaksi.product_id', '=', 'produk.id')
                ->where('transaksi.user_id', $user->id)
                ->where('transaksi.status', '!=', 'completed')
                ->get()
                ->filter(function ($transaction) {
                    $createdAt = Carbon::parse($transaction->created_at);
                    $now = Carbon::now();
                    $daysElapsed = $createdAt->diffInDays($now, false);
                    $availableClaims = max(0, min($daysElapsed + 1, $transaction->durasi));

                    $claimedDays = DB::table('transaksi_claims')
                        ->where('transaksi_id', $transaction->id)
                        ->count();

                    return ($availableClaims - $claimedDays) > 0;
                });

            $stats->claimable_count = $claimableTransactions->count();

            // Calculate total pending claims amount
            $totalPendingAmount = 0;
            foreach ($claimableTransactions as $transaction) {
                $createdAt = Carbon::parse($transaction->created_at);
                $now = Carbon::now();
                $daysElapsed = $createdAt->diffInDays($now, false);
                $availableClaims = max(0, min($daysElapsed + 1, $transaction->durasi));

                $claimedDays = DB::table('transaksi_claims')
                    ->where('transaksi_id', $transaction->id)
                    ->count();

                $remainingClaims = max(0, $availableClaims - $claimedDays);
                $dailyReturn = $transaction->total_pendapatan / $transaction->durasi;
                $totalPendingAmount += $remainingClaims * $dailyReturn;
            }

            $stats->pending_claim_amount = $totalPendingAmount;

            return response()->json(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            \Log::error('Error in TransaksiController@getStats: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat statistik'], 500);
        }
    }

    /**
     * Get claim history for a transaction
     */
    public function getClaimHistory($transactionId)
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
            }

            $claims = DB::table('transaksi_claims')
                ->join('transaksi', 'transaksi_claims.transaksi_id', '=', 'transaksi.id')
                ->join('produk', 'transaksi.product_id', '=', 'produk.id')
                ->where('transaksi_claims.transaksi_id', $transactionId)
                ->where('transaksi_claims.user_id', $user->id)
                ->select(
                    'transaksi_claims.*',
                    'produk.nama_produk as product_name'
                )
                ->orderBy('transaksi_claims.day_number')
                ->get();

            return response()->json(['success' => true, 'data' => $claims]);
        } catch (\Exception $e) {
            \Log::error('Error in TransaksiController@getClaimHistory: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal memuat riwayat claim'], 500);
        }
    }
}
