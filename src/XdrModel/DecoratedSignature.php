<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

class DecoratedSignature implements XdrEncodableInterface
{
    /**
     * @var string opaque<4>
     */
    private $hint;

    /**
     * @var string opaque<64>
     */
    private $signature;

    public function __construct($hint, $signature)
    {
        $this->hint = $hint;
        $this->signature = $signature;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= XdrEncoder::opaqueFixed($this->hint, 4);
        $bytes .= XdrEncoder::opaqueVariable($this->signature);

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return DecoratedSignature
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $hint = $xdr->readOpaqueFixed(4);
        $signature = $xdr->readOpaqueVariable();

        return new DecoratedSignature($hint, $signature);
    }

    /**
     * @return string
     */
    public function toBase64()
    {
        return base64_encode($this->toXdr());
    }

    /**
     * @return string
     */
    public function getWithoutHintBase64()
    {
        return base64_encode($this->signature);
    }

    /**
     * Returns the raw 64 bytes representing the signature
     *
     * This does not include the hint
     *
     * @return string
     */
    public function getRawSignature()
    {
        return $this->signature;
    }
}