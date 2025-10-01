<?php

namespace Coinpay\Finance\Services\CoinPay\Webhook;

class WebhookService implements WebhookServiceInterface
{
    /**
     * Handle a payment gateway webhook.
     *
     * This method receives the webhook payload from the specified gateway,
     * processes it (e.g., logging, saving to database, or dispatching an event),
     * and returns a standard response.
     *
     * @param array $payload The webhook payload data sent by the gateway.
     *
     * @return array{
     *     is_success: bool,
     *     message: string
     * }
     */
    public function handleWebhook(array $payload): array
    {

        // Currently, just return a success response
        return [
            'is_success' => true,
            'message' => 'Webhook handled successfully',
        ];
    }
}