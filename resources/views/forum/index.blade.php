@extends('layouts.app')

@section('content')
    <div class="community-rules-card">
        <div class="rules-header" onclick="toggleRules()">
            <h2>Aturan postingan komunitas</h2>
            <i class="fas fa-chevron-down rules-icon" id="rulesIcon"></i>
        </div>
        <div class="rules-content" id="rulesContent" style="display: none;">
            <ol class="rules-list">
                Posting tangkapan layar penarikan yang berhasil dan tunggu tinjauan sistem. Setelah lolos review, Anda
                akan menerima bonus acak sebesar Rp 2000~20.000.
                Posting screenshot pengalaman investasi atau pengalaman menghasilkan uang dan tunggu review sistem.
                Setelah lolos review, Anda bisa mendapatkan bonus acak sebesar Rp 2000~20.000.
                Harap patuhi standar komunitas dan jangan memposting konten ilegal.
            </ol>
        </div>
    </div>

    <div class="latest-posts">
        <h3>Daftar postingan terbaru</h3>

        <div id="postsContainer">
            @forelse($posts as $post)
                <!-- Post Card -->
                <div class="post-card">
                    <div class="post-header">
                        <strong>Postingan dari {{ $post->nama_user }}</strong>, dipublikasikan pada
                        {{ date('Y-m-d', strtotime($post->created_at)) }}<br>
                        {{ date('H:i:s', strtotime($post->created_at)) }}
                    </div>
                    <hr>
                    <div class="post-content">
                        {{ $post->konten }}
                    </div>
                    @if ($post->gambar)
                        <div class="post-image">
                            <img src="{{ $post->gambar }}" alt="Gambar Post" class="thumbnail-image" loading="lazy" />
                        </div>
                    @endif
                </div>
            @empty
                <div class="post-card">
                    <div class="post-content">
                        Belum ada postingan tersedia.
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Loading indicator -->
        <div id="loadingIndicator" style="display: none; text-align: center; padding: 20px;">
            <div class="spinner"></div>
            <p>Memuat postingan...</p>
        </div>

        <!-- Trigger element for lazy loading -->
        <div id="loadMoreTrigger" style="height: 10px;"></div>
    </div>

    <div class="fab">
        <i class="fas fa-plus"></i> Posting
    </div>


    <div class="modal-overlay" id="postModal" style="display: none;">
        <div class="modal-container">
            <div class="modal-header">
                <h3>Buat Postingan Baru</h3>
                <button class="close-modal" type="button">&times;</button>
            </div>
            <form id="postForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="postContent">Konten Postingan</label>
                        <textarea id="postContent" name="konten" rows="5" placeholder="Apa yang ingin Anda bagikan?" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="postImage">Unggah Gambar (Opsional)</label>
                        <div class="image-upload-container">
                            <label for="postImage" class="image-upload-label">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Pilih Gambar</span>
                                <input type="file" id="postImage" name="gambar" accept="image/*" style="display: none;">
                            </label>
                            <div class="image-preview" id="imagePreview" style="display: none;">
                                <img id="previewImage" src="#" alt="Preview Gambar">
                                <button type="button" class="remove-image" id="removeImage">&times;</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel">Batal</button>
                    <button type="submit" class="btn-submit">Posting</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 0.3s ease;
            overflow-y: auto;
            padding: 1rem;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-container {
            background-color: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            animation: slideUp 0.3s ease;
            display: flex;
            flex-direction: column;
            margin: auto;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
            }

            to {
                transform: translateY(0);
            }
        }

        .modal-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 1.25rem;
            color: #1e293b;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #64748b;
            padding: 0.25rem;
        }

        .modal-body {
            padding: 1.5rem;
            overflow-y: auto;
            flex: 1;
            max-height: calc(90vh - 150px);
            /* Tambahkan max-height untuk memastikan footer tetap terlihat */
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #1e293b;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            resize: vertical;
            min-height: 120px;
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .image-upload-container {
            margin-top: 0.5rem;
        }

        .image-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            border: 2px dashed #cbd5e1;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            color: #64748b;
        }

        .image-upload-label:hover {
            border-color: #2563eb;
            background-color: #f8fafc;
        }

        .image-upload-label i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #94a3b8;
        }

        .image-preview {
            position: relative;
            margin-top: 1rem;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            max-height: 300px;
            /* Batasi tinggi preview gambar */
            overflow: hidden;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: contain;
            /* Pastikan gambar tetap proporsional */
        }

        .remove-image {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1rem;
        }

        .modal-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            flex-shrink: 0;
            position: sticky;
            bottom: 0;
            background-color: white;
        }

        .btn-cancel,
        .btn-submit {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel {
            background-color: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .btn-cancel:hover {
            background-color: #e2e8f0;
        }

        .btn-submit {
            background-color: #2563eb;
            color: white;
            border: none;
        }

        .btn-submit:hover {
            background-color: #1d4ed8;
        }

        .btn-submit:disabled {
            background-color: #94a3b8;
            cursor: not-allowed;
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .modal-container {
                background-color: #2d2d2d;
                color: #e0e0e0;
            }

            .modal-header h3 {
                color: #ffffff;
            }

            .form-group label {
                color: #e0e0e0;
            }

            .form-group textarea {
                background-color: #3d3d3d;
                border-color: #404040;
                color: #e0e0e0;
            }

            .form-group textarea:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
            }

            .image-upload-label {
                border-color: #4a4a4a;
                color: #a0a0a0;
            }

            .image-upload-label:hover {
                border-color: #3b82f6;
                background-color: #3d3d3d;
            }

            .image-upload-label i {
                color: #7a7a7a;
            }

            .btn-cancel {
                background-color: #3d3d3d;
                color: #a0a0a0;
                border-color: #4a4a4a;
            }

            .btn-cancel:hover {
                background-color: #4a4a4a;
            }

            .modal-footer {
                background-color: #2d2d2d;
            }
        }

        /* Community Posts Container */
        .community-posts-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1.5rem;
            background-color: #f8f9fa;
        }

        /* Community Rules Card */
        .community-rules-card {
            background-color: white;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .rules-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .community-rules-card h2 {
            font-size: 1rem;
            color: var(--primary, #007bff);
            margin: 0;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .rules-icon {
            transition: transform 0.3s ease;
            color: #088742;
            font-size: 1.2rem;
        }

        .rules-content {
            padding-top: 1rem;
            border-top: 1px solid #e9ecef;
            margin-top: 1rem;
            animation: fadeIn 0.3s ease;
        }

        .rules-list {
            padding-left: 1.5rem;
            margin: 0;
        }

        .rules-list li {
            margin-bottom: 0.5rem;
            line-height: 1.5;
        }

        /* Animasi untuk expand/collapse */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .community-rules-card {
                background-color: #2d2d2d;
                border-color: #404040;
            }

            .rules-content {
                border-top-color: #404040;
            }

            .rules-icon {
                color: #3b82f6;
            }

            .community-rules-card h2 {
                color: #3b82f6;
            }
        }

        /* Latest Posts Section */
        .latest-posts h3 {
            font-size: 1.1rem;
            color: white;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        /* Post Card */
        .post-card {
            background-color: white;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .post-card:last-child {
            margin-bottom: 0;
        }

        .post-header {
            font-size: 0.9rem;
            color: var(--text-light, #6c757d);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .post-header strong {
            color: var(--text, #333);
            font-weight: 600;
        }

        .post-content {
            font-size: 1rem;
            color: var(--text, #333);
            line-height: 1.5;
            min-height: 1.5rem;
            margin-bottom: 1rem;
        }

        /* Post Image */
        .post-image {
            margin-top: 0.75rem;
            text-align: center;
            /* Pusatkan gambar */
        }

        .thumbnail-image {
            max-width: 200px;
            /* Lebar maksimum gambar */
            max-height: 200px;
            /* Tinggi maksimum gambar */
            width: auto;
            /* Pertahankan rasio aspek */
            height: auto;
            /* Pertahankan rasio aspek */
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            cursor: pointer;
            /* Ubah kursor saat hover */
            border: 1px solid #e9ecef;
            /* Tambahkan border */
        }

        .thumbnail-image:hover {
            transform: scale(1.05);
            /* Sedikit zoom saat hover */
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        /* Responsive Design */
        @media (max-width: 640px) {
            .community-posts-container {
                padding: 1rem;
            }

            .community-rules-card,
            .post-card {
                padding: 1rem;
                border-radius: 6px;
                margin-bottom: 1rem;
            }

            .post-header {
                font-size: 0.85rem;
            }

            .post-content {
                font-size: 0.95rem;
            }

            /* Mobile Modal Optimizations */
            .modal-overlay {
                align-items: flex-start;
                padding: 0.5rem;
            }

            .modal-container {
                width: 95%;
                max-height: 95vh;
                margin-top: 1rem;
                margin-bottom: 1rem;
                border-radius: 8px;
            }

            .modal-header {
                padding: 0.75rem 1rem;
            }

            .modal-header h3 {
                font-size: 1.1rem;
            }

            .modal-body {
                padding: 1rem;
                max-height: calc(95vh - 150px);
                /* Sesuaikan untuk mobile */
            }

            .modal-footer {
                padding: 0.75rem 1rem;
                flex-direction: column;
                gap: 0.5rem;
            }

            .btn-cancel,
            .btn-submit {
                width: 100%;
                padding: 0.75rem;
                justify-content: center;
            }

            .form-group textarea {
                font-size: 16px;
                /* Prevent zoom on iOS */
                min-height: 100px;
            }

            .image-upload-label {
                padding: 1.5rem;
            }

            .image-upload-label i {
                font-size: 1.5rem;
            }
        }

        /* Extra small screens */
        @media (max-width: 480px) {
            .modal-overlay {
                padding: 0.25rem;
            }

            .modal-container {
                width: 98%;
                max-height: 98vh;
                margin-top: 0.5rem;
                margin-bottom: 0.5rem;
            }

            .modal-header {
                padding: 0.5rem 0.75rem;
            }

            .modal-body {
                padding: 0.75rem;
                max-height: calc(98vh - 150px);
                /* Sesuaikan untuk layar sangat kecil */
            }

            .modal-footer {
                padding: 0.5rem 0.75rem;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .community-posts-container {
                background-color: #1a1a1a;
            }

            .community-rules-card,
            .post-card {
                background-color: #2d2d2d;
                border-color: #404040;
                color: #e0e0e0;
            }

            .post-header strong {
                color: #ffffff;
            }

            .post-content {
                color: #e0e0e0;
            }

            .latest-posts h3 {
                color: #ffffff;
            }
        }

        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
    </style>
    <script>
        $(document).ready(function() {
            let isSubmitting = false;

            // Tampilkan modal saat FAB diklik
            $('.fab').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                resetForm();
                openModal();
            });

            // Tutup modal saat tombol close atau cancel diklik
            $('.close-modal, .btn-cancel').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                closeModal();
            });

            // Function untuk membuka modal
            function openModal() {
                $('#postModal').fadeIn(300);
                $('body').addClass('modal-open');

                // Setel scroll ke atas saat modal dibuka
                $('.modal-body').scrollTop(0);
            }

            // Function untuk menutup modal
            function closeModal() {
                $('#postModal').fadeOut(300);
                $('body').removeClass('modal-open');
                resetForm();
            }

            // Function untuk reset form
            function resetForm() {
                $('#postForm')[0].reset();
                $('#imagePreview').hide();
                $('#previewImage').attr('src', '#');
                isSubmitting = false;
                $('.btn-submit').prop('disabled', false).text('Posting');
            }

            // Preview gambar sebelum upload
            $('#postImage').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        $('#previewImage').attr('src', e.target.result);
                        $('#imagePreview').fadeIn();

                        // Scroll ke bawah untuk melihat preview gambar dan tombol posting
                        $('.modal-body').animate({
                            scrollTop: $('.modal-body')[0].scrollHeight
                        }, 300);
                    }

                    reader.readAsDataURL(file);
                }
            });

            // Hapus preview gambar
            $('#removeImage').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $('#postImage').val('');
                $('#imagePreview').fadeOut();
            });

            // Submit form postingan
            $('#postForm').on('submit', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Prevent double submission
                if (isSubmitting) {
                    return false;
                }

                isSubmitting = true;
                $('.btn-submit').prop('disabled', true).text('Memposting...');

                const formData = new FormData(this);

                $.ajax({
                    url: '{{ route('forum.store') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    timeout: 30000, // 30 detik timeout
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: 'Postingan Anda telah berhasil dipublikasikan.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            closeModal();
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);

                        let errorMessage =
                            'Terjadi kesalahan saat memposting. Silakan coba lagi.';

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON?.errors;
                            if (errors) {
                                errorMessage = Object.values(errors).flat().join('\n');
                            }
                        } else if (xhr.status === 0) {
                            errorMessage = 'Koneksi internet bermasalah. Periksa koneksi Anda.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: errorMessage
                        });
                    },
                    complete: function() {
                        isSubmitting = false;
                        $('.btn-submit').prop('disabled', false).text('Posting');
                    }
                });
            });

            // Tutup modal saat klik di luar modal
            $('#postModal').on('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });

            // Prevent modal container clicks from closing modal
            $('.modal-container').on('click', function(e) {
                e.stopPropagation();
            });

            // ESC key untuk tutup modal
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && $('#postModal').is(':visible')) {
                    closeModal();
                }
            });
        });

        function toggleRules() {
            const content = document.getElementById('rulesContent');
            const icon = document.getElementById('rulesIcon');

            if (content.style.display === 'none') {
                content.style.display = 'block';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                content.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>
@endsection
