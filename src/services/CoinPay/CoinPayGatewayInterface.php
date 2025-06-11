<?php

namespace Coinpay\Finance\services\CoinPay;

use Exception;

interface CoinPayGatewayInterface
{
    /**
     * Send a payment request to CoinPay API.
     *
     * @param int $amount The amount to be paid (in Dollar - $).
     * @param string $callbackUrl The URL the user will be redirected to after payment.
     * @param string $client_ref_id A unique reference ID for tracking the transaction.
     * @param string|null $payer_identity The identity of the payer (email or phone number).
     * @param string|null $name Full name of the payer.
     * @param string|null $description Description of the payment (e.g. "Payment for order #123").
     * @param string|null $national_code National identification code of the payer.
     *
     * @return CoinPayPaymentResponse The response from the CoinPay API, including a payment URL.
     *
     * @throws Exception If the request fails or the API returns an error.
     */
    public function request(int $amount, string $callbackUrl, string $client_ref_id, string $payer_identity = null, string $name = null, string $description = null , string $national_code = null) : CoinPayPaymentResponse;

    /**
     * Check the status of a CoinPay transaction.
     *
     * Sends a GET request to the CoinPay API to retrieve the status of a payment
     * based on the provided transaction ID. If the request is successful and the
     * required fields are present in the response, it returns a CoinPayPaymentStatus object.
     *
     * @param string $transaction_id The transaction ID to check.
     * @return CoinPayPaymentStatus Returns the status, amount, transaction ID, reason, hash, and network.
     * @throws Exception If the request fails or the response is invalid.
     */
    public function checkStatus(string $transaction_id): CoinPayPaymentStatus;
}