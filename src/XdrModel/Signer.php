<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 *
 * Struct with fields:
 *  key SignerKey
 *  weight uint32
 *
 * todo: implement the rest of the types
 */
class Signer implements XdrEncodableInterface
{
    /**
     * @var SignerKey
     */
    private $key;

    /**
     * @var int uint32
     */
    private $weight;

    public function __construct(SignerKey $key, $weight = 0)
    {
        $this->key = $key;
        $this->weight = $weight;
    }

    public function toXdr()
    {
        $bytes = '';

        // key
        $bytes .= $this->key->toXdr();

        // weight
        $bytes .= XdrEncoder::unsignedInteger($this->weight);

        return $bytes;
    }
}