<?php

namespace ZuluCrypto\StellarSdk\Util;


class MathSafety
{
    /**
     * Throws an exception if integers cannot support 64-bit operations
     *
     * @throws \ErrorException
     */
    public static function require64Bit()
    {
        if (PHP_INT_SIZE < 8) throw new \ErrorException('A 64-bit operating system is required');
    }
}