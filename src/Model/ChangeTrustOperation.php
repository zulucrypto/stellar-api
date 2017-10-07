<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#change-trust
 */
class ChangeTrustOperation extends Operation
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
        parent::__construct($id, Operation::TYPE_CHANGE_TRUST);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->trusteeAccountId = $rawData['trustee'];
        $this->trustorAccountId = $rawData['trustor'];

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
}