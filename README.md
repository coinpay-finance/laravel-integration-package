# Coinpay

Safe, fast and instant payments; Anytime, anywhere with CoinPay

## Installation

Install with composer:

```bash
composer require coinpay/laravel-integration
```

Publish Vendor Files:
```bash
php artisan vendor:publish --tag=coinpay-config
```

### Configuration
In config/coinpay.php file you can change your api_key:

``
'api_key' => env('COINPAY_API_KEY'),
``

## Usage
```php
try {
    $coinPayGateway = CoinPay::request(
        1,
        'https://your-callback.url',
        'ref123456',
        'payer@example.com',
        'Alimo',
        'Test Payment',
        '1234567890'
    );
    $url = $coinPayGateway->url;
    //  $transactionId = $coinPayGateway->transactionId; // Store this Id if you need

    return response("<a style='word-break: break-all' href=$url > $url </a>");

} catch (\Exception $exception) {
    return $exception->getMessage();
}
