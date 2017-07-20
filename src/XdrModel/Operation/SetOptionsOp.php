<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Signer;

class SetOptionsOp extends Operation
{
    /**
     * @var AccountId
     */
    private $inflationDestinationAccount;

    /**
     * @var integer uint32
     */
    private $clearFlags;

    /**
     * @var integer uint32
     */
    private $setFlags;

    /**
     * @var integer uint32
     */
    private $masterWeight;

    /**
     * @var integer uint32
     */
    private $lowThreshold;

    /**
     * @var integer uint32
     */
    private $mediumThreshold;

    /**
     * @var integer uint32
     */
    private $highThreshold;

    /**
     * Base-32 encoded
     * @var string<32>
     */
    private $homeDomain;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * @return SetOptionsOp
     */
    public function __construct()
    {
        parent::__construct(5);

        return $this;
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        // inflation destination
        $bytes .= XdrEncoder::optional($this->inflationDestinationAccount);

        // clear flags
        $bytes .= XdrEncoder::unsignedInteger($this->clearFlags);

        // set flags
        $bytes .= XdrEncoder::unsignedInteger($this->setFlags);

        // master weight
        $bytes .= XdrEncoder::unsignedInteger($this->masterWeight);

        // low threshold
        $bytes .= XdrEncoder::unsignedInteger($this->lowThreshold);

        // medium threshold
        $bytes .= XdrEncoder::unsignedInteger($this->mediumThreshold);

        // high threshold
        $bytes .= XdrEncoder::unsignedInteger($this->highThreshold);

        // home domain
        $bytes .= XdrEncoder::string($this->homeDomain, 32);

        // Signer
        $bytes .= XdrEncoder::optional($this->signer);

        return $bytes;
    }

    /**
     * todo: accept string or other
     * todo: validate account ID
     * @param $accountId
     * @return SetOptionsOp
     */
    public function setInflationDestination($accountId)
    {
        $this->inflationDestinationAccount = new AccountId($accountId);

        return $this;
    }
}