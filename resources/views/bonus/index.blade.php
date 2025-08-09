@extends('layouts.app')

@section('content')
    <!-- Referral Card with Modern Styling -->
    <div class="referral-card">
        <div class="referral-header">
            <div class="referral-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="referral-header-content">
                <div class="referral-title-section">
                    <h2 class="referral-title">Program Referral</h2>
                    <span class="referral-badge">Dapatkan Bonus</span>
                </div>
                <div class="referral-link-container">
                    <div class="referral-link-box">
                        <input type="text" id="referralLink" class="referral-link-input"
                            value="{{ url('/register?ref=' . Auth::user()->referral_code) }}" readonly>
                        <button class="copy-referral-btn" onclick="copyReferralCode(event)">
                            <i class="fas fa-copy"></i>
                            <span>Salin</span>
                        </button>
                    </div>
                    <small class="referral-link-note">Bagikan link ini untuk mendapatkan komisi</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Information Card -->
    <div class="commission-info-card">
        <div class="commission-rules">
            <div class="rule-item">
                <div class="rule-number">1</div>
                <div class="rule-content">
                    <p>Ketika bawahan Level 1 Anda melakukan investasi pertama, Anda bisa mendapatkan <strong>bonus sebesar
                            20%</strong>.</p>
                </div>
            </div>

            <div class="rule-item">
                <div class="rule-number">2</div>
                <div class="rule-content">
                    <p>Ajak teman Anda untuk melakukan isi ulang dan membeli produk investasi di SmartNiuVolt, Anda akan
                        mendapatkan imbalan (komisi):</p>
                    <div class="commission-levels">
                        <div class="level-item">
                            <div class="level-badge level-1">Level 1</div>
                            <div class="level-commission">Komisi 20%</div>
                        </div>
                        <div class="level-item">
                            <div class="level-badge level-2">Level 2</div>
                            <div class="level-commission">Komisi 3%</div>
                        </div>
                        <div class="level-item">
                            <div class="level-badge level-3">Level 3</div>
                            <div class="level-commission">Komisi 1%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Examples Card -->
    <div class="commission-examples-card">
        <h3 class="examples-title">Contoh Perhitungan Komisi</h3>
        <div class="examples-grid">
            <div class="example-item">
                <div class="example-level">Level 1</div>
                <div class="example-calculation">
                    <div class="example-investment">Investasi: <strong>Rp 1.000.000</strong></div>
                    <div class="example-result">Komisi: <strong>Rp 200.000</strong></div>
                </div>
            </div>

            <div class="example-item">
                <div class="example-level">Level 2</div>
                <div class="example-calculation">
                    <div class="example-investment">Investasi: <strong>Rp 1.000.000</strong></div>
                    <div class="example-result">Komisi: <strong>Rp 30.000</strong></div>
                </div>
            </div>

            <div class="example-item">
                <div class="example-level">Level 3</div>
                <div class="example-calculation">
                    <div class="example-investment">Investasi: <strong>Rp 1.000.000</strong></div>
                    <div class="example-result">Komisi: <strong>Rp 10.000</strong></div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Referral Card Styling */
        .referral-card {
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-xl);
            padding: 2rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: relative;
            overflow: hidden;
        }

        .referral-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .referral-header {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            position: relative;
        }

        @media (min-width: 768px) {
            .referral-header {
                flex-direction: row;
                align-items: flex-start;
                gap: 1.5rem;
            }
        }

        .referral-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--rounded-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow-subtle);
        }

        .referral-header-content {
            flex: 1;
            min-width: 0;
        }

        .referral-title-section {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .referral-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.025em;
        }

        @media (max-width: 480px) {
            .referral-title {
                font-size: 1.25rem;
            }
        }

        .referral-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            white-space: nowrap;
        }

        .referral-link-container {
            width: 100%;
        }

        .referral-link-box {
            display: flex;
            background: rgba(255, 255, 255, 0.15);
            border-radius: var(--rounded-lg);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            margin-bottom: 0.5rem;
        }

        .referral-link-input {
            flex: 1;
            padding: 0.875rem 1rem;
            background: transparent;
            border: none;
            color: white;
            font-size: 0.875rem;
            min-width: 0;
            outline: none;
        }

        .referral-link-input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .copy-referral-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.875rem 1.25rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .copy-referral-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        .copy-referral-btn:active {
            transform: translateY(0);
        }

        .referral-link-note {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.75rem;
            font-weight: 500;
        }

        /* Commission Info Card */
        .commission-info-card {
            background: white;
            border-radius: var(--rounded-xl);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--gray);
        }

        .commission-rules {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .rule-item {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
        }

        .rule-number {
            width: 32px;
            height: 32px;
            background: var(--gradient);
            color: white;
            border-radius: var(--rounded-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .rule-content {
            flex: 1;
        }

        .rule-content p {
            margin: 0;
            color: var(--text);
            font-size: 0.925rem;
            line-height: 1.6;
        }

        .commission-levels {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .level-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.875rem 1rem;
            background: var(--gray-light);
            border-radius: var(--rounded-lg);
            border: 1px solid var(--gray);
        }

        .level-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: var(--rounded-full);
            color: white;
        }

        .level-badge.level-1 {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .level-badge.level-2 {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        .level-badge.level-3 {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .level-commission {
            font-weight: 700;
            color: var(--text);
            font-size: 0.925rem;
        }

        /* Commission Examples Card */
        .commission-examples-card {
            background: white;
            border-radius: var(--rounded-xl);
            padding: 1.5rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--gray);
        }

        .examples-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 1.25rem;
            letter-spacing: -0.025em;
        }

        .examples-grid {
            display: grid;
            gap: 1rem;
        }

        @media (min-width: 640px) {
            .examples-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            }
        }

        .example-item {
            background: var(--gray-light);
            border-radius: var(--rounded-lg);
            padding: 1.25rem;
            border: 1px solid var(--gray);
            transition: all 0.2s ease;
        }

        .example-item:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-soft);
        }

        .example-level {
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.75rem;
        }

        .example-calculation {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .example-investment {
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .example-result {
            font-size: 0.925rem;
            color: var(--text);
        }

        .example-investment strong,
        .example-result strong {
            color: var(--text);
            font-weight: 700;
        }

        /* Mobile Responsive Adjustments */
        @media (max-width: 480px) {

            .referral-card,
            .commission-info-card,
            .commission-examples-card {
                padding: 1.25rem;
                margin-bottom: 1rem;
            }

            .referral-icon {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }

            .copy-referral-btn span {
                display: none;
            }

            .level-item {
                padding: 0.75rem;
            }

            .example-item {
                padding: 1rem;
            }
        }
    </style>

    <script>
        function copyReferralCode(event) {
            event.preventDefault();

            const referralLink = document.getElementById('referralLink');
            const button = event.currentTarget;

            if (referralLink) {
                // Select and copy the text
                referralLink.select();
                referralLink.setSelectionRange(0, 99999);

                try {
                    document.execCommand('copy');

                    // Show success feedback
                    const originalHTML = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check"></i><span>Tersalin!</span>';
                    button.style.background = 'rgba(16, 185, 129, 0.3)';

                    // Reset after 2 seconds
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.style.background = '';
                    }, 2000);

                } catch (err) {
                    console.error('Failed to copy text: ', err);
                }
            }
        }

        // Add loading animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll(
                '.referral-card, .commission-info-card, .commission-examples-card');

            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 150);
            });
        });
    </script>
@endsection
