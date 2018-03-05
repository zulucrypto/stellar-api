<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\AssetAmount;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
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
     * @var StellarAmount
     */
    protected $amount;

    /**
     * Cost of $sellingAsset in terms of $buyingAsset
     *
     * @var Price
     */
    protected $price;

    /**
     * CreatePassiveOfferOp constructor.
     *
     * @param Asset $sellingAsset
     * @param Asset $buyingAsset
     * @param int|BigInteger $amount int representing lumens or BigInteger representing stroops
     * @param Price $price
     * @param null  $sourceAccount
     */
    public function __construct(Asset $sellingAsset, Asset $buyingAsset, $amount, Price $price, $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_CREATE_PASSIVE_OFFER, $sourceAccount);

        $this->sellingAsset = $sellingAsset;
        $this->buyingAsset = $buyingAsset;
        $this->amount = new StellarAmount($amount);
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
        $bytes .= XdrEncoder::signedBigInteger64($this->amount->getUnscaledBigInteger());
        $bytes .= $this->price->toXdr();

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return ManageOfferOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $sellingAsset = Asset::fromXdr($xdr);
        $buyingAsset = Asset::fromXdr($xdr);
        $amount = StellarAmount::fromXdr($xdr);
        $price = Price::fromXdr($xdr);

        return new CreatePassiveOfferOp($sellingAsset,
            $buyingAsset,
            $amount->getUnscaledBigInteger(),
            $price
        );
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
     * @return StellarAmount
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