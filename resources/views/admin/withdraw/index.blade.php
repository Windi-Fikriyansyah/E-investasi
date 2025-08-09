@extends('template.app')
@section('title', 'Transaksi Withdraw')
@section('content')
    <div class="page-heading">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Transaksi Withdraw</h3>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Transaksi Withdraw</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
    <div class="page-content">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible" role="alert">
                <i class="bx bx-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card radius-10">
            <div class="card-header">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">Daftar Withdraw</h5>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="withdrawals-table" style="width: 100%">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th>Nama User</th>
                                <th>Nama Bank</th>
                                <th>No Rekening</th>
                                <th>Nama Pemilik</th>
                                <th>Status</th>
                                <th>Jumlah Penarikan</th>
                                <th>Tanggal</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            const table = $('#withdrawals-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.withdraw.load') }}",
                    type: "POST"
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'phone',
                        name: 'users.phone' // Ubah ini
                    },
                    {
                        data: 'bank_name',
                        name: 'withdrawals.bank_name'
                    },
                    {
                        data: 'bank_number',
                        name: 'withdrawals.bank_number'
                    },
                    {
                        data: 'bank_account',
                        name: 'withdrawals.bank_account'
                    },
                    {
                        data: 'status',
                        name: 'withdrawals.status',
                        render: function(data, type, full, meta) {
                            let badgeClass = '';
                            switch (data) {
                                case 'pending':
                                    badgeClass = 'bg-warning';
                                    break;
                                case 'processing':
                                    badgeClass = 'bg-info';
                                    break;
                                case 'success':
                                    badgeClass = 'bg-success';
                                    break;
                                case 'rejected':
                                    badgeClass = 'bg-danger';
                                    break;
                                default:
                                    badgeClass = 'bg-secondary';
                            }
                            return `<span class="badge ${badgeClass}">${data.toUpperCase()}</span>`;
                        }
                    },
                    {
                        data: 'net_amount',
                        name: 'net_amount',
                        render: function(data, type, full, meta) {
                            var net_amount = parseInt(data);
                            return 'Rp ' + net_amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g,
                                ".");
                        }
                    },
                    {
                        data: 'created_at',
                        name: 'withdrawals.created_at',
                        render: function(data, type, full, meta) {
                            return new Date(data).toLocaleString();
                        }
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, full, meta) {
                            if (full.status === 'processing') {
                                return `<button class="btn btn-sm btn-success complete-btn" data-id="${full.id}">
                        <i class="bi bi-check-circle"></i> Selesaikan
                    </button>`;
                            }
                            return '';
                        }
                    }
                ]
            });

            // Complete Withdraw
            $(document).on('click', '.complete-btn', function() {
                const id = $(this).data('id');
                const url = "{{ route('admin.withdraw.complete', '') }}/" + id;

                Swal.fire({
                    title: 'Konfirmasi',
                    text: "Apakah Anda yakin ingin menyelesaikan transaksi ini?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, selesaikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'POST',
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Sukses',
                                        text: response.message
                                    });
                                    table.ajax.reload();
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: xhr.responseJSON?.message ||
                                        'Terjadi kesalahan'
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
