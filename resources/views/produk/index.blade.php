@extends('layouts.app')

@section('content')
    <div class="tabs-container">
        <div class="tabs">
            <div class="tab active" data-tab="all">Tentang Kami</div>
            @foreach ($categories as $category)
                <div class="tab" data-tab="category-{{ str_replace(' ', '-', strtolower($category->kategori)) }}">
                    {{ $category->kategori }}</div>
            @endforeach
        </div>
    </div>

    <div class="tab-content active" id="all">
        <div class="about-us-container">
            <section class="about-hero">
                <div class="hero-content">
                    <h1>Tentang Perusahaan Kami</h1>
                    <p class="tagline">Menjadi pelopor dalam revolusi transportasi berkelanjutan</p>
                </div>
            </section>

            <section class="about-mission">
                <div class="mission-statement">
                    <h2>Visi Kami</h2>
                    <p>Mengubah wajah transportasi modern dengan solusi listrik yang inovatif, berkelanjutan, dan
                        menguntungkan bagi semua pihak.</p>
                </div>
            </section>

            <section class="about-content-section">
                <div class="about-grid">
                    <div class="about-images">
                        <div class="image-wrapper">
                            <img src="{{ asset('gambar_perusahaan.jpeg') }}" alt="Kantor Perusahaan" class="about-image">
                            <div class="image-caption">Kantor Pusat Kami</div>
                        </div>
                        <div class="image-wrapper">
                            <img src="{{ asset('gambar_perusahaan1.jpeg') }}" alt="Tim Perusahaan" class="about-image">
                            <div class="image-caption">Tim Profesional Kami</div>
                        </div>
                    </div>

                    <div class="about-text-content">
                        <h2>Mengapa Memilih Kami?</h2>
                        <div class="about-text-paragraphs">
                            <p>Kami menawarkan berbagai jenis kendaraan listrik yang dirancang untuk menghadirkan
                                fleksibilitas, efisiensi, dan performa dalam mobilitas modern. Setiap unit tidak hanya
                                menjadi solusi transportasi, tetapi juga merupakan aset potensial dalam portofolio investasi
                                Anda — membuka peluang di sektor energi bersih dan teknologi masa depan yang terus
                                berkembang.</p>

                            <p>Mulai dari model berperforma tinggi untuk penggemar kecepatan, kendaraan serbaguna untuk
                                mobilitas harian, hingga desain ringkas yang cocok menjelajahi kota dengan lincah — semua
                                dirancang untuk memenuhi kebutuhan gaya hidup urban yang dinamis sekaligus menghadirkan
                                nilai tambah bagi investor visioner.</p>

                            <p>Berinvestasi di sektor ini berarti turut mendukung transformasi mobilitas ramah lingkungan,
                                sembari memanfaatkan momentum pertumbuhan global dari tren kendaraan listrik. Ini adalah
                                langkah strategis bagi siapa saja yang ingin menggabungkan keberlanjutan dengan potensi
                                imbal hasil jangka panjang.</p>
                        </div>

                        <div class="about-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-bolt"></i>
                                </div>
                                <h3>Inovasi Teknologi</h3>
                                <p>Menggunakan teknologi terbaru untuk efisiensi maksimal</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-leaf"></i>
                                </div>
                                <h3>Ramah Lingkungan</h3>
                                <p>Berkontribusi pada pengurangan emisi karbon</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h3>Potensi Investasi</h3>
                                <p>Peluang pertumbuhan jangka panjang yang menguntungkan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    @foreach ($categories as $category)
        <div class="tab-content" id="category-{{ str_replace(' ', '-', strtolower($category->kategori)) }}">
            <div class="investment-grid">
                @foreach ($products->where('kategori', $category->kategori) as $product)
                    <div class="investment-card" data-product-id="{{ $product->id }}">
                        <div class="product-image-container">
                            <img src="{{ $product->gambar ? asset('storage/' . $product->gambar) : asset('images/default-investment.jpg') }}"
                                alt="{{ $product->nama_produk }}" class="product-image">

                        </div>

                        <div class="card-content">
                            <div class="card-header">
                                <h3 class="investment-title">{{ $product->nama_produk }}</h3>
                            </div>



                            <div class="investment-details">
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Harga</span>
                                        <span
                                            class="detail-value">Rp{{ number_format($product->harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Siklus</span>
                                        <span class="detail-value">{{ $product->durasi }} Hari</span>
                                    </div>
                                </div>

                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-coins"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Pendapatan Perhari</span>
                                        <span
                                            class="detail-value profit">Rp{{ number_format($product->pendapatan_harian, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-icon">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="detail-content">
                                        <span class="detail-label">Total Pendapatan</span>
                                        <span
                                            class="detail-value">Rp{{ number_format($product->total_pendapatan, 0, ',', '.') }}</span>
                                    </div>
                                </div>


                            </div>

                            <div class="investment-description">
                                <p>{{ Str::limit($product->keterangan, 80) }}</p>
                            </div>

                            <div class="card-actions">
                                <button class="btn-invest" data-product-id="{{ $product->id }}">
                                    <i class="fas fa-plus"></i>
                                    Beli Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach



    <style>
        .about-us-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .about-hero {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 1rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .about-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.1);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .about-hero h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .tagline {
            font-size: 1.25rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-mission {
            background-color: #f8fafc;
            padding: 3rem 2rem;
            border-radius: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }

        .mission-statement h2 {
            font-size: 1.75rem;
            color: #1e293b;
            margin-bottom: 1rem;
        }

        .mission-statement p {
            font-size: 1.1rem;
            color: #475569;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .about-content-section {
            margin: 3rem 0;
        }

        .about-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 992px) {
            .about-grid {
                grid-template-columns: 1fr 1fr;
                align-items: center;
            }
        }

        .about-images {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .about-images {
                grid-template-columns: 1fr 1fr;
            }
        }

        .image-wrapper {
            position: relative;
            border-radius: 0.75rem;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .about-image {
            width: 100%;
            height: auto;
            display: block;
            transition: transform 0.3s ease;
        }

        .image-wrapper:hover .about-image {
            transform: scale(1.03);
        }

        .image-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 0.75rem;
            text-align: center;
            font-size: 0.9rem;
        }

        .about-text-content h2 {
            font-size: 1.75rem;
            color: #1e293b;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .about-text-content h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: #2563eb;
        }

        .about-text-paragraphs p {
            font-size: 1rem;
            color: #475569;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }

        .about-features {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        @media (min-width: 640px) {
            .about-features {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .feature-item {
            background: #f8fafc;
            padding: 1.5rem;
            border-radius: 0.75rem;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: #2563eb;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
        }

        .feature-item h3 {
            font-size: 1.1rem;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .feature-item p {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.5;
        }

        /* Main Content Styles */
        .section-header {
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: white;
            margin-bottom: 0.5rem;
        }

        /* Tabs Styles */
        .tabs-container {
            margin-bottom: 1.5rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .tabs {
            display: flex;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .tab {
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            color: white;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tab:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .tab.active {
            background: white;
            color: #2563eb;
            font-weight: 600;
        }

        /* Investment Grid */
        .investment-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        /* Investment Card */
        .investment-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .investment-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        .product-image-container {
            position: relative;
            height: 160px;
            overflow: hidden;
        }

        .product-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .investment-card:hover .product-image {
            transform: scale(1.05);
        }

        .category-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: rgba(255, 255, 255, 0.9);
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #2563eb;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-content {
            padding: 0 1.25rem 1.25rem;
        }

        .card-header {
            padding: 1rem 0 0.5rem;
        }

        .investment-title {
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: #1e293b;
        }

        /* Investment Stats */
        .investment-stats {
            padding: 0.5rem 0;
        }

        .daily-return {
            display: flex;
            align-items: center;
            background: rgba(37, 99, 235, 0.1);
            padding: 0.75rem;
            border-radius: 0.5rem;
        }

        .trending-icon {
            color: #10b981;
            margin-right: 0.5rem;
            font-size: 1rem;
        }

        .return-amount {
            font-weight: 600;
            color: #2563eb;
            margin-right: 0.25rem;
        }

        .return-label {
            font-size: 0.875rem;
            color: #64748b;
        }

        /* Investment Details */
        .investment-details {
            padding: 0.75rem 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .detail-item {
            display: flex;
            align-items: center;
            padding: 0.5rem 0;
        }

        .detail-icon {
            width: 2rem;
            height: 2rem;
            background: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            color: #2563eb;
            flex-shrink: 0;
        }

        .detail-content {
            flex: 1;
        }

        .detail-label {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            margin-bottom: 0.125rem;
        }

        .detail-value {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1e293b;
        }

        .profit {
            color: #f59e0b;
            font-weight: 600;
        }

        /* Description */
        .investment-description {
            padding: 0.5rem 0 1rem;
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.5;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Card Actions */
        .card-actions {
            padding: 1rem 0 0;
        }

        .btn-invest {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-invest:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        /* Testimonials */
        .testimonials-section {
            margin: 2rem 0;
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .testimonial-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .testimonial-content {
            font-size: 0.875rem;
            color: #334155;
            line-height: 1.6;
            margin-bottom: 1rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
        }

        .testimonial-author img {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 0.75rem;
        }

        .author-info h4 {
            font-size: 0.875rem;
            font-weight: 600;
            margin-bottom: 0.125rem;
            color: #1e293b;
        }

        .author-info p {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        /* About Card */
        .about-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .about-card img {
            width: 5rem;
            height: 5rem;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 1rem;
            border: 3px solid #2563eb;
        }

        .about-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: #1e293b;
        }

        .about-card p {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .about-card .btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .about-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(37, 99, 235, 0.2);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Responsive Styles */
        @media (min-width: 640px) {
            .investment-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .testimonial-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 768px) {
            .section-header {
                text-align: left;
            }

            .investment-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .investment-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Handle tab switching
            $('.tab').click(function() {
                const tabId = $(this).data('tab');

                // Update active tab
                $('.tab').removeClass('active');
                $(this).addClass('active');

                // Update active content
                $('.tab-content').removeClass('active');
                $(`#${tabId}`).addClass('active');
            });

            // Handle invest button click
            $('.btn-invest').click(function() {
                const productId = $(this).data('product-id');
                const card = $(this).closest('.investment-card');
                const productName = card.find('.investment-title').text();
                const productPrice = card.find('.detail-value').first().text();

                Swal.fire({
                    title: 'Konfirmasi Investasi',
                    html: `Anda akan membeli <strong>${productName}</strong> seharga <strong>${productPrice}</strong>?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Beli Sekarang',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Kirim request AJAX
                        $.ajax({
                            url: '{{ route('invest') }}',
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                product_id: productId
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Sukses!',
                                        text: response.message,
                                        icon: 'success'
                                    }).then(() => {
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Terjadi kesalahan';
                                if (xhr.status === 401) {
                                    errorMessage = 'Anda harus login terlebih dahulu';
                                    window.location.href = '{{ route('login') }}';
                                } else if (xhr.responseJSON && xhr.responseJSON
                                    .message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Maaf!',
                                    text: errorMessage,
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
