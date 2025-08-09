@extends('layouts.app')
@section('title', 'Pembayaran Deposit')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xxl">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Pembayaran Deposit</h5>
                    </div>
                    <div class="card-body">
                        <div id="midtrans-payment" class="text-center">
                            <p>Silakan lengkapi pembayaran Anda sebesar:
                                <strong>Rp {{ number_format($amount, 0, ',', '.') }}</strong>
                            </p>
                            <button id="pay-button" class="btn btn-primary">Bayar Sekarang</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
    <script>
        document.getElementById('pay-button').onclick = function() {
            snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    window.location.href = "{{ route('deposit.callback') }}?status=success&order_id=" +
                        result.order_id;
                },
                onPending: function(result) {
                    window.location.href = "{{ route('deposit.callback') }}?status=pending&order_id=" +
                        result.order_id;
                },
                onError: function(result) {
                    window.location.href = "{{ route('deposit.callback') }}?status=failed&order_id=" +
                        result.order_id;
                }
            });
        };
    </script>
@endsection
