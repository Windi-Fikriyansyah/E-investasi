@extends('layouts.app')

@section('content')
    <div class="profile-container">
        <!-- Profile Header Section -->
        <div class="profile-header">
            <div class="profile-avatar">
                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=088742&color=fff"
                    alt="Profile Avatar">
            </div>
            <div class="profile-info">
                <h2>{{ auth()->user()->name }}</h2>
                <p class="email">{{ auth()->user()->email }}</p>
                <div class="membership-label vip-{{ substr(auth()->user()->keanggotaan ?? 'VIP 0', -1) }}">
                    {{ auth()->user()->keanggotaan ?? 'VIP 0' }}
                </div>
                <a href="{{ route('vip.rules') }}" class="vip-rules-btn">
                    <i class="fas fa-info-circle"></i> Aturan VIP
                </a>
                <p class="member-since">Member sejak {{ auth()->user()->created_at->format('d M Y') }}</p>
            </div>
        </div>

        <!-- Balance Card -->
        <div class="balance-card">
            <div class="balance-header">
                <h3>Saldo Anda</h3>
                <i class="fas fa-wallet"></i>
            </div>
            <div class="balance-amount">
                Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}
            </div>
            <div class="balance-actions">
                <a href="{{ route('deposit.index') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Deposit
                </a>
                <a href="{{ route('withdrawal.index') }}" class="btn btn-outline">
                    <i class="fas fa-arrow-right"></i> Tarik
                </a>
            </div>
        </div>

        <!-- Profile Menu -->
        <div class="profile-menu">
            <a href="{{ route('profile.edit') }}" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <div class="menu-content">
                    <h4>Edit Profil</h4>
                    <p>Ubah data pribadi Anda</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="{{ route('referral.index') }}" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-link"></i>
                </div>
                <div class="menu-content">
                    <h4>Link Referral</h4>
                    <p>Link Referral Anda</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="{{ route('bank.index') }}"
                class="menu-item d-flex align-items-center justify-between p-3 rounded shadow-sm">
                <div class="menu-icon me-3 text-primary">
                    <i class="fas fa-university fa-lg"></i>
                </div>
                <div class="menu-content flex-grow-1">
                    <h4 class="mb-0 fw-semibold">Atur Bank</h4>
                    <p class="text-muted mb-0 small">Kelola akun bank Anda</p>
                </div>
                <div class="text-muted">
                    <i class="fas fa-chevron-right"></i>
                </div>
            </a>


            <a href="{{ route('withdrawal.history') }}" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-hand-holding-usd"></i>
                </div>
                <div class="menu-content">
                    <h4>Riwayat Withdraw</h4>
                    <p>Lihat semua withdraw Anda</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="{{ route('deposit.riwayat') }}" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="menu-content">
                    <h4>Riwayat Deposit</h4>
                    <p>Lihat semua deposit Anda</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="https://t.me/+4hDdNT3klRwzYzI1" target="_blank" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-paper-plane"></i>
                </div>
                <div class="menu-content">
                    <h4>Bergabung Grub Telegram</h4>
                    <p>Gabung ke grup untuk informasi terbaru</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="{{ route('profile.ubahpassword') }}" class="menu-item">
                <div class="menu-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="menu-content">
                    <h4>Keamanan</h4>
                    <p>Ubah password dan pengaturan keamanan</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <a href="#" class="menu-item"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <div class="menu-icon">
                    <i class="fas fa-sign-out-alt"></i>
                </div>
                <div class="menu-content">
                    <h4>Logout</h4>
                    <p>Keluar dari akun Anda</p>
                </div>
                <i class="fas fa-chevron-right"></i>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>

    <style>
        .vip-rules-btn {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 0.5rem;
            font-size: 0.75rem;
            background-color: rgba(255, 255, 255, 0.2);
            color: #088742;
            text-decoration: none;
            margin-bottom: 0.5rem;
            transition: background-color 0.2s;
        }

        .vip-rules-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .vip-rules-btn i {
            margin-right: 0.25rem;
        }

        .profile-container {
            max-width: 600px;
            margin: 0 auto;
            margin-bottom: 2rem;
            padding: 0.5rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1rem;
            background: white;
            border-radius: var(--rounded-md);
            box-shadow: var(--shadow-sm);
        }

        .profile-avatar img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary);
        }

        .profile-info {
            margin-left: 1.5rem;
            flex: 1;
        }

        .profile-info h2 {
            font-size: 1.25rem;
            margin-bottom: 0.25rem;
            color: var(--text);
        }

        .profile-info .email {
            color: var(--text-light);
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .profile-info .member-since {
            color: var(--dark-gray);
            font-size: 0.75rem;
        }

        .balance-card {
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-md);
        }

        .balance-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .balance-header h3 {
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
            color: white;
        }

        .balance-header i {
            font-size: 1.5rem;
            opacity: 0.8;
        }

        .balance-amount {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
        }

        .balance-actions {
            display: flex;
            gap: 0.75rem;
        }

        .balance-actions .btn {
            flex: 1;
            padding: 0.75rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background-color: white;
            color: var(--primary);
        }

        .btn-outline {
            background-color: transparent;
            border: 1px solid white;
            color: white;
        }

        .profile-menu {
            background: white;
            border-radius: var(--rounded-md);
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            text-decoration: none;
            color: var(--text);
            border-bottom: 1px solid var(--gray);
            transition: background-color 0.2s;
        }

        .menu-item:last-child {
            border-bottom: none;
        }

        .menu-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .menu-icon {
            width: 40px;
            height: 40px;
            background-color: var(--primary-light);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .menu-icon i {
            font-size: 1rem;
        }

        .menu-content {
            flex: 1;
        }

        .menu-content h4 {
            font-size: 1rem;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }

        .menu-content p {
            font-size: 0.75rem;
            color: var(--text-light);
            margin: 0;
        }

        .menu-item i.fa-chevron-right {
            color: var(--dark-gray);
            font-size: 0.875rem;
        }

        @media (max-width: 480px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
                padding: 1.5rem;
            }

            .profile-info {
                margin-left: 0;
                margin-top: 1rem;
            }

            .balance-actions {
                flex-direction: column;
            }
        }

        .membership-label {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
            color: white;
        }

        /* Warna untuk masing-masing level VIP */
        .membership-label.vip-0 {
            background-color: #6b7280;
            /* Gray */
        }

        .membership-label.vip-1 {
            background-color: #10b981;
            /* Emerald */
        }

        .membership-label.vip-2 {
            background-color: #3b82f6;
            /* Blue */
        }

        .membership-label.vip-3 {
            background-color: #8b5cf6;
            /* Violet */
        }

        .membership-label.vip-4 {
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            /* Violet to Pink gradient */
        }

        .membership-label.vip-5 {
            background: linear-gradient(135deg, #ec4899, #ef4444);
            /* Pink to Red gradient */
        }

        .membership-label.vip-6 {
            background: linear-gradient(135deg, #f59e0b, #ef4444, #ec4899);
            /* Amber to Red to Pink gradient */
            background-size: 200% 200%;
            animation: rainbow 3s linear infinite;
        }

        /* Animasi untuk VIP 6 */
        @keyframes rainbow {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }
    </style>
@endsection
