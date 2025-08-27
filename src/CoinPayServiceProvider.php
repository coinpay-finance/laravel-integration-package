<?php

namespace Coinpay\Finance;

use Coinpay\Finance\Services\CoinPay\CoinPayGateway;
use Coinpay\Finance\Services\CoinPay\CoinPayGatewayInterface;
use Illuminate\Support\ServiceProvider;

class CoinPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(CoinPayGatewayInterface::class, function ($app) {
            return new CoinPayGateway();
        });
        $this->mergeConfigFrom(
            __DIR__.'/config/coinpay.php', 'coinpay'
        );

    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/webhook.php');

        $this->publishes([
            __DIR__.'/config/coinpay.php' => config_path('coinpay.php'),
        ], 'coinpay-config');
    }
}
