@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="investment-card-modern">


            <div class="investment-description-modern">
                @if (isset($error))
                    <div class="alert alert-danger"
                        style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                        <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #b91c1c;">Error!</h4>
                        <p style="margin-bottom: 0;">{{ $error }}</p>
                    </div>
                @else
                    @if ($status == 'success')
                        <div class="alert alert-success"
                            style="background: #dcfce7; color: #166534; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #166534;">
                                <i class="fas fa-check-circle"></i> Deposit Berhasil!
                            </h4>
                            <p style="margin-bottom: 0.5rem;">Pembayaran untuk Order ID:
                                <strong>{{ $order_id }}</strong> telah berhasil diproses.
                            </p>
                            <hr style="border-color: rgba(22, 101, 52, 0.2); margin: 0.75rem 0;">
                            <p class="mb-0">Saldo sebesar <strong class="total-earning">Rp
                                    {{ number_format($amount, 0, ',', '.') }}</strong> telah ditambahkan ke akun Anda.</p>
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('deposit.riwayat') }}" class="btn-invest" style="text-decoration: none;">
                                <i class="fas fa-home"></i> Kembali ke Dashboard
                            </a>
                        </div>
                    @elseif($status == 'pending')
                        <div class="alert alert-warning"
                            style="background: #fef3c7; color: #854d0e; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #854d0e;">
                                <i class="fas fa-clock"></i> Menunggu Pembayaran
                            </h4>
                            <p style="margin-bottom: 0.5rem;">Pembayaran untuk Order ID:
                                <strong>{{ $order_id }}</strong> masih menunggu proses pembayaran.
                            </p>
                            <hr style="border-color: rgba(133, 77, 14, 0.2); margin: 0.75rem 0;">
                            <p class="mb-0">Silakan selesaikan pembayaran Anda.</p>
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('deposit.riwayat') }}" class="btn-invest" style="text-decoration: none;">
                                <i class="fas fa-arrow-left"></i> Kembali ke Riwayat Deposit
                            </a>
                        </div>
                    @else
                        <div class="alert alert-danger"
                            style="background: #fee2e2; color: #b91c1c; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
                            <h4 style="font-size: 1.125rem; font-weight: 600; margin-bottom: 0.5rem; color: #b91c1c;">
                                <i class="fas fa-times-circle"></i> Pembayaran Gagal
                            </h4>
                            <p style="margin-bottom: 0.5rem;">Pembayaran untuk Order ID:
                                <strong>{{ $order_id }}</strong> tidak berhasil diproses.
                            </p>
                            <hr style="border-color: rgba(185, 28, 28, 0.2); margin: 0.75rem 0;">
                            <p class="mb-0">Silakan coba lagi atau hubungi admin.</p>
                        </div>
                        <div class="card-actions">
                            <a href="{{ route('deposit.riwayat') }}" class="btn-invest" style="text-decoration: none;">
                                <i class="fas fa-redo"></i> Coba Deposit Lagi
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
