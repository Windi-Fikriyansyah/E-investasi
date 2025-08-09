@extends('template.app')
@section('title', 'Produk')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Basic Layout & Basic with Icons -->
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($produk) ? 'Edit' : 'Tambah' }} Produk</h5>
                        <small class="text-muted float-end">Form Produk</small>
                    </div>
                    <div class="card-body">
                        <form
                            action="{{ isset($produk) ? route('admin.produk.update', $produk->id) : route('admin.produk.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($produk))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="nama_produk">Nama Produk</label>
                                <div class="col-sm-10">
                                    <input type="text" id="nama_produk" name="nama_produk"
                                        class="form-control @error('nama_produk') is-invalid @enderror"
                                        value="{{ old('nama_produk', $produk->nama_produk ?? '') }}" required />
                                    @error('nama_produk')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="gambar">Gambar Produk</label>
                                <div class="col-sm-10">
                                    <input type="file" id="gambar" name="gambar"
                                        class="form-control @error('gambar') is-invalid @enderror" />
                                    @error('gambar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror

                                    @if (isset($produk) && $produk->gambar)
                                        <div class="mt-2">
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="Gambar Produk"
                                                style="max-width: 200px; max-height: 200px;">
                                            <p class="text-muted mt-1">Gambar saat ini</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="kategori">Kategori</label>
                                <div class="col-sm-10">
                                    <select id="kategori" name="kategori"
                                        class="form-select select2 @error('kategori') is-invalid @enderror" required>
                                        @if (isset($produk) && $produk->kategori)
                                            <option value="{{ $produk->kategori }}" selected>{{ $produk->kategori }}
                                            </option>
                                        @endif
                                    </select>
                                    @error('kategori')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="harga">Harga</label>
                                <div class="col-sm-10">
                                    <input type="text" id="harga" name="harga"
                                        class="form-control @error('harga') is-invalid @enderror"
                                        value="{{ old('harga', isset($produk) ? number_format($produk->harga, 0, ',', '.') : '') }}"
                                        required />
                                    @error('harga')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Masukkan Harga (Rupiah)</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="durasi">Durasi (hari)</label>
                                <div class="col-sm-10">
                                    <input type="number" id="durasi" name="durasi"
                                        class="form-control @error('durasi') is-invalid @enderror"
                                        value="{{ old('durasi', $produk->durasi ?? '') }}" required />
                                    @error('durasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="pendapatan_harian">Pendapatan Harian</label>
                                <div class="col-sm-10">
                                    <input type="text" id="pendapatan_harian" name="pendapatan_harian"
                                        class="form-control @error('pendapatan_harian') is-invalid @enderror"
                                        value="{{ old('pendapatan_harian', isset($produk) ? number_format($produk->pendapatan_harian, 0, ',', '.') : '') }}"
                                        required />
                                    @error('pendapatan_harian')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Masukkan Pendapatan Harian (Rupiah)</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="total_pendapatan">Total Pendapatan</label>
                                <div class="col-sm-10">
                                    <input type="text" id="total_pendapatan" name="total_pendapatan" readonly
                                        class="form-control @error('total_pendapatan') is-invalid @enderror"
                                        value="{{ old('total_pendapatan', isset($produk) ? number_format($produk->total_pendapatan, 0, ',', '.') : '') }}"
                                        required />
                                    @error('total_pendapatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Total Pendapatan akan dihitung otomatis</div>
                                </div>
                            </div>




                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="keterangan">Keterangan</label>
                                <div class="col-sm-10">
                                    <textarea id="keterangan" name="keterangan" rows="4"
                                        class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $produk->keterangan ?? '') }}</textarea>
                                    @error('keterangan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($produk) ? 'Update' : 'Simpan' }}
                                    </button>
                                    <a href="{{ route('admin.produk.index') }}" class="btn btn-secondary">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .right-gap {
            margin-right: 10px
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            padding-top: 4px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize Select2 for kategori
            $('#kategori').select2({
                placeholder: 'Pilih Kategori',
                allowClear: true,
                theme: "bootstrap-5",
                ajax: {
                    url: '{{ route('admin.produk.getkategori') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.text,
                                    id: item.text
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            $('#vip').select2({
                placeholder: 'Pilih vip',
                allowClear: true,
                theme: "bootstrap-5",
                ajax: {
                    url: '{{ route('admin.produk.getvip') }}',
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: $.map(data, function(item) {
                                return {
                                    text: item.text,
                                    id: item.text
                                }
                            })
                        };
                    },
                    cache: true
                }
            });

            // Format harga as number with thousand separator
            $('#harga, #pendapatan_harian, #total_pendapatan').on('input', function() {
                let value = this.value.replace(/[^0-9]/g, '');

                if (value.length > 0) {
                    value = parseInt(value).toLocaleString('id-ID');
                }

                this.value = value;
            });

            // Calculate total_pendapatan when durasi or pendapatan_harian changes
            function calculateTotal() {
                let durasi = parseInt($('#durasi').val()) || 0;
                let pendapatan_harian = $('#pendapatan_harian').val().replace(/[^0-9]/g, '');
                pendapatan_harian = parseInt(pendapatan_harian) || 0;

                let total = durasi * pendapatan_harian;
                $('#total_pendapatan').val(total.toLocaleString('id-ID'));
            }

            $('#durasi, #pendapatan_harian').on('input', calculateTotal);

            // Before form submission, remove thousand separators
            $('form').on('submit', function() {
                $('#harga').val($('#harga').val().replace(/[^0-9]/g, ''));
                $('#pendapatan_harian').val($('#pendapatan_harian').val().replace(/[^0-9]/g, ''));
                $('#total_pendapatan').val($('#total_pendapatan').val().replace(/[^0-9]/g, ''));
                return true;
            });
        });
    </script>
@endpush
