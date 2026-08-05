<?php

namespace Coinpay\Finance\Tests;

use Coinpay\Finance\CoinPayServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app): array
    {
        return [CoinPayServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('coinpay.api_key', 'test-api-key');
        $app['config']->set('coinpay.base_url', 'https://example.test/api/v1/coin-pay');
        $app['config']->set('coinpay.webhook_secret', 'test-webhook-secret');
        $app['config']->set('coinpay.webhook_signature_tolerance', 300);
        $app['config']->set('coinpay.redirect_url', 'https://example.test/callback');
        $app['config']->set('cache.default', 'array');
    }
}
