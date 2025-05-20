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
        1,                                              // The amount to be paid (in Dollar).
        'https://your-callback.url',                    // The URL the user will be redirected to after payment.
        'ref123456',                                    // A unique reference ID for tracking the transaction.
        'payer@example.com',                            // The identity of the payer (email or phone number).
        'Alimo',                                        // Full name of the payer.
        'Test Payment',                                 // Description of the payment (e.g. "Payment for order #123").
        '1234567890'                                    // National identification code of the payer.
    );
    $url = $coinPayGateway->url;
    //$transactionId = $coinPayGateway->transactionId;  //Store this Id if you need

    return response("<a style='word-break: break-all' href=$url > $url </a>");

}catch (\Exception $exception){
    return $exception->getMessage();
}
