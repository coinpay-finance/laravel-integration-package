<?php

namespace Coinpay\Finance\Services\CoinPay;

class CoinPayPaymentResponse
{
    /**
     * @param string $url
     * @param string $transactionId
     * @param bool $status
     */
    public function __construct(protected string $url, protected string $transactionId, protected bool $status) {
    }
}