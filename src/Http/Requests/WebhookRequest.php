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

    public function authorize(): bool
    {
        return true;
    }
}