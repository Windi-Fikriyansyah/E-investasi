@extends('template.app')
@section('title', 'vip')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Basic Layout & Basic with Icons -->
        <div class="row">
            <!-- Basic Layout -->
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">{{ isset($vip) ? 'Edit' : 'Tambah' }} VIP</h5>
                        <small class="text-muted float-end">Form VIP</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ isset($vip) ? route('admin.vip.update', $vip->id) : route('admin.vip.store') }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            @if (isset($vip))
                                @method('PUT')
                            @endif

                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label" for="vip">Nama vip</label>
                                <div class="col-sm-10">
                                    <input type="text" id="vip" name="vip"
                                        class="form-control @error('vip') is-invalid @enderror"
                                        value="{{ old('vip', $vip->vip ?? '') }}" required />
                                    @error('vip')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>



                            <div class="row justify-content-end">
                                <div class="col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        {{ isset($vip) ? 'Update' : 'Simpan' }}
                                    </button>
                                    <a href="{{ route('admin.vip.index') }}" class="btn btn-secondary">Kembali</a>
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
@endpush
