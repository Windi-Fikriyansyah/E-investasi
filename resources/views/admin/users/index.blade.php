@extends('template.app')
@section('title', 'Data Pengguna')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Data Pengguna</h3>
            </div>
        </div>
    </div>
    <div class="page-content">
        @if (session('message'))
            <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Total Pengguna</h6>
                                <h4 class="mb-0">{{ $totalUsers }}</h4>
                            </div>
                            <div class="icon">
                                <i class="bi bi-people-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Pengguna Aktif</h6>
                                <h4 class="mb-0">{{ $activeUsers }}</h4>
                            </div>
                            <div class="icon">
                                <i class="bi bi-person-check-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Pengguna Nonaktif</h6>
                                <h4 class="mb-0">{{ $inactiveUsers }}</h4>
                            </div>
                            <div class="icon">
                                <i class="bi bi-person-x-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-white">Total Saldo</h6>
                                <h4 class="mb-0">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h4>
                            </div>
                            <div class="icon">
                                <i class="bi bi-wallet-fill fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Daftar Pengguna</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="products" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Saldo</th>
                                <th>Status</th>
                                <th>Keanggotaan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for User Detail -->
    <div class="modal fade" id="userDetailModal" tabindex="-1" aria-labelledby="userDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userDetailModalLabel">Detail Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h4 id="detail-user-name" class="mb-1"></h4>
                            <span id="detail-user-status" class="badge mb-2"></span>
                            <p id="detail-user-email" class="text-muted"></p>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Informasi Pengguna</h5>
                                    <div class="table-responsive">
                                        <table class="table table-borderless">
                                            <tr>
                                                <th width="30%">Saldo</th>
                                                <td id="detail-user-saldo"></td>
                                            </tr>
                                            <tr>
                                                <th>Tanggal Daftar</th>
                                                <td id="detail-user-created-at"></td>
                                            </tr>
                                            <tr>
                                                <th>Terakhir Diupdate</th>
                                                <td id="detail-user-updated-at"></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">Edit Password Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editPasswordForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit-password-user-id" name="id">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Modal for Add Balance -->
    <div class="modal fade" id="addBalanceModal" tabindex="-1" aria-labelledby="addBalanceModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBalanceModalLabel">Tambah Saldo Pengguna</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addBalanceForm">
                    <div class="modal-body">
                        <input type="hidden" id="add-balance-user-id" name="id">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Jumlah Saldo</label>
                            <input type="number" class="form-control" id="amount" name="amount" required
                                min="1000">
                            <small class="text-muted">Minimal penambahan saldo: Rp 1.000</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Tambahkan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <style>
        .right-gap {
            margin-right: 10px
        }

        .status-label {
            padding: 3px 8px;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }

        .status-label.active {
            background-color: green;
        }

        .status-label.inactive {
            background-color: red;
        }

        .action-buttons .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            margin-right: 0.25rem;
        }

        .swal2-container {
            z-index: 999999 !important;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(document).on('click', '.add-balance-btn', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                var addBalanceUrl = $(this).data('url');
                $('#add-balance-user-id').val(userId);
                $('#addBalanceForm').attr('action', addBalanceUrl);
                $('#addBalanceModal').modal('show');
            });

            // Add Balance Form Submission
            $('#addBalanceForm').submit(function(e) {
                e.preventDefault();
                var formData = $(this).serialize();
                var url = $(this).attr('action');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#addBalanceModal').modal('hide');
                        Swal.fire({
                            title: 'Memproses',
                            html: 'Sedang menambahkan saldo...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Saldo berhasil ditambahkan',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#addBalanceModal').modal('hide');
                                    $('#addBalanceForm')[0].reset();
                                    $('#products').DataTable().ajax.reload();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        if (errors && errors.amount) {
                            errorMessage = errors.amount[0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else {
                            errorMessage = 'Terjadi kesalahan saat menambahkan saldo.';
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
            $('#products').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.user.load') }}",
                    type: "POST"
                },
                pageLength: 10,
                searching: true,
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'phone',
                        name: 'phone'
                    },
                    {
                        data: 'balance',
                        name: 'balance'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'keanggotaan',
                        name: 'keanggotaan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                columnDefs: [{
                        className: "dt-head-center",
                        targets: ['_all']
                    },
                    {
                        className: "dt-body-center",
                        targets: [0, 1, 2, 3, 4, 5]
                    }
                ]
            });

            $(document).on('click', '.delete-btn', function(e) {
                e.preventDefault();
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pelanggan ini akan dihapus!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Terhapus!',
                                        'Pelanggan berhasil dihapus.',
                                        'success'
                                    );
                                    $('#products').DataTable().ajax.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal menghapus pelanggan.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal menghapus pelanggan.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.toggle-status-btn', function(e) {
                e.preventDefault();
                var toggleUrl = $(this).data('url');
                var userId = $(this).data('id');
                var currentStatus = $(this).data('status');
                var newStatus = currentStatus === 'Aktif' ? 'Nonaktif' : 'Aktif';

                Swal.fire({
                    title: 'Ubah Status Pengguna',
                    text: `Anda yakin ingin mengubah status pengguna ini menjadi ${newStatus}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, ubah!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: toggleUrl,
                            type: 'POST',
                            data: {
                                status: newStatus
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Berhasil!',
                                        `Status pengguna berhasil diubah menjadi ${newStatus}.`,
                                        'success'
                                    );
                                    location.reload();
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'Gagal mengubah status pengguna.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error!',
                                    'Gagal mengubah status pengguna.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.detail-btn', function(e) {
                e.preventDefault();
                var detailUrl = $(this).attr('href');

                $.get(detailUrl, function(response) {
                    if (response.success) {
                        var user = response.data;

                        // Set user data in modal
                        $('#detail-user-name').text(user.name);
                        $('#detail-user-email').text(user.email);
                        var saldo = user.saldo ? 'Rp ' + Number(user.saldo).toLocaleString(
                            'id-ID') : 'Rp 0';
                        $('#detail-user-saldo').text(saldo);
                        $('#detail-user-created-at').text(new Date(user.created_at)
                            .toLocaleDateString('id-ID'));
                        $('#detail-user-updated-at').text(new Date(user.updated_at)
                            .toLocaleDateString('id-ID'));

                        // Set status badge
                        var statusBadge = $('#detail-user-status');
                        statusBadge.text(user.status);
                        statusBadge.removeClass('badge-success badge-danger');
                        statusBadge.addClass(user.status === 'Aktif' ? 'badge-success' :
                            'badge-danger');

                        // Set avatar
                        $('#detail-user-avatar').attr('src', user.avatar);

                        // Show modal
                        $('#userDetailModal').modal('show');
                    }
                }).fail(function() {
                    Swal.fire(
                        'Error!',
                        'Gagal memuat detail pengguna.',
                        'error'
                    );
                });
            });


            $(document).on('click', '.edit-password-btn', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                $('#edit-password-user-id').val(userId);
                $('#editPasswordModal').modal('show');
            });

            // Handle password form submission
            $('#editPasswordForm').submit(function(e) {
                e.preventDefault();

                // Ambil nilai password dan konfirmasi password
                var password = $('#password').val();
                var password_confirmation = $('#password_confirmation').val();

                // Validasi client-side sebelum kirim ke server
                if (password !== password_confirmation) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Password dan konfirmasi password tidak sama!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                // Jika validasi client-side berhasil, lanjutkan ke server
                var userId = $('#edit-password-user-id').val();
                var formData = $(this).serialize();
                var url = "{{ route('admin.user.update-password', '') }}/" + userId;

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        $('#editPasswordModal').modal('hide');
                        // Show loading indicator
                        Swal.fire({
                            title: 'Memproses',
                            html: 'Sedang mengubah password...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#editPasswordModal').modal('hide');
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Password berhasil diubah.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $('#editPasswordModal').modal('hide');
                                    $('#editPasswordForm')[0].reset();
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        var errors = xhr.responseJSON.errors;
                        var errorMessage = '';

                        if (errors && errors.password) {
                            errorMessage = errors.password[0];
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else {
                            errorMessage = 'Terjadi kesalahan saat mengubah password.';
                        }

                        Swal.fire({
                            title: 'Error!',
                            text: errorMessage,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>
@endpush
