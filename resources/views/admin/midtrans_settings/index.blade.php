@extends('template.app')
@section('title', 'Pengaturan Midtrans')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Basic Layout & Basic with Icons -->
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">Pengaturan Midtrans</h5>
                        <small class="text-muted float-end">Form Pengaturan Pembayaran</small>
                    </div>
                    <div class="card-body">
                        <form id="midtransForm" method="POST">
                            @csrf
                            <input type="hidden" id="settingId" name="id">

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="server_key">Server Key</label>
                                <div class="col-sm-10">
                                    <input type="text" id="server_key" name="server_key"
                                        class="form-control @error('server_key') is-invalid @enderror"
                                        value="{{ old('server_key') }}" required />
                                    @error('server_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="client_key">Client Key</label>
                                <div class="col-sm-10">
                                    <input type="text" id="client_key" name="client_key"
                                        class="form-control @error('client_key') is-invalid @enderror"
                                        value="{{ old('client_key') }}" required />
                                    @error('client_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="mode">Mode</label>
                                <div class="col-sm-10">
                                    <select id="mode" name="mode"
                                        class="form-select @error('mode') is-invalid @enderror" required>
                                        <option value="">Pilih Mode</option>
                                        <option value="sandbox">Sandbox</option>
                                        <option value="production">Production</option>
                                    </select>
                                    @error('mode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                                    <button type="button" class="btn btn-secondary" id="cancelBtn">Batal</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable -->
        <div class="card">
            <div class="card-header">
                <h5>Daftar Pengaturan Midtrans</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="midtransTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Server Key</th>
                                <th>Client Key</th>
                                <th>Mode</th>
                                <th>Dibuat Pada</th>
                                <th>Diperbarui Pada</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize DataTable
            const table = $('#midtransTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('admin.midtrans_settings.load') }}",
                    type: "POST",
                    data: function(d) {
                        d.type = $('#search-type').val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'server_key',
                        name: 'server_key'
                    },
                    {
                        data: 'client_key',
                        name: 'client_key'
                    },
                    {
                        data: 'mode',
                        name: 'mode'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                drawCallback: function(settings) {
                    // Cek apakah ada data atau tidak
                    var api = this.api();
                    var isEmpty = api.rows().count() === 0;

                    // Tampilkan tombol Simpan jika tidak ada data
                    if (isEmpty) {
                        $('#submitBtn').text('Simpan').show();
                    } else {
                        $('#submitBtn').hide();
                    }
                }
            });

            // Handle form submission
            $('#midtransForm').on('submit', function(e) {
                e.preventDefault();

                var formData = $(this).serialize();
                var url = '{{ route('admin.midtrans_settings.store') }}';
                var method = 'POST';

                // If editing, change to PUT method
                if ($('#settingId').val()) {
                    url = '{{ url('admin/midtrans_settings') }}/' + $('#settingId').val();
                    method = 'PUT';
                }

                $.ajax({
                    url: url,
                    type: method,
                    data: formData,
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });

                        // Reset form and reload table
                        $('#midtransForm')[0].reset();
                        $('#settingId').val('');
                        table.ajax.reload();
                    },
                    error: function(xhr) {
                        var errorMessage = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: errorMessage
                        });
                    }
                });
            });

            // Handle edit button click
            $(document).on('click', '.edit-btn', function(e) {
                e.preventDefault();
                var url = $(this).attr('href');

                $.get(url, function(response) {
                    $('#settingId').val(response.id);
                    $('#server_key').val(response.server_key);
                    $('#client_key').val(response.client_key);
                    $('#mode').val(response.mode);

                    // Ubah teks tombol submit menjadi "Update"
                    $('#submitBtn').text('Update').show();

                    // Scroll to form
                    $('html, body').animate({
                        scrollTop: $('#midtransForm').offset().top
                    }, 500);
                });
            });

            // Handle delete button click
            $(document).on('click', '.delete-btn', function() {
                var url = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: response.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    table.ajax.reload();
                                });
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

            // Handle cancel button
            $('#cancelBtn').click(function() {
                $('#midtransForm')[0].reset();
                $('#settingId').val('');
                // Kembalikan teks tombol ke "Simpan" dan sembunyikan jika ada data
                var isEmpty = table.rows().count() === 0;
                if (isEmpty) {
                    $('#submitBtn').text('Simpan').show();
                } else {
                    $('#submitBtn').hide();
                }
            });
        });
    </script>
@endpush
