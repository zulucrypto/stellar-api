<?php


namespace ZuluCrypto\StellarSdk\Xdr\Iface;


interface XdrEncodableInterface
{
    /**
     * Returns the binary representation of the object in XDR format
     *
     * @return string
     */
    public function toXdr();
}