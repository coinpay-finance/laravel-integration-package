<?php
use Illuminate\Support\Facades\Route;
use Coinpay\Finance\Http\Controllers\WebhookController;

Route::post(config('coinpay.webhook_route', '/coinpay/webhook'), [WebhookController::class, 'handle'])->name('coinpay.webhook');