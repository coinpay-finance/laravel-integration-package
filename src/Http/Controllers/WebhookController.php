<?php

namespace Coinpay\Finance\Http\Controllers;

use Coinpay\Finance\Enums\TypeGatewaysEnum;
use Coinpay\Finance\Http\Requests\WebhookRequest;
use Coinpay\Finance\Services\CoinPay\Webhook\WebhookService;
use Coinpay\Finance\Services\CoinPay\Webhook\WebhookServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class WebhookController
{

    /**
     * WebhookController constructor.
     *
     * @param WebhookService $webhookService
     */
    public function __construct(
        protected WebhookServiceInterface $webhookService
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
        $validate = $request->only(['status', 'reason', 'transaction_id', 'amount', 'transaction_hash']);

        // Process webhook via the service
        $result = $this->webhookService->handleWebhook(TypeGatewaysEnum::COINPAY->value, $validate);

        // If handling failed, return error response
        if (! $result['is_success']) {
            return Response::json([
                'is_success' => false,
                'message' => $result['message'] ?? null,
                'transaction_id' => $validate['transaction_id'],
            ], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Return success response
        return Response::json([
            'is_success' => true,
            'message' => 'successfully',
            'transaction_hash' => $validate['transaction_hash']
        ]);
    }
}