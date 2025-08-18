@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header-gradient">
            <div class="investment-icon">
                <i class="fas fa-user-friends"></i>
            </div>
            <h2 class="investment-title-modern">Aturan VIP</h2>
        </div>

        <div class="investment-description-modern">
            <p>Jumlah total investasi saya</p>
            <p>Dengan membeli jumlah akumulasi investasi "Produk stabil", Anda dapat membuka level VIP yang berbeda dan
                membuka lebih banyak produk VIP.</p>

            <h3 style="color: #1e293b">Aturan Peningkatan VIP</h3>
            <ul class="vip-levels">
                <li><strong>VIP1</strong><br>Investasi kumulatif: Rp 50.000</li>
                <li><strong>VIP2</strong><br>Investasi kumulatif: Rp 420.000</li>
                <li><strong>VIP3</strong><br>Investasi kumulatif: Rp 3.200.000</li>
                <li><strong>VIP4</strong><br>Investasi kumulatif: Rp 10.000.000</li>
                <li><strong>VIP5</strong><br>Investasi kumulatif: Rp 15.000.000</li>
                <li><strong>VIP6</strong><br>Investasi kumulatif: Rp 52.000.000</li>
            </ul>
        </div>


    </div>

    <style>
        .investment-description-modern {
            padding: 15px;
            line-height: 1.6;
        }

        .vip-levels {
            list-style-type: none;
            padding-left: 0;
            margin-top: 20px;
        }

        .vip-levels li {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border-left: 4px solid #4CAF50;
        }

        .vip-levels li strong {
            color: #2c3e50;
            font-size: 1.1em;
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
