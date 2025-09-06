# CoinPay

Safe, fast and instant payments; Anytime, anywhere with CoinPay.

## Installation

Install with composer:

```bash
composer require coinpay/laravel-integration
```

Publish Vendor Files:

```bash
php artisan vendor:publish --tag=coinpay-config
```

## Configuration

After publishing, a configuration file `config/coinpay.php` will be available. You can set your API key and webhook route:

```php
return [
     /**
     * Your CoinPay API Key.
     *
     * You should set this value in your `.env` file as `COINPAY_API_KEY`.
     *
     * @var string
     */
    'api_key' => env('COINPAY_API_KEY'),          // Your CoinPay API key
	
	 /**
     * Webhook Route.
     *
     * This is the URI path where CoinPay will send asynchronous payment
     * notifications (webhooks). You can customize it in your `.env` file
     * as `COINPAY_WEBHOOK_ROUTE`.
     *
     * Example: '/coinpay/webhook'
     *
     * @var string
     */
    'webhook_route' => '/coinpay/webhook',       // The route for receiving webhook callbacks
	
	    /**
     * Webhook Service.
     *
     * This must implement the `WebhookServiceInterface`. By default, the package
     * provides a basic implementation. You can override it by binding your own
     * class in a ServiceProvider.
     *
     * Example:
     * $this->app->bind(
     *     \Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface::class,
     *     \App\Services\MyCustomWebhookService::class
     * );
     *
     * @var class-string<\Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface>
     */
    'webhook_service' => \Coinpay\Finance\Services\CoinPay\Webhook\WebhookService::class,
	
	/**
     * Redirect URL.
     *
     * This is the URL where the user will be redirected after completing
     * the payment process on the CoinPay gateway.
     *
     * You should set this value in your `.env` file as `COINPAY_REDIRECT_URL`.
     *
     * Example: 'https://your-app.com/payment/callback'
     *
     * @var string
     */
    'redirect_url' => env('COINPAY_REDIRECT_URL', 'https://your-website.com/callback'), // Redirect URL after payment
];
```

Add these to your `.env` file:

```
COINPAY_API_KEY=your_api_key_here
COINPAY_REDIRECT_URL=https://your-website.com/callback
```

## Usage

### Creating a Payment

```php
use Coinpay\Finance\Services\CoinPayPaymentRequest;
use Coinpay\Finance\Facades\CoinPay;


$paymentRequest = new CoinPayPaymentRequest(
    amount: 100000,                     // Amount in smallest currency unit
    clientRefId: 'INV12345',            // Unique reference ID
    payerIdentity: 'user@example.com',  // Payer identity (email/phone)
    name: 'John Doe',                   // Payer name
    description: 'Invoice Payment',     // Payment description
    nationalCode: '1234567890'          // National code of payer
);

$response = CoinPay::createPayment($paymentRequest);

$url = $response->url;
// Optionally store $response->transactionId in your database
```

### Handling Webhook

Extend `WebhookService` to handle webhook callbacks:

```php
namespace App\Services;

use App\Models\Payment;
use Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface;

class MyCustomWebhookService implements WebhookServiceInterface
{
    public function handleWebhook(string $gateway, array $payload): array
    {
        // Save payment data to database
        Payment::create([
            'transaction_id' => $payload['transaction_id'],
            'amount' => $payload['amount'],
            'status' => $payload['status'],
        ]);

        return [
            'is_success' => true,
            'message' => 'Stored successfully',
        ];
    }
}
```

## Notes

* Always use `route('coinpay.webhook')` for the webhook callback URL to respect any custom route defined by the user.
* The `redirect_url` is fetched from the config, so you can change it per environment without editing code.
