<?php

/**
 * CoinPay Laravel Integration Configuration
 *
 * This configuration file contains settings required to integrate
 * CoinPay payment gateway with your Laravel application.
 *
 * You can publish this file using:
 * php artisan vendor:publish --tag=coinpay-config
 *
 * @package CoinPay\Finance
 */

return [

    /**
     * Your CoinPay API Key.
     *
     * You should set this value in your `.env` file as `COINPAY_API_KEY`.
     *
     * @var string
     */
    'api_key' => env('COINPAY_API_KEY', ''),

    /**
     * Webhook Route.
     *
     * This is the URI path where CoinPay will send asynchronous payment
     * notifications (webhooks). You can customize it in your `.env` file
     * as `COINPAY_WEBHOOK_ROUTE`.
     *
     * Example: '/coinpay/webhook'
     *
     * @var string
     */
    'webhook_route' => env('COINPAY_WEBHOOK_ROUTE', '/coinpay/webhook'),

    /**
     * Redirect URL.
     *
     * This is the URL where the user will be redirected after completing
     * the payment process on the CoinPay gateway.
     *
     * You should set this value in your `.env` file as `COINPAY_REDIRECT_URL`.
     *
     * Example: 'https://your-app.com/payment/callback'
     *
     * @var string
     */
    'redirect_url' => env('COINPAY_REDIRECT_URL', 'https://your-app.com/payment/callback'),

];