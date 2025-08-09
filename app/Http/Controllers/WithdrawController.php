<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WithdrawController extends Controller
{
    public function index()
    {
        $banks = DB::table('bank')
            ->where('id_user', auth()->id())
            ->get();

        // Ambil riwayat penarikan tanpa model
        $withdrawals = DB::table('withdrawals')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('tarik.index', compact('banks', 'withdrawals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount_raw' => 'required|numeric|min:50000',
            'bank' => 'required|exists:bank,id',
        ]);

        // Mulai transaction
        DB::beginTransaction();

        try {
            $adminFee = round($request->amount_raw * 0.1);
            $totalAmount = $request->amount_raw;

            // Dapatkan data user
            $user = DB::table('users')
                ->where('id', auth()->id())
                ->first();

            // Cek saldo mencukupi
            if ($user->balance < $totalAmount) {
                return back()->with('error', 'Saldo tidak mencukupi untuk penarikan ini');
            }

            // Dapatkan data bank
            $bank = DB::table('bank')
                ->where('id', $request->bank)
                ->first();

            // Buat penarikan tanpa model
            $withdrawalId = DB::table('withdrawals')->insertGetId([
                'user_id' => auth()->id(),
                'bank_id' => $bank->id,
                'amount' => $request->amount_raw,
                'admin_fee' => $adminFee,
                'status' => 'processing',
                'bank_name' => $bank->nama_bank,
                'bank_number' => $bank->no_rekening,
                'bank_account' => $bank->nama_pemilik,
                'notes' => $request->notes,
                'created_at' => now(),
                'updated_at' => now()
            ]);



            DB::table('users')
                ->where('id', auth()->id())
                ->decrement('balance', $totalAmount);

            // Commit transaction
            DB::commit();

            return response()->json([
                'message' => 'Penarikan berhasil diajukan'
            ]);
        } catch (\Exception $e) {
            // Rollback transaction jika ada error
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses penarikan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function history(Request $request)
    {
        $withdrawals = DB::table('withdrawals')
            ->where('user_id', auth()->id())
            ->when($request->has('date_filter') && $request->date_filter, function ($query) use ($request) {
                $date = Carbon::createFromFormat('Y-m-d', $request->date_filter);
                return $query->whereDate('created_at', $date);
            }, function ($query) {
                // Default tampilkan data hari ini
                return $query->whereDate('created_at', Carbon::today());
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('tarik.history', compact('withdrawals'));
    }
}
