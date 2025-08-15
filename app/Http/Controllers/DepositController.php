<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PaylabsService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DepositController extends Controller
{
    protected $paylabsService;

    public function __construct(PaylabsService $paylabsService)
    {
        $this->paylabsService = $paylabsService;
    }

    public function index()
    {
        return view('deposit.index');
    }

    public function checkPendingPayment()
    {
        $hasPending = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->exists();

        return response()->json(['has_pending' => $hasPending]);
    }

    public function createQRIS(Request $request)
    {
        // Validasi input
        $request->validate([
            'amount_raw' => 'required|numeric|min:10000',
        ]);

        // Cek apakah ada transaksi pending untuk user ini
        $pendingDeposit = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->select('order_id', 'payment_method')
            ->first();

        if ($pendingDeposit) {
            // Jika ada transaksi pending, arahkan ke halaman payment yang sudah ada
            return redirect()->route('deposit.payment', ['order_id' => $pendingDeposit->order_id])
                ->with('info', 'Anda sudah memiliki transaksi yang sedang berlangsung');
        }

        // Generate merchant trade no
        $merchantTradeNo = 'DEPO' . auth()->id() . time() . mt_rand(100, 999);
        $amount = (int) $request->amount_raw; // Amount dalam rupiah

        Log::info('Creating deposit', [
            'user_id' => auth()->id(),
            'amount' => $amount,
            'merchantTradeNo' => $merchantTradeNo
        ]);

        // Simpan ke database terlebih dahulu
        $depositId = DB::table('deposits')->insertGetId([
            'user_id' => auth()->id(),
            'order_id' => $merchantTradeNo,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => 'QRIS',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $response = $this->paylabsService->createQRISPayment(
                $amount,
                'Deposit Saldo',
                $merchantTradeNo
            );

            Log::info('Paylabs Response', ['response' => $response]);

            if (isset($response['errCode']) && $response['errCode'] === '0') {
                // Update database dengan platform trade no
                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update([
                        'platform_trade_no' => $response['platformTradeNo'] ?? null,
                        'qr_code' => $response['qrCode'] ?? null,
                        'qris_url' => $response['qrisUrl'] ?? null,
                        'expired_time' => isset($response['expiredTime']) ?
                            Carbon::createFromFormat('YmdHis', $response['expiredTime'])->format('Y-m-d H:i:s') : null,
                        'updated_at' => now(),
                    ]);

                // Redirect ke halaman payment
                return redirect()->route('deposit.payment', ['order_id' => $merchantTradeNo]);
            } else {
                // Update status ke failed
                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);

                $errorMessage = $response['errCodeDes'] ?? 'Gagal membuat transaksi';
                Log::error('Paylabs Error', [
                    'errCode' => $response['errCode'] ?? 'Unknown',
                    'errCodeDes' => $errorMessage
                ]);

                return back()->with('error', 'Gagal membuat transaksi: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            // Update status ke failed jika terjadi exception
            DB::table('deposits')
                ->where('id', $depositId)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);

            Log::error('Exception in create deposit', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    public function payment($order_id)
    {
        $deposit = DB::table('deposits')
            ->where('order_id', $order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return redirect()->route('deposit.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        // Jika status sudah bukan pending, arahkan ke riwayat
        if (!in_array($deposit->status, ['pending', 'processing'])) {
            return redirect()->route('deposit.riwayat')
                ->with('info', 'Transaksi ini sudah selesai');
        }
        $expiredTime = $deposit->expired_time
            ? Carbon::parse($deposit->expired_time)->format('YmdHis')
            : null;


        return view('deposit.payment', [
            'deposit' => (object) [
                'id' => $deposit->id,
                'amount' => $deposit->amount,
                'order_id' => $deposit->order_id,
                'qrCode' => $deposit->qr_code,
                'qrisUrl' => $deposit->qris_url,
                'expiredTime' => $expiredTime,
                'platformTradeNo' => $deposit->platform_trade_no,
                'status' => $deposit->status
            ]
        ]);
    }

    public function continuePayment($order_id)
    {
        $deposit = DB::table('deposits')
            ->where('order_id', $order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return redirect()->route('deposit.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        // Redirect ke halaman payment sesuai metode pembayaran
        switch ($deposit->payment_method) {
            case 'QRIS':
                return redirect()->route('deposit.payment', ['order_id' => $order_id]);
                break;

            case in_array($deposit->payment_method, ['DANABALANCE', 'SHOPEEBALANCE', 'LINKAJABALANCE', 'OVOBALANCE', 'GOPAYBALANCE']):
                return redirect()->route('deposit.ewallet.payment', ['order_id' => $order_id]);
                break;

            case in_array($deposit->payment_method, ['SinarmasVA', 'MaybankVA', 'DanamonVA', 'BNCVA', 'BCAVA', 'INAVA', 'BNIVA', 'PermataVA', 'MuamalatVA', 'BSIVA', 'BRIVA', 'MandiriVA', 'CIMBVA']):
                return redirect()->route('deposit.va.payment', ['order_id' => $order_id]);
                break;

            default:
                return redirect()->route('deposit.index')
                    ->with('error', 'Metode pembayaran tidak dikenali');
        }
    }

    public function cancelTransaction(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'platform_trade_no' => 'nullable|string',
            'qr_code' => 'nullable|string'
        ]);

        $deposit = DB::table('deposits')
            ->where('order_id', $request->order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        if (!in_array($deposit->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dibatalkan karena status sudah ' . $deposit->status
            ], 400);
        }

        try {
            // Panggil API cancel ke Paylabs
            $response = $this->paylabsService->cancelQRISPayment(
                $deposit->order_id,
                $request->platform_trade_no ?? $deposit->platform_trade_no,
                $request->qr_code ?? $deposit->qr_code
            );

            Log::info('Paylabs Cancel Response', $response);

            if (isset($response['errCode']) && $response['errCode'] === '0') {
                // Update status ke cancelled
                DB::table('deposits')
                    ->where('id', $deposit->id)
                    ->update([
                        'status' => 'cancelled',
                        'updated_at' => now(),
                    ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Transaksi berhasil dibatalkan'
                ]);
            } else {
                $errorMessage = $response['errCodeDes'] ?? 'Gagal membatalkan transaksi di Paylabs';
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error cancelling transaction', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
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
    public function handleQrisNotification(Request $request)
    {
        Log::info('Received Paylabs QRIS Notification', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Validasi signature
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $partnerId = $request->header('X-PARTNER-ID');
        $requestId = $request->header('X-REQUEST-ID');

        // Verifikasi signature
        $isValid = $this->paylabsService->verifyPaylabsSignature(
            '/v2/qris/notify', // endpoint path sesuai dengan route
            $request->all(),
            $timestamp,
            $signature
        );

        if (!$isValid) {
            Log::error('Invalid Paylabs signature', [
                'request_id' => $requestId,
                'timestamp' => $timestamp,
                'signature' => $signature
            ]);

            return response()->json([
                'errCode' => 'SIGNATURE_INVALID',
                'errCodeDes' => 'Invalid signature'
            ], 400);
        }

        // Proses notifikasi
        $notificationData = $request->all();
        $merchantTradeNo = $notificationData['merchantTradeNo'] ?? null;
        $status = $notificationData['status'] ?? null;
        $amount = $notificationData['amount'] ?? null;
        $platformTradeNo = $notificationData['platformTradeNo'] ?? null;

        try {
            DB::beginTransaction();

            // Cari transaksi berdasarkan merchantTradeNo
            $deposit = DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->first();

            if (!$deposit) {
                Log::error('Deposit not found', ['merchant_trade_no' => $merchantTradeNo]);
                DB::rollBack();

                return $this->generateNotificationResponse(
                    $notificationData['merchantId'],
                    $notificationData['requestId'],
                    'TRANSACTION_NOT_FOUND',
                    'Transaction not found'
                );
            }

            // Update status transaksi berdasarkan notifikasi
            $newStatus = $this->mapPaylabsStatus($status);
            $updateData = [
                'status' => $newStatus,
                'updated_at' => now(),
                'platform_trade_no' => $platformTradeNo
            ];

            // Jika sukses, tambahkan data tambahan
            if ($status === '02') { // Success
                $updateData['success_time'] = Carbon::createFromFormat('YmdHis', $notificationData['successTime'] ?? now()->format('YmdHis'));

                // Jika ada fee, simpan juga
                if (isset($notificationData['transFeeAmount'])) {
                    $updateData['fee_amount'] = $notificationData['transFeeAmount'];
                }
                if (isset($notificationData['totalTransFee'])) {
                    $updateData['total_fee'] = $notificationData['totalTransFee'];
                }
            }

            DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->update($updateData);

            // Jika sukses, tambahkan saldo ke user
            if ($status === '02' && $newStatus === 'completed') {
                $user = DB::table('users')->where('id', $deposit->user_id)->first();
                if ($user) {
                    DB::table('users')
                        ->where('id', $deposit->user_id)
                        ->increment('balance', $deposit->amount);

                    // Catat riwayat transaksi
                    DB::table('balance_histories')->insert([
                        'user_id' => $deposit->user_id,
                        'amount' => $deposit->amount,
                        'type' => 'deposit',
                        'description' => 'Deposit via QRIS',
                        'reference_id' => $deposit->order_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            Log::info('Successfully processed Paylabs notification', [
                'merchant_trade_no' => $merchantTradeNo,
                'status' => $status,
                'new_status' => $newStatus
            ]);

            // Berikan response sukses ke Paylabs
            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId']
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing Paylabs notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId'],
                'PROCESSING_ERROR',
                'Error processing notification: ' . $e->getMessage()
            );
        }
    }

    /**
     * Map Paylabs status to our system status
     */
    private function mapPaylabsStatus($paylabsStatus)
    {
        switch ($paylabsStatus) {
            case '02': // Success
                return 'completed';
            case '09': // Failed
                return 'failed';
            case '01': // Pending
            default:
                return 'processing';
        }
    }

    /**
     * Generate proper response for Paylabs notification
     */
    private function generateNotificationResponse($merchantId, $requestId, $errCode = '0', $errCodeDes = null)
    {
        $timestamp = now()->format('Y-m-d\TH:i:s.vP');
        $responseData = [
            'merchantId' => $merchantId,
            'requestId' => $requestId,
            'errCode' => $errCode
        ];

        if ($errCodeDes) {
            $responseData['errCodeDes'] = $errCodeDes;
        }

        $signature = $this->paylabsService->generateResponseSignature($responseData, $timestamp);

        return response()->json($responseData, 200, [
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $merchantId,
            'X-REQUEST-ID' => $requestId
        ]);
    }


    private function generateSignature($timestamp, $partnerId, $body)
    {
        $privateKey = file_get_contents(storage_path('app/keys/paylabs_private_key.pem'));
        openssl_sign($timestamp . $partnerId . $body, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    public function createEWallet(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'amount_raw' => 'required|numeric|min:10000',
            'ewallet_type' => 'required|in:DANABALANCE,SHOPEEBALANCE,LINKAJABALANCE,OVOBALANCE,GOPAYBALANCE',
            'phone_number' => [
                'nullable',
                'string',
                'min:10',
                'max:15',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->ewallet_type === 'OVOBALANCE' && empty($value)) {
                        $fail('Nomor HP wajib untuk pembayaran OVO.');
                    }
                },
                'regex:/^[0-9]+$/'
            ],
        ]);

        // Cek transaksi pending
        $pendingDeposit = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($pendingDeposit) {
            return redirect()->route('deposit.ewallet-payment', ['order_id' => $pendingDeposit->order_id])
                ->with('info', 'Anda sudah memiliki transaksi yang sedang berlangsung');
        }

        // Generate merchant trade no
        $merchantTradeNo = 'DEPO' . auth()->id() . time() . mt_rand(100, 999);
        $amount = (int) $request->amount_raw;
        $ewalletType = $request->ewallet_type;
        $phoneNumber = $request->phone_number;

        // Simpan ke database
        $depositId = DB::table('deposits')->insertGetId([
            'user_id' => auth()->id(),
            'order_id' => $merchantTradeNo,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => $ewalletType,
            'phone_number' => $phoneNumber,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $redirectUrl = route('deposit.ewallet.callback', ['order_id' => $merchantTradeNo]);
            Log::info('Attempting to create E-Wallet payment', [
                'amount' => $amount,
                'merchantTradeNo' => $merchantTradeNo,
                'ewalletType' => $ewalletType,
                'phoneNumber' => $phoneNumber
            ]);
            $response = $this->paylabsService->createEWalletPayment(
                $amount,
                'Deposit Saldo',
                $merchantTradeNo,
                $ewalletType,
                $redirectUrl,
                $phoneNumber
            );

            if (isset($response['errCode']) && $response['errCode'] === '0') {
                // Update database dengan response data
                $updateData = [
                    'platform_trade_no' => $response['platformTradeNo'] ?? null,
                    'updated_at' => now(),
                ];

                if (isset($response['expiredTime'])) {
                    $updateData['expired_time'] = Carbon::createFromFormat('YmdHis', $response['expiredTime'])
                        ->format('Y-m-d H:i:s');
                }

                if (isset($response['paymentActions'])) {
                    $paymentActions = $response['paymentActions'];
                    $updateData['payment_url'] = $paymentActions['pcPayUrl'] ?? $paymentActions['mobilePayUrl'] ?? null;
                    $updateData['push_pay'] = $paymentActions['pushPay'] ?? null;
                }

                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update($updateData);

                return redirect()->route('deposit.ewallet.payment', ['order_id' => $merchantTradeNo]);
            } else {
                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);

                $errorMessage = $response['errCodeDes'] ?? 'Gagal membuat transaksi e-wallet';
                return back()->with('error', 'Gagal membuat transaksi: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            DB::table('deposits')
                ->where('id', $depositId)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);

            Log::error('E-Wallet Deposit Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Show E-Wallet payment page
     */
    public function ewalletPayment($order_id)
    {
        $deposit = DB::table('deposits')
            ->where('order_id', $order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return redirect()->route('deposit.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        // Jika status sudah bukan pending, arahkan ke riwayat
        if (!in_array($deposit->status, ['pending', 'processing'])) {
            return redirect()->route('deposit.riwayat')
                ->with('info', 'Transaksi ini sudah selesai');
        }

        return view('deposit.ewallet-payment', [
            'deposit' => (object) [
                'id' => $deposit->id,
                'amount' => $deposit->amount,
                'order_id' => $deposit->order_id,
                'payment_method' => $deposit->payment_method,
                'payment_url' => $deposit->payment_url,
                'app_deeplink' => $deposit->app_deeplink,
                'push_pay' => $deposit->push_pay,
                'expired_time' => $deposit->expired_time,
                'platform_trade_no' => $deposit->platform_trade_no,
                'status' => $deposit->status
            ]
        ]);
    }

    /**
     * Handle E-Wallet callback from payment
     */
    public function ewalletCallback(Request $request, $order_id)
    {
        Log::info('E-Wallet callback received', [
            'order_id' => $order_id,
            'request_data' => $request->all()
        ]);

        $deposit = DB::table('deposits')
            ->where('order_id', $order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return redirect()->route('deposit.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        // Query status dari Paylabs untuk memastikan
        try {
            $statusResponse = $this->paylabsService->queryEWalletPayment(
                $deposit->order_id,
                $deposit->payment_method
            );

            if (isset($statusResponse['status'])) {
                $status = $statusResponse['status'];

                if ($status === '02') { // Success
                    return redirect()->route('deposit.riwayat')
                        ->with('success', 'Pembayaran berhasil! Saldo Anda telah ditambahkan.');
                } elseif ($status === '09') { // Failed
                    return redirect()->route('deposit.index')
                        ->with('error', 'Pembayaran gagal. Silakan coba lagi.');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error querying e-wallet status', [
                'order_id' => $order_id,
                'error' => $e->getMessage()
            ]);
        }

        // Jika belum ada status pasti, kembali ke halaman payment
        return redirect()->route('deposit.ewallet.payment', ['order_id' => $order_id])
            ->with('info', 'Menunggu konfirmasi pembayaran...');
    }

    /**
     * Handle E-Wallet notification from Paylabs
     */
    public function handleEWalletNotification(Request $request)
    {
        Log::info('Received Paylabs E-Wallet Notification', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Validasi signature
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $partnerId = $request->header('X-PARTNER-ID');
        $requestId = $request->header('X-REQUEST-ID');

        // Verifikasi signature
        $isValid = $this->paylabsService->verifyEWalletNotificationSignature(
            '/v2/ewallet/notify', // endpoint path
            $request->all(),
            $timestamp,
            $signature
        );

        if (!$isValid) {
            Log::error('Invalid E-Wallet notification signature', [
                'request_id' => $requestId,
                'timestamp' => $timestamp,
                'signature' => $signature
            ]);

            return response()->json([
                'errCode' => 'SIGNATURE_INVALID',
                'errCodeDes' => 'Invalid signature'
            ], 400);
        }

        // Proses notifikasi
        $notificationData = $request->all();
        $merchantTradeNo = $notificationData['merchantTradeNo'] ?? null;
        $status = $notificationData['status'] ?? null;
        $amount = $notificationData['amount'] ?? null;
        $platformTradeNo = $notificationData['platformTradeNo'] ?? null;

        try {
            DB::beginTransaction();

            // Cari transaksi berdasarkan merchantTradeNo
            $deposit = DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->first();

            if (!$deposit) {
                Log::error('E-Wallet deposit not found', ['merchant_trade_no' => $merchantTradeNo]);
                DB::rollBack();

                return $this->generateNotificationResponse(
                    $notificationData['merchantId'],
                    $notificationData['requestId'],
                    'TRANSACTION_NOT_FOUND',
                    'Transaction not found'
                );
            }

            // Update status transaksi berdasarkan notifikasi
            $newStatus = $this->mapPaylabsStatus($status);
            $updateData = [
                'status' => $newStatus,
                'updated_at' => now(),
                'platform_trade_no' => $platformTradeNo
            ];

            // Jika sukses, tambahkan data tambahan
            if ($status === '02') { // Success
                if (isset($notificationData['successTime'])) {
                    $updateData['success_time'] = Carbon::createFromFormat('YmdHis', $notificationData['successTime']);
                }

                // Simpan fee information jika ada
                if (isset($notificationData['transFeeAmount'])) {
                    $updateData['fee_amount'] = $notificationData['transFeeAmount'];
                }
                if (isset($notificationData['totalTransFee'])) {
                    $updateData['total_fee'] = $notificationData['totalTransFee'];
                }
            }

            DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->update($updateData);

            // Jika sukses, tambahkan saldo ke user
            if ($status === '02' && $newStatus === 'completed') {
                $user = DB::table('users')->where('id', $deposit->user_id)->first();
                if ($user) {
                    DB::table('users')
                        ->where('id', $deposit->user_id)
                        ->increment('balance', $deposit->amount);

                    // Catat riwayat transaksi
                    DB::table('balance_histories')->insert([
                        'user_id' => $deposit->user_id,
                        'amount' => $deposit->amount,
                        'type' => 'deposit',
                        'description' => 'Deposit via ' . $deposit->payment_method,
                        'reference_id' => $deposit->order_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            Log::info('Successfully processed E-Wallet notification', [
                'merchant_trade_no' => $merchantTradeNo,
                'status' => $status,
                'new_status' => $newStatus
            ]);

            // Berikan response sukses ke Paylabs
            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId']
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing E-Wallet notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId'],
                'PROCESSING_ERROR',
                'Error processing notification: ' . $e->getMessage()
            );
        }
    }

    public function cancelEWallet(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string'
        ]);

        $deposit = DB::table('deposits')
            ->where('order_id', $request->order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan'
            ], 404);
        }

        // Hanya bisa membatalkan transaksi yang masih pending/processing
        if (!in_array($deposit->status, ['pending', 'processing'])) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak dapat dibatalkan karena status sudah ' . $deposit->status
            ], 400);
        }

        try {
            // Untuk e-wallet, kita tidak bisa membatalkan di sisi Paylabs, jadi cukup update status
            DB::table('deposits')
                ->where('id', $deposit->id)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                    'cancel_reason' => 'Dibatalkan oleh pengguna'
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            Log::error('Error cancelling e-wallet transaction', [
                'order_id' => $request->order_id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createVA(Request $request)
    {

        // Validasi input
        $validated = $request->validate([
            'amount_raw' => 'required|numeric|min:10000',
            'va_type' => 'required|in:SinarmasVA,MaybankVA,DanamonVA,BNCVA,BCAVA,INAVA,BNIVA,PermataVA,MuamalatVA,BSIVA,BRIVA,MandiriVA,CIMBVA',
        ]);

        // Cek transaksi pending
        $pendingDeposit = DB::table('deposits')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($pendingDeposit) {
            return redirect()->route('deposit.va.payment', ['order_id' => $pendingDeposit->order_id])
                ->with('info', 'Anda sudah memiliki transaksi yang sedang berlangsung');
        }

        // Generate merchant trade no
        $merchantTradeNo = 'DEPO' . auth()->id() . time() . mt_rand(100, 999);
        $amount = (int) $request->amount_raw;
        $vaType = $request->va_type;

        // Simpan ke database
        $depositId = DB::table('deposits')->insertGetId([
            'user_id' => auth()->id(),
            'order_id' => $merchantTradeNo,
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => $vaType,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Log::info('Creating VA payment', [
                'user_id' => auth()->id(),
                'amount' => $amount,
                'merchantTradeNo' => $merchantTradeNo,
                'va_type' => $vaType
            ]);

            $response = $this->paylabsService->createVAPayment(
                $amount,
                'Deposit Saldo',
                $merchantTradeNo,
                $vaType,
                auth()->user()->name ?? 'user' // payer name
            );

            Log::info('VA Payment Response', $response);

            if (isset($response['errCode']) && $response['errCode'] === '0') {
                // Update database dengan response data
                $updateData = [
                    'platform_trade_no' => $response['platformTradeNo'] ?? null,
                    'va_code' => $response['vaCode'] ?? null,
                    'updated_at' => now(),
                ];

                if (isset($response['expiredTime'])) {
                    $updateData['expired_time'] = Carbon::createFromFormat('YmdHis', $response['expiredTime'])
                        ->format('Y-m-d H:i:s');
                }

                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update($updateData);

                return redirect()->route('deposit.va.payment', ['order_id' => $merchantTradeNo]);
            } else {
                DB::table('deposits')
                    ->where('id', $depositId)
                    ->update([
                        'status' => 'failed',
                        'updated_at' => now(),
                    ]);

                $errorMessage = $response['errCodeDes'] ?? 'Gagal membuat Virtual Account';
                return back()->with('error', 'Gagal membuat transaksi: ' . $errorMessage);
            }
        } catch (\Exception $e) {
            DB::table('deposits')
                ->where('id', $depositId)
                ->update([
                    'status' => 'failed',
                    'updated_at' => now(),
                ]);

            Log::error('VA Deposit Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Show VA payment page
     */
    public function vaPayment($order_id)
    {
        $deposit = DB::table('deposits')
            ->where('order_id', $order_id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$deposit) {
            return redirect()->route('deposit.index')
                ->with('error', 'Transaksi tidak ditemukan');
        }

        // Jika status sudah bukan pending, arahkan ke riwayat
        if (!in_array($deposit->status, ['pending', 'processing'])) {
            return redirect()->route('deposit.riwayat')
                ->with('info', 'Transaksi ini sudah selesai');
        }

        return view('deposit.va-payment', [
            'deposit' => (object) [
                'id' => $deposit->id,
                'amount' => $deposit->amount,
                'order_id' => $deposit->order_id,
                'payment_method' => $deposit->payment_method,
                'va_code' => $deposit->va_code,
                'expired_time' => $deposit->expired_time,
                'platform_trade_no' => $deposit->platform_trade_no,
                'status' => $deposit->status
            ]
        ]);
    }

    /**
     * Handle VA notification from Paylabs
     */
    public function handleVANotification(Request $request)
    {
        Log::info('Received Paylabs VA Notification', [
            'headers' => $request->headers->all(),
            'body' => $request->all()
        ]);

        // Validasi signature
        $timestamp = $request->header('X-TIMESTAMP');
        $signature = $request->header('X-SIGNATURE');
        $partnerId = $request->header('X-PARTNER-ID');
        $requestId = $request->header('X-REQUEST-ID');

        // Verifikasi signature
        $isValid = $this->paylabsService->verifyPaylabsSignature(
            '/v2/va/notify',
            $request->all(),
            $timestamp,
            $signature
        );

        if (!$isValid) {
            Log::error('Invalid VA notification signature', [
                'request_id' => $requestId,
                'timestamp' => $timestamp,
                'signature' => $signature
            ]);

            return response()->json([
                'errCode' => 'SIGNATURE_INVALID',
                'errCodeDes' => 'Invalid signature'
            ], 400);
        }

        // Proses notifikasi
        $notificationData = $request->all();
        $merchantTradeNo = $notificationData['merchantTradeNo'] ?? null;
        $status = $notificationData['status'] ?? null;
        $amount = $notificationData['amount'] ?? null;
        $platformTradeNo = $notificationData['platformTradeNo'] ?? null;

        try {
            DB::beginTransaction();

            // Cari transaksi berdasarkan merchantTradeNo
            $deposit = DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->first();

            if (!$deposit) {
                Log::error('VA deposit not found', ['merchant_trade_no' => $merchantTradeNo]);
                DB::rollBack();

                return $this->generateNotificationResponse(
                    $notificationData['merchantId'],
                    $notificationData['requestId'],
                    'TRANSACTION_NOT_FOUND',
                    'Transaction not found'
                );
            }

            // Update status transaksi berdasarkan notifikasi
            $newStatus = $this->mapPaylabsStatus($status);
            $updateData = [
                'status' => $newStatus,
                'updated_at' => now(),
                'platform_trade_no' => $platformTradeNo
            ];

            // Jika sukses, tambahkan data tambahan
            if ($status === '02') { // Success
                if (isset($notificationData['successTime'])) {
                    $updateData['success_time'] = Carbon::createFromFormat('YmdHis', $notificationData['successTime']);
                }

                // Simpan fee information jika ada
                if (isset($notificationData['transFeeAmount'])) {
                    $updateData['fee_amount'] = $notificationData['transFeeAmount'];
                }
                if (isset($notificationData['totalTransFee'])) {
                    $updateData['total_fee'] = $notificationData['totalTransFee'];
                }
            }

            DB::table('deposits')
                ->where('order_id', $merchantTradeNo)
                ->update($updateData);

            // Jika sukses, tambahkan saldo ke user
            if ($status === '02' && $newStatus === 'completed') {
                $user = DB::table('users')->where('id', $deposit->user_id)->first();
                if ($user) {
                    DB::table('users')
                        ->where('id', $deposit->user_id)
                        ->increment('balance', $deposit->amount);

                    // Catat riwayat transaksi
                    DB::table('balance_histories')->insert([
                        'user_id' => $deposit->user_id,
                        'amount' => $deposit->amount,
                        'type' => 'deposit',
                        'description' => 'Deposit via ' . $deposit->payment_method,
                        'reference_id' => $deposit->order_id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            Log::info('Successfully processed VA notification', [
                'merchant_trade_no' => $merchantTradeNo,
                'status' => $status,
                'new_status' => $newStatus
            ]);

            // Berikan response sukses ke Paylabs
            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId']
            );
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing VA notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->generateNotificationResponse(
                $notificationData['merchantId'],
                $notificationData['requestId'],
                'PROCESSING_ERROR',
                'Error processing notification: ' . $e->getMessage()
            );
        }
    }

    public function checkVAStatus(Request $request)
    {
        try {
            $order_id = $request->input('order_id');

            if (!$order_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order ID is required'
                ], 400);
            }

            $deposit = DB::table('deposits')
                ->where('order_id', $order_id)
                ->where('user_id', auth()->id())
                ->first();

            if (!$deposit) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }

            // If status is already completed or failed, return immediately
            if (in_array($deposit->status, ['completed', 'failed'])) {
                return response()->json([
                    'status' => $deposit->status,
                    'message' => $this->getStatusMessage($deposit->status),
                    'expired_time' => $deposit->expired_time
                ]);
            }

            // Query status from Paylabs
            $statusResponse = $this->paylabsService->queryVAPayment(
                $deposit->order_id,
                $deposit->payment_method
            );

            if (isset($statusResponse['status'])) {
                $paylabsStatus = $statusResponse['status'];
                $systemStatus = $this->mapPaylabsStatus($paylabsStatus);

                // Update local status if different
                if ($deposit->status !== $systemStatus) {
                    $updateData = [
                        'status' => $systemStatus,
                        'updated_at' => now()
                    ];

                    // If payment completed, add success time if available
                    if ($systemStatus === 'completed' && isset($statusResponse['successTime'])) {
                        $updateData['success_time'] = Carbon::createFromFormat('YmdHis', $statusResponse['successTime']);
                    }

                    DB::table('deposits')
                        ->where('id', $deposit->id)
                        ->update($updateData);

                    // If payment completed, update user balance
                    if ($systemStatus === 'completed') {
                        DB::table('users')
                            ->where('id', $deposit->user_id)
                            ->increment('balance', $deposit->amount);

                        // Record balance history
                        DB::table('balance_histories')->insert([
                            'user_id' => $deposit->user_id,
                            'amount' => $deposit->amount,
                            'type' => 'deposit',
                            'description' => 'Deposit via ' . $deposit->payment_method,
                            'reference_id' => $deposit->order_id,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }

                return response()->json([
                    'status' => $systemStatus,
                    'message' => $this->getStatusMessage($systemStatus),
                    'expired_time' => $deposit->expired_time
                ]);
            }

            return response()->json([
                'status' => $deposit->status,
                'message' => $this->getStatusMessage($deposit->status),
                'expired_time' => $deposit->expired_time
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking VA status', [
                'order_id' => $order_id ?? null,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengecek status'
            ], 500);
        }
    }

    private function getStatusMessage($status)
    {
        $messages = [
            'pending' => 'Menunggu pembayaran',
            'processing' => 'Pembayaran sedang diproses',
            'completed' => 'Pembayaran berhasil',
            'failed' => 'Pembayaran gagal'
        ];

        return $messages[$status] ?? 'Status tidak diketahui';
    }
}
