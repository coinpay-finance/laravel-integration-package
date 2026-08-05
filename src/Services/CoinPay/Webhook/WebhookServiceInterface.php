<?php

namespace Coinpay\Finance\Services\CoinPay\Webhook;

interface WebhookServiceInterface
{
    /**
     * Handle a verified CoinPay webhook payload.
     *
     * By the time this is called, the request has already passed
     * VerifyCoinPayWebhookSignature, so the caller only needs to process
     * the payload (e.g., logging, saving to database, dispatching an
     * event) and report back whether that processing succeeded.
     *
     * This package only integrates with CoinPay, so the payload is not
     * tagged with a gateway name — implementations that need to share
     * logic across multiple payment providers should distinguish
     * gateways at a higher level (e.g. separate bindings/routes).
     *
     * @param array{
     *     status: string,
     *     reason: ?string,
     *     transaction_id: string,
     *     amount: numeric-string|float,
     *     transaction_hash: string
     * } $payload The webhook payload data sent by CoinPay.
     *
     * @return array{
     *     is_success: bool,
     *     message?: string
     * }
     */
    public function handleWebhook(array $payload): array;
}