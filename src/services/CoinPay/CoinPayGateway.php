<?php

namespace Coinpay\Finance\services\CoinPay;

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

    public function request(int $amount, string $callbackUrl, string $client_ref_id, string $payer_identity = null , string $name = null , string $description = null , string $national_code = null): CoinPayPaymentResponse
    {
        try {
            $data = [
                'amount' => $amount,
                'redirect_url' => $callbackUrl,
                'client_ref_id' => $client_ref_id,
            ];
            !empty($payer_identity) && $data['payer_identity'] = $payer_identity;
            !empty($name) && $data['name'] = $name;
            !empty($description) && $data['description'] = $description;
            !empty($national_code) && $data['national_code'] = $national_code;

            $response = $this->client->post(self::$PREFIX . '/payment', ['json' => $data]);

            $responseBody = json_decode($response->getBody(), true);

            if ($response->getStatusCode() == 200 && is_array($responseBody) && !empty($responseBody['status']) && !empty($responseBody['url'])) {
                return new CoinPayPaymentResponse($responseBody['url'], $responseBody['transaction_id'] ?? 0);
            }

            throw new \Exception($responseBody['message'] ?? 'Payment request failed', $response->getStatusCode());

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
