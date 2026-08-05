<?php

namespace Coinpay\Finance\Exceptions;

use Exception;

class CoinPayException extends Exception
{
    protected array $data;

    public function __construct(array $data, $code = 400)
    {
        parent::__construct($data['message'] ?? 'Payment request failed', $code);
        $this->data = $data;
    }

    /**
     * The full context array the exception was constructed with — e.g.
     * the raw CoinPay API response body (message/is_success/code/etc.),
     * so a catching merchant can inspect exactly what went wrong.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Convenience accessor for a single key from the context data.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * The HTTP status code returned by the CoinPay API, if known.
     * Falls back to the exception's own code.
     */
    public function getHttpStatusCode(): ?int
    {
        $code = $this->data['code'] ?? $this->getCode();

        return is_numeric($code) ? (int) $code : null;
    }
}
