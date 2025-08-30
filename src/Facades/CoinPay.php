<?php
namespace Coinpay\Finance\Facades;

use Coinpay\Finance\Services\CoinPay\CoinPayGatewayInterface;
use Coinpay\Finance\Services\CoinPay\CoinPayPaymentRequest;
use Coinpay\Finance\Services\CoinPay\CoinPayPaymentResponse;
use Coinpay\Finance\Services\CoinPay\CoinPayPaymentStatus;
use Illuminate\Support\Facades\Facade;

/**
 * Facade for interacting with the CoinPay payment gateway.
 *
 * Provides a simplified interface for creating payments.
 *
 * @method static CoinPayPaymentResponse createPayment(CoinPayPaymentRequest $paymentRequest)
 *     Create a payment request using a CoinPayPaymentRequest DTO.
 *
 * @method static CoinPayPaymentStatus checkStatus(string $transaction_id)
 *    Check the status of a CoinPay transaction using the transaction ID.
 *
 * @see CoinPayGatewayInterface
 */
class CoinPay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'coinpay.gateway';
    }
}
