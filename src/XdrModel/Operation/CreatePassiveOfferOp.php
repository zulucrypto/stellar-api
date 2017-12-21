<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Model\AssetAmount;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Price;

/**
 * https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L111
 *
 * To cancel an offer, set the $amount to 0
 */
class CreatePassiveOfferOp extends Operation
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
     * Cost of $sellingAsset in terms of $buyingAsset
     *
     * @var Price
     */
    protected $price;

    public function __construct(Asset $sellingAsset, Asset $buyingAsset, $amount, Price $price, $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_CREATE_PASSIVE_OFFER, $sourceAccount);

        $this->sellingAsset = $sellingAsset;
        $this->buyingAsset = $buyingAsset;
        $this->amount = $amount;
        $this->price = $price;
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
}