<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class AllowTrustOp extends Operation
{
    /**
     * @var Asset
     */
    protected $asset;

    /**
     * The account that is trusted to hold the asset
     *
     * @var AccountId
     */
    protected $trustor;

    /**
     * @var bool
     */
    protected $isAuthorized;

    public function __construct(Asset $asset, AccountId $trustor = null, $sourceAccountId = null)
    {
        if ($asset->isNative()) throw new \InvalidArgumentException('Trust cannot be added for native assets');

        parent::__construct(Operation::TYPE_ALLOW_TRUST, $sourceAccountId);

        $this->asset = $asset;
        $this->trustor = $trustor;
        // $this->isAuthorized intentionally left null
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    public function toXdr()
    {
        // isAuthorized must be set to a value
        if ($this->isAuthorized === null) throw new \ErrorException('isAuthorized must be set to true or false');

        $bytes = parent::toXdr();

        // Trusted account
        $bytes .= $this->trustor->toXdr();

        // Asset is encoded as a union
        $bytes .= XdrEncoder::unsignedInteger($this->asset->getType());
        if ($this->asset->getType() == Asset::TYPE_ALPHANUM_4) {
            $bytes .= XdrEncoder::opaqueFixed($this->asset->getAssetCode(), 4, true);
        }
        elseif ($this->asset->getType() == Asset::TYPE_ALPHANUM_12) {
            $bytes .= XdrEncoder::opaqueFixed($this->asset->getAssetCode(), 12, true);
        }

        // Is authorized?
        $bytes .= XdrEncoder::boolean($this->isAuthorized);

        return $bytes;
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

    /**
     * @return AccountId
     */
    public function getTrustor()
    {
        return $this->trustor;
    }

    /**
     * @param AccountId $trustor
     */
    public function setTrustor($trustor)
    {
        $this->trustor = $trustor;
    }

    /**
     * @return bool
     */
    public function isAuthorized()
    {
        return $this->isAuthorized;
    }

    /**
     * @param bool $isAuthorized
     */
    public function setIsAuthorized($isAuthorized)
    {
        $this->isAuthorized = $isAuthorized;
    }
}