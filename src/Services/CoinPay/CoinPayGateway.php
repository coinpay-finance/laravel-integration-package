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
     * with default headers for authentication, JSON handling, and
     * connect/request timeouts (an HTTP call that never gets a response
     * would otherwise hang the merchant's own request indefinitely).
     *
     * An existing Client can optionally be injected (e.g. for testing
     * with a Guzzle MockHandler); when omitted, one is built from config.
     *
     * @param Client|null $client
     * @return void
     */
    public function __construct(?Client $client = null)
    {
        $this->baseUrl = config('coinpay.base_url');

        $this->client = $client ?? new Client([
            'headers' => [
                'Authorization' => config('coinpay.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'connect_timeout' => (float) config('coinpay.connect_timeout', 5),
            'timeout' => (float) config('coinpay.timeout', 15),
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
            'message' => (is_array($responseBody) ? $responseBody['message'] ?? null : null) ?? 'Payment request failed',
            'code' => $response->getStatusCode(),
        ], $response->getStatusCode());
    }

    /* @inheritdoc */
    public function checkStatus(string $transaction_id): CoinPayPaymentStatus
    {
        // NOTE: unlike every other CoinPay endpoint, this hits the bare
        // base URL with no `/payment` (or any other) path segment - e.g.
        // GET https://platform.coinpay.finance/api/v1/coin-pay?transaction_id=...
        // That's unusual for a REST API, but it's what CoinPay's platform
        // actually expects today (verified against this package's prior
        // behavior and the other first-party SDKs). Don't "fix" this to a
        // more conventional path without confirming with the CoinPay team
        // / official API docs first.
        $response = $this->client->get($this->baseUrl, ['query' => ['transaction_id' => $transaction_id]]);

        $responseBody = json_decode($response->getBody(), true);

        if (
            $response->getStatusCode() == 200
            && is_array($responseBody)
            && !empty($responseBody['status'])
            && !empty($responseBody['amount'])
            && !empty($responseBody['transaction_id'])
        ) {
            return new CoinPayPaymentStatus(
                $responseBody['status'],
                $responseBody['amount'],
                $responseBody['transaction_id'],
                $responseBody['reason'] ?? null,
                $responseBody['transaction_hash'] ?? null,
                $responseBody['network'] ?? null,
            );
        }

        throw new CoinPayException([
            'is_success' => false,
            'message' => (is_array($responseBody) ? $responseBody['message'] ?? null : null) ?? 'Check payment status failed',
            'code' => $response->getStatusCode(),
        ], $response->getStatusCode());
    }
}
