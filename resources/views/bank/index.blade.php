@extends('layouts.app')

@section('content')
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-button active" data-tab="bank-list">Daftar Bank</button>
            <button class="tab-button" data-tab="add-bank">Tambah Bank</button>
        </div>
    </div>

    <div class="tab-content active" id="bank-list">
        @forelse($banks as $bank)
            <div class="card bank-card">
                <div class="bank-header">
                    <div class="bank-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="bank-info">
                        <h3>{{ $bank->nama_bank }}</h3>
                        <span class="bank-status">Aktif</span>
                    </div>
                </div>

                <div class="bank-details">
                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Nomor Rekening</span>
                            <span class="detail-value">{{ $bank->no_rekening }}</span>
                        </div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="detail-content">
                            <span class="detail-label">Nama Pemilik</span>
                            <span class="detail-value">{{ $bank->nama_pemilik }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <form action="{{ route('bank.destroy', $bank->id) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-delete">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-university"></i>
                </div>
                <h3>Belum Ada Rekening Bank</h3>
                <p>Anda belum menambahkan rekening bank</p>
                <button class="btn-primary" onclick="switchToTab('add-bank')">
                    <i class="fas fa-plus"></i> Tambah Bank
                </button>
            </div>
        @endforelse
    </div>

    <div class="tab-content" id="add-bank">
        <div class="card">
            <h3 class="form-title">Tambah Rekening Bank Baru</h3>
            <form action="{{ route('bank.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nama_bank">Nama Bank</label>
                    <select name="nama_bank" id="nama_bank" class="form-control" required>
                        <option value="">Pilih Bank</option>
                        <option value="BCA">BCA</option>
                        <option value="BRI">BRI</option>
                        <option value="Mandiri">Mandiri</option>
                        <option value="BNI">BNI</option>
                        <option value="CIMB Niaga">CIMB Niaga</option>
                        <option value="Permata">Permata</option>
                        <option value="Danamon">Danamon</option>
                        <option value="Maybank">Maybank</option>
                        <option value="OCBC NISP">OCBC NISP</option>
                        <option value="Bank Jago">Bank Jago</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="no_rekening">Nomor Rekening</label>
                    <input type="text" name="no_rekening" id="no_rekening" class="form-control" required
                        value="{{ old('no_rekening') }}" placeholder="Masukkan nomor rekening tanpa spasi atau tanda baca">
                    @error('no_rekening')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nama_pemilik">Nama Pemilik Rekening</label>
                    <input type="text" name="nama_pemilik" id="nama_pemilik" class="form-control" required
                        value="{{ old('nama_pemilik') }}" placeholder="Nama harus sesuai dengan buku rekening">
                    @error('nama_pemilik')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                @if (!$hasBank)
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> Simpan Rekening
                    </button>
                @endif
            </form>
            @if ($hasBank)
                <div class="alert-message">
                    <p>Anda sudah memiliki rekening bank. Hanya satu rekening yang diperbolehkan.</p>
                </div>
            @endif
        </div>
    </div>

    <style>
        /* Container dan Tabs */
        .tabs-container {
            margin-bottom: 1.5rem;
        }

        .tabs {
            background: white;
            border-radius: var(--rounded-lg);
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tab-button {
            flex: 1;
            padding: 0.875rem 1rem;
            background: transparent;
            border: none;
            border-radius: var(--rounded-sm);
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-light);
        }

        .tab-button.active {
            background: var(--gradient);
            color: white;
            box-shadow: var(--shadow-subtle);
        }

        .tab-button:hover:not(.active) {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Card Styles */
        .card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--rounded-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Bank Card */
        .bank-card {
            padding: 1.25rem;
        }

        .bank-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .bank-icon {
            width: 48px;
            height: 48px;
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
            box-shadow: var(--shadow-subtle);
        }

        .bank-icon i {
            font-size: 1.25rem;
        }

        .bank-info h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 0.25rem 0;
        }

        .bank-status {
            background: #10b981;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .bank-details {
            margin-bottom: 1.25rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--gray);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            background: var(--gray-light);
            color: var(--primary);
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 0.125rem;
        }

        .detail-value {
            display: block;
            font-size: 0.925rem;
            font-weight: 500;
            color: var(--text);
        }

        .card-footer {
            display: flex;
            justify-content: flex-end;
            padding-top: 1rem;
            border-top: 1px solid var(--gray);
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            border: none;
            padding: 0.625rem 1rem;
            border-radius: var(--rounded-lg);
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-delete:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: var(--shadow-medium);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--rounded-xl);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: var(--shadow-soft);
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-full);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            opacity: 0.8;
        }

        .empty-icon i {
            font-size: 2rem;
        }

        .empty-state h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.925rem;
        }

        /* Form Styles */
        .form-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
            font-size: 0.875rem;
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: var(--rounded-lg);
            font-size: 0.925rem;
            transition: all 0.2s ease;
            background-color: white;
            color: var(--text);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            padding-right: 2.5rem;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.8125rem;
            margin-top: 0.375rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .error-message::before {
            content: "⚠";
            font-size: 0.875rem;
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: var(--rounded-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.925rem;
            margin-top: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-medium);
        }

        /* Responsive */
        @media (max-width: 480px) {
            .tabs {
                padding: 0.25rem;
            }

            .tab-button {
                padding: 0.75rem 0.5rem;
                font-size: 0.8125rem;
            }

            .bank-card {
                padding: 1rem;
            }

            .empty-state {
                padding: 2rem 1rem;
            }
        }

        /* Desktop View */
        @media (min-width: 768px) {
            .tabs {
                max-width: 400px;
                margin: 0 auto 2rem;
            }
        }
    </style>

    <script>
        // Tab switching functionality
        function switchToTab(tabId) {
            // Update active tab button
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector(`[data-tab="${tabId}"]`).classList.add('active');

            // Update active tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        }

        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', function() {
                const tabId = this.getAttribute('data-tab');
                switchToTab(tabId);
            });
        });

        // Format nomor rekening saat input
        document.getElementById('no_rekening').addEventListener('input', function(e) {
            // Hanya angka yang diperbolehkan
            this.value = this.value.replace(/[^0-9]/g, '');

            // Validasi panjang
            if (this.value.length > 30) {
                this.value = this.value.substring(0, 30);
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan',
                    text: 'Nomor rekening maksimal 30 digit',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        });

        // Format nama pemilik (huruf besar di awal setiap kata)
        document.getElementById('nama_pemilik').addEventListener('input', function(e) {
            this.value = this.value.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        });

        // Konfirmasi penghapusan dengan SweetAlert
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Anda tidak akan dapat mengembalikan data ini!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });

        // Tampilkan pesan sukses/gagal dari session dengan SweetAlert
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: '{{ session('success') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                timer: 3000,
                showConfirmButton: false
            });
        @endif

        // Add smooth animations on load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.card, .bank-card');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
@endsection
