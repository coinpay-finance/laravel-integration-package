<?php
namespace Coinpay\Finance\facades;

use Coinpay\Finance\services\CoinPay\CoinPayGatewayInterface;
use Coinpay\Finance\services\CoinPay\PaymentResponse;
use Illuminate\Support\Facades\Facade;

/**
 * @method static PaymentResponse request(int $amount, string $callbackUrl, string $client_ref_id, string $payer_identity = null, string $name = null, string $description = null , string $national_code = null)
 * @see CoinPayGatewayInterface
 */
class CoinPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return CoinPayGatewayInterface::class;
    }
}
