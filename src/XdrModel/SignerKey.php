<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 *
 * Union with arms:
 *  ed25519: uint256
 *  preAuthTx: uint256
 *  hashX: uint256
 *
 * todo: implement the rest of the types
 */
class SignerKey implements XdrEncodableInterface
{
    const TYPE_ED25519 = 0;
    const TYPE_PRE_AUTH_TX = 1;
    const TYPE_HASH_X = 2;

    /**
     * See the TYPE constants
     *
     * @var int
     */
    private $type;

    /**
     * Binary representation of the key
     *
     * @var string
     */
    private $key;

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function toXdr()
    {
        $bytes = '';

        // Type
        $bytes .= XdrEncoder::unsignedInteger($this->type);

        // Key
        $bytes .= XdrEncoder::unsignedInteger256($this->key);

        return $bytes;
    }
}