@extends('layouts.app')

@section('content')
    <div class="payment-container">
        <div class="card">
            <h2>
                <i class="fas fa-wallet"></i>
                Pembayaran {{ $deposit->payment_method ?? $deposit->paymentType }}
            </h2>

            <div class="payment-info">
                <div class="amount-display">
                    <span class="label">Total Pembayaran</span>
                    <span class="amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</span>
                </div>

                <div class="order-info">
                    <small class="text-muted">Order ID: {{ $deposit->order_id }}</small>
                </div>

                <div class="ewallet-info">
                    <div class="ewallet-badge">
                        @switch($deposit->payment_method)
                            @case('DANABALANCE')
                                <img src="{{ asset('images/dana.svg') }}" alt="DANA" class="ewallet-logo">
                                <span>DANA</span>
                            @break

                            @case('SHOPEEBALANCE')
                                <img src="{{ asset('images/ShopeePay.svg') }}" alt="ShopeePay" class="ewallet-logo">
                                <span>ShopeePay</span>
                            @break

                            @case('LINKAJABALANCE')
                                <img src="{{ asset('images/linkaja.svg') }}" alt="LinkAja" class="ewallet-logo">
                                <span>LinkAja</span>
                            @break

                            @case('OVOBALANCE')
                                <img src="{{ asset('images/OVO.svg') }}" alt="OVO" class="ewallet-logo">
                                <span>OVO</span>
                            @break

                            @case('GOPAYBALANCE')
                                <img src="{{ asset('images/gopay.svg') }}" alt="GoPay" class="ewallet-logo">
                                <span>GoPay</span>
                            @break

                            @default
                                <span>{{ $deposit->payment_method }}</span>
                        @endswitch
                    </div>
                </div>
            </div>

            @if ($deposit->payment_url || $deposit->app_deeplink)
                <div class="payment-section">
                    <div class="payment-instructions">
                        <h4>Cara Pembayaran:</h4>
                        <ol>
                            @if ($deposit->payment_method === 'OVOBALANCE')
                                <li>Klik tombol "Buka Aplikasi OVO" di bawah</li>
                                <li>Masukkan PIN OVO Anda</li>
                                <li>Konfirmasi pembayaran</li>
                            @else
                                <li>Klik tombol "Lanjutkan Pembayaran" di bawah</li>
                                <li>Anda akan diarahkan ke aplikasi {{ $deposit->payment_method }}</li>
                                <li>Login ke akun Anda jika diminta</li>
                                <li>Pastikan nominal sudah sesuai</li>
                                <li>Konfirmasi pembayaran</li>
                            @endif
                        </ol>
                    </div>

                    @if ($deposit->expired_time)
                        <div class="countdown-section">
                            <span class="countdown-label">Sisa Waktu:</span>
                            <span id="countdown" class="countdown-timer">Loading...</span>
                        </div>
                    @endif

                    <div class="payment-buttons">
                        @if ($deposit->app_deeplink)
                            <a href="{{ $deposit->app_deeplink }}" class="btn btn-primary payment-btn">
                                <i class="fas fa-mobile-alt"></i>
                                Buka Aplikasi
                            </a>
                        @endif

                        @if ($deposit->payment_url)
                            <a href="{{ $deposit->payment_url }}" target="_blank" class="btn btn-primary payment-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Lanjutkan Pembayaran
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="error-section">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>URL pembayaran tidak tersedia. Silakan coba lagi.</p>
                </div>
            @endif

            <div class="action-buttons">
                <button type="button" class="btn btn-secondary" onclick="checkPaymentStatus()">
                    <i class="fas fa-sync-alt"></i> Cek Status Pembayaran
                </button>

                <button type="button" class="btn btn-outline-danger" onclick="cancelPayment()">
                    <i class="fas fa-times"></i> Batalkan Pembayaran
                </button>
            </div>
        </div>

        <div class="help-section">
            <h4>Butuh Bantuan?</h4>
            <p>Jika mengalami kendala, silakan hubungi customer service kami atau coba refresh halaman ini untuk melihat
                status terbaru.</p>
        </div>
    </div>

    <style>
        .payment-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 1rem;
        }

        .card {
            background-color: var(--white);
            border-radius: var(--rounded-md);
            padding: 2rem;
            box-shadow: var(--shadow-md);
            margin-bottom: 1.5rem;
        }

        h2 {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            color: var(--primary-dark);
        }

        .payment-info {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid var(--gray);
        }

        .amount-display {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .amount-display .label {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .amount-display .amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
        }

        .order-info {
            margin-bottom: 1rem;
        }

        .ewallet-info {
            margin-top: 1rem;
        }

        .ewallet-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--secondary);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .ewallet-logo {
            width: 24px;
            height: 24px;
            object-fit: contain;
        }

        .payment-section {
            margin-bottom: 2rem;
        }

        .payment-instructions {
            text-align: left;
            background: var(--secondary);
            padding: 1.5rem;
            border-radius: var(--rounded-md);
            margin-bottom: 1.5rem;
        }

        .payment-instructions h4 {
            margin-bottom: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
        }

        .payment-instructions ol {
            margin: 0;
            padding-left: 1.5rem;
        }

        .payment-instructions li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        .countdown-section {
            background: var(--accent);
            color: white;
            padding: 1rem;
            border-radius: var(--rounded-md);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .countdown-label {
            font-size: 0.875rem;
            margin-right: 0.5rem;
        }

        .countdown-timer {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .payment-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .payment-btn {
            padding: 1rem 2rem;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: white;
            text-decoration: none;
            border-radius: var(--rounded-md);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            min-width: 200px;
            justify-content: center;
        }

        .payment-btn:hover {
            background: var(--primary-dark);
            color: white;
            text-decoration: none;
        }

        .error-section {
            text-align: center;
            padding: 2rem;
            color: #ef4444;
            margin-bottom: 1.5rem;
        }

        .error-section i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .action-buttons .btn {
            flex: 1;
            min-width: 200px;
            padding: 0.875rem 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border-radius: var(--rounded-md);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-secondary {
            background: var(--primary);
            color: white;
        }

        .btn-secondary:hover {
            background: var(--primary-dark);
        }

        .btn-outline-danger {
            background: transparent;
            color: #ef4444;
            border: 1px solid #ef4444;
        }

        .btn-outline-danger:hover {
            background: #ef4444;
            color: white;
        }

        .help-section {
            background: var(--secondary);
            padding: 1.5rem;
            border-radius: var(--rounded-md);
            text-align: center;
        }

        .help-section h4 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        @media (max-width: 480px) {
            .payment-container {
                padding: 0.5rem;
            }

            .card {
                padding: 1.5rem;
            }

            .amount-display .amount {
                font-size: 1.5rem;
            }

            .payment-buttons {
                flex-direction: column;
            }

            .payment-btn {
                min-width: auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-buttons .btn {
                min-width: auto;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
            fetch('{{ route('deposit.checkPending') }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.has_pending) {
                        clearInterval(checkInterval);

                        Swal.fire({
                            icon: 'success',
                            title: 'Pembayaran Berhasil!',
                            text: 'Terima kasih, pembayaran Anda telah berhasil diproses.',
                            confirmButtonColor: '#4f46e5',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '{{ route('deposit.riwayat') }}';
                        });
                    } else if (showAlert) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Status Pembayaran',
                            text: 'Pembayaran masih dalam proses. Silakan tunggu sebentar.',
                            confirmButtonColor: '#4f46e5'
                        });
                    }
                })
                .catch(error => {
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
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                checkPaymentStatus(false);
            }
        });

        // Check payment status when coming back from payment app
        window.addEventListener('focus', function() {
            setTimeout(() => {
                checkPaymentStatus(false);
            }, 2000);
        });
    </script>
@endsection
