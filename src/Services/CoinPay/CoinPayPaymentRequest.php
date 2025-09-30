<?php

namespace Coinpay\Finance\Services\CoinPay;

/**
 * Data Transfer Object (DTO) for creating a CoinPay payment request.
 *
 * This class encapsulates all the required and optional parameters
 * needed to initiate a payment through the CoinPay gateway.
 */
class CoinPayPaymentRequest
{
    /**
     * @param float $amount Amount to be paid (in smallest currency unit, e.g. cents).
     * @param string $clientRefId Unique reference ID from the client system (order/invoice ID).
     * @param string|null $payerIdentity Identifier of the payer (e.g. email, phone number).
     * @param string|null $name Payer's name.
     * @param string|null $description Payment description (e.g. invoice description).
     * @param string|null $nationalCode Payer's national code (if required).
     * @param string|null $redirectUrl URL where the user will be redirected after payment.
     * @param string|null $webhookCallback URL to receive asynchronous payment status updates (webhook).
     */
    public function __construct(
        public float     $amount,
        public string  $clientRefId,
        public ?string $payerIdentity = null,
        public ?string $name = null,
        public ?string $description = null,
        public ?string $nationalCode = null,
        public ?string  $redirectUrl = null,
        public ?string $webhookCallback = null,
    ) {
        $this->webhookCallback = $this->webhookCallback ?? route('coinpay.webhook');
        $this->redirectUrl = $redirectUrl ?? config('coinpay.redirect_url');
    }

    /**
     * Convert the payment request object into an array
     * suitable for sending to the CoinPay API.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'amount' => $this->amount,
            'redirect_url' => $this->redirectUrl,
            'webhook_callback' => $this->webhookCallback,
            'client_ref_id' => $this->clientRefId,
        ];

        if ($this->payerIdentity) {
            $data['payer_identity'] = $this->payerIdentity;
        }
        if ($this->name) {
            $data['name'] = $this->name;
        }
        if ($this->description) {
            $data['description'] = $this->description;
        }
        if ($this->nationalCode) {
            $data['national_code'] = $this->nationalCode;
        }

        return $data;
    }
}