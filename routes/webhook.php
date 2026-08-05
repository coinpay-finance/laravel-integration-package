<?php

use Coinpay\Finance\Http\Controllers\WebhookController;
use Coinpay\Finance\Http\Middleware\VerifyCoinPayWebhookSignature;
use Illuminate\Support\Facades\Route;

Route::post(config('coinpay.webhook_route', '/coinpay/webhook'), [WebhookController::class, 'handle'])
    ->middleware(VerifyCoinPayWebhookSignature::class)
    ->name(config('coinpay.webhook_route_name', 'coinpay.webhook'));
