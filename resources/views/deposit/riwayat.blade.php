@extends('layouts.app')

@section('content')
    <div class="deposit-history">


        <div class="tabs-container">
            <div class="tabs">
                <div class="tab active" data-tab="all">Semua</div>
                <div class="tab" data-tab="completed">Berhasil</div>
                <div class="tab" data-tab="pending">Pending</div>
                <div class="tab" data-tab="failed">Gagal</div>
            </div>
        </div>

        <div class="tab-content active" id="all">
            <div class="deposit-list">
                @forelse($deposits as $deposit)
                    <div class="deposit-card {{ $deposit->status }}">
                        <div class="deposit-info">
                            <div class="deposit-icon">
                                @if ($deposit->status == 'completed')
                                    <i class="fas fa-check-circle"></i>
                                @elseif($deposit->status == 'pending' || $deposit->status == 'processing')
                                    <i class="fas fa-clock text-warning"></i>
                                @else
                                    <i class="fas fa-times-circle"></i>
                                @endif
                            </div>
                            <div class="deposit-details">
                                <h3 class="deposit-amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</h3>
                                <p class="deposit-method">
                                    @if (!empty($deposit->payment_method))
                                        {{ ucfirst($deposit->payment_method) }}
                                    @else
                                        Metode Pembayaran Tidak Diketahui
                                    @endif
                                </p>
                                <p class="deposit-date">{{ $deposit->created_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="deposit-status">
                            @if ($deposit->status == 'completed')
                                <span class="status-badge completed">Berhasil</span>
                            @elseif($deposit->status == 'pending')
                                <span class="status-badge pending">Menunggu</span>
                                <a href="{{ route('deposit.continue', ['order_id' => $deposit->order_id]) }}"
                                    class="complete-payment-btn">
                                    Lanjutkan Pembayaran
                                </a>
                            @else
                                <span class="status-badge failed">
                                    {{ $deposit->status == 'cancelled' ? 'Dibatalkan' : 'Gagal' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="empty-text">Belum ada riwayat deposit</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tab-content" id="completed">
            <div class="deposit-list">
                @foreach ($deposits->where('status', 'completed') as $deposit)
                    <div class="deposit-card completed">
                        <div class="deposit-info">
                            <div class="deposit-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="deposit-details">
                                <h3 class="deposit-amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</h3>
                                <p class="deposit-method">
                                    @if (!empty($deposit->payment_method))
                                        {{ ucfirst($deposit->payment_method) }}
                                    @else
                                        Metode Pembayaran Tidak Diketahui
                                    @endif
                                </p>
                                <p class="deposit-date">{{ $deposit->created_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="deposit-status">
                            <span class="status-badge completed">Berhasil</span>
                        </div>
                    </div>
                @endforeach

                @if ($deposits->where('status', 'completed')->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="empty-text">Belum ada deposit yang berhasil</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="tab-content" id="pending">
            <div class="deposit-list">
                @foreach ($deposits->where('status', 'pending') as $deposit)
                    <div class="deposit-card pending">
                        <div class="deposit-info">
                            <div class="deposit-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="deposit-details">
                                <h3 class="deposit-amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</h3>
                                <p class="deposit-method">
                                    @if (!empty($deposit->payment_method))
                                        {{ ucfirst($deposit->payment_method) }}
                                    @else
                                        Metode Pembayaran Tidak Diketahui
                                    @endif
                                </p>
                                <p class="deposit-date">{{ $deposit->created_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="deposit-status">
                            <span class="status-badge pending">Menunggu</span>
                            <a href="{{ route('deposit.continue', ['order_id' => $deposit->order_id]) }}"
                                class="complete-payment-btn">
                                Selesaikan Pembayaran
                            </a>
                        </div>
                    </div>
                @endforeach

                @if ($deposits->where('status', 'pending')->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="empty-text">Belum ada deposit yang menunggu</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="tab-content" id="failed">
            <div class="deposit-list">
                @foreach ($deposits->whereIn('status', ['failed', 'cancelled']) as $deposit)
                    <div class="deposit-card failed">
                        <div class="deposit-info">
                            <div class="deposit-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="deposit-details">
                                <h3 class="deposit-amount">Rp {{ number_format($deposit->amount, 0, ',', '.') }}</h3>
                                <p class="deposit-method">
                                    @if (!empty($deposit->payment_method))
                                        {{ ucfirst($deposit->payment_method) }}
                                    @else
                                        Metode Pembayaran Tidak Diketahui
                                    @endif
                                </p>
                                <p class="deposit-date">{{ $deposit->created_at->format('d F Y, H:i') }} WIB</p>
                            </div>
                        </div>
                        <div class="deposit-status">
                            <span class="status-badge failed">
                                {{ $deposit->status == 'cancelled' ? 'Dibatalkan' : 'Gagal' }}
                            </span>
                        </div>
                    </div>
                @endforeach

                @if ($deposits->whereIn('status', ['failed', 'cancelled'])->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <p class="empty-text">Belum ada deposit yang gagal</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .deposit-history {
            padding: 0;
            margin-bottom: 25px;
            color: var(--white);
        }

        .tabs-container {
            margin: 1.5rem 0;
            background: rgba(255, 255, 255, 0.1);
            border-radius: var(--rounded-lg);
            padding: 0.5rem;
        }

        .tabs {
            display: flex;
            justify-content: space-between;
            background: transparent;
            border-radius: var(--rounded-md);
            overflow: hidden;
        }

        .tab {
            flex: 1;
            text-align: center;
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            color: rgba(255, 255, 255, 0.7);
            transition: all 0.3s ease;
            border-radius: var(--rounded-md);
        }

        .tab.active {
            background: rgba(255, 255, 255, 0.9);
            color: var(--primary);
            font-weight: 600;
            box-shadow: var(--shadow-sm);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .deposit-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 1rem;
        }

        .deposit-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--rounded-lg);
            padding: 1.25rem;
            box-shadow: var(--shadow-sm);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s ease;
            color: var(--text);
        }

        .deposit-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .deposit-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .deposit-icon {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .deposit-card.completed .deposit-icon {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .deposit-card.pending .deposit-icon {
            background-color: rgba(251, 191, 36, 0.1);
            color: #f59e0b;
        }

        .deposit-card.failed .deposit-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .deposit-card.cancelled .deposit-icon {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .deposit-details {
            display: flex;
            flex-direction: column;
        }

        .deposit-amount {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.25rem;
        }

        .deposit-method {
            font-size: 0.875rem;
            color: var(--text-light);
            margin-bottom: 0.25rem;
        }

        .deposit-date {
            font-size: 0.75rem;
            color: var(--dark-gray);
        }

        .deposit-status {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 0.5rem;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-badge.completed {
            background-color: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .status-badge.pending {
            background-color: rgba(251, 191, 36, 0.1);
            color: #f59e0b;
        }

        .status-badge.failed {
            background-color: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .complete-payment-btn {
            padding: 0.5rem 1rem;
            background-color: var(--primary);
            color: white;
            border-radius: var(--rounded-md);
            font-size: 0.75rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .complete-payment-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: rgba(255, 255, 255, 0.9);
            border-radius: var(--rounded-lg);
            margin-top: 1rem;
            color: var(--text);
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--primary);
            margin-bottom: 1rem;
            opacity: 0.7;
        }

        .empty-text {
            color: var(--text-light);
            font-size: 0.925rem;
        }

        @media (max-width: 640px) {
            .deposit-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }

            .deposit-status {
                width: 100%;
                align-self: stretch;
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
            }

            .complete-payment-btn {
                flex-grow: 1;
                text-align: center;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Tab switching functionality
            $('.tab').click(function() {
                const tabId = $(this).data('tab');

                // Remove active class from all tabs and contents
                $('.tab').removeClass('active');
                $('.tab-content').removeClass('active');

                // Add active class to clicked tab and corresponding content
                $(this).addClass('active');
                $('#' + tabId).addClass('active');
            });
        });
    </script>
@endsection
