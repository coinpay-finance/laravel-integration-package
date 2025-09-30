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
}