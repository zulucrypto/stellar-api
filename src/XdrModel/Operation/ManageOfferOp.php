<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Model\AssetAmount;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Price;

/**
 * https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L93
 *
 * To update an offer, pass the $offerId
 *
 * To cancel an offer, pass the $offerId and set the $amount to 0
 */
class ManageOfferOp extends Operation
{
    /**
     * @var Asset
     */
    protected $sellingAsset;

    /**
     * @var Asset
     */
    protected $buyingAsset;

    /**
     *
     * @var int
     */
    protected $amount;

    /**
     * @var Price
     */
    protected $price;

    /**
     * @var int
     */
    protected $offerId;

    public function __construct(Asset $sellingAsset, Asset $buyingAsset, $amount, Price $price, $offerId = null, $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_MANAGE_OFFER, $sourceAccount);

        $this->sellingAsset = $sellingAsset;
        $this->buyingAsset = $buyingAsset;
        $this->amount = $amount;
        $this->price = $price;
        $this->offerId = $offerId;
    }

    /**
     * @return string XDR bytes
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->sellingAsset->toXdr();
        $bytes .= $this->buyingAsset->toXdr();
        $bytes .= XdrEncoder::signedInteger64($this->amount * AssetAmount::ASSET_SCALE);
        $bytes .= $this->price->toXdr();
        $bytes .= XdrEncoder::unsignedInteger64($this->offerId);

        return $bytes;
    }

    /**
     * @return Asset
     */
    public function getSellingAsset()
    {
        return $this->sellingAsset;
    }

    /**
     * @param Asset $sellingAsset
     */
    public function setSellingAsset($sellingAsset)
    {
        $this->sellingAsset = $sellingAsset;
    }

    /**
     * @return Asset
     */
    public function getBuyingAsset()
    {
        return $this->buyingAsset;
    }

    /**
     * @param Asset $buyingAsset
     */
    public function setBuyingAsset($buyingAsset)
    {
        $this->buyingAsset = $buyingAsset;
    }

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return Price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param Price $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @param int $offerId
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
    }
}