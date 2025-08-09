@extends('layouts.app')

@section('content')
    <div class="withdrawal-container">

        <div class="card">

            <div class="balance-info">
                <div class="balance-card">
                    <div class="balance-label">Saldo Tersedia</div>
                    <div class="balance-amount">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</div>
                </div>
            </div>

            <form action="{{ route('withdrawal.store') }}" method="POST" class="withdrawal-form">
                @csrf
                <div class="form-group">
                    <label for="amount">Jumlah Penarikan</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="amount" name="amount" class="form-control"
                            placeholder="Masukkan jumlah" min="50000" required>
                        <input type="hidden" id="amount_raw" name="amount_raw">
                    </div>
                    <small class="text-muted">Minimal penarikan Rp 50.000</small>
                </div>

                <div class="form-group">
                    <label for="bank">Bank Tujuan</label>
                    <select id="bank" name="bank" class="form-control select2-bank" required>
                        <option value="" disabled selected>Pilih bank</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}">{{ $bank->nama_bank }} - {{ $bank->no_rekening }}
                                ({{ $bank->nama_pemilik }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="notes">Catatan (Opsional)</label>
                    <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Tambahkan catatan jika perlu"></textarea>
                </div>

                <div class="fee-info">
                    <div class="fee-item">
                        <span>Biaya Admin (10%)</span>
                        <span id="admin-fee">Rp 0</span>
                    </div>
                    <div class="fee-item total">
                        <span>Total Diterima</span>
                        <span id="total-received">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary withdrawal-btn">
                    <i class="fas fa-paper-plane"></i> Ajukan Penarikan
                </button>
            </form>
        </div>


    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .status.processing {
            background-color: #bfdbfe;
            color: #1e40af;
        }

        .status.pending {
            background-color: rgba(234, 179, 8, 0.1);
            /* Light orange/yellow background with 10% opacity */
            color: rgb(234, 179, 8);
            /* Amber-500 color for text */
        }

        .status.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-dark);
        }

        .status.failed {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .withdrawal-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 0.5rem;
        }

        .card {
            background-color: var(--white);
            border-radius: var(--rounded-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        h2,
        h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-dark);
        }

        h2 i,
        h3 i {
            color: var(--primary);
        }

        .balance-info {
            margin-bottom: 1.5rem;
        }

        .balance-card {
            background: var(--gradient);
            color: white;
            padding: 1.5rem;
            border-radius: var(--rounded-md);
            text-align: center;
        }

        .balance-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .balance-amount {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .input-group {
            display: flex;
            margin-bottom: 0.25rem;
        }

        .input-group-text {
            padding: 0.75rem 1rem;
            background-color: var(--gray);
            border: 1px solid var(--dark-gray);
            border-right: none;
            border-radius: var(--rounded-sm) 0 0 var(--rounded-sm);
            color: var(--text-light);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--dark-gray);
            border-radius: 0 var(--rounded-sm) var(--rounded-sm) 0;
            font-size: 1rem;
        }

        select.form-control {
            border-radius: var(--rounded-sm);
            padding: 0.75rem 1rem;
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
        }

        textarea.form-control {
            resize: vertical;
        }

        .add-bank-link {
            display: inline-block;
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--primary);
            text-decoration: none;
        }

        .add-bank-link:hover {
            text-decoration: underline;
        }

        .fee-info {
            background-color: var(--secondary);
            border-radius: var(--rounded-sm);
            padding: 1rem;
            margin: 1.5rem 0;
        }

        .fee-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .fee-item.total {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid var(--gray);
            font-weight: 600;
        }

        .withdrawal-btn {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            background: var(--primary);
            border: none;
            border-radius: var(--rounded-sm);
            color: white;
        }

        .withdrawal-btn:hover {
            background: var(--primary-dark);
        }

        .history-list {
            margin-top: 1rem;
        }

        .history-item {
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray);
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .status {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
        }

        .status.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-dark);
        }



        .status.failed {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .date {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .history-details {
            display: flex;
            justify-content: space-between;
        }

        .amount {
            font-weight: 600;
        }

        .bank {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .history-note {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #ef4444;
        }

        .view-all {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .view-all:hover {
            text-decoration: underline;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .balance-amount {
                font-size: 1.5rem;
            }

            .card {
                padding: 1.25rem;
            }
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inisialisasi Select2
            $('#bank').select2({
                placeholder: "Pilih bank",
                allowClear: true,
                width: '100%',
                theme: 'bootstrap-5',
            });

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
                const minWithdrawal = 50000;

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
                const minWithdrawal = 50000;
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

                if (!bank) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Bank Tujuan Belum Dipilih',
                        text: 'Silakan pilih bank tujuan untuk penarikan',
                        confirmButtonColor: '#4f46e5',
                    });
                    return;
                }

                Swal.fire({
                    title: 'Konfirmasi Penarikan',
                    html: `Anda akan melakukan penarikan sebesar <b>Rp ${formatRupiah(amount)}</b> ke rekening ${$('#bank option:selected').text().split(' - ')[0]}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#4f46e5',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Konfirmasi',
                    cancelButtonText: 'Batal'
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
