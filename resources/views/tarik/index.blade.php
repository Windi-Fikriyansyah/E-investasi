@extends('layouts.app')

@section('content')
    <div class="withdrawal-container">
        <div class="card">


            @if (count($banks) > 0)
                <div class="bank-info">
                    <h3 class="section-title">Rekening Tujuan</h3>
                    <div class="bank-card">
                        <div class="bank-name">
                            <i class="fas fa-university"></i> {{ $banks[0]->nama_bank }}
                        </div>
                        <div class="bank-number">
                            <i class="fas fa-credit-card"></i> {{ $banks[0]->no_rekening }}
                        </div>
                        <div class="bank-owner">
                            <i class="fas fa-user"></i> {{ $banks[0]->nama_pemilik }}
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Bank belum ada ditambahkan.
                    <a href="{{ route('bank.index') }}" class="alert-link">Tambahkan rekening bank</a>
                </div>
            @endif
        </div>

        <div class="card">
            <h3 class="section-title">Formulir Penarikan</h3>

            <form action="{{ route('withdrawal.store') }}" method="POST" class="withdrawal-form">
                @csrf
                <div class="form-group">
                    <label for="amount" class="balance-label">
                        <span class="balance-content">
                            <i class="fas fa-wallet"></i> Saldo:
                            <span class="balance-amount">Rp
                                {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
                        </span>
                    </label>

                    <div class="form-group">
                        <label for="amount">Jumlah Penarikan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="text" id="amount" name="amount" class="form-control"
                                placeholder="Masukkan jumlah" min="35000" required>
                            <input type="hidden" id="amount_raw" name="amount_raw">
                        </div>
                        <small class="text-muted"><i class="fas fa-info-circle"></i> Minimal penarikan Rp 35.000</small>
                    </div>

                    <!-- Hidden bank input (since we're using the first bank) -->
                    @if (count($banks) > 0)
                        <input type="hidden" id="bank" name="bank" value="{{ $banks[0]->id }}">
                    @endif

                    <div class="fee-info">
                        <div class="fee-item">
                            <span><i class="fas fa-percentage"></i> Biaya Admin (10%)</span>
                            <span id="admin-fee">Rp 0</span>
                        </div>
                        <div class="fee-item total">
                            <span><i class="fas fa-hand-holding-usd"></i> Total Diterima</span>
                            <span id="total-received">Rp 0</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary withdrawal-btn">
                        <i class="fas fa-paper-plane"></i> Ajukan Penarikan
                    </button>
                </div>
            </form>
        </div>

        <div class="card info-card">
            <h3 class="section-title"><i class="fas fa-info-circle"></i> Informasi Penting</h3>
            <ul class="info-list">
                <li><i class="fas fa-clock"></i> Waktu penarikan: 12:00-19:00</li>
                <li><i class="fas fa-money-bill-wave"></i> Biaya penarikan: 10%</li>
                <li><i class="fas fa-coins"></i> Penarikan minimum: Rp 35.000</li>
                <li><i class="fas fa-bolt"></i> Proses penarikan: 1-15 menit</li>
            </ul>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #f8f9fa;
            --white: #ffffff;
            --text: #1f2937;
            --text-light: #6b7280;
            --accent-dark: #10b981;
            --gray: #e5e7eb;
            --dark-gray: #d1d5db;
            --rounded-sm: 0.25rem;
            --rounded-md: 0.5rem;
            --rounded-lg: 0.75rem;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --gradient: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .withdrawal-container {
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .card {
            background-color: var(--white);
            border-radius: var(--rounded-lg);
            padding: 1.75rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.75rem;
        }

        .card-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gradient);
            border-radius: 3px;
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Bank Info Card Styles */
        .bank-info {
            margin-bottom: 1rem;
        }

        .bank-card {
            background: var(--gradient);
            color: white;
            padding: 1.5rem;
            border-radius: var(--rounded-md);
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .bank-name,
        .bank-number,
        .bank-owner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .bank-name {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .bank-number {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .bank-owner {
            font-size: 1rem;
            opacity: 0.95;
        }

        /* Balance Info Styles */
        .balance-label {
            font-size: 1rem;
            color: var(--text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .balance-amount {
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 20px;
            margin-left: 0.25rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: var(--text);
        }

        .input-group {
            display: flex;
            margin-bottom: 0.5rem;
            border-radius: var(--rounded-sm);
            overflow: hidden;
            border: 1px solid var(--dark-gray);
        }

        .input-group:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .input-group-text {
            padding: 0.75rem 1rem;
            background-color: var(--gray);
            color: var(--text-light);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: none;
            font-size: 1rem;
            outline: none;
        }

        .form-control:focus {
            box-shadow: none;
        }

        .text-muted {
            font-size: 0.85rem;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .fee-info {
            background-color: var(--secondary);
            border-radius: var(--rounded-md);
            padding: 1.25rem;
            margin: 1.75rem 0;
            border: 1px solid var(--gray);
        }

        .fee-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.95rem;
        }

        .fee-item i {
            margin-right: 0.5rem;
            width: 18px;
            text-align: center;
        }

        .fee-item.total {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid var(--gray);
            font-weight: 600;
            font-size: 1.05rem;
            color: var(--text);
        }

        .withdrawal-btn {
            width: 100%;
            padding: 1rem;
            font-size: 1.05rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: var(--gradient);
            border: none;
            border-radius: var(--rounded-md);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }

        .withdrawal-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(79, 70, 229, 0.3);
        }

        /* Info Card Styles */
        .info-card {
            background-color: #f0f9ff;
            border-left: 4px solid var(--primary);
        }

        .info-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .info-list li {
            padding: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.95rem;
            color: var(--text);
        }

        .info-list li i {
            color: var(--primary);
            width: 20px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .withdrawal-container {
                padding: 0 0.75rem;
            }

            .card {
                padding: 1.5rem;
            }

            .card-title {
                font-size: 1.3rem;
            }

            .bank-card {
                padding: 1.25rem;
            }

            .bank-name {
                font-size: 1.1rem;
            }

            .bank-number {
                font-size: 1rem;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Format input amount to Rupiah
            $('#amount').on('input', function() {
                // Remove non-digit characters
                let value = $(this).val().replace(/\D/g, '');

                // Store raw value in hidden field
                $('#amount_raw').val(value);

                // Format to Rupiah
                if (value.length > 0) {
                    value = parseInt(value, 10);
                    $(this).val(formatRupiah(value));
                } else {
                    $(this).val('');
                }

                // Calculate total amount after fee
                const amount = value ? parseInt(value) : 0;
                const fee = Math.round(amount * 0.1);
                const minWithdrawal = 35000;

                if (amount > 0 && amount < minWithdrawal) {
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }

                const total = amount > fee ? amount - fee : 0;
                $('#admin-fee').text('Rp ' + formatRupiah(fee));
                $('#total-received').text('Rp ' + formatRupiah(total));
            });

            // Function to format number as Rupiah
            function formatRupiah(angka) {
                if (!angka) return '0';
                const number_string = angka.toString();
                const sisa = number_string.length % 3;
                let rupiah = number_string.substr(0, sisa);
                const ribuan = number_string.substr(sisa).match(/\d{3}/g);

                if (ribuan) {
                    const separator = sisa ? '.' : '';
                    rupiah += separator + ribuan.join('.');
                }

                return rupiah;
            }

            // Form submission
            $('.withdrawal-form').on('submit', function(e) {
                e.preventDefault();

                const form = this;
                const amount = parseInt($('#amount_raw').val()) || 0;
                const bank = $('#bank').val();
                const minWithdrawal = 35000;
                const userBalance = {{ auth()->user()->balance ?? 0 }};
                const totalWithFee = amount;

                if (!amount || amount < minWithdrawal) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Jumlah Tidak Valid',
                        text: `Minimal penarikan adalah Rp ${minWithdrawal.toLocaleString('id-ID')}`,
                        confirmButtonColor: '#4f46e5',
                    });
                    return;
                }

                if (totalWithFee > userBalance) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Saldo Tidak Cukup',
                        text: `Saldo Anda tidak mencukupi untuk penarikan ini. Total penarikan + biaya admin: Rp ${formatRupiah(totalWithFee)}`,
                        confirmButtonColor: '#4f46e5',
                    });
                    return;
                }

                @if (count($banks) === 0)
                    Swal.fire({
                        icon: 'error',
                        title: 'Bank Tidak Tersedia',
                        text: 'Anda belum menambahkan rekening bank untuk penarikan',
                        confirmButtonColor: '#4f46e5',
                    });
                    return;
                @endif

                Swal.fire({
                    title: 'Konfirmasi Penarikan',
                    html: `Anda akan melakukan penarikan sebesar <b>Rp ${formatRupiah(amount)}</b> ke rekening {{ count($banks) > 0 ? $banks[0]->nama_bank : '' }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Konfirmasi',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading indicator
                        Swal.fire({
                            title: 'Memproses...',
                            html: 'Sedang memproses penarikan Anda',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form via AJAX
                        $.ajax({
                            url: $(form).attr('action'),
                            method: 'POST',
                            data: $(form).serialize(),
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses!',
                                    text: response.message ||
                                        'Penarikan berhasil diajukan',
                                    confirmButtonColor: '#4f46e5',
                                }).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                let errorMessage =
                                    'Terjadi kesalahan saat memproses penarikan';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: errorMessage,
                                    confirmButtonColor: '#4f46e5',
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#4f46e5',
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: '{{ session('error') }}',
                confirmButtonColor: '#4f46e5',
            });
        </script>
    @endif
@endsection
