<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;

use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Util\MathSafety;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;


/**
 * Manages trust lines
 *
 * https://www.stellar.org/developers/horizon/reference/resources/operation.html#change-trust
 */
class ChangeTrustOp extends Operation
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * @var int
     */
    protected $limit;

    public function __construct(Asset $asset, $limit, AccountId $sourceAccount = null)
    {
        MathSafety::require64Bit();

        parent::__construct(Operation::TYPE_CHANGE_TRUST, $sourceAccount);

        $this->asset = $asset;
        $this->limit = $limit;
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->asset->toXdr();
        $bytes .= XdrEncoder::unsignedInteger64($this->limit);

        return $bytes;
    }

    /**
     * Sets the limit of the trust line to the maximum amount
     */
    public function setMaxLimit()
    {
        $this->limit = PHP_INT_MAX;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param Asset $asset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
    }
}