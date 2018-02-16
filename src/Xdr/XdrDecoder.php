<?php


namespace ZuluCrypto\StellarSdk\Xdr;


use ZuluCrypto\StellarSdk\Util\MathSafety;

class XdrDecoder
{
    /**
     * NOTE: do not access this directly, it's used for caching whether the current
     * platform is big endian.
     *
     * Instead, call self::nativeIsBigEndian()
     *
     * @deprecated use nativeIsBigEndian() instead
     * @var bool
     */
    private static $nativeIsBigEndian;

    /**
     * @param $xdr
     * @return int
     */
    public static function unsignedInteger($xdr)
    {
        // unsigned 32-bit big-endian
        return array_pop(unpack('N', $xdr));
    }

    /**
     * @param $xdr
     * @return int
     */
    public static function signedInteger($xdr)
    {
        // pack() does not support a signed 32-byte int, so work around this with
        // custom encoding
        return (self::nativeIsBigEndian()) ? array_pop(unpack('l', $xdr)) : array_pop(unpack('l', strrev($xdr)));
    }

    /**
     * @param $xdr
     * @return integer
     * @throws \ErrorException
     */
    public static function unsignedInteger64($xdr)
    {
        MathSafety::require64Bit();

        // unsigned 64-bit big-endian
        return array_pop(unpack('J', $xdr));
    }

    /**
     * @param $xdr
     * @return int
     * @throws \ErrorException
     */
    public static function unsignedHyper($xdr)
    {
        return self::unsignedInteger64($xdr);
    }

    /**
     * @param $xdr
     * @return integer
     * @throws \ErrorException
     */
    public static function signedInteger64($xdr)
    {
        MathSafety::require64Bit();

        // pack() does not support a signed 64-byte int, so work around this with
        // custom encoding
        return (self::nativeIsBigEndian()) ? array_pop(unpack('q', $xdr)) : array_pop(unpack('q', strrev($xdr)));
    }

    /**
     * @param $xdr
     * @return int
     * @throws \ErrorException
     */
    public static function signedHyper($xdr)
    {
        return self::signedInteger64($xdr);
    }

    /**
     * @param $xdr
     * @return bool
     */
    public static function boolean($xdr)
    {
        $value = self::unsignedInteger($xdr);
        if ($value !== 1 && $value !== 0) {
            throw new \InvalidArgumentException('Unexpected XDR for a boolean value');
        }

        // Equivalent to 1 or 0 uint32
        return (self::unsignedInteger($xdr)) ? true : false;
    }

    /**
     * @param $xdr
     * @return bool|string
     */
    public static function string($xdr)
    {
        return self::opaqueVariable($xdr);
    }

    /**
     * Reads a fixed opaque value and returns it as a string
     *
     * @param $xdr
     * @param $length
     * @return string
     */
    public static function opaqueFixedString($xdr, $length)
    {
        $bytes = static::opaqueFixed($xdr, $length);

        // remove trailing nulls
        return strval(rtrim($bytes, "\0x00"));
    }

    /**
     * @param $xdr
     * @param $length
     * @return bool|string
     */
    public static function opaqueFixed($xdr, $length)
    {
        return substr($xdr, 0, $length);
    }

    /**
     * @param $xdr
     * @return bool|string
     */
    public static function opaqueVariable($xdr)
    {
        $length = static::unsignedInteger($xdr);

        // first 4 bytes are the length
        return substr($xdr, 4, $length);
    }

    /**
     * @return bool
     */
    private static function nativeIsBigEndian()
    {
        if (null === self::$nativeIsBigEndian) {
            self::$nativeIsBigEndian = pack('L', 1) === pack('N', 1);
        }

        return self::$nativeIsBigEndian;
    }
}