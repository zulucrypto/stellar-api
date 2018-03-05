<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 * Used for adding signers to accounts
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

    /**
     * @param Keypair $keypair
     * @return SignerKey
     */
    public static function fromKeypair(Keypair $keypair)
    {
        $signerKey = new SignerKey(static::TYPE_ED25519);
        $signerKey->key = $keypair->getPublicKeyBytes();

        return $signerKey;
    }

    /**
     * @param $hashBytes
     * @return SignerKey
     */
    public static function fromPreauthorizedHash($hashBytes)
    {
        if (strlen($hashBytes) != 32) {
            throw new \InvalidArgumentException('$hashBytes must be 32 bytes representing the sha256 hash of the transaction');
        }

        $signerKey = new SignerKey(static::TYPE_PRE_AUTH_TX);
        $signerKey->key = $hashBytes;

        return $signerKey;
    }

    /**
     * Adds the value of $x as a signer on the account
     *
     * @param $x
     * @return SignerKey
     */
    public static function fromHashX($x)
    {
        $signerKey = new SignerKey(static::TYPE_HASH_X);
        $signerKey->key = hash('sha256', $x, true);

        return $signerKey;
    }

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

    /**
     * @param XdrBuffer $xdr
     * @return SignerKey
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $type = $xdr->readUnsignedInteger();
        $model = new SignerKey($type);

        $model->key = $xdr->readOpaqueFixed(32);

        return $model;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}