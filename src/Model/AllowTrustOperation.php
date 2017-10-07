<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#allow-trust
 */
class AllowTrustOperation extends Operation
{
    /**
     * The asset being trusted. The balance field represents the amount of the
     * asset that is trusted.
     *
     * @var AssetAmount
     */
    protected $asset;

    /**
     * The account being trusted to issue the asset
     *
     * @var string
     */
    protected $trusteeAccountId;

    /**
     * The account ID granting the trust
     *
     * @var string
     */
    protected $trustorAccountId;

    /**
     * True when allowing trust, false when revoking
     *
     * @var bool
     */
    protected $isAuthorized;

    /**
     * @param array $rawData
     * @return ChangeTrustOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new ChangeTrustOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_ALLOW_TRUST);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->trusteeAccountId = $rawData['trustee'];
        $this->trustorAccountId = $rawData['trustor'];
        $this->isAuthorized = $rawData['authorize'];

        $this->asset = new AssetAmount($rawData['limit'], $rawData['asset_type']);
        $this->asset->setAssetIssuerAccountId($rawData['asset_issuer']);
        $this->asset->setAssetCode($rawData['asset_code']);
    }

    /**
     * @return AssetAmount
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param AssetAmount $asset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
    }

    /**
     * @return string
     */
    public function getTrusteeAccountId()
    {
        return $this->trusteeAccountId;
    }

    /**
     * @param string $trusteeAccountId
     */
    public function setTrusteeAccountId($trusteeAccountId)
    {
        $this->trusteeAccountId = $trusteeAccountId;
    }

    /**
     * @return string
     */
    public function getTrustorAccountId()
    {
        return $this->trustorAccountId;
    }

    /**
     * @param string $trustorAccountId
     */
    public function setTrustorAccountId($trustorAccountId)
    {
        $this->trustorAccountId = $trustorAccountId;
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