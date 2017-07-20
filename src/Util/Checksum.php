<?php


namespace ZuluCrypto\StellarSdk\Util;


/**
 * Utility methods for calculating checksums
 */
class Checksum
{
    /**
     * @param $binaryString
     * @return string CRC-16 checksum of $binaryString as a 2-byte little-endian
     */
    public static function generate($binaryString)
    {
        return pack('v', self::crc16($binaryString));
    }

    /**
     * Returns true if $expected matches the checksum of $binaryStringToChecksum
     *
     * @param $expected
     * @param $binaryStringToChecksum
     * @return bool
     */
    public static function verify($expected, $binaryStringToChecksum)
    {
        return self::generate($binaryStringToChecksum) === $expected;
    }

    /**
     * Returns the crc16 checksum of $binaryString
     *
     * Ported from Java implementation at: http://introcs.cs.princeton.edu/java/61data/CRC16CCITT.java.html
     *
     * Initial value changed to 0x0000 to match Stellar configuration.
     *
     * @param $binaryString
     * @return int (4-byte checksum)
     */
    protected static function crc16($binaryString)
    {
        $crc = 0x0000;
        $polynomial = 0x1021;

        foreach (str_split($binaryString) as $byte) {
            $byte = ord($byte);

            for ($i = 0; $i < 8; $i++) {
                $bit = (($byte >> (7 - $i) & 1) == 1);
                $c15 = (($crc >> 15 & 1) == 1);
                $crc <<= 1;
                if ($c15 ^ $bit) $crc ^= $polynomial;
            }
        }

        return $crc & 0xffff;
    }
}