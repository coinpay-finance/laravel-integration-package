<?php

namespace Coinpay\Finance\Services\CoinPay;

use Coinpay\Finance\Exceptions\CoinPayException;
use GuzzleHttp\Client;

/**
 * Class CoinPayGateway
 *
 * This service provides integration with the CoinPay API for
 * creating new cryptocurrency payment requests and checking
 * the status of existing transactions.
 *
 * Responsibilities:
 * - Initialize the HTTP client with authentication headers
 * - Send requests to the CoinPay API endpoints
 * - Handle responses and exceptions
 *
 * @package Coinpay\Finance\Services\CoinPay
 */
class CoinPayGateway implements CoinPayGatewayInterface
{


    /**
     * The base URL of the CoinPay API.
     *
     * @var string
     */
    protected string $baseUrl;

    /**
     * The Guzzle HTTP client instance.
     *
     * @var Client
     */
    protected Client $client;

    /**
     * Create a new CoinPayGateway instance.
     *
     * Initializes the base API URL and configures the Guzzle client
     * with default headers for authentication and JSON handling.
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseUrl = config('coinpay.base_url');

        $this->client = new Client([
            'headers' => [
                'Authorization' => config('coinpay.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /* @inheritdoc */
    public function createPayment(CoinPayPaymentRequest $paymentRequest): CoinPayPaymentResponse
    {
        $response = $this->client->post($this->baseUrl . '/payment', [
            'json' => $paymentRequest->toArray()
        ]);

        $responseBody = json_decode($response->getBody(), true);

        if (
            $response->getStatusCode() == 200
            && is_array($responseBody)
            && !empty($responseBody['status'])
            && !empty($responseBody['url'])
        ) {
            return new CoinPayPaymentResponse(
                $responseBody['url'],
                $responseBody['transaction_id'],
                $responseBody['status']
            );
        }

        throw new CoinPayException([
            'is_success' => false,
            'message' => $responseBody['message'] ?? 'Payment request failed',
            'code' => $response->getStatusCode(),
        ], $response->getStatusCode());
    }

    /* @inheritdoc */
    public function checkStatus(string $transaction_id): CoinPayPaymentStatus
    {

        $response = $this->client->get($this->baseUrl, ['query' => ['transaction_id' => $transaction_id]]);

        $responseBody = json_decode($response->getBody(), true);

        if ($response->getStatusCode() == 200 && is_array($responseBody) && !empty($responseBody['status']) && !empty($responseBody['amount']) && !empty($responseBody['transaction_id'])) {
            return new CoinPayPaymentStatus($responseBody['status'], $responseBody['amount'], $responseBody['transaction_id'], $responseBody['reason'], $responseBody['transaction_hash'], $responseBody['network']);
        }

        throw new CoinPayException($responseBody['message'] ?? 'Check payment status failed', $response->getStatusCode());
    }
}
