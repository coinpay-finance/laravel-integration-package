<?php

namespace Coinpay\Finance\Services\CoinPay;

use Exception;

interface CoinPayGatewayInterface
{
    /**
     * Create a new payment session with the CoinPay API.
     *
     * This method sends a request to the CoinPay platform in order to create
     * a new payment. It uses the provided DTO, converts it to an array, and
     * performs an HTTP POST request. If successful, it returns a response
     * object containing the payment URL and transaction ID.
     *
     * @param  CoinPayPaymentRequest $paymentRequest  Data Transfer Object containing all required
     *                                                and optional parameters for initiating the payment.
     *
     * @return CoinPayPaymentResponse Returns a response object containing the payment URL and transaction ID.
     *
     * @throws \Exception             If the request fails or the API responds with an error message.
     */
    public function createPayment(CoinPayPaymentRequest $paymentRequest): CoinPayPaymentResponse;

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