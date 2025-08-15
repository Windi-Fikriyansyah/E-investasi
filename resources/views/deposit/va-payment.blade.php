@extends('layouts.app')

@section('content')
    <div class="virtual-account-container">
        <div class="payment-card">
            <div class="payment-header">
                <i class="fas fa-university"></i>
                <h2>Pembayaran Virtual Account</h2>
            </div>

            <div class="payment-body">
                <div class="payment-details">
                    <div class="detail-row">
                        <span class="detail-label">Bank Tujuan</span>
                        <span class="detail-value bank-name">
                            @php
                                $vaTypes = app('App\Services\PaylabsService')->getSupportedVATypes();
                                $bankCode = $vaTypes[$deposit->payment_method]['bank_code'] ?? 'default';
                                $bankName = $vaTypes[$deposit->payment_method]['name'] ?? $deposit->payment_method;
                            @endphp
                            {{ $bankName }}
                        </span>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Nomor Virtual Account</span>
                        <div class="va-number-container">
                            <span class="detail-value va-number">{{ $deposit->va_code }}</span>
                            <button class="copy-btn" data-clipboard-text="{{ $deposit->va_code }}">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="detail-row">
                        <span class="detail-label">Jumlah Pembayaran</span>
                        <span class="detail-value amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                    </div>

                    @if ($deposit->expired_time)
                        <div class="countdown-section">
                            <span class="countdown-label">Sisa Waktu:</span>
                            <span id="countdown" class="countdown-timer">Loading...</span>
                        </div>
                    @endif
                </div>

                <div class="payment-instructions">
                    <h3 class="instructions-title">
                        <i class="fas fa-info-circle"></i>
                        Cara Pembayaran
                    </h3>
                    <ol class="steps-list">
                        <li>Buka aplikasi mobile banking atau internet banking bank {{ $bankName }}</li>
                        <li>Pilih menu <strong>Transfer</strong> atau <strong>Pembayaran</strong></li>
                        <li>Masukkan nomor Virtual Account: <strong>{{ $deposit->va_code }}</strong></li>
                        <li>Masukkan jumlah yang harus dibayar: <strong>Rp
                                {{ number_format($deposit->amount, 0, ',', '.') }}</strong></li>
                        <li>Ikuti instruksi selanjutnya untuk menyelesaikan pembayaran</li>
                    </ol>
                </div>

                <div class="payment-status">
                    <div class="status-badge {{ $deposit->status }}">
                        @if ($deposit->status === 'pending')
                            <i class="fas fa-clock"></i> Menunggu Pembayaran
                        @elseif($deposit->status === 'processing')
                            <i class="fas fa-sync-alt fa-spin"></i> Sedang Diproses
                        @elseif($deposit->status === 'completed')
                            <i class="fas fa-check-circle"></i> Pembayaran Berhasil
                        @else
                            <i class="fas fa-times-circle"></i> Gagal
                        @endif
                    </div>
                </div>


            </div>
        </div>
    </div>

    <style>
        .virtual-account-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
        }

        .payment-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid rgba(37, 99, 235, 0.1);
        }

        .payment-header {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .payment-header i {
            font-size: 1.5rem;
        }

        .payment-header h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: -0.025em;
        }

        .payment-body {
            padding: 1.5rem;
        }

        .payment-details {
            margin-bottom: 1.5rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(224, 232, 240, 0.8);
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
        }

        .detail-value {
            font-size: 0.925rem;
            font-weight: 600;
            color: #1e293b;
            text-align: right;
        }

        .bank-name {
            color: #2563eb;
            font-weight: 600;
        }

        .va-number-container {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .va-number {
            font-family: monospace;
            font-size: 1rem;
            letter-spacing: 1px;
            color: #2563eb;
        }

        .copy-btn {
            background: rgba(37, 99, 235, 0.1);
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            color: #2563eb;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .copy-btn:hover {
            background: rgba(37, 99, 235, 0.2);
            transform: translateY(-1px);
        }

        .amount {
            color: #2563eb;
            font-size: 1.1rem;
            font-weight: 700;
        }

        .expiry {
            color: #ef4444;
            font-weight: 600;
        }

        .payment-instructions {
            background: rgba(240, 247, 255, 0.5);
            border-radius: 0.75rem;
            padding: 1.25rem;
            margin: 1.5rem 0;
            border: 1px solid rgba(224, 232, 240, 0.8);
        }

        .instructions-title {
            font-size: 1rem;
            color: #1e293b;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .instructions-title i {
            color: #2563eb;
        }

        .steps-list {
            padding-left: 1.25rem;
            font-size: 0.875rem;
            color: #475569;
        }

        .steps-list li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .steps-list strong {
            color: #1e293b;
            font-weight: 600;
        }

        .payment-status {
            margin: 1.5rem 0;
            text-align: center;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .status-badge i {
            font-size: 1rem;
        }

        .status-badge.pending {
            background: rgba(251, 191, 36, 0.1);
            color: #d97706;
        }

        .status-badge.processing {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
        }

        .status-badge.completed {
            background: rgba(16, 185, 129, 0.1);
            color: #047857;
        }

        .status-badge.failed {
            background: rgba(239, 68, 68, 0.1);
            color: #b91c1c;
        }

        .payment-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .payment-actions .btn {
            flex: 1;
            padding: 0.875rem 1rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        }

        .btn-secondary {
            background: white;
            color: #2563eb;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 480px) {
            .payment-header {
                padding: 1.25rem;
            }

            .payment-body {
                padding: 1.25rem;
            }

            .payment-actions {
                flex-direction: column;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
    <script>
        // Countdown timer
        @if ($deposit->expired_time)
            let expiredTime = '{{ $deposit->expired_time }}';
            if (expiredTime) {
                let expiredDate = new Date(expiredTime);

                function updateCountdown() {
                    let now = new Date();
                    let diff = expiredDate - now;

                    if (diff <= 0) {
                        document.getElementById('countdown').textContent = 'Waktu habis';
                        checkPaymentStatus();
                        return;
                    }

                    // Calculate hours, minutes, and seconds
                    let hours = Math.floor(diff / 3600000);
                    let minutes = Math.floor((diff % 3600000) / 60000);
                    let seconds = Math.floor((diff % 60000) / 1000);

                    // Format as HH:MM:SS
                    let formattedHours = hours.toString().padStart(2, '0');
                    let formattedMinutes = minutes.toString().padStart(2, '0');
                    let formattedSeconds = seconds.toString().padStart(2, '0');

                    document.getElementById('countdown').textContent =
                        formattedHours + ':' + formattedMinutes + ':' + formattedSeconds;
                }

                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
        @endif
        // Auto check payment status
        let checkInterval = setInterval(function() {
            checkPaymentStatus(false);
        }, 15000); // Check every 15 seconds

        function checkPaymentStatus(showAlert = true) {
            fetch('{{ route('deposit.check-status') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        order_id: '{{ $deposit->order_id }}'
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'completed') {
                        clearInterval(checkInterval);
                        // Update UI immediately
                        document.querySelector('.status-badge').className = 'status-badge completed';
                        document.querySelector('.status-badge').innerHTML =
                            '<i class="fas fa-check-circle"></i> Pembayaran Berhasil';

                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: 'Terima kasih, pembayaran Anda telah berhasil diproses.',
                            confirmButtonColor: '#4f46e5',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('deposit.riwayat') }}';
                        });
                    } else if (data.status === 'failed') {
                        clearInterval(checkInterval);
                        // Update UI immediately
                        document.querySelector('.status-badge').className = 'status-badge failed';
                        document.querySelector('.status-badge').innerHTML =
                            '<i class="fas fa-times-circle"></i> Pembayaran Gagal';

                        if (showAlert) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Pembayaran Gagal',
                                text: data.message || 'Pembayaran tidak berhasil diproses',
                                confirmButtonColor: '#4f46e5'
                            });
                        }
                    } else if (showAlert) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Status Pembayaran',
                            text: data.message || 'Pembayaran masih dalam proses. Silakan tunggu sebentar.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }

                    // Update countdown if expired
                    if (data.expired_time && new Date(data.expired_time) < new Date()) {
                        document.getElementById('countdown').textContent = 'Waktu habis';
                    }
                })
                .catch(error => {
                    console.error('Error checking payment status:', error);
                    if (showAlert) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Cek Status',
                            text: 'Terjadi kesalahan saat mengecek status pembayaran.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                });
        }

        function cancelPayment() {
            Swal.fire({
                title: 'Batalkan Pembayaran?',
                text: 'Apakah Anda yakin ingin membatalkan transaksi ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Batalkan',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Panggil API untuk membatalkan transaksi
                    fetch('{{ route('deposit.cancel-ewallet') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify({
                                order_id: '{{ $deposit->order_id }}'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                clearInterval(checkInterval);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Pembayaran Dibatalkan',
                                    text: data.message,
                                    confirmButtonColor: '#4f46e5'
                                }).then(() => {
                                    window.location.href = '{{ route('deposit.index') }}';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Membatalkan',
                                    text: data.message,
                                    confirmButtonColor: '#4f46e5'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat membatalkan transaksi',
                                confirmButtonColor: '#4f46e5'
                            });
                        });
                }
            });
        }
        // Handle page visibility change
        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Check immediately when page becomes visible
                checkPaymentStatus(false);
                // Then check again after 5 seconds in case of delays
                setTimeout(() => checkPaymentStatus(false), 5000);
            }
        });

        // Check payment status when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Initial check after 10 seconds
            setTimeout(() => checkPaymentStatus(false), 10000);
        });
    </script>
@endsection
