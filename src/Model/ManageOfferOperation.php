<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#manage-offer
 *
 * todo: implementation details of this class will probably change
 */
class ManageOfferOperation extends Operation
{
    /**
     *
     * @var int
     */
    protected $offerId;

    /**
     * @var AssetAmount
     */
    protected $buyingAsset;

    /**
     * @var AssetAmount
     */
    protected $sellingAsset;

    /**
     * Price to buy a buying_asset
     *
     * @var string
     */
    protected $price;

    /**
     * n: price numerator, d: price denominator
     *
     * @var array
     */
    protected $priceR;

    /**
     * @param array $rawData
     * @return ManageOfferOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new ManageOfferOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type = Operation::TYPE_MANAGE_OFFER)
    {
        parent::__construct($id, $type);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->offerId = $rawData['offer_id'];
        $this->price = $rawData['price'];
        $this->priceR = $rawData['price_r'];

        // todo: verify that amount is shared between buying and selling?
        if (isset($rawData['buying_asset_code'])) {
            $this->buyingAsset = new AssetAmount($rawData['amount'], $rawData['buying_asset_code']);
            $this->buyingAsset->setAssetIssuerAccountId($rawData['buying_asset_issuer']);
            $this->buyingAsset->setAssetType($rawData['buying_asset_type']);
        }
        else if (isset($rawData['selling_asset_code'])) {
            $this->sellingAsset = new AssetAmount($rawData['amount'], $rawData['selling_asset_code']);
            $this->sellingAsset->setAssetIssuerAccountId($rawData['selling_asset_issuer']);
            $this->sellingAsset->setAssetType($rawData['selling_asset_type']);
        }
    }
}