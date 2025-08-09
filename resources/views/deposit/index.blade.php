@extends('layouts.app')

@section('content')
    <div class="withdrawal-container">

        <div class="card">



            <form id="formDeposit" action="{{ route('deposit.create') }}" method="POST" class="withdrawal-form">
                @csrf
                <div class="form-group">
                    <label for="amount">Jumlah Deposit</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="amount" name="amount" class="form-control"
                            placeholder="Masukkan jumlah" min="10000" required>
                        <input type="hidden" id="amount_raw" name="amount_raw">
                    </div>
                    <small class="text-muted">Minimal Deposit Rp 10.000</small>
                </div>


                <button type="submit" class="btn btn-primary withdrawal-btn">
                    <i class="fas fa-paper-plane"></i> Lanjutkan Pembayaran
                </button>
            </form>
        </div>


    </div>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .status.processing {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary);
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

        .status.processing {
            background-color: rgba(59, 130, 246, 0.1);
            color: var(--primary);
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
            // Format currency input
            $('#amount').on('keyup', function() {
                let value = $(this).val().replace(/\D/g, '');
                $('#amount_raw').val(value); // Simpan nilai numerik ke hidden input

                if (value.length > 0) {
                    // Format dengan titik sebagai pemisah ribuan
                    let formatted = new Intl.NumberFormat('id-ID').format(value);
                    $(this).val(formatted);
                }
            });

            // Form validation
            $('#formDeposit').on('submit', function(e) {
                e.preventDefault();

                // Cek dulu apakah ada pending payment
                $.ajax({
                    url: '{{ route('deposit.checkPending') }}',
                    type: 'GET',
                    success: function(response) {
                        if (response.has_pending) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Pembayaran Pending',
                                text: 'Anda masih memiliki pembayaran yang belum diselesaikan. Silakan selesaikan pembayaran tersebut terlebih dahulu.',
                                confirmButtonColor: '#696cff',
                                confirmButtonText: 'Ke Riwayat Pembayaran'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href =
                                        '{{ route('deposit.riwayat') }}';
                                }
                            });
                        } else {
                            // Lanjutkan validasi amount
                            let rawValue = $('#amount_raw').val();
                            if (!rawValue || parseInt(rawValue) < 10000) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Minimum deposit adalah Rp 10.000',
                                    confirmButtonColor: '#696cff'
                                });
                                return false;
                            }

                            // Jika validasi OK, submit form
                            e.currentTarget.submit();
                        }
                    },
                    error: function() {
                        // Jika error saat cek pending, tetap lanjutkan
                        e.currentTarget.submit();
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
