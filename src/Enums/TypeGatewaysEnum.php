<?php

namespace Coinpay\Finance\Enums;

/**
 * Enum TypeGatewaysEnum
 *
 * This enum represents the types and gateways that we support and develop.
 * Each case corresponds to a specific gateway identifier.
 *
 * @package Coinpay\Finance\Enums
 */
enum TypeGatewaysEnum: string
{
    /**
     * COINPAY gateway.
     *
     * Represents the "coinpay" payment gateway.
     */
    case COINPAY = 'coinpay';

    /**
     * Get all available gateway types.
     *
     * This method returns an array of all defined enum values,
     * useful for validation, selection lists, or API responses.
     *
     * @return string[] An array of gateway type strings.
     */
    public static function getTypes(): array
    {
        return array_column(self::cases(), 'value');
    }
}