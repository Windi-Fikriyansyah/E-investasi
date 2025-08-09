@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header-gradient">
            <div class="investment-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <h2 class="investment-title-modern">Program Referral</h2>
            <span class="category-badge">Dapatkan Bonus</span>
        </div>

        <div class="investment-stats">
            <div class="stat-highlight">
                <div class="daily-return">
                    <i class="fas fa-gift trending-icon"></i>
                    <div>
                        <div class="return-amount">30%</div>
                        <div class="return-label">Dari Investasi Teman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="investment-description-modern">
            <ol>
                <li>Ketika bawahan Level 1 Anda melakukan investasi pertama, Anda bisa mendapatkan bonus sebesar 3%.</li>
                <li>Ajak teman Anda untuk melakukan isi ulang dan membeli produk investasi di SmartNiuVolt, Anda akan
                    mendapatkan imbalan (komisi):
                    <ul class="commission-levels">
                        <li><strong>Level 1:</strong> Komisi 30%</li>
                        <li><strong>Level 2:</strong> Komisi 3%</li>
                        <li><strong>Level 3:</strong> Komisi 1%</li>
                    </ul>
                </li>
            </ol>

            <div class="commission-examples">
                <h4>Contoh Perhitungan Komisi:</h4>
                <div class="example-item">
                    <p>Jika bawahan <strong>Level 1</strong> Anda berinvestasi <strong>Rp 1.000.000</strong>, Anda menerima
                        komisi <strong>Rp 300.000</strong></p>
                </div>
                <div class="example-item">
                    <p>Jika bawahan <strong>Level 2</strong> Anda berinvestasi <strong>Rp 1.000.000</strong>, Anda menerima
                        komisi <strong>Rp 30.000</strong></p>
                </div>
                <div class="example-item">
                    <p>Jika bawahan <strong>Level 3</strong> Anda berinvestasi <strong>Rp 1.000.000</strong>, Anda menerima
                        komisi <strong>Rp 10.000</strong></p>
                </div>
            </div>
        </div>

        <div class="investment-details-modern">
            <div class="detail-row">
                <div class="detail-item full-width">
                    <div class="detail-icon">
                        <i class="fas fa-user-tag"></i>
                    </div>
                    <div class="detail-content">
                        <div class="detail-label">Kode Referral Anda</div>
                        <div class="detail-value">{{ Auth::user()->referral_code }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="roi-section">
            <div class="roi-badge">
                <i class="fas fa-link"></i>
                <div>
                    <div class="roi-label">Link Referral</div>
                    <div class="roi-value">{{ url('/register?ref=' . Auth::user()->referral_code) }}</div>
                </div>
            </div>
            <div class="profit-indicator">
                <i class="fas fa-copy"></i> Salin
            </div>
        </div>

        <div class="card-actions">
            <button class="btn-invest" onclick="copyReferralCode()">
                <i class="fas fa-copy"></i> Salin Kode
            </button>
            <button class="btn-invest" onclick="shareReferralLink()"
                style="background: linear-gradient(135deg, #10b981, #059669);">
                <i class="fas fa-share-alt"></i> Bagikan
            </button>
        </div>

        <div class="investment-progress">
            <div class="progress-text">
                <i class="fas fa-users"></i> {{ $referralCount ?? 0 }} orang telah mendaftar menggunakan kode Anda
            </div>
        </div>
    </div>

    <style>
        .investment-description-modern {
            padding: 15px;
            line-height: 1.6;
        }

        .investment-description-modern ol {
            padding-left: 20px;
            margin-bottom: 15px;
        }

        .investment-description-modern ol li {
            margin-bottom: 10px;
        }

        .commission-levels {
            margin-top: 10px;
            padding-left: 25px;
            list-style-type: none;
        }

        .commission-levels li {
            margin-bottom: 5px;
            position: relative;
        }

        .commission-levels li:before {
            content: "•";
            color: #4CAF50;
            font-weight: bold;
            display: inline-block;
            width: 1em;
            margin-left: -1em;
        }

        .commission-examples {
            margin-top: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .commission-examples h4 {
            margin-top: 0;
            color: #333;
            font-size: 16px;
        }

        .example-item {
            margin-bottom: 10px;
            padding: 10px;
            background-color: white;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }

        .example-item p {
            margin: 0;
        }
    </style>
    <script>
        function copyReferralCode() {
            const referralLink = "{{ url('/register?ref=' . Auth::user()->referral_code) }}";
            navigator.clipboard.writeText(referralLink).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disalin!',
                    text: 'Link referral telah disalin ke clipboard',
                    timer: 2000,
                    showConfirmButton: false
                });
            }).catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyalin',
                    text: 'Tidak dapat menyalin link: ' + err,
                });
            });
        }

        function shareReferralLink() {
            const referralLink = "{{ url('/register?ref=' . Auth::user()->referral_code) }}";
            const shareText =
                "Bergabunglah dengan SmartNiuVolt menggunakan kode referral saya dan dapatkan bonus mulai dari 10%! Kode: {{ Auth::user()->referral_code }}";

            if (navigator.share) {
                navigator.share({
                    title: 'SmartNiuVolt Referral',
                    text: shareText,
                    url: referralLink
                }).catch(err => {
                    console.log('Error sharing:', err);
                });
            } else {
                // Fallback for browsers that don't support Web Share API
                const tempInput = document.createElement('input');
                tempInput.value = `${shareText}\n\n${referralLink}`;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand('copy');
                document.body.removeChild(tempInput);

                Swal.fire({
                    icon: 'info',
                    title: 'Bagikan Link',
                    html: `Salin dan bagikan link ini:<br><br>
                       <input type="text" class="swal2-input" value="${referralLink}" readonly style="width: 100%; text-align: center;">`,
                    confirmButtonText: 'Tutup'
                });
            }
        }
    </script>
@endsection
