<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PaylabsService
{
    private $merchantId;
    private $baseUrl;
    private $privateKey;
    private $publicKey;
    private $paylabsPublicKey;

    public function __construct()
    {
        $this->merchantId = config('paylabs.merchant_id');
        $this->baseUrl = config('paylabs.base_url');

        try {
            // Load private key from storage
            $privateKeyPath = storage_path('app/' . env('PAYLABS_PRIVATE_KEY', 'keys/paylabs_private_key.pem'));
            if (!file_exists($privateKeyPath)) {
                throw new \Exception("Private key file not found at: " . $privateKeyPath);
            }

            $privateKeyContent = file_get_contents($privateKeyPath);
            $privateKeyContent = trim($privateKeyContent);

            Log::debug('Private key file loaded', [
                'path' => $privateKeyPath,
                'content_length' => strlen($privateKeyContent),
                'starts_with' => substr($privateKeyContent, 0, 50)
            ]);

            // Parse the private key
            $this->privateKey = openssl_pkey_get_private($privateKeyContent);
            if ($this->privateKey === false) {
                $error = $this->getOpenSSLErrors();
                throw new \Exception("Failed to parse private key: " . $error);
            }

            // Get private key details
            $keyDetails = openssl_pkey_get_details($this->privateKey);
            if (!$keyDetails || $keyDetails['type'] !== OPENSSL_KEYTYPE_RSA) {
                throw new \Exception("Private key is not a valid RSA key");
            }

            Log::info('Private key loaded successfully', [
                'bits' => $keyDetails['bits'],
                'type' => $keyDetails['type']
            ]);

            // Generate public key from private key instead of loading separate file
            // This ensures they are always a matching pair
            $publicKeyPem = $keyDetails['key'];

            $this->publicKey = openssl_pkey_get_public($publicKeyPem);
            if ($this->publicKey === false) {
                $error = $this->getOpenSSLErrors();
                throw new \Exception("Failed to extract public key from private key: " . $error);
            }

            Log::info('Public key extracted from private key successfully');

            // Load Paylabs public key for verification - PENTING untuk verifikasi signature
            $paylabsPublicKeyPath = storage_path('app/' . env('PAYLABS_PUBLIC_KEY', 'keys/paylabs_public_key.pem'));
            if (file_exists($paylabsPublicKeyPath)) {
                $paylabsPublicKeyContent = file_get_contents($paylabsPublicKeyPath);
                $this->paylabsPublicKey = openssl_pkey_get_public($paylabsPublicKeyContent);

                if ($this->paylabsPublicKey === false) {
                    Log::warning('Failed to load Paylabs public key for verification');
                    $this->paylabsPublicKey = null;
                } else {
                    Log::info('Paylabs public key loaded for signature verification');
                }
            } else {
                Log::warning('Paylabs public key file not found - SIGNATURE VERIFICATION WILL BE DISABLED', [
                    'expected_path' => $paylabsPublicKeyPath
                ]);
                $this->paylabsPublicKey = null;
            }

            // Test signature generation and verification with our key pair
            $testData = 'test_signature_verification_' . time();
            $signature = '';

            if (!openssl_sign($testData, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
                throw new \Exception("Failed to sign test data with private key");
            }

            $verifyResult = openssl_verify($testData, $signature, $this->publicKey, OPENSSL_ALGO_SHA256);
            if ($verifyResult !== 1) {
                if ($verifyResult === 0) {
                    throw new \Exception("Key pair verification failed - this should not happen with extracted public key");
                } else {
                    throw new \Exception("Error during signature verification: " . $this->getOpenSSLErrors());
                }
            }

            Log::info('Paylabs service initialized successfully', [
                'merchant_id' => $this->merchantId,
                'key_bits' => $keyDetails['bits'],
                'has_paylabs_public_key' => !is_null($this->paylabsPublicKey)
            ]);
        } catch (\Exception $e) {
            Log::error('Paylabs key initialization error: ' . $e->getMessage());
            throw new \Exception('Failed to initialize Paylabs payment service: ' . $e->getMessage());
        }
    }

    /**
     * Get all OpenSSL errors as a string
     */
    private function getOpenSSLErrors()
    {
        $errors = [];
        while ($msg = openssl_error_string()) {
            $errors[] = $msg;
        }
        return empty($errors) ? 'Unknown OpenSSL error' : implode('; ', $errors);
    }

    /**
     * Get detailed information about the loaded keys
     */
    public function getKeyInfo()
    {
        $privateKeyDetails = openssl_pkey_get_details($this->privateKey);

        return [
            'private_key' => [
                'bits' => $privateKeyDetails['bits'],
                'type' => $privateKeyDetails['type'],
                'key_size' => strlen($privateKeyDetails['key'])
            ],
            'has_paylabs_public_key' => !is_null($this->paylabsPublicKey),
            'merchant_id' => $this->merchantId,
            'base_url' => $this->baseUrl
        ];
    }

    /**
     * Verify signature from Paylabs notification
     * Fixed method yang sesuai dengan endpoint path yang benar
     */
    public function verifyPaylabsSignature(string $endpoint, array $body, string $timestamp, string $signature): bool
    {
        try {
            // Jika tidak ada Paylabs public key, skip verification (untuk development)
            if (!$this->paylabsPublicKey) {
                Log::warning('Paylabs public key not available, signature verification skipped');
                return true; // Untuk development - dalam production sebaiknya return false
            }

            $minifiedBody = $this->minifyJson($body);
            $bodyHash = strtolower(hash('sha256', $minifiedBody));

            // Format string yang akan diverifikasi: POST:endpoint:bodyHash:timestamp
            $stringContent = "POST:{$endpoint}:{$bodyHash}:{$timestamp}";

            Log::debug('Signature verification debug', [
                'endpoint' => $endpoint,
                'minified_body' => $minifiedBody,
                'body_hash' => $bodyHash,
                'timestamp' => $timestamp,
                'string_to_verify' => $stringContent,
                'signature_received' => $signature
            ]);

            $result = openssl_verify($stringContent, base64_decode($signature), $this->paylabsPublicKey, OPENSSL_ALGO_SHA256);

            Log::info('Signature verification result', [
                'result' => $result,
                'is_valid' => $result === 1
            ]);

            return $result === 1;
        } catch (\Exception $e) {
            Log::error('Error in signature verification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    public function createQRISPayment(float $amount, string $productName, string $merchantTradeNo)
    {
        if (empty($this->merchantId)) {
            throw new \Exception("Merchant ID is not configured");
        }
        $endpoint = '/payment/v2.1/qris/create';
        $timestamp = now()->format('Y-m-d\TH:i:s.vP'); // 2022-09-16T16:58:47.964+07:00

        $body = [
            'merchantId' => $this->merchantId,
            'merchantTradeNo' => $merchantTradeNo,
            'requestId' => Str::uuid()->toString(),
            'paymentType' => 'QRIS',
            'amount' => $amount,
            'productName' => $productName,
            'notifyUrl' => config('app.url') . '/v2/qris/notify',
        ];

        Log::info('Paylabs QRIS Request Body', $body);

        $signature = $this->generateSignature($endpoint, $body, $timestamp);

        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $this->merchantId,
            'X-REQUEST-ID' => $body['requestId']
        ];

        $response = Http::withHeaders($headers)
            ->post($this->baseUrl . $endpoint, $body);

        Log::info('Paylabs QRIS Response', [
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        return $response->json();
    }

    public function queryQRISPayment($merchantTradeNo, $rrn = null)
    {
        $endpoint = '/payment/v2.1/qris/query';
        $requestId = Str::uuid()->toString();
        $timestamp = Carbon::now()->format('Y-m-d\TH:i:s.vP');

        $requestBody = [
            'merchantId' => $this->merchantId,
            'requestId' => $requestId,
            'paymentType' => 'QRIS',
        ];

        // Use either merchantTradeNo or rrn (as per documentation)
        if ($rrn) {
            $requestBody['rrn'] = $rrn;
        } else {
            $requestBody['merchantTradeNo'] = $merchantTradeNo;
        }

        $signature = $this->generateSignature($endpoint, $requestBody, $timestamp);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $this->merchantId,
            'X-REQUEST-ID' => $requestId,
        ])->post($this->baseUrl . $endpoint, $requestBody);

        return $response->json();
    }

    public function cancelQRISPayment($merchantTradeNo, $platformTradeNo, $qrCode = null)
    {
        $endpoint = '/payment/v2.1/qris/cancel';
        $requestId = Str::uuid()->toString();
        $timestamp = Carbon::now()->format('Y-m-d\TH:i:s.vP');

        $requestBody = [
            'merchantId' => $this->merchantId,
            'merchantTradeNo' => $merchantTradeNo,
            'platformTradeNo' => $platformTradeNo,
            'requestId' => $requestId,
        ];

        if ($qrCode) {
            $requestBody['qrCode'] = $qrCode;
        }

        $signature = $this->generateSignature($endpoint, $requestBody, $timestamp);

        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $this->merchantId,
            'X-REQUEST-ID' => $requestId,
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $requestBody);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Paylabs Cancel QRIS Failed', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
                throw new \Exception('Failed to cancel QRIS payment. Status: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Paylabs Cancel QRIS Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function minifyJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function generateSignature(string $endpoint, array $body, string $timestamp): string
    {
        $method = 'POST';
        $minifiedBody = $this->minifyJson($body);
        $bodyHash = strtolower(hash('sha256', $minifiedBody));

        $stringContent = "{$method}:{$endpoint}:{$bodyHash}:{$timestamp}";

        openssl_sign($stringContent, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * Deprecated - use verifyPaylabsSignature instead
     */
    public function verifySignature($response, $timestamp, $signature)
    {
        try {
            // Use Paylabs public key if available, otherwise skip verification
            if ($this->paylabsPublicKey === null) {
                Log::warning('Paylabs public key not available, skipping signature verification');
                return true; // or false, depending on your security requirements
            }

            // Minify JSON body - same as generation process
            $minifiedBody = json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Calculate SHA256 hash (lowercase)
            $bodyHash = strtolower(hash('sha256', $minifiedBody));

            // Prepare string to verify - for notification endpoint
            $stringToVerify = "POST:/payment/v2.1/qris/notify:{$bodyHash}:{$timestamp}";

            Log::debug('Signature verification details:', [
                'string_to_verify' => $stringToVerify,
                'received_signature' => $signature
            ]);

            // Verify signature using Paylabs public key
            $result = openssl_verify(
                $stringToVerify,
                base64_decode($signature),
                $this->paylabsPublicKey,
                OPENSSL_ALGO_SHA256
            );

            Log::info('Signature verification result:', [
                'result' => $result,
                'is_valid' => $result === 1
            ]);

            return $result === 1;
        } catch (\Exception $e) {
            Log::error('Signature verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Generate signature for response to Paylabs notification
     */
    public function generateResponseSignature(array $responseData, string $timestamp): string
    {
        // Minify JSON response body
        $minifiedBody = $this->minifyJson($responseData);

        // Calculate SHA256 hash (lowercase)
        $bodyHash = strtolower(hash('sha256', $minifiedBody));

        // Prepare string to sign - format: HTTPMethod:EndpointUrl:BodyHash:Timestamp
        $endpointUrl = '/v2/qris/notify'; // Same as notification endpoint
        $stringToSign = "POST:{$endpointUrl}:{$bodyHash}:{$timestamp}";

        // Generate signature
        openssl_sign($stringToSign, $signature, $this->privateKey, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    /**
     * Clean up resources when the object is destroyed
     */
    public function __destruct()
    {
        if (is_resource($this->privateKey)) {
            openssl_pkey_free($this->privateKey);
        }
        if (is_resource($this->publicKey)) {
            openssl_pkey_free($this->publicKey);
        }
        if (is_resource($this->paylabsPublicKey)) {
            openssl_pkey_free($this->paylabsPublicKey);
        }
    }

    public function createEWalletPayment(
        float $amount,
        string $productName,
        string $merchantTradeNo,
        string $paymentType,
        string $redirectUrl,
        string $phoneNumber = null
    ): array {
        try {
            $endpoint = '/payment/v2.1/ewallet/create';
            $timestamp = now()->format('Y-m-d\TH:i:s.vP');
            $requestId = Str::uuid()->toString();

            $body = [
                'merchantId' => $this->merchantId,
                'merchantTradeNo' => $merchantTradeNo,
                'requestId' => $requestId,
                'paymentType' => $paymentType,
                'amount' => number_format($amount, 2, '.', ''),
                'productName' => $productName,
                'notifyUrl' => config('app.url') . '/v2/ewallet/notify',
                'paymentParams' => ['redirectUrl' => $redirectUrl]
            ];

            // Tambahkan nomor HP untuk OVO jika ada
            if ($paymentType === 'OVOBALANCE' && $phoneNumber) {
                $body['paymentParams']['phoneNumber'] = $phoneNumber;
            }

            $signature = $this->generateSignature($endpoint, $body, $timestamp);

            $headers = [
                'Content-Type' => 'application/json;charset=utf-8',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => $this->merchantId,
                'X-REQUEST-ID' => $requestId
            ];

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $body);

            if ($response->failed()) {
                throw new \Exception("HTTP request failed with status: " . $response->status());
            }

            $responseData = $response->json();

            if (!isset($responseData['errCode'])) {
                throw new \Exception("Invalid response format from Paylabs");
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paylabs E-Wallet API Error', [
                'error' => $e->getMessage(),
                'paymentType' => $paymentType,
                'merchantTradeNo' => $merchantTradeNo
            ]);
            throw $e;
        }
    }

    /**
     * Query E-Wallet Payment Status
     *
     * @param string $merchantTradeNo Merchant trade number
     * @param string $paymentType Payment type
     * @param string|null $storeId Store ID if applicable
     * @return array API response
     */
    public function queryEWalletPayment(
        string $merchantTradeNo,
        string $paymentType,
        string $storeId = null
    ): array {
        $endpoint = '/payment/v2.1/ewallet/query';
        $timestamp = now()->format('Y-m-d\TH:i:s.vP');
        $requestId = Str::uuid()->toString();

        $body = [
            'requestId' => $requestId,
            'merchantId' => $this->merchantId,
            'merchantTradeNo' => $merchantTradeNo,
            'paymentType' => $paymentType,
        ];

        if ($storeId) {
            $body['storeId'] = $storeId;
        }

        $signature = $this->generateSignature($endpoint, $body, $timestamp);

        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $this->merchantId,
            'X-REQUEST-ID' => $requestId
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $body);

            $responseData = $response->json();

            Log::info('E-Wallet Query Response', [
                'merchant_trade_no' => $merchantTradeNo,
                'payment_type' => $paymentType,
                'response' => $responseData
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('E-Wallet Query API Error', [
                'error' => $e->getMessage(),
                'merchant_trade_no' => $merchantTradeNo
            ]);

            throw new \Exception('Failed to query e-wallet payment: ' . $e->getMessage());
        }
    }

    /**
     * Get supported e-wallet payment types
     *
     * @return array List of supported payment types with their codes
     */
    public function getSupportedEWalletTypes(): array
    {
        return [
            'DANABALANCE' => [
                'name' => 'DANA E-Money',
                'supports_refund' => true,
                'requires_redirect_url' => true,
                'supports_phone_number' => false
            ],
            'SHOPEEBALANCE' => [
                'name' => 'Shopee E-Money',
                'supports_refund' => true,
                'requires_redirect_url' => true,
                'supports_phone_number' => false
            ],
            'LINKAJABALANCE' => [
                'name' => 'LinkAja E-Money',
                'supports_refund' => true,
                'requires_redirect_url' => true,
                'supports_phone_number' => false
            ],
            'OVOBALANCE' => [
                'name' => 'OVO E-Money',
                'supports_refund' => false,
                'requires_redirect_url' => false,
                'supports_phone_number' => true
            ],
            'GOPAYBALANCE' => [
                'name' => 'GoPay E-Money',
                'supports_refund' => true,
                'requires_redirect_url' => true,
                'supports_phone_number' => false
            ]
        ];
    }

    /**
     * Verify E-Wallet notification signature
     *
     * @param string $endpoint Notification endpoint path
     * @param array $body Notification body
     * @param string $timestamp Request timestamp
     * @param string $signature Received signature
     * @return bool True if signature is valid
     */
    public function verifyEWalletNotificationSignature(
        string $endpoint,
        array $body,
        string $timestamp,
        string $signature
    ): bool {
        // Reuse the existing signature verification method
        return $this->verifyPaylabsSignature($endpoint, $body, $timestamp, $signature);
    }


    public function createVAPayment(
        float $amount,
        string $productName,
        string $merchantTradeNo,
        string $paymentType,
        string $payerName,
        string $storeId = null
    ): array {
        try {
            $endpoint = '/payment/v2.1/va/create';
            $timestamp = now()->format('Y-m-d\TH:i:s.vP');
            $requestId = Str::uuid()->toString();

            $body = [
                'merchantId' => $this->merchantId,
                'merchantTradeNo' => $merchantTradeNo,
                'requestId' => $requestId,
                'paymentType' => $paymentType,
                'amount' => number_format($amount, 2, '.', ''),
                'productName' => $productName,
                'payer' => $payerName,
                'notifyUrl' => config('app.url') . '/v2/va/notify',
            ];

            // Add store ID if provided
            if ($storeId) {
                $body['storeId'] = $storeId;
            }

            $signature = $this->generateSignature($endpoint, $body, $timestamp);

            $headers = [
                'Content-Type' => 'application/json;charset=utf-8',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => $this->merchantId,
                'X-REQUEST-ID' => $requestId
            ];

            Log::info('VA Payment Request', [
                'endpoint' => $endpoint,
                'body' => $body,
                'headers' => $headers
            ]);

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $body);

            if ($response->failed()) {
                throw new \Exception("HTTP request failed with status: " . $response->status());
            }

            $responseData = $response->json();

            Log::info('VA Payment Response', [
                'status' => $response->status(),
                'response' => $responseData
            ]);

            if (!isset($responseData['errCode'])) {
                throw new \Exception("Invalid response format from Paylabs");
            }

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paylabs VA API Error', [
                'error' => $e->getMessage(),
                'paymentType' => $paymentType,
                'merchantTradeNo' => $merchantTradeNo
            ]);
            throw $e;
        }
    }

    /**
     * Query Virtual Account Payment Status
     *
     * @param string $merchantTradeNo Merchant trade number
     * @param string $paymentType Payment type
     * @param string|null $storeId Store ID if applicable
     * @return array API response
     */
    public function queryVAPayment(
        string $merchantTradeNo,
        string $paymentType,
        string $storeId = null
    ): array {
        $endpoint = '/payment/v2.1/va/query';
        $timestamp = now()->format('Y-m-d\TH:i:s.vP');
        $requestId = Str::uuid()->toString();

        $body = [
            'requestId' => $requestId,
            'merchantId' => $this->merchantId,
            'merchantTradeNo' => $merchantTradeNo,
            'paymentType' => $paymentType,
        ];

        if ($storeId) {
            $body['storeId'] = $storeId;
        }

        $signature = $this->generateSignature($endpoint, $body, $timestamp);

        $headers = [
            'Content-Type' => 'application/json;charset=utf-8',
            'X-TIMESTAMP' => $timestamp,
            'X-SIGNATURE' => $signature,
            'X-PARTNER-ID' => $this->merchantId,
            'X-REQUEST-ID' => $requestId
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $body);

            $responseData = $response->json();

            Log::info('VA Query Response', [
                'merchant_trade_no' => $merchantTradeNo,
                'payment_type' => $paymentType,
                'response' => $responseData
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('VA Query API Error', [
                'error' => $e->getMessage(),
                'merchant_trade_no' => $merchantTradeNo
            ]);

            throw new \Exception('Failed to query VA payment: ' . $e->getMessage());
        }
    }

    /**
     * Create Static Virtual Account
     *
     * @param string $paymentType Static VA payment type (StaticMandiriVA, StaticBCAVA)
     * @param string $payerName Name of the payer
     * @param string|null $beUsedFor Usage description
     * @param string|null $storeId Store ID (optional)
     * @return array API response
     */
    public function createStaticVA(
        string $paymentType,
        string $payerName,
        string $beUsedFor = null,
        string $storeId = null
    ): array {
        try {
            $endpoint = '/payment/v2.1/staticva/create';
            $timestamp = now()->format('Y-m-d\TH:i:s.vP');
            $requestId = Str::uuid()->toString();

            $body = [
                'merchantId' => $this->merchantId,
                'requestId' => $requestId,
                'paymentType' => $paymentType,
                'payer' => $payerName,
                'notifyUrl' => config('app.url') . '/v2/staticva/notify',
            ];

            // Add optional fields
            if ($beUsedFor) {
                $body['beUsedFor'] = $beUsedFor;
            }

            if ($storeId) {
                $body['storeId'] = $storeId;
            }

            $signature = $this->generateSignature($endpoint, $body, $timestamp);

            $headers = [
                'Content-Type' => 'application/json;charset=utf-8',
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signature,
                'X-PARTNER-ID' => $this->merchantId,
                'X-REQUEST-ID' => $requestId
            ];

            $response = Http::withHeaders($headers)
                ->timeout(30)
                ->post($this->baseUrl . $endpoint, $body);

            if ($response->failed()) {
                throw new \Exception("HTTP request failed with status: " . $response->status());
            }

            $responseData = $response->json();

            Log::info('Static VA Response', [
                'payment_type' => $paymentType,
                'response' => $responseData
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Paylabs Static VA API Error', [
                'error' => $e->getMessage(),
                'paymentType' => $paymentType
            ]);
            throw $e;
        }
    }

    /**
     * Get supported Virtual Account payment types
     *
     * @return array List of supported VA types with their details
     */
    public function getSupportedVATypes(): array
    {
        return [
            'SinarmasVA' => [
                'name' => 'Bank Sinarmas',
                'bank_code' => 'SINARMAS',
                'supports_static' => false
            ],
            'MaybankVA' => [
                'name' => 'Maybank Indonesia',
                'bank_code' => 'MAYBANK',
                'supports_static' => false
            ],
            'DanamonVA' => [
                'name' => 'Bank Danamon',
                'bank_code' => 'DANAMON',
                'supports_static' => false
            ],
            'BNCVA' => [
                'name' => 'Bank Neo Commerce',
                'bank_code' => 'BNC',
                'supports_static' => false
            ],
            'BCAVA' => [
                'name' => 'Bank Central Asia',
                'bank_code' => 'BCA',
                'supports_static' => true
            ],
            'INAVA' => [
                'name' => 'Bank INA Perdana',
                'bank_code' => 'INA',
                'supports_static' => false
            ],
            'BNIVA' => [
                'name' => 'Bank Negara Indonesia',
                'bank_code' => 'BNI',
                'supports_static' => false
            ],
            'PermataVA' => [
                'name' => 'Bank Permata',
                'bank_code' => 'PERMATA',
                'supports_static' => false
            ],
            'MuamalatVA' => [
                'name' => 'Bank Muamalat',
                'bank_code' => 'MUAMALAT',
                'supports_static' => false
            ],
            'BSIVA' => [
                'name' => 'Bank Syariah Indonesia',
                'bank_code' => 'BSI',
                'supports_static' => false
            ],
            'BRIVA' => [
                'name' => 'Bank Rakyat Indonesia',
                'bank_code' => 'BRI',
                'supports_static' => false
            ],
            'MandiriVA' => [
                'name' => 'Bank Mandiri',
                'bank_code' => 'MANDIRI',
                'supports_static' => true
            ],
            'CIMBVA' => [
                'name' => 'Bank CIMB Niaga',
                'bank_code' => 'CIMB',
                'supports_static' => false
            ]
        ];
    }

    /**
     * Verify VA notification signature
     *
     * @param string $endpoint Notification endpoint path
     * @param array $body Notification body
     * @param string $timestamp Request timestamp
     * @param string $signature Received signature
     * @return bool True if signature is valid
     */
    public function verifyVANotificationSignature(
        string $endpoint,
        array $body,
        string $timestamp,
        string $signature
    ): bool {
        // Reuse the existing signature verification method
        return $this->verifyPaylabsSignature($endpoint, $body, $timestamp, $signature);
    }
}
