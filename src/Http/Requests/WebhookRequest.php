<?php

namespace Coinpay\Finance\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebhookRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', 'in:completed,failed,pending'],
            'reason' => ['nullable', 'string'],
            'transaction_id' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_hash' => ['required', 'string'],
        ];
    }

    /**
     * Authentication for this request is handled by the
     * VerifyCoinPayWebhookSignature route middleware, which runs before
     * this FormRequest is resolved and has access to the raw request body
     * needed for HMAC signature verification (validation would otherwise
     * force JSON decoding first, and decode/re-encode can change the
     * bytes the signature was computed over). By the time this method
     * runs, the request has already been verified, so it's safe to
     * always authorize here.
     *
     * @see \Coinpay\Finance\Http\Middleware\VerifyCoinPayWebhookSignature
     */
    public function authorize(): bool
    {
        return true;
    }
}
