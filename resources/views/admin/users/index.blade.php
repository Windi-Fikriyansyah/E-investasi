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
                            <img id="detail-user-avatar" src="" alt="User Avatar"
                                class="img-fluid rounded-circle mb-3" width="150">
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
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
                        targets: [0, 1, 2, 3, 4]
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
                        $('#detail-user-name').text(user.phone);
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
        });
    </script>
@endpush
