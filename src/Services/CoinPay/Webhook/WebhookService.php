<?php

namespace Coinpay\Finance\Services\CoinPay\Webhook;

class WebhookService implements WebhookServiceInterface
{
    /**
     * Default WebhookServiceInterface implementation.
     *
     * Signature authenticity is already guaranteed by the time this runs
     * (see VerifyCoinPayWebhookSignature). This base implementation is
     * intentionally a no-op pass-through — it doesn't know how a given
     * application wants to persist/react to a payment update, so it just
     * acknowledges the payload. Applications that need to actually store
     * the transaction, dispatch events, etc. should bind their own
     * implementation via the `coinpay.webhook_service` config value.
     *
     * @inheritDoc
     */
    public function handleWebhook(array $payload): array
    {
        return [
            'is_success' => true,
            'message' => 'Webhook handled successfully',
        ];
    }
}