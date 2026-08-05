<?php

namespace Coinpay\Finance\Tests\Feature;

use Coinpay\Finance\Tests\TestCase;

class WebhookSignatureVerificationTest extends TestCase
{
    protected string $secret = 'test-webhook-secret';

    public function test_valid_hmac_signature_is_accepted(): void
    {
        [$body, $headers] = $this->signedRequest($this->payload());

        $response = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $body);

        $response->assertStatus(200);
        $response->assertJson(['is_success' => true]);
    }

    public function test_invalid_hmac_signature_is_rejected(): void
    {
        [$body, $headers] = $this->signedRequest($this->payload());
        $headers['HTTP_X-Coinpay-Signature'] = 'v1=' . str_repeat('0', 64);

        $response = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $body);

        $response->assertStatus(401);
        $response->assertJson(['is_success' => false]);
    }

    public function test_tampered_body_is_rejected(): void
    {
        [$body, $headers] = $this->signedRequest($this->payload());
        $tamperedBody = str_replace('"completed"', '"failed"', $body);

        $response = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $tamperedBody);

        $response->assertStatus(401);
    }

    public function test_stale_timestamp_is_rejected(): void
    {
        [$body, $headers] = $this->signedRequest($this->payload(), time() - 3600);

        $response = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $body);

        $response->assertStatus(401);
    }

    public function test_duplicate_delivery_id_is_rejected_on_replay(): void
    {
        [$body, $headers] = $this->signedRequest($this->payload());

        $first = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $body);
        $first->assertStatus(200);

        $second = $this->call('POST', route('coinpay.webhook'), [], [], [], $headers, $body);
        $second->assertStatus(401);
    }

    public function test_legacy_secret_header_is_accepted_as_fallback(): void
    {
        $response = $this->call('POST', route('coinpay.webhook'), $this->payload(), [], [], [
            'HTTP_SECRET' => $this->secret,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['is_success' => true]);
    }

    public function test_legacy_secret_mismatch_is_rejected(): void
    {
        $response = $this->call('POST', route('coinpay.webhook'), $this->payload(), [], [], [
            'HTTP_SECRET' => 'wrong-secret',
        ]);

        $response->assertStatus(401);
    }

    public function test_request_without_any_credentials_is_rejected(): void
    {
        $response = $this->call('POST', route('coinpay.webhook'), $this->payload());

        $response->assertStatus(401);
    }

    /**
     * @return array{0: string, 1: array<string, string>}
     */
    protected function signedRequest(array $payload, ?int $timestamp = null): array
    {
        $body = json_encode($payload);
        $timestamp = (string) ($timestamp ?? time());
        $deliveryId = 'delivery-' . bin2hex(random_bytes(8));
        $signature = 'v1=' . hash_hmac('sha256', $timestamp . '.' . $deliveryId . '.' . $body, $this->secret);

        return [$body, [
            'HTTP_X-Coinpay-Signature' => $signature,
            'HTTP_X-Coinpay-Timestamp' => $timestamp,
            'HTTP_X-Coinpay-Delivery' => $deliveryId,
            'CONTENT_TYPE' => 'application/json',
        ]];
    }

    protected function payload(): array
    {
        return [
            'status' => 'completed',
            'reason' => null,
            'transaction_id' => 'txn_123',
            'amount' => 100,
            'transaction_hash' => '0xabc',
        ];
    }
}
