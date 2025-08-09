@extends('layouts.app')

@section('content')
    <!-- Balance Card - Consistent with app style -->
    <div class="balance-card">
        <div class="balance-header">
            <h3>Tim Dashboard</h3>
            <i class="fas fa-users"></i>
        </div>
        <div class="balance-amount">
            Rp
            {{ number_format($level1Commission + $level2Commission + $level3Commission + $totalClaimedBonuses, 0, ',', '.') }}
        </div>
        <div class="balance-actions">
            <div class="balance-stat">
                <span class="stat-number">{{ $level1Investors + $level2Investors + $level3Investors }}</span>
                <span class="stat-label">Total Investor</span>
            </div>
            <div class="balance-stat">
                <span class="stat-number">{{ $level1Count + $level2Count + $level3Count }}</span>
                <span class="stat-label">Total Anggota</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="profile-menu">
        <div class="tabs-navigation">
            <button class="tab-btn active" data-tab="overview">
                <i class="fas fa-chart-line"></i>
                <span>Overview</span>
            </button>
            <button class="tab-btn" data-tab="level1">
                <i class="fas fa-users"></i>
                <span>Level 1</span>
            </button>
            <button class="tab-btn" data-tab="level2">
                <i class="fas fa-user-friends"></i>
                <span>Level 2</span>
            </button>
            <button class="tab-btn" data-tab="level3">
                <i class="fas fa-users-cog"></i>
                <span>Level 3</span>
            </button>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content">
        <!-- Overview Tab -->
        <div class="tab-pane active" id="overview">
            <!-- Investment Return Highlight -->
            <div class="profile-menu highlight-card">
                <div class="highlight-content">
                    <div class="highlight-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="highlight-text">
                        <h4>20% Bonus</h4>
                        <p>Dari Investasi Teman</p>
                    </div>
                </div>
            </div>

            <!-- Bonus Milestones -->
            <div class="profile-menu">
                <div class="menu-header">
                    <h3><i class="fas fa-star"></i> Bonus Undangan Khusus</h3>
                    <p>Dapatkan bonus tambahan untuk setiap milestone undangan yang Anda capai</p>
                </div>

                <!-- Milestone 1: Starter Bonus -->
                <div
                    class="menu-item milestone-item {{ $level1Investors >= 5 ? 'completed' : ($level1Investors > 0 ? 'in-progress' : '') }}">
                    <div class="menu-icon {{ $level1Investors >= 5 ? 'completed' : '' }}">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Starter Bonus</h4>
                        <p>Rp 30.000 - {{ $level1Investors }}/5 investor</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(($level1Investors / 5) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="milestone-status">
                        @if ($level1Investors >= 5)
                            @if (in_array('starter', $claimedBonuses))
                                <i class="fas fa-check-circle completed"></i>
                            @else
                                <form action="{{ route('claim.bonus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="milestone" value="starter">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            @endif
                        @else
                            <span class="remaining">{{ 5 - $level1Investors }} lagi</span>
                        @endif
                    </div>
                </div>

                <!-- Milestone 2: Bronze Agent -->
                <div
                    class="menu-item milestone-item {{ $level1Investors >= 20 ? 'completed' : ($level1Investors > 5 ? 'in-progress' : '') }}">
                    <div class="menu-icon bronze {{ $level1Investors >= 20 ? 'completed' : '' }}">
                        <i class="fas fa-medal"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Bronze Agent</h4>
                        <p>Rp 100.000 - {{ $level1Investors }}/20 investor</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(($level1Investors / 20) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="milestone-status">
                        @if ($level1Investors >= 20)
                            @if (in_array('starter', $claimedBonuses))
                                <i class="fas fa-check-circle completed"></i>
                            @else
                                <form action="{{ route('claim.bonus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="milestone" value="starter">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            @endif
                        @else
                            <span class="remaining">{{ 20 - $level1Investors }} lagi</span>
                        @endif
                    </div>
                </div>

                <!-- Milestone 3: Silver Agent -->
                <div
                    class="menu-item milestone-item {{ $level1Investors >= 35 ? 'completed' : ($level1Investors > 20 ? 'in-progress' : '') }}">
                    <div class="menu-icon silver {{ $level1Investors >= 35 ? 'completed' : '' }}">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Silver Agent</h4>
                        <p>Rp 150.000 - {{ $level1Investors }}/35 investor</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(($level1Investors / 35) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="milestone-status">
                        @if ($level1Investors >= 35)
                            @if (in_array('starter', $claimedBonuses))
                                <i class="fas fa-check-circle completed"></i>
                            @else
                                <form action="{{ route('claim.bonus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="milestone" value="starter">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            @endif
                        @else
                            <span class="remaining">{{ 35 - $level1Investors }} lagi</span>
                        @endif
                    </div>
                </div>

                <!-- Milestone 4: Gold Agent -->
                <div
                    class="menu-item milestone-item {{ $level1Investors >= 50 ? 'completed' : ($level1Investors > 35 ? 'in-progress' : '') }}">
                    <div class="menu-icon gold {{ $level1Investors >= 50 ? 'completed' : '' }}">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Gold Agent</h4>
                        <p>Rp 400.000 - {{ $level1Investors }}/50 investor</p>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ min(($level1Investors / 50) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="milestone-status">
                        @if ($level1Investors >= 50)
                            @if (in_array('starter', $claimedBonuses))
                                <i class="fas fa-check-circle completed"></i>
                            @else
                                <form action="{{ route('claim.bonus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="milestone" value="starter">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            @endif
                        @else
                            <span class="remaining">{{ 50 - $level1Investors }} lagi</span>
                        @endif
                    </div>
                </div>

                <!-- Milestone 5: Platinum Agent -->
                <div
                    class="menu-item milestone-item platinum-special {{ $level1Investors >= 100 ? 'completed' : ($level1Investors > 50 ? 'in-progress' : '') }}">
                    <div class="menu-icon platinum {{ $level1Investors >= 100 ? 'completed' : '' }}">
                        <i class="fas fa-gem"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Platinum Agent</h4>
                        <p>Rp 850.000 - {{ $level1Investors }}/100 investor</p>
                        <div class="progress-bar">
                            <div class="progress-fill platinum"
                                style="width: {{ min(($level1Investors / 100) * 100, 100) }}%"></div>
                        </div>
                    </div>
                    <div class="milestone-status">
                        @if ($level1Investors >= 100)
                            @if (in_array('starter', $claimedBonuses))
                                <i class="fas fa-check-circle completed"></i>
                            @else
                                <form action="{{ route('claim.bonus') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="milestone" value="starter">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            @endif
                        @else
                            <span class="remaining">{{ 100 - $level1Investors }} lagi</span>
                        @endif
                    </div>
                    <div class="platinum-badge">
                        <i class="fas fa-star"></i> PLATINUM
                    </div>
                </div>
            </div>

            <!-- Current Level Summary -->
            <div class="profile-menu level-summary">
                @php
                    $currentLevel = 'Starter';
                    $currentLevelIcon = 'fa-user';
                    $totalBonus = 0;

                    if ($level1Investors >= 100) {
                        $currentLevel = 'Platinum Agent';
                        $currentLevelIcon = 'fa-gem';
                        $totalBonus = 1530000; // Sum of all bonuses
                    } elseif ($level1Investors >= 50) {
                        $currentLevel = 'Gold Agent';
                        $currentLevelIcon = 'fa-crown';
                        $totalBonus = 680000;
                    } elseif ($level1Investors >= 35) {
                        $currentLevel = 'Silver Agent';
                        $currentLevelIcon = 'fa-award';
                        $totalBonus = 280000;
                    } elseif ($level1Investors >= 20) {
                        $currentLevel = 'Bronze Agent';
                        $currentLevelIcon = 'fa-medal';
                        $totalBonus = 130000;
                    } elseif ($level1Investors >= 5) {
                        $currentLevel = 'Starter Bonus';
                        $currentLevelIcon = 'fa-trophy';
                        $totalBonus = 30000;
                    }
                @endphp

                <div class="menu-item">
                    <div class="menu-icon current-level">
                        <i class="fas {{ $currentLevelIcon }}"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Level Saat Ini: {{ $currentLevel }}</h4>
                        <p>Total Bonus Diperoleh: Rp {{ number_format($totalBonus, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="stats-grid">
                <div class="profile-menu stat-card">
                    <div class="menu-item">
                        <div class="menu-icon level-1">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="menu-content">
                            <h4>{{ $level1Count }}</h4>
                            <p>Tim Level 1</p>
                        </div>
                    </div>
                </div>
                <div class="profile-menu stat-card">
                    <div class="menu-item">
                        <div class="menu-icon level-2">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="menu-content">
                            <h4>{{ $level2Count }}</h4>
                            <p>Tim Level 2</p>
                        </div>
                    </div>
                </div>
                <div class="profile-menu stat-card">
                    <div class="menu-item">
                        <div class="menu-icon level-3">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div class="menu-content">
                            <h4>{{ $level3Count }}</h4>
                            <p>Tim Level 3</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 1 Tab -->
        <div class="tab-pane" id="level1">
            <div class="profile-menu">
                <div class="menu-header">
                    <h3><i class="fas fa-users"></i> Tim Level 1</h3>
                    <span class="level-badge">Direct Referral</span>
                </div>

                <div class="menu-item">
                    <div class="menu-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level1Count }}</h4>
                        <p>Anggota Tim</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon investors">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level1Investors }}</h4>
                        <p>Investor Aktif</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon commission">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Rp {{ number_format($level1Commission, 0, ',', '.') }}</h4>
                        <p>Total Komisi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 2 Tab -->
        <div class="tab-pane" id="level2">
            <div class="profile-menu">
                <div class="menu-header">
                    <h3><i class="fas fa-user-friends"></i> Tim Level 2</h3>
                    <span class="level-badge">Indirect Referral</span>
                </div>

                <div class="menu-item">
                    <div class="menu-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level2Count }}</h4>
                        <p>Anggota Tim</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon investors">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level2Investors }}</h4>
                        <p>Investor Aktif</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon commission">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Rp {{ number_format($level2Commission, 0, ',', '.') }}</h4>
                        <p>Total Komisi</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Level 3 Tab -->
        <div class="tab-pane" id="level3">
            <div class="profile-menu">
                <div class="menu-header">
                    <h3><i class="fas fa-users-cog"></i> Tim Level 3</h3>
                    <span class="level-badge">Extended Network</span>
                </div>

                <div class="menu-item">
                    <div class="menu-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level3Count }}</h4>
                        <p>Anggota Tim</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon investors">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="menu-content">
                        <h4>{{ $level3Investors }}</h4>
                        <p>Investor Aktif</p>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="menu-icon commission">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="menu-content">
                        <h4>Rp {{ number_format($level3Commission, 0, ',', '.') }}</h4>
                        <p>Total Komisi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .claim-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-sm);
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .claim-btn:hover {
            background: linear-gradient(135deg, #059669, #047857);
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Additional styles specific to referral dashboard */
        .balance-stat {
            text-align: center;
        }

        .balance-stat .stat-number {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .balance-stat .stat-label {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        /* Tab Navigation */
        .tabs-navigation {
            display: flex;
            background: none;
            border: none;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0;
            margin: 0;
        }

        .tabs-navigation::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.875rem;
            white-space: nowrap;
            position: relative;
            min-width: fit-content;
            flex: 1;
            justify-content: center;
            border-bottom: 1px solid var(--gray);
        }

        .tab-btn:hover {
            color: var(--primary);
            background: var(--gray-light);
        }

        .tab-btn.active {
            color: var(--primary);
            background: var(--secondary);
            font-weight: 600;
            border-bottom-color: var(--primary);
        }

        .tab-btn i {
            font-size: 1rem;
        }

        /* Tab Content */
        .tab-content {
            margin-top: 1.5rem;
            margin-bottom: 2rem;
        }

        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Menu Header */
        .menu-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid var(--gray);
        }

        .menu-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text);
            margin: 0 0 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .menu-header p {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin: 0;
            line-height: 1.4;
        }

        .level-badge {
            background: var(--secondary);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 500;
            margin-left: auto;
        }

        /* Highlight Card */
        .highlight-card {
            background: var(--gradient);
            color: white;
            margin-bottom: 1.5rem;
        }

        .highlight-content {
            display: flex;
            align-items: center;
            padding: 1.5rem;
            gap: 1rem;
        }

        .highlight-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .highlight-text h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
        }

        .highlight-text p {
            font-size: 0.875rem;
            margin: 0;
            opacity: 0.9;
        }

        /* Milestone Items */
        .milestone-item {
            position: relative;
        }

        .milestone-item.completed .menu-icon {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .milestone-item.in-progress .menu-icon {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        }

        .milestone-item.completed {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border-left: 4px solid #10b981;
        }

        .milestone-item.in-progress {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
            border-left: 4px solid #f59e0b;
        }

        .menu-icon.bronze {
            background: linear-gradient(135deg, #cd7f32, #b8860b) !important;
        }

        .menu-icon.silver {
            background: linear-gradient(135deg, #c0c0c0, #a8a8a8) !important;
        }

        .menu-icon.gold {
            background: linear-gradient(135deg, #ffd700, #ffb347) !important;
        }

        .menu-icon.platinum {
            background: linear-gradient(135deg, #6366f1, #4f46e5) !important;
        }

        /* Progress Bar */
        .progress-bar {
            width: 100%;
            height: 4px;
            background: var(--gray);
            border-radius: var(--rounded-full);
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: var(--rounded-full);
            transition: width 0.6s ease;
        }

        .progress-fill.platinum {
            background: linear-gradient(135deg, #6366f1, #4f46e5);
        }

        /* Milestone Status */
        .milestone-status {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
        }

        .milestone-status .completed {
            color: #10b981;
            font-size: 1.25rem;
        }

        .milestone-status .remaining {
            color: #f59e0b;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            background: #fef3c7;
            border-radius: var(--rounded-sm);
        }

        /* Platinum Badge */
        .platinum-special {
            position: relative;
        }

        .platinum-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: linear-gradient(135deg, #6366f1, #4f46e5);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: var(--rounded-sm);
            font-size: 0.625rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        /* Level Summary */
        .level-summary .menu-icon.current-level {
            background: linear-gradient(135deg, #2563eb, #3b82f6) !important;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            margin-bottom: 0;
        }

        .stat-card .menu-item {
            border: none;
            padding: 1rem;
            text-align: center;
        }

        .stat-card .menu-content {
            text-align: center;
        }

        .stat-card .menu-content h4 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .stat-card .menu-content p {
            font-size: 0.75rem;
        }

        .menu-icon.level-1 {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        }

        .menu-icon.level-2 {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .menu-icon.level-3 {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        }

        .menu-icon.users {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8) !important;
        }

        .menu-icon.investors {
            background: linear-gradient(135deg, #10b981, #059669) !important;
        }

        .menu-icon.commission {
            background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .tab-btn {
                flex-direction: column;
                gap: 0.25rem;
                padding: 1rem 0.75rem;
            }

            .tab-btn i {
                font-size: 0.875rem;
            }

            .tab-btn span {
                font-size: 0.75rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .balance-actions {
                gap: 1rem;
            }

            .highlight-content {
                padding: 1rem;
            }

            .menu-header {
                padding: 1rem;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabPanes = document.querySelectorAll('.tab-pane');

            tabBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetTab = this.dataset.tab;

                    // Remove active class from all tabs and panes
                    tabBtns.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));

                    // Add active class to clicked tab and corresponding pane
                    this.classList.add('active');
                    document.getElementById(targetTab).classList.add('active');
                });
            });

            // Animate elements on load
            const animateElements = document.querySelectorAll('.profile-menu, .balance-card');
            animateElements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 100);
            });


            @if (session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Sukses!',
                    text: '{{ session('success') }}',
                    confirmButtonColor: '#3b82f6',
                    timer: 3000
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '{{ session('error') }}',
                    confirmButtonColor: '#ef4444'
                });
            @endif

            // Tambahkan event listener untuk semua form claim
            document.querySelectorAll('form[action="{{ route('claim.bonus') }}"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const form = this;
                    const button = form.querySelector('button[type="submit"]');
                    const originalText = button.innerHTML;

                    // Tampilkan loading
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                    button.disabled = true;

                    fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': form.querySelector('input[name="_token"]')
                                    .value,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                milestone: form.querySelector('input[name="milestone"]')
                                    .value
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sukses!',
                                    text: data.message,
                                    confirmButtonColor: '#3b82f6',
                                    timer: 3000
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: data.message || 'Terjadi kesalahan',
                                    confirmButtonColor: '#ef4444'
                                });
                                button.innerHTML = originalText;
                                button.disabled = false;
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Terjadi kesalahan jaringan',
                                confirmButtonColor: '#ef4444'
                            });
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                });
            });
        });
    </script>
@endsection
