@extends('layouts.app')

@section('content')
    <div class="deposit-container">
        <div class="card">
            <h2>
                <i class="fas fa-wallet"></i>
                Deposit Saldo
            </h2>

            <form id="formDeposit" method="POST" class="deposit-form">
                @csrf
                <div class="form-group">
                    <label for="amount">Jumlah Deposit</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" id="amount" name="amount" class="form-control"
                            placeholder="Masukkan jumlah" required>
                        <input type="hidden" id="amount_raw" name="amount_raw">
                    </div>
                    <small class="text-muted">Minimal Deposit Rp 10.000</small>
                </div>

                <div class="form-group">
                    <label class="mb-3">Pilih Metode Pembayaran</label>
                    <div class="payment-methods">
                        <div class="payment-option" data-method="QRIS" data-route="{{ route('deposit.create_qris') }}">
                            <div class="payment-card">
                                <div class="payment-icon">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                                <div class="payment-details">
                                    <h5>QRIS</h5>
                                    <p>Scan kode QR dengan aplikasi e-wallet atau mobile banking</p>
                                </div>
                                <div class="payment-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="payment-option" data-method="VA" data-route="{{ route('deposit.create_va') }}">
                            <div class="payment-card">
                                <div class="payment-icon">
                                    <i class="fas fa-university"></i>
                                </div>
                                <div class="payment-details">
                                    <h5>Virtual Account</h5>
                                    <p>Transfer ke Virtual Account bank pilihan Anda</p>
                                </div>
                                <div class="payment-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>

                        <div class="payment-option" data-method="E-WALLET"
                            data-route="{{ route('deposit.create_ewallet') }}">
                            <div class="payment-card">
                                <div class="payment-icon">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="payment-details">
                                    <h5>E-Wallet</h5>
                                    <p>Bayar dengan OVO, Dana, LinkAja, atau e-wallet lainnya</p>
                                </div>
                                <div class="payment-check">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="payment_method" name="payment_method" required>
                    <input type="hidden" id="form_action" name="form_action" value="">
                </div>

                <div class="fee-info">
                    <div class="fee-item">
                        <span>Jumlah Deposit</span>
                        <span id="deposit-amount">Rp 0</span>
                    </div>
                    <div class="fee-item">
                        <span>Biaya Admin</span>
                        <span>Gratis</span>
                    </div>
                    <div class="fee-item total">
                        <span>Total Pembayaran</span>
                        <span id="total-amount">Rp 0</span>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary deposit-btn">
                    <i class="fas fa-qrcode"></i> Lanjutkan Pembayaran
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
            color: rgb(234, 179, 8);
        }

        .status.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-dark);
        }

        .status.failed {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .deposit-container {
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

        h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary-dark);
        }

        h2 i {
            color: var(--primary);
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

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.1);
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

        .deposit-btn {
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
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .deposit-btn:hover {
            background: var(--primary-dark);
        }

        .deposit-btn:disabled {
            background: var(--gray);
            cursor: not-allowed;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .card {
                padding: 1.25rem;
            }
        }

        .payment-methods {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 8px;
        }

        .payment-option {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .payment-option:hover .payment-card {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.03);
        }

        .payment-option.active .payment-card {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.05);
        }

        .payment-card {
            display: flex;
            align-items: center;
            padding: 16px;
            border: 1px solid var(--dark-gray);
            border-radius: var(--rounded-sm);
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
        }

        .payment-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 16px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            color: white;
            font-size: 20px;
        }

        .payment-option:hover .payment-icon {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transform: scale(1.05);
            transition: all 0.3s ease;
        }

        .payment-option.active .payment-icon {
            background: linear-gradient(135deg, var(--accent), var(--accent-dark));
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
        }

        /* Alternative color schemes for each payment method */
        .payment-option[data-method="QRIS"] .payment-icon {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
        }

        .payment-option[data-method="VA"] .payment-icon {
            background: linear-gradient(135deg, #059669, #10b981);
        }

        .payment-option[data-method="E-WALLET"] .payment-icon {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
        }

        .payment-option[data-method="QRIS"]:hover .payment-icon {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
        }

        .payment-option[data-method="VA"]:hover .payment-icon {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .payment-option[data-method="E-WALLET"]:hover .payment-icon {
            background: linear-gradient(135deg, #a855f7, #9333ea);
        }

        .payment-option[data-method="QRIS"].active .payment-icon {
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }

        .payment-option[data-method="VA"].active .payment-icon {
            background: linear-gradient(135deg, #059669, #047857);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.4);
        }

        .payment-option[data-method="E-WALLET"].active .payment-icon {
            background: linear-gradient(135deg, #9333ea, #7c3aed);
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.4);
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .payment-icon {
                width: 40px;
                height: 40px;
                margin-right: 12px;
                font-size: 18px;
                border-radius: 10px;
            }
        }

        .payment-details {
            flex-grow: 1;
        }

        .payment-details h5 {
            margin: 0 0 4px 0;
            font-size: 1rem;
            color: var(--text);
        }

        .payment-details p {
            margin: 0;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .payment-check {
            color: var(--primary);
            font-size: 1.25rem;
            opacity: 0;
            transition: opacity 0.2s ease;
            margin-left: 12px;
        }

        .payment-option.active .payment-check {
            opacity: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 480px) {
            .payment-card {
                padding: 12px;
            }

            .payment-icon {
                width: 40px;
                height: 40px;
                margin-right: 12px;
            }
        }

        /* VA Selection Modal Styles */
        .va-selection {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px 0;
        }

        .va-option {
            cursor: pointer;
            margin-bottom: 10px;
        }

        .va-card {
            display: flex;
            align-items: center;
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .va-option:hover .va-card {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.03);
        }

        .va-option.active .va-card {
            border-color: var(--primary);
            background-color: rgba(79, 70, 229, 0.05);
        }

        .va-icon {
            width: 40px;
            height: 40px;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            border-radius: 8px;
            color: white;
            font-size: 16px;
        }

        .va-details {
            flex-grow: 1;
            text-align: left;
        }

        .va-details h5 {
            margin: 0 0 2px 0;
            font-size: 14px;
            font-weight: 600;
        }

        .va-details p {
            margin: 0;
            font-size: 12px;
            color: #6b7280;
        }

        .va-check {
            color: var(--primary);
            font-size: 18px;
            opacity: 0;
            transition: opacity 0.2s;
        }

        .va-option.active .va-check {
            opacity: 1;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function updateFeeDisplay(amount) {
            let formattedAmount = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
            $('#deposit-amount').text(formattedAmount);
            $('#total-amount').text(formattedAmount);
        }
        $(document).ready(function() {
            // Format currency input
            $('#amount').on('keyup input', function() {
                let value = $(this).val().replace(/\D/g, '');
                $('#amount_raw').val(value);

                if (value.length > 0) {
                    // Format dengan titik sebagai pemisah ribuan
                    let formatted = new Intl.NumberFormat('id-ID').format(value);
                    $(this).val(formatted);

                    // Update fee display
                    updateFeeDisplay(parseInt(value));
                } else {
                    updateFeeDisplay(0);
                }
            });
        });

        $(document).ready(function() {
            // Variabel untuk menyimpan route yang dipilih
            let selectedRoute = '';

            // Handle payment method selection
            $('.payment-option').click(function() {
                const method = $(this).data('method');

                if (method === 'VA') {
                    showVASelection();
                    return;
                } else if (method === 'E-WALLET') {
                    showEWalletSelection();
                    return;
                }

                $('.payment-option').removeClass('active');
                $(this).addClass('active');
                selectedRoute = $(this).data('route');

                $('#payment_method').val(method);
                $('#form_action').val(selectedRoute);

                // Update form action
                $('#formDeposit').attr('action', selectedRoute);

                // Update fees if amount already entered
                let rawValue = $('#amount_raw').val();
                if (rawValue) {
                    updateFeeDisplay(parseInt(rawValue));
                }
            });

            // Form validation
            $('#formDeposit').on('submit', function(e) {
                e.preventDefault();

                // Validasi manual sebelum submit
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

                if (!selectedRoute) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Silakan pilih metode pembayaran',
                        confirmButtonColor: '#696cff'
                    });
                    return false;
                }

                // Cek pending payment
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
                            // Submit form ke route yang sesuai
                            $('#formDeposit').off('submit').submit();
                        }
                    },
                    error: function() {
                        // Jika error saat cek pending, tetap lanjutkan submit
                        $('#formDeposit').off('submit').submit();
                    }
                });
            });

            // VA Selection Function
            function showVASelection() {
                const vaTypes = [{
                        code: 'MandiriVA',
                        name: 'Bank Mandiri',
                        description: 'Virtual Account Bank Mandiri'
                    },
                    {
                        code: 'BCAVA',
                        name: 'Bank BCA',
                        description: 'Virtual Account Bank Central Asia'
                    },
                    {
                        code: 'BNIVA',
                        name: 'Bank BNI',
                        description: 'Virtual Account Bank Negara Indonesia'
                    },
                    {
                        code: 'BRIVA',
                        name: 'Bank BRI',
                        description: 'Virtual Account Bank Rakyat Indonesia'
                    },
                    {
                        code: 'PermataVA',
                        name: 'Bank Permata',
                        description: 'Virtual Account Bank Permata'
                    },
                    {
                        code: 'CIMBVA',
                        name: 'Bank CIMB Niaga',
                        description: 'Virtual Account Bank CIMB Niaga'
                    },
                    {
                        code: 'MaybankVA',
                        name: 'Maybank Indonesia',
                        description: 'Virtual Account Maybank Indonesia'
                    },
                    {
                        code: 'DanamonVA',
                        name: 'Bank Danamon',
                        description: 'Virtual Account Bank Danamon'
                    }
                ];

                let optionsHtml = vaTypes.map(va => `
                    <div class="va-option" data-code="${va.code}" data-name="${va.name}">
                        <div class="va-card">
                            <div class="va-icon">
                                <i class="fas fa-university"></i>
                            </div>
                            <div class="va-details">
                                <h5>${va.name}</h5>
                                <p>${va.description}</p>
                            </div>
                            <div class="va-check">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                `).join('');

                Swal.fire({
                    title: 'Pilih Bank Virtual Account',
                    html: `
                        <div class="va-selection">
                            ${optionsHtml}
                        </div>
                    `,
                    showCancelButton: true,
                    confirmButtonText: 'Pilih',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#059669',
                    allowOutsideClick: false,
                    didOpen: () => {
                        // Handle VA selection
                        $('.va-option').click(function() {
                            $('.va-option').removeClass('active');
                            $(this).addClass('active');
                        });
                    },
                    preConfirm: () => {
                        const selected = $('.va-option.active');
                        if (selected.length === 0) {
                            Swal.showValidationMessage('Silakan pilih salah satu bank');
                            return false;
                        }

                        return {
                            va_type: selected.data('code'),
                            va_name: selected.data('name')
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        // Set hidden inputs
                        $('#payment_method').val('VA');

                        // Hapus input hidden sebelumnya jika ada
                        $('input[name="va_type"]').remove();

                        // Tambahkan input hidden baru
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'va_type',
                            value: data.va_type
                        }).appendTo('#formDeposit');

                        // Update form action
                        selectedRoute = '{{ route('deposit.create_va') }}';
                        $('#form_action').val(selectedRoute);
                        $('#formDeposit').attr('action', selectedRoute);

                        // Mark VA option as selected
                        $('.payment-option').removeClass('active');
                        $('.payment-option[data-method="VA"]').addClass('active');

                        // Update display text
                        $('.payment-option[data-method="VA"] .payment-details h5').text(
                            `Virtual Account (${data.va_name})`);

                        // Update fee display
                        let rawValue = $('#amount_raw').val();
                        if (rawValue) {
                            updateFeeDisplay(parseInt(rawValue));
                        }
                    }
                });
            }

            // E-Wallet Selection Function (keeping existing function)
            function showEWalletSelection() {
                const ewalletTypes = [{
                        code: 'DANABALANCE',
                        name: 'DANA',
                        logo: '{{ asset('images/dana.svg') }}',
                        description: 'Bayar dengan saldo DANA'
                    },
                    {
                        code: 'SHOPEEBALANCE',
                        name: 'ShopeePay',
                        logo: '{{ asset('images/ShopeePay.svg') }}',
                        description: 'Bayar dengan ShopeePay'
                    },
                    {
                        code: 'LINKAJABALANCE',
                        name: 'LinkAja',
                        logo: '{{ asset('images/linkaja.svg') }}',
                        description: 'Bayar dengan LinkAja'
                    },
                    {
                        code: 'OVOBALANCE',
                        name: 'OVO',
                        logo: '{{ asset('images/OVO.svg') }}',
                        description: 'Bayar dengan OVO (memerlukan nomor HP)'
                    },
                    {
                        code: 'GOPAYBALANCE',
                        name: 'GoPay',
                        logo: '{{ asset('images/gopay.svg') }}',
                        description: 'Bayar dengan GoPay'
                    }
                ];

                let optionsHtml = ewalletTypes.map(ewallet => `
                <div class="ewallet-option" data-code="${ewallet.code}" data-name="${ewallet.name}">
                    <div class="ewallet-card">
                        <div class="ewallet-icon">
                            <img src="${ewallet.logo}" alt="${ewallet.name}" onerror="this.style.display='none'">
                        </div>
                        <div class="ewallet-details">
                            <h5>${ewallet.name}</h5>
                            <p>${ewallet.description}</p>
                        </div>
                        <div class="ewallet-check">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            `).join('');

                Swal.fire({
                    title: 'Pilih E-Wallet',
                    html: `
                    <div class="ewallet-selection">
                        ${optionsHtml}
                        <div id="phone-number-container"></div>
                    </div>
                    <style>
                        .ewallet-selection {
                            max-height: 400px;
                            overflow-y: auto;
                            padding: 10px 0;
                        }
                        .ewallet-option {
                            cursor: pointer;
                            margin-bottom: 10px;
                        }
                        .ewallet-card {
                            display: flex;
                            align-items: center;
                            padding: 12px;
                            border: 1px solid #e5e7eb;
                            border-radius: 8px;
                            transition: all 0.2s;
                        }
                        .ewallet-option:hover .ewallet-card {
                            border-color: var(--primary);
                            background-color: rgba(79, 70, 229, 0.03);
                        }
                        .ewallet-option.active .ewallet-card {
                            border-color: var(--primary);
                            background-color: rgba(79, 70, 229, 0.05);
                        }
                        .ewallet-icon {
                            width: 40px;
                            height: 40px;
                            margin-right: 12px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .ewallet-icon img {
                            max-width: 100%;
                            max-height: 100%;
                            object-fit: contain;
                        }
                        .ewallet-details {
                            flex-grow: 1;
                            text-align: left;
                        }
                        .ewallet-details h5 {
                            margin: 0 0 2px 0;
                            font-size: 14px;
                            font-weight: 600;
                        }
                        .ewallet-details p {
                            margin: 0;
                            font-size: 12px;
                            color: #6b7280;
                        }
                        .ewallet-check {
                            color: var(--primary);
                            font-size: 18px;
                            opacity: 0;
                            transition: opacity 0.2s;
                        }
                        .ewallet-option.active .ewallet-check {
                            opacity: 1;
                        }
                        #phone-number-group {
                            margin-top: 15px;
                            text-align: left;
                        }
                    </style>
                `,
                    showCancelButton: true,
                    confirmButtonText: 'Pilih',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#4f46e5',
                    allowOutsideClick: false,
                    didOpen: () => {
                        // Handle ewallet selection
                        $('.ewallet-option').click(function() {
                            $('.ewallet-option').removeClass('active');
                            $(this).addClass('active');
                            let selectedEWalletType = $(this).data('code');

                            // Show/hide phone number input for OVO
                            const phoneContainer = $('#phone-number-container');
                            phoneContainer.empty();

                            if (selectedEWalletType === 'OVOBALANCE') {
                                phoneContainer.html(`
                                <div class="form-group" id="phone-number-group">
                                    <label for="phone_number">Nomor HP (untuk OVO)</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control"
                                           placeholder="Contoh: 08123456789" pattern="[0-9]{10,15}">
                                    <small class="text-muted">Nomor HP yang terdaftar di OVO</small>
                                </div>
                            `);
                            }
                        });
                    },
                    preConfirm: () => {
                        const selected = $('.ewallet-option.active');
                        if (selected.length === 0) {
                            Swal.showValidationMessage('Silakan pilih salah satu e-wallet');
                            return false;
                        }

                        const ewalletCode = selected.data('code');
                        const phoneNumber = $('#phone_number').val();

                        // Validasi khusus untuk OVO
                        if (ewalletCode === 'OVOBALANCE') {
                            if (!phoneNumber || phoneNumber.length < 10) {
                                Swal.showValidationMessage('Nomor HP harus minimal 10 digit untuk OVO');
                                return false;
                            }

                            if (!/^[0-9]{10,15}$/.test(phoneNumber)) {
                                Swal.showValidationMessage('Nomor HP hanya boleh berisi angka');
                                return false;
                            }
                        }

                        return {
                            ewallet_type: ewalletCode,
                            phone_number: phoneNumber || '',
                            ewallet_name: selected.data('name')
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        const data = result.value;

                        // Set hidden inputs
                        $('#payment_method').val('E-WALLET');

                        // Hapus input hidden sebelumnya jika ada
                        $('input[name="ewallet_type"]').remove();
                        $('input[name="phone_number"]').remove();

                        // Tambahkan input hidden baru
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'ewallet_type',
                            value: data.ewallet_type
                        }).appendTo('#formDeposit');

                        if (data.phone_number) {
                            $('<input>').attr({
                                type: 'hidden',
                                name: 'phone_number',
                                value: data.phone_number
                            }).appendTo('#formDeposit');
                        }

                        // Update form action
                        selectedRoute = '{{ route('deposit.create_ewallet') }}';
                        $('#form_action').val(selectedRoute);
                        $('#formDeposit').attr('action', selectedRoute);

                        // Mark E-Wallet option as selected
                        $('.payment-option').removeClass('active');
                        $('.payment-option[data-method="E-WALLET"]').addClass('active');

                        // Update display text
                        $('.payment-option[data-method="E-WALLET"] .payment-details h5').text(
                            `E-Wallet (${data.ewallet_name})`);

                        // Update fee display
                        let rawValue = $('#amount_raw').val();
                        if (rawValue) {
                            updateFeeDisplay(parseInt(rawValue));
                        }
                    }
                });
            }

            // Expose functions to global scope
            window.showVASelection = showVASelection;
            window.showEWalletSelection = showEWalletSelection;
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
