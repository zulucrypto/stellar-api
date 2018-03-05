<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
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
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return AllowTrustOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $trustedAccount = AccountId::fromXdr($xdr);

        // Needs to be manually decoded since issuer is not present in the XDR
        $assetType = $xdr->readUnsignedInteger();
        $assetCode = null;
        if ($assetType === Asset::TYPE_ALPHANUM_4) {
            $assetCode = $xdr->readOpaqueFixedString(4);
        }
        if ($assetType === Asset::TYPE_ALPHANUM_12) {
            $assetCode = $xdr->readOpaqueFixedString(12);
        }

        $asset = new Asset($assetType);
        $asset->setAssetCode($assetCode);

        $model = new AllowTrustOp($asset, $trustedAccount);
        $model->setIsAuthorized($xdr->readBoolean());

        return $model;
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