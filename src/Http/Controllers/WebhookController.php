<?php

namespace Coinpay\Finance\Http\Controllers;

use Coinpay\Finance\Http\Requests\WebhookRequest;
use Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class WebhookController
{
    /**
     * WebhookController constructor.
     *
     * @param WebhookServiceInterface $webhookService
     */
    public function __construct(
        protected WebhookServiceInterface $webhookService
    ) {}

    /**
     * Handle incoming webhook requests from CoinPay.
     *
     * By the time this method runs, the request has already passed the
     * VerifyCoinPayWebhookSignature route middleware, so the sender is
     * authenticated. This method only deals with the payload shape and
     * handing off to the configured WebhookServiceInterface.
     *
     * @param WebhookRequest $request
     * @return JsonResponse
     */
    public function handle(WebhookRequest $request): JsonResponse
    {
        // Extract relevant data from the request
        $validated = $request->only(['status', 'reason', 'transaction_id', 'amount', 'transaction_hash']);

        // Process webhook via the service
        $result = $this->webhookService->handleWebhook($validated);

        // If handling failed, return error response
        if (! ($result['is_success'] ?? false)) {
            return Response::json([
                'is_success' => false,
                'message' => $result['message'] ?? null,
                'transaction_id' => $validated['transaction_id'],
            ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Return success response
        return Response::json([
            'is_success' => true,
            'message' => $result['message'] ?? 'successfully',
            'transaction_id' => $validated['transaction_id'],
            'transaction_hash' => $validated['transaction_hash'],
        ]);
    }
}
