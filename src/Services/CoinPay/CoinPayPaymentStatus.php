<?php

namespace Coinpay\Finance\Services\CoinPay;

/**
 * Data Transfer Object representing the result of a status check
 * against the CoinPay API.
 *
 * `reason`, `transactionHash` and `network` are nullable: CoinPay does
 * not always populate them (e.g. a pending transaction typically has no
 * hash/network yet, and `reason` is generally only set on failure).
 */
class CoinPayPaymentStatus
{
    public function __construct(
        public string $status,
        public string $amount,
        public string $transactionId,
        public ?string $reason = null,
        public ?string $transactionHash = null,
        public ?string $network = null,
    ) {
    }
}
