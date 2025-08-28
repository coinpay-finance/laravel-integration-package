<?php

namespace Coinpay\Finance\Services\CoinPay;

class CoinPayPaymentResponse
{
    /**
     * @param string $url
     * @param string $transactionId
     * @param bool $status
     */
    public function __construct(public string $url, public string $transactionId, public bool $status) {
    }
}