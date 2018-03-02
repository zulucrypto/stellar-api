<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class OfferEntry
{
    const FLAG_IS_PASSIVE   = 1;

    /**
     * @var AccountId
     */
    protected $seller;

    /**
     * @var BigInteger
     */
    protected $offerId;

    /**
     * @var Asset
     */
    protected $sellingAsset;

    /**
     * @var Asset
     */
    protected $buyingAsset;

    /**
     * @var StellarAmount
     */
    protected $sellingAmount;

    /**
     * Price of sellingAsset in terms of buyingAsset
     * This price is after fees
     *
     * @var Price
     */
    protected $price;

    /**
     * If true, this is a passive offer
     *
     * @var bool
     */
    protected $isPassive = false;

    /**
     * @param XdrBuffer $xdr
     * @return OfferEntry
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new OfferEntry();

        $model->seller = AccountId::fromXdr($xdr);
        $model->offerId = $xdr->readUnsignedInteger64();

        $model->sellingAsset = Asset::fromXdr($xdr);
        $model->buyingAsset = Asset::fromXdr($xdr);
        $model->sellingAmount = new StellarAmount(new BigInteger($xdr->readInteger64()));
        $model->price = Price::fromXdr($xdr);

        $flags = $xdr->readUnsignedInteger();

        if ($flags & OfferEntry::FLAG_IS_PASSIVE) {
            $model->isPassive = true;
        }

        return $model;
    }

    /**
     * @return AccountId
     */
    public function getSeller()
    {
        return $this->seller;
    }

    /**
     * @param AccountId $seller
     */
    public function setSeller($seller)
    {
        $this->seller = $seller;
    }

    /**
     * @return BigInteger
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @param BigInteger $offerId
     */
    public function setOfferId($offerId)
    {
        $this->offerId = $offerId;
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
    public function getSellingAmount()
    {
        return $this->sellingAmount;
    }

    /**
     * @param StellarAmount $sellingAmount
     */
    public function setSellingAmount($sellingAmount)
    {
        $this->sellingAmount = $sellingAmount;
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
     * @return bool
     */
    public function isPassive()
    {
        return $this->isPassive;
    }

    /**
     * @param bool $isPassive
     */
    public function setIsPassive($isPassive)
    {
        $this->isPassive = $isPassive;
    }
}