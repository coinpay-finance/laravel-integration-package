<?php

namespace Coinpay\Finance\services\CoinPay;

class CoinPayPaymentStatus
{
    public $status = "";
    public $amount = "";
    public $reason = "";
    public $transactionId = "";
    public $transactionHash = "";
    public $network = "";

    public function __construct(string $status, string $amount, string $transactionId, string $reason, string $transactionHash, string $network) {
        $this->status = $status;
        $this->amount = $amount;
        $this->transactionId = $transactionId;
        $this->reason = $reason;
        $this->transactionHash = $transactionHash;
        $this->network = $network;
    }
}
