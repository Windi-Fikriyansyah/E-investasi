@extends('layouts.app')

@section('content')
    <div class="withdrawal-history">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Riwayat Penarikan</h2>
            </div>

            <div class="tabs-container">
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
            </div>

            <div class="card-body">
                @if ($withdrawals->isEmpty())
                    <div class="empty-state">
                        <div class="empty-state-content">
                            <i class="fas fa-history fa-2x"></i>
                            <p>Belum ada riwayat penarikan</p>
                        </div>
                    </div>
                @else
                    <div class="withdrawal-list">
                        @foreach ($withdrawals as $withdrawal)
                            <div class="withdrawal-item" data-status="{{ $withdrawal->status }}">
                                <div class="withdrawal-header">
                                    <div class="withdrawal-date">
                                        <i class="far fa-calendar-alt"></i>
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
        .withdrawal-history {
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }

        .card {
            background: white;
            border-radius: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        .tabs-container {
            padding: 0 1.5rem;
            background: #f8fafc;
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
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            font-weight: 500;
            color: #64748b;
            background-color: transparent;
            position: relative;
            white-space: nowrap;
            flex-shrink: 0;
            margin-right: 0.5rem;
        }

        .tab-item:hover {
            background-color: rgba(255, 255, 255, 0.7);
            color: #2563eb;
        }

        .tab-item.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
        }

        .tab-item.active .tab-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
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
            transition: all 0.3s ease;
        }

        .tab-badge.success {
            background-color: #dcfce7;
            color: #166534;
        }

        .tab-badge.processing {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .tab-badge.failed {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .withdrawal-list {
            display: grid;
            gap: 1rem;
        }

        .withdrawal-item {
            background-color: white;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
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
            border-bottom: 1px solid #e2e8f0;
        }

        .withdrawal-date {
            color: #64748b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .withdrawal-status {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
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
            background-color: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.25rem;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
        }

        .empty-state {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 0;
            color: #64748b;
        }

        .empty-state-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .empty-state-content i {
            font-size: 2rem;
            color: #cbd5e1;
        }

        .empty-state-content p {
            margin: 0;
            font-size: 0.875rem;
        }

        /* Responsive styles */
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

            .card-header,
            .card-body {
                padding: 1rem;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-item');
            const withdrawalItems = document.querySelectorAll('.withdrawal-item');

            if (tabs.length > 0) {
                updateTabCounters();

                tabs.forEach(function(tab) {
                    tab.addEventListener('click', function(e) {
                        e.preventDefault();
                        const tabId = this.getAttribute('data-tab_rekening');

                        tabs.forEach(function(t) {
                            t.classList.remove('active');
                        });
                        this.classList.add('active');

                        filterWithdrawals(tabId);
                    });
                });
            }

            function filterWithdrawals(status) {
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

                const withdrawalList = document.querySelector('.withdrawal-list');
                if (withdrawalList && visibleCount === 0) {
                    const emptyStateHTML = `
                        <div class="empty-state empty-state-filtered">
                            <div class="empty-state-content">
                                <i class="fas fa-filter fa-2x"></i>
                                <p>Tidak ada data untuk filter ini</p>
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
