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
     */
    'api_key' => env('COINPAY_API_KEY', ''),

    /**
     * CoinPay Base URL.
     *
     * This is the base API endpoint for CoinPay requests.
     * You can override it in your `.env` file as `COINPAY_BASE_URL`.
     *
     * Example (production): 'https://platform.coinpay.finance/api/v1/coin-pay'
     * Example (staging): 'https://staging.coinpay.finance/api/v1/coin-pay'
     */
    'base_url' => env('COINPAY_BASE_URL', 'https://platform.coinpay.finance/api/v1/coin-pay'),


    /**
     * Webhook Route.
     *
     * This is the URI path where CoinPay will send asynchronous payment
     * notifications (webhooks). You can customize it in your `.env` file
     * as `COINPAY_WEBHOOK_ROUTE`.
     *
     * Example: '/coinpay/webhook'
     */
    'webhook_route' => env('COINPAY_WEBHOOK_ROUTE', '/coinpay/webhook'),

    /**
     * Webhook Service.
     *
     * This class is responsible for handling webhook callbacks from CoinPay.
     * By default, it uses the base WebhookService provided by the package.
     *
     * You can override this by binding your own implementation or by changing
     * this config value to point to a custom class (e.g., in your Payments module).
     *
     * Example: \Modules\Payments\Services\PaymentWebhookService::class
     *
     */
    'webhook_service' => \Coinpay\Finance\Services\CoinPay\Webhook\WebhookService::class,

    /**
     * Webhook Secret.
     *
     * A secret key used to verify the authenticity of incoming webhook requests.
     * You should set this value in your `.env` file as `COINPAY_WEBHOOK_SECRET`.
     * CoinPay will include this secret in the request headers for verification.
     */
    'webhook_secret' => env('COINPAY_WEBHOOK_SECRET', ''),

    /**
     * Redirect URL.
     *
     * This is the URL where the user will be redirected after completing
     * the payment process on the CoinPay gateway.
     *
     * You should set this value in your `.env` file as `COINPAY_REDIRECT_URL`.
     *
     * Example: 'https://your-app.com/payment/callback'
     */
    'redirect_url' => env('COINPAY_REDIRECT_URL', 'https://your-app.com/payment/callback'),

];