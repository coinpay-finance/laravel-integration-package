<?php

namespace Coinpay\Finance\Services\CoinPay;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class CoinPayGateway implements CoinPayGatewayInterface
{
    protected static $PREFIX = 'https://platform.coinpay.finance/api/v1/coin-pay';

    protected $client;
    public function __construct()
    {
        $this->client = new Client([
            'headers' => [
                'Authorization' => config('coinpay.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function createPayment(CoinPayPaymentRequest $paymentRequest): CoinPayPaymentResponse
    {
        try {
            $response = $this->client->post(self::$PREFIX . '/payment', [
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
                    $responseBody['transaction_id']
                );
            }

            throw new \Exception(
                $responseBody['message'] ?? 'Payment request failed',
                $response->getStatusCode()
            );

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $resp = $e->getResponse();
                $msg = (string) $resp->getBody();
                $code = $resp->getStatusCode();
                throw new \Exception("Guzzle Request failed: {$msg}", $code);
            }

            throw new \Exception("Guzzle Request failed: " . $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function checkStatus(string $transaction_id): CoinPayPaymentStatus
    {
        try {

            $response = $this->client->get(self::$PREFIX , ['query' => ['transaction_id' => $transaction_id]]);

            $responseBody = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200 && is_array($responseBody) && !empty($responseBody['status']) && !empty($responseBody['amount']) && !empty($responseBody['transaction_id'])) {
                return new CoinPayPaymentStatus($responseBody['status'], $responseBody['amount'], $responseBody['transaction_id'], $responseBody['reason'], $responseBody['transaction_hash'], $responseBody['network']);
            }

            throw new \Exception($responseBody['message'] ?? 'Check payment status failed', $response->getStatusCode());

        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $resp = $e->getResponse();
                $msg = (string) $resp->getBody();
                $code = $resp->getStatusCode();
                throw new \Exception("Guzzle Request failed: {$msg}", $code);
            } else {
                throw new \Exception("Guzzle Request failed: " . $e->getMessage());
            }
        }
    }

}
