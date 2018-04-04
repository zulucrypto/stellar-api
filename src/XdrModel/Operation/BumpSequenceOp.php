<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

class BumpSequenceOp extends Operation
{

    /** @var BigInteger */
    protected $bumpTo;

    public function __construct(BigInteger $bumpTo, $sourceAccountId = null)
    {
        parent::__construct(Operation::TYPE_BUMP_SEQUENCE, $sourceAccountId);

        $this->bumpTo = $bumpTo;
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= XdrEncoder::unsignedBigInteger64($this->bumpTo);

        return $bytes;
    }

    /**
     * NOTE: This only parses the XDR that's specific to this operation and cannot
     * load a full Operation
     *
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return BumpSequenceOp
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        return new BumpSequenceOp($xdr->readBigInteger());
    }

    /**
     * @return BigInteger
     */
    public function getBumpTo()
    {
        return $this->bumpTo;
    }

    /**
     * @param BigInteger $bumpTo
     */
    public function setBumpTo($bumpTo)
    {
        $this->bumpTo = $bumpTo;
    }
}