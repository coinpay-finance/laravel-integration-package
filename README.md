# CoinPay

Safe, fast and instant payments; Anytime, anywhere with CoinPay.

Official Laravel SDK for integrating [CoinPay](https://coinpay.finance/en) cryptocurrency payments into your Laravel application. Supports Laravel 10, 11, 12 and 13.

## Installation

Install with composer:

```bash
composer require coinpay/laravel-integration
```

Publish the config file:

```bash
php artisan vendor:publish --tag=coinpay-config
```

## Configuration

After publishing, `config/coinpay.php` will be available. Set at least your API key, webhook secret, and redirect URL in your `.env` file:

```
COINPAY_API_KEY=your_api_key_here
COINPAY_BASE_URL=https://platform.coinpay.finance/api/v1/coin-pay
COINPAY_REDIRECT_URL=https://your-website.com/callback
COINPAY_WEBHOOK_SECRET=your_webhook_secret_here
```

Optional tuning:

```
# HTTP client timeouts (seconds) for calls to the CoinPay API
COINPAY_CONNECT_TIMEOUT=5
COINPAY_TIMEOUT=15

# Clock-skew tolerance (seconds) for webhook signature verification
COINPAY_WEBHOOK_SIGNATURE_TOLERANCE=300

# Where CoinPay should POST webhooks, and the route's name
COINPAY_WEBHOOK_ROUTE=/coinpay/webhook
COINPAY_WEBHOOK_ROUTE_NAME=coinpay.webhook
```

See `config/coinpay.php` for the full list of options and their doc comments.

## Usage

### Creating a Payment

```php
use Coinpay\Finance\Services\CoinPay\CoinPayPaymentRequest;
use Coinpay\Finance\Facades\CoinPay;
use Coinpay\Finance\Exceptions\CoinPayException;

$paymentRequest = new CoinPayPaymentRequest(
    amount: 100000,                     // Amount in smallest currency unit
    clientRefId: 'INV12345',            // Unique reference ID
    payerIdentity: 'user@example.com',  // Payer identity (email/phone)
    name: 'John Doe',                   // Payer name
    description: 'Invoice Payment',     // Payment description
    nationalCode: '1234567890'          // National code of payer
);

try {
    $response = CoinPay::createPayment($paymentRequest);

    $url = $response->url;
    // Optionally store $response->transactionId in your database
} catch (CoinPayException $e) {
    // $e->getMessage() / $e->getCode() as usual, plus extra context:
    $e->getData();            // raw context array (e.g. ['message' => ..., 'code' => ...])
    $e->getHttpStatusCode();  // HTTP status returned by the CoinPay API, if known
}
```

### Checking a Payment's Status

```php
use Coinpay\Finance\Facades\CoinPay;

$status = CoinPay::checkStatus($transactionId);

$status->status;          // e.g. 'completed', 'failed', 'pending'
$status->amount;
$status->reason;          // nullable
$status->transactionHash; // nullable
$status->network;         // nullable
```

> **Note:** `checkStatus()` calls `GET {base_url}` (the bare base URL, no additional path segment) with `transaction_id` as a query parameter. This is unusual for a REST API but is what the CoinPay platform actually expects today — it is intentionally preserved as-is rather than "corrected" to a guessed path. See `CoinPayGateway::checkStatus()` for details.

### Handling Webhooks

Incoming webhook requests to your configured `webhook_route` are protected by the `VerifyCoinPayWebhookSignature` middleware (applied automatically to the package's route), which supports two schemes:

* **Current (HMAC-SHA256)** — CoinPay sends `X-Coinpay-Signature: v1=<hex-hmac-sha256>`, `X-Coinpay-Timestamp: <unix-seconds>` and `X-Coinpay-Delivery: <uuid>` headers. The signature covers `"{timestamp}.{deliveryId}.{rawBody}"`, HMAC'd with your `COINPAY_WEBHOOK_SECRET`. Requests older/newer than `webhook_signature_tolerance` seconds, or with a delivery id already seen within that window, are rejected.
* **Legacy (deprecated, fallback only)** — a static `SECRET` header compared against `COINPAY_WEBHOOK_SECRET`. No payload binding or replay protection. Only evaluated when none of the HMAC headers are present; kept for merchants who haven't moved to signature verification yet.

By the time your webhook logic runs, the request is already verified — you only need to process the payload. Implement `WebhookServiceInterface` and point `coinpay.webhook_service` at your class:

```php
namespace App\Services;

use App\Models\Payment;
use Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface;

class MyCustomWebhookService implements WebhookServiceInterface
{
    public function handleWebhook(array $payload): array
    {
        // $payload contains: status, reason, transaction_id, amount, transaction_hash
        Payment::updateOrCreate(
            ['transaction_id' => $payload['transaction_id']],
            [
                'amount' => $payload['amount'],
                'status' => $payload['status'],
                'transaction_hash' => $payload['transaction_hash'],
            ]
        );

        return [
            'is_success' => true,
            'message' => 'Stored successfully',
        ];
    }
}
```

```php
// config/coinpay.php
'webhook_service' => \App\Services\MyCustomWebhookService::class,
```

## Notes

* Always use `route('coinpay.webhook')` for the webhook callback URL to respect any custom route defined by the user.
* The `redirect_url` is fetched from the config, so you can change it per environment without editing code.
* The Guzzle client used for CoinPay API calls has connect/request timeouts set (`connect_timeout` / `timeout` config keys) so a hung upstream call can't hang your application's request indefinitely.

## Testing this package

```bash
composer install
vendor/bin/phpunit
```

Tests use [Orchestra Testbench](https://packages.tools/testbench) to boot a minimal Laravel application for the package.
