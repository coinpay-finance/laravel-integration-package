<?php
use Illuminate\Support\Facades\Route;
use Coinpay\Finance\Http\Controllers\WebhookController;

Route::post('/coinpay/webhook', [WebhookController::class, 'handle'])->name('coinpay.webhook');