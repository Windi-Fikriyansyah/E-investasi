@extends('layouts.app')

@section('content')
    <div class="edit-profile-container">
        <div class="profile-header">
            <h2>Edit Profil</h2>
            <p>Perbarui informasi pribadi Anda</p>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Profile Picture Section -->
            <div class="form-section">
                <div class="profile-identifier">
                    <div class="avatar-preview">
                        <img id="imagePreview"
                            src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=088742&color=fff' }}"
                            alt="Profile Picture">
                    </div>
                    <div class="user-info">
                        <div class="username">{{ auth()->user()->username }}</div>
                        <div class="referral-code">
                            Kode Referral: <span class="code">{{ auth()->user()->referral_code ?? 'N/A' }}</span>
                            <button type="button" class="btn-copy" onclick="copyReferralCode()">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="form-section">
                <h3>Informasi Pribadi</h3>

                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name) }}"
                        required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                        value="{{ old('username', auth()->user()->username) }}" required>
                    @error('username')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                        required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="referral_code ">Kode Referral</label>
                    <input type="text" disabled id="referral_code " name="referral_code "
                        value="{{ old('referral_code', auth()->user()->referral_code) }}">
                    @error('referral_code ')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="keanggotaan ">Keanggotaan</label>
                    <input type="text" disabled id="keanggotaan " name="keanggotaan "
                        value="{{ old('keanggotaan', auth()->user()->keanggotaan) }}">
                    @error('keanggotaan ')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Address Section -->


            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn-cancel" onclick="window.history.back()">
                    Batal
                </button>
                <button type="submit" class="btn-save">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Sukses!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#2563eb',
            });
        </script>
    @endif
    <style>
        .edit-profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 1rem;
        }

        .profile-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .profile-header h2 {
            font-size: 1.75rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .profile-header p {
            color: #e2e8f0;
        }

        .form-section {
            background: white;
            border-radius: var(--rounded-md);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        .form-section h3 {
            font-size: 1.25rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            padding-bottom: 0.5rem;
            border-bottom: 1px solid var(--gray);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--gray);
            border-radius: var(--rounded-sm);
            font-size: 1rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-row .form-group {
            flex: 1;
        }

        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: block;
        }

        /* Avatar Upload Styles */
        .avatar-upload {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .avatar-preview {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary-light);
            margin-bottom: 1rem;
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-upload-controls {
            display: flex;
            gap: 1rem;
        }

        .btn-upload {
            background-color: var(--primary);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: var(--rounded-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: background-color 0.2s;
        }

        .btn-upload:hover {
            background-color: var(--primary-dark);
        }

        .btn-remove {
            background-color: #f8fafc;
            color: var(--text-light);
            padding: 0.5rem 1rem;
            border-radius: var(--rounded-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            border: 1px solid var(--gray);
            transition: all 0.2s;
        }

        .btn-remove:hover {
            background-color: #f1f5f9;
            color: var(--text);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-cancel {
            background-color: white;
            color: var(--text);
            padding: 0.75rem 1.5rem;
            border-radius: var(--rounded-sm);
            border: 1px solid var(--gray);
            cursor: pointer;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: #f8fafc;
        }

        .btn-save {
            background-color: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--rounded-sm);
            border: none;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .btn-save:hover {
            background-color: var(--primary-dark);
        }

        @media (max-width: 640px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .avatar-upload-controls {
                flex-direction: column;
                gap: 0.5rem;
                width: 100%;
            }

            .btn-upload,
            .btn-remove {
                justify-content: center;
                width: 100%;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn-cancel,
            .btn-save {
                width: 100%;
            }
        }

        .profile-identifier {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .avatar-preview {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            border: 3px solid var(--primary-light);
        }

        .avatar-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            flex: 1;
        }

        .username {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 0.25rem;
        }

        .referral-code {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: var(--text-light);
        }

        .referral-code .code {
            font-family: monospace;
            background: rgba(37, 99, 235, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            color: var(--primary);
        }

        .btn-copy {
            background: none;
            border: none;
            color: var(--primary);
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .btn-copy:hover {
            background-color: rgba(37, 99, 235, 0.1);
        }

        @media (max-width: 480px) {
            .profile-identifier {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .user-info {
                text-align: center;
            }
        }
    </style>

    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sukses!',
                            text: data.message,
                            confirmButtonColor: '#2563eb',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        });
        // Preview uploaded image
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('imagePreview').src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove avatar
        document.getElementById('removeAvatar').addEventListener('click', function() {
            document.getElementById('imagePreview').src =
                'https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=fff';
            document.getElementById('avatar').value = '';
        });
    </script>

    <script>
        function copyReferralCode() {
            const referralCode = "{{ auth()->user()->referral_code }}";
            navigator.clipboard.writeText(referralCode).then(() => {
                // Show tooltip or alert
                alert('Kode referral berhasil disalin: ' + referralCode);
            }).catch(err => {
                console.error('Gagal menyalin kode: ', err);
            });
        }
    </script>
@endsection
