@extends('layouts.app')

@section('content')
    <div class="container">

        <div class="tabs">
            <div class="tab active" data-tab="active">
                Aktif
                @php
                    $activeCount = $transactions
                        ->filter(function ($transaction) {
                            return in_array($transaction->status, ['aktif', 'pending']) && !$transaction->can_claim;
                        })
                        ->count();
                @endphp
                @if ($activeCount > 0)
                    <span class="tab-badge">{{ $activeCount }}</span>
                @endif
            </div>
            <div class="tab" data-tab="claim">
                Claim
                @php
                    $claimCount = $transactions
                        ->filter(function ($transaction) {
                            return $transaction->can_claim &&
                                $transaction->status != 'completed' &&
                                $transaction->can_claim_now;
                        })
                        ->count();
                @endphp
                @if ($claimCount > 0)
                    <span class="tab-badge">{{ $claimCount }}</span>
                @endif
            </div>
            <div class="tab" data-tab="completed">
                Selesai
                @php
                    $completedCount = $transactions
                        ->filter(function ($transaction) {
                            return $transaction->status == 'completed';
                        })
                        ->count();
                @endphp
                @if ($completedCount > 0)
                    <span class="tab-badge">{{ $completedCount }}</span>
                @endif
            </div>
        </div>

        <!-- Tab Aktif -->
        <div class="tab-content active" id="active">
            <div class="transaction-list">
                @php
                    $activeTransactions = $transactions->filter(function ($transaction) {
                        return in_array($transaction->status, ['aktif', 'pending']) && !$transaction->can_claim;
                    });
                @endphp

                @if ($activeTransactions->count() > 0)
                    @foreach ($activeTransactions as $transaction)
                        <div class="transaction-card {{ $transaction->vip ? 'vip' : '' }}">
                            @if ($transaction->vip)
                                <div class="vip-badge">VIP</div>
                            @endif

                            <div class="transaction-header">
                                <div class="transaction-title">{{ $transaction->product_name }}</div>
                                <div class="transaction-amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-info">
                                    <div class="transaction-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}
                                    </div>
                                    <div class="transaction-duration">
                                        <i class="fas fa-clock"></i>
                                        Durasi: {{ $transaction->durasi }} hari
                                    </div>
                                    <div class="transaction-daily-return">
                                        <i class="fas fa-coins"></i>
                                        Return Harian: Rp {{ number_format($transaction->daily_return, 0, ',', '.') }}
                                    </div>
                                    <div class="transaction-progress">
                                        <i class="fas fa-chart-line"></i>
                                        Hari ke-{{ $transaction->days_elapsed }} dari {{ $transaction->durasi }}
                                        ({{ $transaction->claimed_days }} diklaim)
                                    </div>
                                    <div class="transaction-progress">
                                        <i class="fas fa-hourglass-half"></i>
                                        @if ($transaction->days_remaining > 0)
                                            Sisa: {{ $transaction->days_remaining }} hari
                                        @else
                                            Periode investasi selesai
                                        @endif
                                    </div>
                                </div>
                                <div class="transaction-status {{ $transaction->status_class }}">
                                    {{ ucfirst($transaction->status) }}
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill"
                                        style="width: {{ ($transaction->days_elapsed / $transaction->durasi) * 100 }}%">
                                    </div>
                                </div>
                                <div class="progress-text">
                                    {{ round(($transaction->days_elapsed / $transaction->durasi) * 100, 1) }}% selesai
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-clock"></i>
                        <p>Tidak ada transaksi aktif</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Claim -->
        <!-- Tab Claim -->
        <div class="tab-content" id="claim">
            <div class="transaction-list">
                @php
                    $claimTransactions = $transactions->filter(function ($transaction) {
                        return $transaction->can_claim &&
                            $transaction->status != 'completed' &&
                            $transaction->can_claim_now;
                    });
                @endphp

                @if ($claimTransactions->count() > 0)
                    @foreach ($claimTransactions as $transaction)
                        <div class="transaction-card {{ $transaction->vip ? 'vip' : '' }}">
                            @if ($transaction->vip)
                                <div class="vip-badge">VIP</div>
                            @endif

                            <div class="transaction-header">
                                <div class="transaction-title">{{ $transaction->product_name }}</div>
                                <div class="transaction-amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-info">
                                    <div class="transaction-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}
                                    </div>
                                    <div class="transaction-duration">
                                        <i class="fas fa-clock"></i>
                                        Durasi: {{ $transaction->durasi }} hari
                                    </div>
                                    <div class="transaction-daily-return">
                                        <i class="fas fa-coins"></i>
                                        Return Harian: Rp {{ number_format($transaction->daily_return, 0, ',', '.') }}
                                    </div>
                                    <div class="transaction-claim-info">
                                        <i class="fas fa-calendar-check"></i>
                                        Tersedia: {{ $transaction->remaining_claims }} hari
                                        ({{ $transaction->claimed_days }}/{{ $transaction->available_claims }} diklaim)
                                    </div>
                                    <div class="transaction-next-claim">
                                        <i class="fas fa-clock"></i>
                                        @if ($transaction->next_claim_time)
                                            Waktu claim berikutnya: {{ $transaction->next_claim_time }}
                                        @else
                                            Semua hari sudah diklaim
                                        @endif
                                    </div>
                                    <div class="transaction-return-preview highlight">
                                        <i class="fas fa-money-bill-wave"></i>
                                        Dapat diklaim: Rp
                                        {{ number_format($transaction->pending_claim_amount, 0, ',', '.') }}
                                    </div>
                                </div>
                                <div class="transaction-status ready-claim">
                                    Siap Klaim ({{ $transaction->remaining_claims }} hari)
                                </div>
                            </div>

                            <!-- Progress Bar untuk Claim -->
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-claimed"
                                        style="width: {{ ($transaction->claimed_days / $transaction->durasi) * 100 }}%">
                                    </div>
                                    <div class="progress-available"
                                        style="width: {{ (($transaction->available_claims - $transaction->claimed_days) / $transaction->durasi) * 100 }}%;
                                        left: {{ ($transaction->claimed_days / $transaction->durasi) * 100 }}%">
                                    </div>
                                </div>
                                <div class="progress-text">
                                    {{ $transaction->claimed_days }} diklaim, {{ $transaction->remaining_claims }}
                                    tersedia
                                </div>
                            </div>

                            <div class="transaction-actions">
                                <form action="{{ route('claim', $transaction->id) }}" method="POST" class="claim-form">
                                    @csrf
                                    <button type="submit" class="claim-button"
                                        @if (!$transaction->can_claim_now) disabled @endif>
                                        <i class="fas fa-coins"></i>
                                        Klaim {{ $transaction->remaining_claims }} Hari
                                        <span class="claim-amount">
                                            (Rp {{ number_format($transaction->pending_claim_amount, 0, ',', '.') }})
                                        </span>
                                    </button>
                                </form>
                                <button type="button" class="history-button"
                                    onclick="showClaimHistory({{ $transaction->id }})">
                                    <i class="fas fa-history"></i> Riwayat
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-coins"></i>
                        <p>Tidak ada transaksi yang bisa diklaim saat ini</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab Selesai -->
        <div class="tab-content" id="completed">
            <div class="transaction-list">
                @php
                    $completedTransactions = $transactions->filter(function ($transaction) {
                        return $transaction->status == 'completed';
                    });
                @endphp

                @if ($completedTransactions->count() > 0)
                    @foreach ($completedTransactions as $transaction)
                        <div class="transaction-card {{ $transaction->vip ? 'vip' : '' }}">
                            @if ($transaction->vip)
                                <div class="vip-badge">VIP</div>
                            @endif

                            <div class="transaction-header">
                                <div class="transaction-title">{{ $transaction->product_name }}</div>
                                <div class="transaction-amount">Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                                </div>
                            </div>
                            <div class="transaction-details">
                                <div class="transaction-info">
                                    <div class="transaction-date">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}
                                    </div>
                                    <div class="transaction-duration">
                                        <i class="fas fa-clock"></i>
                                        Durasi: {{ $transaction->durasi }} hari
                                    </div>
                                    <div class="transaction-daily-return">
                                        <i class="fas fa-coins"></i>
                                        Return Harian: Rp {{ number_format($transaction->daily_return, 0, ',', '.') }}
                                    </div>
                                    @if ($transaction->updated_at && $transaction->updated_at != $transaction->created_at)
                                        <div class="transaction-claimed-date">
                                            <i class="fas fa-check-circle"></i>
                                            Selesai:
                                            {{ \Carbon\Carbon::parse($transaction->updated_at)->format('d M Y H:i') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="transaction-status {{ $transaction->status_class }}">
                                    {{ ucfirst($transaction->status) }}
                                </div>
                            </div>

                            <!-- Progress Bar Complete -->
                            <div class="progress-container">
                                <div class="progress-bar">
                                    <div class="progress-fill completed" style="width: 100%"></div>
                                </div>
                                <div class="progress-text">100% selesai - Semua hari diklaim</div>
                            </div>

                            <div class="transaction-return">
                                <div class="return-label">Total Return yang Diklaim</div>
                                <div class="return-amount">Rp
                                    {{ number_format($transaction->return_amount ?? ($transaction->total_pendapatan ?? 0), 0, ',', '.') }}
                                </div>
                            </div>

                            <div class="transaction-actions">
                                <button type="button" class="history-button"
                                    onclick="showClaimHistory({{ $transaction->id }})">
                                    <i class="fas fa-history"></i> Lihat Riwayat Claim
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Tidak ada transaksi selesai</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Riwayat Claim -->
    <div id="claimHistoryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Riwayat Claim Harian</h3>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-body">
                <div id="claimHistoryContent">
                    <div class="loading">Memuat...</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                title: 'Sukses!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0.5rem;
            margin-bottom: 2rem;
        }

        h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-dark);
        }

        /* Tab Styles */
        .tabs {
            display: flex;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--rounded-lg);
            padding: 0.25rem;
            margin-bottom: 1.5rem;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tab {
            flex: 1;
            padding: 0.875rem 1rem;
            text-align: center;
            border-radius: var(--rounded-md);
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }

        .tab.active {
            background: rgba(255, 255, 255, 0.95);
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
            transform: translateY(-1px);
        }

        .tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .transaction-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .transaction-card {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: var(--rounded-lg);
            padding: 1.25rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .transaction-card.vip {
            border: 2px solid #f59e0b;
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(255, 255, 255, 0.95));
        }

        .vip-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: linear-gradient(135deg, #f59e0b, #f97316);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
        }

        .transaction-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .transaction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
        }

        .transaction-title {
            font-weight: 600;
            color: var(--text);
            font-size: 1rem;
        }

        .transaction-amount {
            font-weight: 700;
            color: var(--primary);
            font-size: 1rem;
        }

        .transaction-details {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .transaction-info {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .transaction-date,
        .transaction-duration,
        .transaction-progress,
        .transaction-daily-return,
        .transaction-claim-info,
        .transaction-return-preview,
        .transaction-claimed-date {
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .transaction-daily-return {
            color: #8b5cf6;
            font-weight: 600;
        }

        .transaction-claim-info {
            color: #f59e0b;
            font-weight: 600;
        }

        .transaction-return-preview {
            color: #10b981;
            font-weight: 600;
        }

        .transaction-return-preview.highlight {
            background: rgba(16, 185, 129, 0.1);
            padding: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .transaction-progress {
            font-weight: 600;
        }

        .transaction-status {
            padding: 0.4rem 0.875rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
            white-space: nowrap;
        }

        .transaction-status.active,
        .transaction-status.aktif {
            background-color: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .transaction-status.completed {
            background-color: #dbeafe;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .transaction-status.pending {
            background-color: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        .transaction-status.ready-claim {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
            }

            50% {
                box-shadow: 0 0 20px rgba(16, 185, 129, 0.8);
            }
        }

        /* Progress Bar Styles */
        .progress-container {
            margin: 1rem 0;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background-color: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.3s ease;
        }

        .progress-fill.completed {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
        }

        .progress-claimed {
            height: 100%;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            position: absolute;
            top: 0;
        }

        .progress-available {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #059669);
            position: absolute;
            top: 0;
            animation: glow 2s infinite alternate;
        }

        @keyframes glow {
            0% {
                opacity: 0.8;
            }

            100% {
                opacity: 1;
            }
        }

        .progress-text {
            font-size: 0.75rem;
            color: var(--text-light);
            margin-top: 0.25rem;
            text-align: center;
        }

        .transaction-actions {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed rgba(37, 99, 235, 0.2);
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .claim-button {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--rounded-lg);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            flex: 1;
        }

        .claim-button:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
        }

        .claim-amount {
            font-size: 0.75rem;
            opacity: 0.9;
        }

        .history-button {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
            border: 1px solid rgba(99, 102, 241, 0.3);
            padding: 0.75rem 1rem;
            border-radius: var(--rounded-lg);
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .history-button:hover {
            background: rgba(99, 102, 241, 0.2);
            transform: translateY(-1px);
        }

        .transaction-return {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px dashed rgba(37, 99, 235, 0.2);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .return-label {
            color: var(--text-light);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .return-amount {
            font-weight: 700;
            color: #10b981;
            font-size: 1rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--rounded-lg);
            border: 1px dashed rgba(255, 255, 255, 0.2);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.6;
        }

        .empty-state p {
            font-size: 1rem;
            margin-top: 0.5rem;
            opacity: 0.8;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 1rem;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            color: var(--text);
        }

        .close {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }

        .close:hover {
            color: #000;
        }

        .modal-body {
            padding: 1.5rem;
            max-height: 60vh;
            overflow-y: auto;
        }

        .loading {
            text-align: center;
            padding: 2rem;
            color: var(--text-light);
        }

        .claim-history-item {
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            margin-bottom: 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .claim-history-item:last-child {
            margin-bottom: 0;
        }

        .claim-day {
            font-weight: 600;
            color: var(--primary);
        }

        .claim-amount {
            color: #10b981;
            font-weight: 600;
        }

        .claim-date {
            color: var(--text-light);
            font-size: 0.875rem;
        }

        @media (max-width: 640px) {
            .transaction-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            .transaction-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .transaction-actions {
                flex-direction: column;
                gap: 0.75rem;
            }

            .claim-button,
            .history-button {
                width: 100%;
                justify-content: center;
            }

            .tabs {
                padding: 0.125rem;
            }

            .tab {
                padding: 0.75rem 0.5rem;
                font-size: 0.825rem;
            }

            .modal-content {
                width: 95%;
                margin: 10% auto;
            }
        }

        .tab-badge {
            background-color: #f97316;
            color: white;
            border-radius: 50%;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-left: 0.5rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.25rem;
            height: 1.25rem;
            line-height: 1;
        }

        .tab.active .tab-badge {
            background-color: #fff;
            color: #f97316;
        }
    </style>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));

                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    const tabId = tab.getAttribute('data-tab');
                    document.getElementById(tabId).classList.add('active');
                });
            });

            // Add loading animation for claim buttons
            document.querySelectorAll('.claim-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const button = this.querySelector('.claim-button');
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    button.disabled = true;
                });
            });
        });

        // Modal functions
        function showClaimHistory(transactionId) {
            const modal = document.getElementById('claimHistoryModal');
            const content = document.getElementById('claimHistoryContent');

            modal.style.display = 'block';
            content.innerHTML = '<div class="loading"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat...</div>';

            // Fetch claim history
            fetch(`/transactions/${transactionId}/claim-history`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.data.length > 0) {
                            let html = '';
                            data.data.forEach(claim => {
                                html += `
                                    <div class="claim-history-item">
                                        <div>
                                            <div class="claim-day">Hari ke-${claim.day_number}</div>
                                            <div class="claim-date">${new Date(claim.claim_date).toLocaleString('id-ID')}</div>
                                        </div>
                                        <div class="claim-amount">
                                            Rp ${Number(claim.claim_amount).toLocaleString('id-ID')}
                                        </div>
                                    </div>
                                `;
                            });
                            content.innerHTML = html;
                        } else {
                            content.innerHTML =
                                '<div class="empty-state"><i class="fas fa-history"></i><p>Belum ada riwayat claim</p></div>';
                        }
                    } else {
                        content.innerHTML =
                            '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat riwayat</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    content.innerHTML =
                        '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Terjadi kesalahan</p></div>';
                });
        }

        function closeModal() {
            document.getElementById('claimHistoryModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('claimHistoryModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
@endsection
