<?php

namespace Coinpay\Finance\Tests\Unit;

use Coinpay\Finance\Services\CoinPay\CoinPayPaymentStatus;
use PHPUnit\Framework\TestCase;

class CoinPayPaymentStatusTest extends TestCase
{
    public function test_accepts_null_reason_hash_and_network(): void
    {
        // Regression test: the API does not always populate reason /
        // transaction_hash / network (e.g. while a transaction is still
        // pending). Passing null used to throw a TypeError.
        $status = new CoinPayPaymentStatus(
            status: 'pending',
            amount: '100.00',
            transactionId: 'txn_123',
            reason: null,
            transactionHash: null,
            network: null,
        );

        $this->assertSame('pending', $status->status);
        $this->assertSame('100.00', $status->amount);
        $this->assertSame('txn_123', $status->transactionId);
        $this->assertNull($status->reason);
        $this->assertNull($status->transactionHash);
        $this->assertNull($status->network);
    }

    public function test_accepts_fully_populated_response(): void
    {
        $status = new CoinPayPaymentStatus(
            'completed',
            '100.00',
            'txn_123',
            null,
            '0xabc123',
            'ETH',
        );

        $this->assertSame('0xabc123', $status->transactionHash);
        $this->assertSame('ETH', $status->network);
    }
}
