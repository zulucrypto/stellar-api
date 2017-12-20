<?php


namespace ZuluCrypto\StellarSdk\Util;


class Json
{
    /**
     * @param $jsonString
     * @return mixed
     */
    public static function mustDecode($jsonString)
    {
        $decoded = json_decode($jsonString, true);

        if ($decoded === null && json_last_error()) {
            throw new \InvalidArgumentException(sprintf("JSON decoding error: %s", json_last_error_msg()));
        }

        return $decoded;
    }
}