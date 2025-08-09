@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Riwayat Penarikan</h2>
        </div>

        <div class="card">
            <!-- Tambahkan filter tanggal -->
            <div class="card-header">
                <form action="{{ route('withdrawal.history') }}" method="GET" class="filter-form">
                    <div class="filter-group">
                        <div class="filter-item">
                            <label for="date_filter" class="filter-label" style="color: #1e293b">Filter Tanggal:</label>
                            <input type="date" id="date_filter" name="date_filter" class="filter-input"
                                value="{{ request('date_filter') ?? \Carbon\Carbon::today()->format('Y-m-d') }}">
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary filter-btn"
                                style="width: 100%; background: var(--primary); color: white; padding: 0.75rem; border: none; border-radius: var(--rounded-sm);">Filter</button>
                            @if (request('date_filter'))
                                <a href="{{ route('withdrawal.history') }}" class="btn btn-secondary filter-btn">Reset</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>


            <div class="tabs">
                <div class="tab-item active" data-tab_rekening="all">
                    <span class="tab-text">Semua</span>
                    <span class="tab-badge" data-count="0">0</span>
                </div>
                <div class="tab-item" data-tab_rekening="success">
                    <span class="tab-text">Success</span>
                    <span class="tab-badge success" data-count="0">0</span>
                </div>
                <div class="tab-item" data-tab_rekening="processing">
                    <span class="tab-text">Processing</span>
                    <span class="tab-badge processing" data-count="0">0</span>
                </div>
                <div class="tab-item" data-tab_rekening="failed">
                    <span class="tab-text">Failed</span>
                    <span class="tab-badge failed" data-count="0">0</span>
                </div>
            </div>


            <div class="tab-content1 active" id="all">
                @if ($withdrawals->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-content">
                            <i class="fas fa-history fa-2x text-gray-300"></i>
                            <p style="color: #1e293b">Belum ada riwayat penarikan</p>
                        </div>
                    </div>
                @else
                    <div class="withdrawal-list">
                        @foreach ($withdrawals as $withdrawal)
                            <div class="withdrawal-item" data-status="{{ $withdrawal->status }}">
                                <div class="withdrawal-header">
                                    <div class="withdrawal-date">
                                        <i class="far fa-calendar-alt mr-2"></i>&nbsp;
                                        {{ \Carbon\Carbon::parse($withdrawal->created_at)->format('d M Y H:i') }}
                                    </div>
                                    <div class="withdrawal-status {{ $withdrawal->status }}">
                                        @if ($withdrawal->status == 'success')
                                            Success
                                        @elseif($withdrawal->status == 'processing')
                                            Processing
                                        @elseif($withdrawal->status == 'failed')
                                            Failed
                                        @else
                                            {{ ucfirst($withdrawal->status) }}
                                        @endif
                                    </div>
                                </div>

                                <div class="withdrawal-details">
                                    <div class="detail-row">
                                        <div class="details-items">
                                            <div class="detail-icon">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </div>
                                            <div class="detail-content">
                                                <span class="detail-label">Jumlah Penarikan</span>
                                                <span class="detail-value">Rp
                                                    {{ number_format($withdrawal->amount, 0, ',', '.') }}</span>
                                            </div>
                                        </div>

                                        <div class="details-items">
                                            <div class="detail-icon">
                                                <i class="fas fa-percentage"></i>
                                            </div>
                                            <div class="detail-content">
                                                <span class="detail-label">Biaya Admin</span>
                                                <span class="detail-value">Rp
                                                    {{ number_format($withdrawal->admin_fee, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="detail-row">
                                        <div class="details-items">
                                            <div class="detail-icon">
                                                <i class="fas fa-university"></i>
                                            </div>
                                            <div class="detail-content">
                                                <span class="detail-label">Bank Tujuan</span>
                                                <span class="detail-value">{{ $withdrawal->bank_name }} -
                                                    {{ $withdrawal->bank_account }}</span>
                                            </div>
                                        </div>

                                        <div class="details-items">
                                            <div class="detail-icon">
                                                <i class="fas fa-credit-card"></i>
                                            </div>
                                            <div class="detail-content">
                                                <span class="detail-label">Nomor Rekening</span>
                                                <span class="detail-value">{{ $withdrawal->bank_number }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($withdrawal->notes)
                                        <div class="detail-row">
                                            <div class="details-items full-width">
                                                <div class="detail-icon">
                                                    <i class="fas fa-sticky-note"></i>
                                                </div>
                                                <div class="detail-content">
                                                    <span class="detail-label">Catatan</span>
                                                    <span class="detail-value">{{ $withdrawal->notes }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Counter untuk setiap status -->
                    <div class="status-counter" style="display: none;">
                        <span id="counter-all">{{ $withdrawals->count() }}</span>
                        <span id="counter-success">{{ $withdrawals->where('status', 'success')->count() }}</span>
                        <span id="counter-processing">{{ $withdrawals->where('status', 'processing')->count() }}</span>
                        <span id="counter-failed">{{ $withdrawals->where('status', 'failed')->count() }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .withdrawal-list {
            display: grid;
            gap: 1rem;
        }

        .withdrawal-item {
            background-color: var(--white);
            border-radius: var(--rounded-md);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray);
            transition: all 0.3s ease;
        }

        .withdrawal-item.hidden {
            display: none;
        }

        .withdrawal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid var(--gray);
        }

        .withdrawal-date {
            color: var(--text-light);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .withdrawal-status {
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .withdrawal-status.success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .withdrawal-status.processing {
            background-color: #bfdbfe;
            color: #1e40af;
        }

        .withdrawal-status.failed {
            background-color: #fee2e2;
            color: #b91c1c;
        }

        .detail-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .details-items {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .details-items.full-width {
            flex: 100%;
        }

        .detail-icon {
            width: 36px;
            height: 36px;
            background-color: var(--gray);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-size: 0.75rem;
            color: var(--text-light);
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text);
        }

        /* Improved Tabs Styling */
        .tabs-container {
            padding: 0 1.25rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-radius: 12px 12px 0 0;
            overflow: hidden;
        }

        .tabs {
            display: flex;
            position: relative;
            background-color: transparent;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0.5rem 0;
        }

        .tabs::-webkit-scrollbar {
            display: none;
        }

        .tab-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 500;
            color: #64748b;
            background-color: transparent;
            position: relative;
            white-space: nowrap;
            flex-shrink: 0;
            min-width: fit-content;
            margin-right: 0.25rem;
        }

        .tab-item:hover {
            background-color: rgba(255, 255, 255, 0.7);
            color: var(--primary, #3b82f6);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .tab-item.active {
            background: linear-gradient(135deg, var(--primary, #3b82f6) 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
            transform: translateY(-2px);
        }

        .tab-item.active .tab-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .tab-text {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .tab-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.5rem;
            height: 1.5rem;
            padding: 0 0.375rem;
            background-color: #e2e8f0;
            color: #475569;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #cbd5e1;
            transition: all 0.3s ease;
        }

        .tab-badge.success {
            background-color: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .tab-badge.processing {
            background-color: #dbeafe;
            color: #1e40af;
            border-color: #93c5fd;
        }

        .tab-badge.failed {
            background-color: #fee2e2;
            color: #dc2626;
            border-color: #fecaca;
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .tabs-container {
                padding: 0 0.75rem;
                margin: 0 -1.25rem 1.5rem -1.25rem;
                border-radius: 0;
            }

            .tabs {
                gap: 0.25rem;
                padding: 0.75rem 0;
            }

            .tab-item {
                padding: 0.625rem 0.75rem;
                margin-right: 0.125rem;
            }

            .tab-text {
                font-size: 0.8125rem;
            }

            .tab-badge {
                min-width: 1.25rem;
                height: 1.25rem;
                font-size: 0.6875rem;
            }
        }

        @media (max-width: 480px) {
            .tab-item {
                padding: 0.5rem 0.625rem;
            }

            .tab-text {
                font-size: 0.75rem;
            }
        }

        /* Enhanced Visual Effects */
        .tab-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .tab-item:hover::before {
            opacity: 1;
        }

        .tab-item.active::before {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0.1) 100%);
            opacity: 1;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
        }

        .pagination {
            display: flex;
            gap: 0.5rem;
        }

        .page-item {
            list-style: none;
        }

        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: var(--rounded-sm);
            background-color: var(--white);
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            border: 1px solid var(--gray);
        }

        .page-item.active .page-link {
            background-color: var(--primary);
            color: var(--white);
            border-color: var(--primary);
        }

        .page-item.disabled .page-link {
            color: var(--dark-gray);
            pointer-events: none;
        }

        .filter-form {
            width: 100%;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }

        .filter-item {
            width: 100%;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .filter-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--gray);
            border-radius: var(--rounded-sm);
            font-size: 0.875rem;
            height: 2.5rem;
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
            width: 100%;
            margin-bottom: 10px;
        }

        .filter-btn {
            flex: 1;
            padding: 0.5rem;
            height: 2.5rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-width: 1px;
            border-style: solid;
            border-color: #64748b;
            background-color: #e2e8f0;
        }

        /* Desktop view */
        @media (min-width: 768px) {
            .filter-group {
                flex-direction: row;
                align-items: flex-end;
            }

            .filter-item {
                flex: 1;
            }

            .filter-actions {
                flex: 0 0 auto;
                width: auto;
            }

            .filter-btn {
                width: auto;
                padding: 0.5rem 1rem;
            }
        }

        /* Mobile view khusus untuk input date */
        @media (max-width: 767px) {
            input[type="date"]::-webkit-calendar-picker-indicator {
                padding: 0;
                margin: 0;
                width: 1.5rem;
                height: 1.5rem;
            }

            input[type="date"] {
                min-height: 2.5rem;
            }
        }

        @media (max-width: 640px) {
            .detail-row {
                flex-direction: column;
                gap: 0.75rem;
            }

            .details-items {
                flex: 100%;
            }

            .withdrawal-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .filter-form {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        .empty-state {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem 0;
        }

        .empty-state-content {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .empty-state-content i {
            margin-bottom: 0;
        }

        @media (max-width: 640px) {
            .empty-state-content {
                flex-direction: column;
                gap: 0.5rem;
            }

            .empty-state-content i {
                font-size: 1.5rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah elemen tabs ada
            const tabs = document.querySelectorAll('.tab-item');
            const withdrawalItems = document.querySelectorAll('.withdrawal-item');

            if (tabs.length > 0) {
                // Inisialisasi counter untuk tabs
                updateTabCounters();

                // Tab functionality
                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tabId = this.getAttribute('data-tab_rekening');

                        // Update active tab
                        tabs.forEach(function(t) {
                            t.classList.remove('active');
                        });
                        this.classList.add('active');

                        // Filter withdrawals based on status
                        filterWithdrawals(tabId);
                    });
                });
            }

            function filterWithdrawals(status) {
                // Hapus empty state filtered sebelumnya
                const existingEmptyState = document.querySelector('.empty-state-filtered');
                if (existingEmptyState) {
                    existingEmptyState.remove();
                }

                let visibleCount = 0;

                withdrawalItems.forEach(function(item) {
                    if (status === 'all') {
                        item.classList.remove('hidden');
                        item.style.display = 'block';
                        visibleCount++;
                    } else {
                        const itemStatus = item.getAttribute('data-status');
                        if (itemStatus === status) {
                            item.classList.remove('hidden');
                            item.style.display = 'block';
                            visibleCount++;
                        } else {
                            item.classList.add('hidden');
                            item.style.display = 'none';
                        }
                    }
                });

                // Update empty state
                const withdrawalList = document.querySelector('.withdrawal-list');
                if (withdrawalList && visibleCount === 0) {
                    const emptyStateHTML = `
                        <div class="empty-state empty-state-filtered">
                            <div class="empty-state-content">
                                <i class="fas fa-filter fa-2x text-gray-300"></i>
                                <p style="color: #1e293b">Tidak ada data untuk filter ini</p>
                            </div>
                        </div>
                    `;
                    withdrawalList.insertAdjacentHTML('afterend', emptyStateHTML);
                }
            }

            function updateTabCounters() {
                if (withdrawalItems.length > 0) {
                    const allCount = withdrawalItems.length;
                    const successCount = document.querySelectorAll('.withdrawal-item[data-status="success"]')
                        .length;
                    const processingCount = document.querySelectorAll('.withdrawal-item[data-status="processing"]')
                        .length;
                    const failedCount = document.querySelectorAll('.withdrawal-item[data-status="failed"]').length;

                    // Update tab badges
                    const allTab = document.querySelector('.tab-item[data-tab_rekening="all"] .tab-badge');
                    const successTab = document.querySelector('.tab-item[data-tab_rekening="success"] .tab-badge');
                    const processingTab = document.querySelector(
                        '.tab-item[data-tab_rekening="processing"] .tab-badge');
                    const failedTab = document.querySelector('.tab-item[data-tab_rekening="failed"] .tab-badge');

                    if (allTab) {
                        allTab.textContent = allCount;
                        allTab.setAttribute('data-count', allCount);
                    }
                    if (successTab) {
                        successTab.textContent = successCount;
                        successTab.setAttribute('data-count', successCount);
                    }
                    if (processingTab) {
                        processingTab.textContent = processingCount;
                        processingTab.setAttribute('data-count', processingCount);
                    }
                    if (failedTab) {
                        failedTab.textContent = failedCount;
                        failedTab.setAttribute('data-count', failedCount);
                    }
                }
            }
        });
    </script>
@endsection
