<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\DepositController as AdminDepositController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\Admin\SettingMidtransController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VipController;
use App\Http\Controllers\Admin\WithdrawController as AdminWithdrawController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProdukController as ControllersProdukController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\WithdrawController;
use App\Models\ApiSupplier;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
});
Route::get('/dashboard', function () {
    if (auth()->user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('beranda.dashboard');
    }
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboard::class, 'index'])->name('admin.dashboard');
});

// User routes
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/saya', [DashboardController::class, 'index'])->name('user.dashboard');
});


Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::resource('api-suppliers', ApiSupplier::class);

    Route::prefix('user')->as('admin.user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/load', [UserController::class, 'load'])->name('load');
        Route::post('/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
    });

    Route::prefix('admin')->as('admin.admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('/load', [AdminController::class, 'load'])->name('load');
        Route::get('/create', [AdminController::class, 'create'])->name('create');
        Route::post('/', [AdminController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('produk')->as('admin.produk.')->group(function () {
        Route::get('/', [ProdukController::class, 'index'])->name('index');
        Route::post('/load', [ProdukController::class, 'load'])->name('load');
        Route::get('/create', [ProdukController::class, 'create'])->name('create');
        Route::post('/', [ProdukController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ProdukController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ProdukController::class, 'update'])->name('update');
        Route::delete('/{id}', [ProdukController::class, 'destroy'])->name('destroy');
        Route::get('/getkategori', [ProdukController::class, 'getKategori'])->name('getkategori');
        Route::get('/getvip', [ProdukController::class, 'getvip'])->name('getvip');
    });

    Route::prefix('withdraw')->as('admin.withdraw.')->group(function () {
        Route::get('/', [AdminWithdrawController::class, 'index'])->name('index');
        Route::post('/load', [AdminWithdrawController::class, 'load'])->name('load');
        Route::post('/complete/{id}', [AdminWithdrawController::class, 'complete'])->name('complete');
    });

    Route::prefix('deposit')->as('admin.deposit.')->group(function () {
        Route::get('/', [AdminDepositController::class, 'index'])->name('index');
        Route::post('/load', [AdminDepositController::class, 'load'])->name('load');
        Route::post('/complete/{id}', [AdminDepositController::class, 'complete'])->name('complete');
    });

    Route::prefix('kategori')->as('admin.kategori.')->group(function () {
        Route::get('/', [KategoriController::class, 'index'])->name('index');
        Route::post('/load', [KategoriController::class, 'load'])->name('load');
        Route::get('/create', [KategoriController::class, 'create'])->name('create');
        Route::post('/', [KategoriController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [KategoriController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KategoriController::class, 'update'])->name('update');
        Route::delete('/{id}', [KategoriController::class, 'destroy'])->name('destroy');
    });
    Route::prefix('vip')->as('admin.vip.')->group(function () {
        Route::get('/', [VipController::class, 'index'])->name('index');
        Route::post('/load', [VipController::class, 'load'])->name('load');
        Route::get('/create', [VipController::class, 'create'])->name('create');
        Route::post('/', [VipController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [VipController::class, 'edit'])->name('edit');
        Route::put('/{id}', [VipController::class, 'update'])->name('update');
        Route::delete('/{id}', [VipController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('midtrans_settings')->as('admin.midtrans_settings.')->group(function () {
        Route::get('/', [SettingMidtransController::class, 'index'])->name('index');
        Route::post('/load', [SettingMidtransController::class, 'load'])->name('load');
        Route::get('/create', [SettingMidtransController::class, 'create'])->name('create');
        Route::post('/', [SettingMidtransController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SettingMidtransController::class, 'edit'])->name('edit');
        Route::put('/{id}', [SettingMidtransController::class, 'update'])->name('update');
        Route::delete('/{id}', [SettingMidtransController::class, 'destroy'])->name('destroy');
        Route::get('/get-types', [SettingMidtransController::class, 'getTypes'])->name('getTypes');
    });
});



Route::middleware('auth')->group(function () {
    Route::get('/Home', [ControllersProdukController::class, 'index'])->name('beranda.dashboard');
    Route::get('/produk', [ControllersProdukController::class, 'index'])->name('produk.index');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/ubah-password', [ProfileController::class, 'ubah_password'])->name('profile.ubahpassword');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/ubah_password', [ProfileController::class, 'updatePassword'])->name('profile.ubah_password');
    Route::post('/invest', [ControllersProdukController::class, 'invest'])->name('invest');
    Route::get('/pesanan', [TransaksiController::class, 'index'])->name('pesanan');
    Route::post('/pesanan/claim/{id}', [TransaksiController::class, 'claim'])->name('claim');
    Route::post('/transactions/{id}/claim', [TransaksiController::class, 'claim'])->name('claim');

    // Get transaction statistics
    Route::get('/transactions/stats', [TransaksiController::class, 'getStats'])->name('transactions.stats');

    // Get claim history for specific transaction
    Route::get('/transactions/{id}/claim-history', [TransaksiController::class, 'getClaimHistory'])->name('transactions.claim-history');

    Route::get('/tentang-kami', [DashboardController::class, 'tentang'])->name('tentang.index');
    Route::get('/vip-rules', [DashboardController::class, 'rules'])->name('vip.rules');
    Route::get('/referral', [ReferralController::class, 'index'])->name('referral.index');
    Route::get('/tim', [ReferralController::class, 'bonus'])->name('bonus.index');
    Route::post('/claim-bonus', [ReferralController::class, 'claimBonus'])->name('claim.bonus');

    Route::prefix('deposit')->as('deposit.')->group(function () {
        Route::get('/', [DepositController::class, 'index'])->name('index');
        Route::get('/riwayat', [DepositController::class, 'riwayat'])->name('riwayat');
        Route::get('/deposit/continue/{order_id}', [DepositController::class, 'continuePayment'])->name('continue');
        Route::post('/deposit/cancel', [DepositController::class, 'cancelTransaction'])
            ->name('cancelqris');
        Route::get('/check-pending', [DepositController::class, 'checkPendingPayment'])->name('checkPending');
        // routes/web.php
        Route::get('/payment/{order_id}', [DepositController::class, 'payment'])->name('payment');

        Route::post('/deposit/qris', [DepositController::class, 'createQRIS'])->name('create_qris');
        Route::post('/deposit/create-va', [DepositController::class, 'createVA'])->name('create_va');
        Route::get('/deposit/va/payment/{order_id}', [DepositController::class, 'vaPayment'])->name('va.payment');



        Route::post('/deposit/ewallet/create', [DepositController::class, 'createEWallet'])->name('create_ewallet');

        // E-Wallet payment page
        Route::get('/deposit/ewallet/payment/{order_id}', [DepositController::class, 'ewalletPayment'])->name('ewallet.payment');

        // E-Wallet callback (success/failure redirect)
        Route::get('/deposit/ewallet/callback/{order_id}', [DepositController::class, 'ewalletCallback'])->name('ewallet.callback');
        Route::post('/deposit/cancel-ewallet', [DepositController::class, 'cancelEWallet'])
            ->name('cancel-ewallet');
        Route::post('/deposit/check-status', [DepositController::class, 'checkVAStatus'])->name('check-status');
    });

    Route::prefix('bank')->as('bank.')->group(function () {
        Route::get('/', [BankController::class, 'index'])->name('index');
        Route::post('/', [BankController::class, 'store'])->name('store');
        Route::delete('/{bank}', [BankController::class, 'destroy'])->name('destroy');
    });


    Route::prefix('forum')->as('forum.')->group(function () {
        Route::get('/', [ForumController::class, 'index'])->name('index');
        Route::post('/', [ForumController::class, 'store'])->name('store');
        Route::delete('/{id}', [ForumController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('withdrawal')->as('withdrawal.')->group(function () {
        Route::get('/', [WithdrawController::class, 'index'])->name('index');
        Route::post('/', [WithdrawController::class, 'store'])->name('store');
        Route::get('/history', [WithdrawController::class, 'history'])->name('history');
    });
});
Route::post('/v2/qris/notify', [DepositController::class, 'handleQrisNotification'])
    ->name('paylabs.notification');
Route::post('/v2/ewallet/notify', [DepositController::class, 'handleEWalletNotification'])->name('ewallet.notify');
Route::post('/v2/va/notify', [DepositController::class, 'handleVANotification'])->name('deposit.va.notify');

require __DIR__ . '/auth.php';
