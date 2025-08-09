<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class DepositController extends Controller
{
    public function index()
    {
        return view('deposit.index');
    }

    public function riwayat()
    {
        $deposits = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                $item->created_at = \Carbon\Carbon::parse($item->created_at);
                $item->updated_at = \Carbon\Carbon::parse($item->updated_at);
                return $item;
            });

        return view('deposit.riwayat', compact('deposits'));
    }
    public function createTransaction(Request $request)
    {
        $request->validate([
            'amount_raw' => 'required|numeric|min:10000',
        ]);
        $amount = $request->amount_raw;

        // Ambil pengaturan Midtrans dari database
        $midtransSettings = DB::table('midtrans_settings')->first();

        if (!$midtransSettings) {
            return back()->with('error', 'Midtrans settings not configured');
        }

        // Konfigurasi Midtrans
        Config::$serverKey = $midtransSettings->server_key;
        Config::$clientKey = $midtransSettings->client_key;
        Config::$isProduction = ($midtransSettings->mode === 'production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'DEPO-' . uniqid();

        // Simpan transaksi ke database sebelum membuat ke Midtrans
        $depositId = DB::table('deposits')->insertGetId([
            'user_id' => auth()->id(),
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if (!$depositId) {
            return back()->with('error', 'Failed to save transaction record');
        }

        // Buat transaksi
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => auth()->user()->name,
                'email' => auth()->user()->email,
            ],
            'enabled_payments' => ['bca_va', 'bni_va', 'bri_va', 'permata_va', 'gopay', 'shopeepay'],
            'callbacks' => [
                'finish' => route('deposit.callback')
            ]
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            // Simpan snap token ke database
            DB::table('deposits')
                ->where('id', $depositId)
                ->update([
                    'snap_token' => $snapToken,
                    'updated_at' => now()
                ]);

            return view('deposit.payment', [
                'snapToken' => $snapToken,
                'amount' => $amount,
                'clientKey' => $midtransSettings->client_key
            ]);
        } catch (\Exception $e) {
            // Hapus record deposit jika gagal
            DB::table('deposits')->where('id', $depositId)->delete();
            return back()->with('error', 'Failed to create transaction: ' . $e->getMessage());
        }
    }

    public function checkPendingPayment()
    {
        $pendingPayment = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->exists();

        return response()->json(['has_pending' => $pendingPayment]);
    }
    public function continuePayment(Request $request)
    {
        $orderId = $request->order_id;

        if (!$orderId) {
            return back()->with('error', 'Order ID tidak ditemukan');
        }

        // Cari transaksi di database
        $deposit = DB::table('deposits')
            ->where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$deposit) {
            return back()->with('error', 'Transaksi tidak ditemukan atau sudah tidak valid');
        }

        // Cek apakah snap token masih ada
        if (!$deposit->snap_token) {
            return back()->with('error', 'Token pembayaran tidak valid, silakan buat transaksi baru');
        }

        // Ambil pengaturan Midtrans dari database
        $midtransSettings = DB::table('midtrans_settings')->first();

        if (!$midtransSettings) {
            return back()->with('error', 'Midtrans settings not configured');
        }

        return view('deposit.payment', [
            'snapToken' => $deposit->snap_token,
            'amount' => $deposit->amount,
            'clientKey' => $midtransSettings->client_key
        ]);
    }

    public function cancelTransaction(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $orderId = $request->order_id;

        // Cari transaksi di database
        $deposit = DB::table('deposits')
            ->where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->where('status', 'pending')
            ->first();

        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan atau sudah tidak dapat dibatalkan'
            ]);
        }

        // Update status menjadi cancelled
        $updated = DB::table('deposits')
            ->where('order_id', $orderId)
            ->where('user_id', auth()->id())
            ->update([
                'status' => 'cancelled',
                'updated_at' => now()
            ]);

        if ($updated) {
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan transaksi'
            ]);
        }
    }

    public function handleCallback(Request $request)
    {
        try {
            // Ambil order_id dari request jika ada
            $orderId = $request->order_id;

            if (!$orderId) {
                return view('deposit.success', ['error' => 'Order ID not provided']);
            }

            // Cari transaksi di database
            $deposit = DB::table('deposits')
                ->where('order_id', $orderId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$deposit) {
                return view('deposit.success', ['error' => 'Transaction not found']);
            }

            return view('deposit.success', [
                'status' => $deposit->status,
                'order_id' => $deposit->order_id,
                'amount' => $deposit->amount
            ]);
        } catch (\Exception $e) {
            return view('deposit.success', [
                'error' => $e->getMessage()
            ]);
        }
    }


    public function handleNotification()
    {
        $midtransSettings = DB::table('midtrans_settings')->first();

        if (!$midtransSettings) {
            \Log::error('Midtrans settings not found');
            return response()->json(['status' => 'error', 'message' => 'Midtrans settings not configured'], 500);
        }

        // Konfigurasi Midtrans
        Config::$serverKey = $midtransSettings->server_key;
        Config::$isProduction = ($midtransSettings->mode === 'production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $notification = new Notification();

        $transaction = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;
        $paymentType = $notification->payment_type;
        $grossAmount = $notification->gross_amount;

        \Log::info('Midtrans Notification:', [
            'order_id' => $orderId,
            'status' => $transaction,
            'fraud' => $fraud,
            'payment_type' => $paymentType,
            'amount' => $grossAmount
        ]);

        // Cari transaksi di database
        $deposit = DB::table('deposits')->where('order_id', $orderId)->first();

        if (!$deposit) {
            \Log::error('Deposit not found for order_id: ' . $orderId);
            return response()->json(['status' => 'error', 'message' => 'Transaction not found'], 404);
        }

        // Jangan proses jika transaksi sudah dibatalkan
        if ($deposit->status === 'cancelled') {
            \Log::info('Transaction was cancelled, ignoring notification: ' . $orderId);
            return response()->json(['status' => 'success', 'message' => 'Transaction was cancelled']);
        }

        $status = $deposit->status; // Default ke status yang ada
        $shouldAddBalance = false;

        if ($transaction == 'capture') {
            if ($fraud == 'challenge') {
                $status = 'pending';
            } else if ($fraud == 'accept') {
                $status = 'success';
                $shouldAddBalance = true;
            }
        } else if ($transaction == 'settlement') {
            $status = 'success';
            $shouldAddBalance = true;
        } else if ($transaction == 'pending') {
            $status = 'pending';
        } else if (
            $transaction == 'deny' ||
            $transaction == 'expire' ||
            $transaction == 'cancel'
        ) {
            $status = 'failed';
        }

        // Tambah saldo user jika status success dan belum pernah ditambah
        if ($shouldAddBalance && $deposit->status !== 'success') {
            DB::table('users')
                ->where('id', $deposit->user_id)
                ->increment('balance', $deposit->amount);
        }

        // Update status deposit
        DB::table('deposits')
            ->where('order_id', $orderId)
            ->update([
                'status' => $status,
                'payment_method' => $paymentType,
                'payment_data' => json_encode($notification),
                'updated_at' => now()
            ]);

        \Log::info('Deposit updated:', [
            'order_id' => $orderId,
            'new_status' => $status,
            'user_id' => $deposit->user_id,
            'amount_added' => $shouldAddBalance ? $deposit->amount : 0
        ]);

        return response()->json(['status' => 'success']);
    }
}
