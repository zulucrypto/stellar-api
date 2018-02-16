<?php


namespace ZuluCrypto\StellarSdk\Model;


class PathPayment extends Operation implements AssetTransferInterface
{
    /**
     * @var string
     */
    protected $fromAccountId;

    /**
     * @var string
     */
    protected $toAccountId;

    /**
     * @var AssetAmount
     */
    protected $destinationAsset;

    /**
     * @var AssetAmount
     */
    protected $sourceAsset;

    /**
     * @var AssetAmount
     */
    protected $sourceMax;

    /**
     * @param array $rawData
     * @return PathPayment
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new PathPayment($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_CREATE_ACCOUNT);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->fromAccountId = $rawData['from'];
        $this->toAccountId = $rawData['to'];

        // Destination asset
        $destinationAsset = new AssetAmount($rawData['amount'], $rawData['asset_type']);
        $destinationAsset->setAssetIssuerAccountId($rawData['asset_issuer']);
        $destinationAsset->setAssetCode($rawData['asset_code']);
        $this->destinationAsset = $destinationAsset;

        // Source asset
        $sourceAsset = new AssetAmount($rawData['source_amount'], $rawData['source_asset_type']);
        $sourceAsset->setAssetIssuerAccountId($rawData['source_asset_issuer']);
        $sourceAsset->setAssetCode($rawData['source_asset_code']);
        $this->sourceAsset = $sourceAsset;

        // Max amount is the same as the source asset, but with a different amount
        $sourceAssetMax = new AssetAmount($rawData['source_max'], $rawData['source_asset_type']);
        $sourceAssetMax->setAssetIssuerAccountId($rawData['source_asset_issuer']);
        $sourceAssetMax->setAssetCode($rawData['source_asset_code']);
        $this->sourceMax = $sourceAssetMax;
    }

    public function getAssetTransferType()
    {
        return $this->type;
    }

    public function getAssetAmount()
    {
        return $this->destinationAsset;
    }
    /**
     * @return string
     */
    public function getFromAccountId()
    {
        return $this->fromAccountId;
    }

    /**
     * @param string $fromAccountId
     */
    public function setFromAccountId($fromAccountId)
    {
        $this->fromAccountId = $fromAccountId;
    }

    /**
     * @return string
     */
    public function getToAccountId()
    {
        return $this->toAccountId;
    }

    /**
     * @param string $toAccountId
     */
    public function setToAccountId($toAccountId)
    {
        $this->toAccountId = $toAccountId;
    }

    /**
     * @return AssetAmount
     */
    public function getDestinationAsset()
    {
        return $this->destinationAsset;
    }

    /**
     * @param AssetAmount $destinationAsset
     */
    public function setDestinationAsset($destinationAsset)
    {
        $this->destinationAsset = $destinationAsset;
    }

    /**
     * @return AssetAmount
     */
    public function getSourceAsset()
    {
        return $this->sourceAsset;
    }

    /**
     * @param AssetAmount $sourceAsset
     */
    public function setSourceAsset($sourceAsset)
    {
        $this->sourceAsset = $sourceAsset;
    }

    /**
     * @return AssetAmount
     */
    public function getSourceMax()
    {
        return $this->sourceMax;
    }

    /**
     * @param AssetAmount $sourceMax
     */
    public function setSourceMax($sourceMax)
    {
        $this->sourceMax = $sourceMax;
    }
}