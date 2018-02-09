<?php

namespace Inisiatif\Midtrans\Factory;

use Veritrans_Config as MidtransConfig;
use Veritrans_VtDirect as MidtransDirect;

class MidtransFactory
{

    /**
     * @param $key
     * @return MidtransDirect
     */
    public static function development($key)
    {
        return static::factory($key, $sanitized = true, $production = false);
    }

    /**
     * @param string $key
     * @param bool $production
     * @param bool $sanitized
     * @return MidtransDirect
     */
    public static function factory(string $key, $production = true, $sanitized = true)
    {
        MidtransConfig::$serverKey = $key;

        MidtransConfig::$isSanitized = $sanitized;

        MidtransConfig::$isProduction = $production;

        return new MidtransDirect;
    }

    /**
     * @param $key
     * @return MidtransDirect
     */
    public static function production($key)
    {
        return static::factory($key);
    }
}