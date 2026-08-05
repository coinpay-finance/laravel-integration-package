<?php

namespace Coinpay\Finance\Tests\Unit;

use Coinpay\Finance\Exceptions\CoinPayException;
use PHPUnit\Framework\TestCase;

class CoinPayExceptionTest extends TestCase
{
    public function test_exposes_message_code_and_raw_context_data(): void
    {
        $exception = new CoinPayException([
            'message' => 'Something went wrong',
            'is_success' => false,
            'code' => 422,
        ], 422);

        $this->assertSame('Something went wrong', $exception->getMessage());
        $this->assertSame(422, $exception->getCode());
        $this->assertSame(422, $exception->getHttpStatusCode());
        $this->assertFalse($exception->getData()['is_success']);
        $this->assertSame('Something went wrong', $exception->get('message'));
        $this->assertNull($exception->get('missing_key'));
        $this->assertSame('fallback', $exception->get('missing_key', 'fallback'));
    }

    public function test_defaults_message_and_code_when_data_is_empty(): void
    {
        $exception = new CoinPayException([]);

        $this->assertSame('Payment request failed', $exception->getMessage());
        $this->assertSame(400, $exception->getCode());
        $this->assertSame([], $exception->getData());
    }
}
