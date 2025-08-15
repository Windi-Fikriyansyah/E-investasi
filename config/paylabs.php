<?php
return [
    'merchant_id' => env('PAYLABS_MERCHANT_ID'),
    'base_url' => env('PAYLABS_BASE_URL'),
    'private_key' => env('PAYLABS_PRIVATE_KEY', 'keys/paylabs_private_key.pem'),
    'public_key' => env('PAYLABS_PUBLIC_KEY', 'keys/paylabs_public_key.pem'),
];
