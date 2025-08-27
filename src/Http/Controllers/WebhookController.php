<?php

namespace Coinpay\Finance\Http\Controllers;

use Coinpay\Finance\Http\Requests\WebhookRequest;
use Coinpay\Finance\Services\WebhookService;
use Illuminate\Http\JsonResponse;

class WebhookController
{
    /**
     * WebhookController constructor.
     *
     * @param WebhookService $webhookService
     */
    public function __construct(
        protected WebhookService $webhookService
    ) {}

    /**
     * Handle incoming webhook requests from Coinpay.
     *
     * @param WebhookRequest $request
     * @return JsonResponse
     */
    public function handle(WebhookRequest $request): JsonResponse
    {
        // Extract relevant data from the request
        $validate = $request->only([
            'status',
            'reason',
            'transaction_id',
            'amount',
            'transaction_hash'
        ]);

        // Process webhook via the service
        $result = $this->webhookService->handleWebhook('coinpay', $validate);

        // If handling failed, return error response
        if (! $result['is_success']) {
            return response()->json([
                'is_success' => false,
                'message' => $result['message'] ?? null,
                'transaction_id' => $validate['transaction_id'],
            ], 422);
        }

        // Return success response
        return response()->json([
            'is_success' => true,
            'message' => 'successfully',
            'transaction_hash' => $validate['transaction_hash']
        ]);
    }
}