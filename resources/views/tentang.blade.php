@extends('layouts.app')

@section('content')
    <div class="about-container">
        <div class="about-header">
            <h2>Tentang Perusahaan Kami</h2>
            <p>Menjadi pelopor dalam revolusi transportasi berkelanjutan</p>
        </div>

        <div class="about-content">
            <div class="about-images">
                <div class="image-container">
                    <img src="{{ asset('gambar_perusahaan.jpeg') }}" alt="Kantor Perusahaan" class="about-image"
                        alt="Gedung Perusahaan" class="about-image">
                </div>

                <div class="image-container">
                    <img src="{{ asset('gambar_perusahaan1.jpeg') }}" alt="Kantor Perusahaan" class="about-image"
                        alt="Tim Perusahaan" class="about-image">
                </div>
            </div>

            <div class="about-text">
                <p>Kami menawarkan berbagai jenis kendaraan listrik yang dirancang untuk menghadirkan fleksibilitas,
                    efisiensi, dan performa dalam mobilitas modern. Setiap unit tidak hanya menjadi solusi transportasi,
                    tetapi juga merupakan aset potensial dalam portofolio investasi Anda — membuka peluang di sektor energi
                    bersih dan teknologi masa depan yang terus berkembang.</p>

                <p>Mulai dari model berperforma tinggi untuk penggemar kecepatan, kendaraan serbaguna untuk mobilitas
                    harian, hingga desain ringkas yang cocok menjelajahi kota dengan lincah — semua dirancang untuk memenuhi
                    kebutuhan gaya hidup urban yang dinamis sekaligus menghadirkan nilai tambah bagi investor visioner.</p>

                <p>Berinvestasi di sektor ini berarti turut mendukung transformasi mobilitas ramah lingkungan, sembari
                    memanfaatkan momentum pertumbuhan global dari tren kendaraan listrik. Ini adalah langkah strategis bagi
                    siapa saja yang ingin menggabungkan keberlanjutan dengan potensi imbal hasil jangka panjang.</p>
            </div>
        </div>
    </div>

    <style>
        .about-container {
            background: white;
            border-radius: var(--rounded-xl);
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            margin-bottom: 2rem;
        }

        .about-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .about-header h2 {
            font-size: 1.75rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
            font-weight: 700;
        }

        .about-header p {
            color: var(--text-light);
            font-size: 1rem;
        }

        .about-images {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .image-container {
            position: relative;
            border-radius: var(--rounded-lg);
            overflow: hidden;
            box-shadow: var(--shadow-subtle);
        }

        .about-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .image-container:hover .about-image {
            transform: scale(1.03);
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            padding: 0.75rem;
            font-size: 0.875rem;
            text-align: center;
            margin: 0;
        }

        .about-text {
            color: var(--text);
            line-height: 1.8;
        }

        .about-text p {
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .about-images {
                grid-template-columns: 1fr;
            }

            .about-container {
                padding: 1.5rem;
            }

            .about-header h2 {
                font-size: 1.5rem;
            }
        }

        @media (min-width: 1200px) {
            .about-content {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
                align-items: center;
            }

            .about-images {
                grid-template-rows: 1fr 1fr;
                grid-template-columns: 1fr;
                height: 100%;
            }

            .about-image {
                height: 250px;
            }
        }
    </style>
@endsection
