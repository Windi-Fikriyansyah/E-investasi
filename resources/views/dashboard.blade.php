@extends('layouts.app')

@section('content')
    <div class="profile-container">
        <!-- Balance Card - BRImo Style -->
        <div class="balance-card">
            <div class="balance-header">
                <div>
                    <div class="greeting">Hai,</div>
                    <div class="user-name">{{ auth()->user()->phone ?? 'User' }}</div>
                </div>
                <div class="header-icons">

                    <div class="help-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                </div>
            </div>

            <div class="balance-section">
                <div class="balance-title">Saldo Utama</div>

                <div class="balance-amount" id="balanceAmount">
                    Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}
                </div>


            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <a href="{{ route('deposit.index') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <span>Deposit</span>
                </a>
                <a href="{{ route('withdrawal.index') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <span>Tarik</span>
                </a>
                <a href="{{ route('pesanan') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <span>Pesanan</span>
                </a>
                <a href="{{ route('referral.index') }}" class="action-btn">
                    <div class="action-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <span>Undang</span>
                </a>
            </div>
        </div>

        <div class="profile-menu">
            <div class="menu-section">
                <h3>Transaksi</h3>

                <a href="{{ route('produk.index') }}" class="menu-item">
                    <div class="menu-icon bg-orange">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Investasi</div>
                        <div class="menu-subtitle">Lihat semua Investasi</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('withdrawal.history') }}" class="menu-item">
                    <div class="menu-icon bg-red">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Riwayat Withdraw</div>
                        <div class="menu-subtitle">Lihat semua withdraw Anda</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('deposit.riwayat') }}" class="menu-item">
                    <div class="menu-icon bg-green">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Riwayat Deposit</div>
                        <div class="menu-subtitle">Lihat semua deposit Anda</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>

            <div class="menu-section">
                <h3>Bantuan & Lainnya</h3>
                <a href="{{ route('referral.index') }}" class="menu-item">
                    <div class="menu-icon bg-purple">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Undang</div>
                        <div class="menu-subtitle">Link Referral Anda</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('bonus.index') }}" class="menu-item">
                    <div class="menu-icon bg-teal">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Tim Saya</div>
                        <div class="menu-subtitle">Tim saya</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="https://t.me/+4hDdNT3klRwzYzI1" target="_blank" class="menu-item">
                    <div class="menu-icon bg-blue">
                        <i class="fab fa-telegram"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Bergabung Grup Telegram</div>
                        <div class="menu-subtitle">Gabung ke grup untuk informasi terbaru</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-external-link-alt"></i>
                    </div>
                </a>
            </div>

            <div class="menu-section">
                <h3>Akun & Profil</h3>

                <a href="{{ route('bank.index') }}" class="menu-item">
                    <div class="menu-icon bg-indigo">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Akun Bank</div>
                        <div class="menu-subtitle">Ubah data pribadi Anda</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="{{ route('profile.ubahpassword') }}" class="menu-item">
                    <div class="menu-icon bg-red">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Keamanan</div>
                        <div class="menu-subtitle">Ubah password dan pengaturan keamanan</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>

                <a href="#" class="menu-item"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <div class="menu-icon bg-gray">
                        <i class="fas fa-sign-out-alt"></i>
                    </div>
                    <div class="menu-content">
                        <div class="menu-title">Logout</div>
                        <div class="menu-subtitle">Keluar dari akun Anda</div>
                    </div>
                    <div class="menu-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </a>
            </div>
        </div>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <style>
        .profile-container {
            max-width: 430px;
            margin: 0 auto;
            padding: 0;
            background: #f5f7fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Balance Card - BRImo Style */
        .balance-card {
            background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
            border-radius: 0 0 25px 25px;
            padding: 25px 20px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2);
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .balance-card::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -10%;
            width: 180px;
            height: 180px;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }

        .balance-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
            position: relative;
            z-index: 2;
        }

        .greeting {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 2px;
        }

        .user-name {
            font-size: 18px;
            font-weight: 600;
        }

        .header-icons {
            display: flex;
            gap: 12px;
        }

        .notification-icon,
        .help-icon {
            width: 36px;
            height: 36px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            position: relative;
            transition: background-color 0.2s ease;
        }

        .notification-icon:hover,
        .help-icon:hover {
            background: rgba(255, 255, 255, 0.25);
        }

        .notification-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
        }

        .balance-section {
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }

        .balance-title {
            font-size: 14px;
            opacity: 0.9;
            margin-bottom: 8px;
        }

        .balance-amount {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }

        .all-accounts {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            opacity: 0.9;
            cursor: pointer;
            transition: opacity 0.2s ease;
            color: white;
            text-decoration: none;
        }

        .all-accounts:hover {
            opacity: 1;
            color: white;
            text-decoration: none;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            position: relative;
            z-index: 2;
        }

        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: white;
            gap: 8px;
            transition: transform 0.2s ease;
        }

        .action-btn:hover {
            transform: translateY(-3px);
        }

        .action-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.2s ease;
        }

        .action-btn:hover .action-icon {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .action-btn span {
            font-size: 12px;
            text-align: center;
            line-height: 1.2;
            font-weight: 500;
        }

        /* Profile Menu */
        .profile-menu {
            padding: 20px 16px;
        }

        .menu-section {
            background: white;
            border-radius: 16px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .menu-section:hover {
            transform: translateY(-2px);
        }

        .menu-section h3 {
            padding: 18px 16px 12px;
            font-size: 16px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 14px 16px;
            text-decoration: none;
            color: #1e293b;
            border-top: 1px solid #f1f5f9;
            transition: all 0.2s ease;
        }

        .menu-item:first-of-type {
            border-top: none;
        }

        .menu-item:hover {
            background-color: #f8fafc;
        }

        .menu-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 14px;
            color: white;
            font-size: 18px;
            transition: transform 0.2s ease;
        }

        .menu-item:hover .menu-icon {
            transform: scale(1.1);
        }

        .bg-blue {
            background: #3b82f6;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .bg-indigo {
            background: #6366f1;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }

        .bg-purple {
            background: #8b5cf6;
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        .bg-red {
            background: #ef4444;
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .bg-orange {
            background: #f97316;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .bg-green {
            background: #10b981;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-teal {
            background: #14b8a6;
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        }

        .bg-gray {
            background: #64748b;
            background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        }

        .menu-content {
            flex: 1;
        }

        .menu-title {
            font-size: 15px;
            font-weight: 600;
            margin-bottom: 3px;
        }

        .menu-subtitle {
            font-size: 12px;
            color: #64748b;
        }

        .menu-arrow {
            color: #cbd5e1;
            font-size: 12px;
            transition: transform 0.2s ease;
        }

        .menu-item:hover .menu-arrow {
            color: #94a3b8;
            transform: translateX(3px);
        }

        /* Mobile Responsive */
        @media (max-width: 480px) {
            .profile-container {
                max-width: 100%;
            }

            .balance-card {
                padding: 20px 16px;
                border-radius: 0 0 20px 20px;
            }

            .quick-actions {
                gap: 12px;
            }

            .action-icon {
                width: 46px;
                height: 46px;
                font-size: 18px;
            }

            .action-btn span {
                font-size: 11px;
            }
        }
    </style>

    <script>
        // Add subtle animations on load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.service-item, .menu-item');
            elements.forEach((el, index) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(10px)';
                setTimeout(() => {
                    el.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
@endsection
