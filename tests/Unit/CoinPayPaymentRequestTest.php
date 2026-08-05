<?php

namespace Coinpay\Finance\Tests\Unit;

use Coinpay\Finance\Services\CoinPay\CoinPayPaymentRequest;
use Coinpay\Finance\Tests\TestCase;

class CoinPayPaymentRequestTest extends TestCase
{
    public function test_to_array_includes_required_fields_and_config_defaults(): void
    {
        $request = new CoinPayPaymentRequest(
            amount: 100.0,
            clientRefId: 'INV-1',
        );

        $data = $request->toArray();

        $this->assertSame(100.0, $data['amount']);
        $this->assertSame('INV-1', $data['client_ref_id']);
        $this->assertSame('https://example.test/callback', $data['redirect_url']);
        $this->assertSame(route('coinpay.webhook'), $data['webhook_callback']);
        $this->assertArrayNotHasKey('payer_identity', $data);
        $this->assertArrayNotHasKey('name', $data);
        $this->assertArrayNotHasKey('description', $data);
        $this->assertArrayNotHasKey('national_code', $data);
    }

    public function test_to_array_includes_optional_fields_when_present(): void
    {
        $request = new CoinPayPaymentRequest(
            amount: 50,
            clientRefId: 'INV-2',
            payerIdentity: 'user@example.com',
            name: 'Jane Doe',
            description: 'Invoice payment',
            nationalCode: '1234567890',
            redirectUrl: 'https://custom.test/back',
        );

        $data = $request->toArray();

        $this->assertSame('user@example.com', $data['payer_identity']);
        $this->assertSame('Jane Doe', $data['name']);
        $this->assertSame('Invoice payment', $data['description']);
        $this->assertSame('1234567890', $data['national_code']);
        $this->assertSame('https://custom.test/back', $data['redirect_url']);
    }
}
