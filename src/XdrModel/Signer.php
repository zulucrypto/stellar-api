<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 *
 * Struct with fields:
 *  key SignerKey
 *  weight uint32
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

    /**
     * @param XdrBuffer $xdr
     * @return Signer
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $signerKey = SignerKey::fromXdr($xdr);
        $weight = $xdr->readUnsignedInteger();

        return new Signer($signerKey, $weight);
    }

    /**
     * @return SignerKey
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param SignerKey $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        if ($weight > 255 || $weight < 0) throw new \InvalidArgumentException('weight must be between 0 and 255');

        $this->weight = $weight;
    }
}