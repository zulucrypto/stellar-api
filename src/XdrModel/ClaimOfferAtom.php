<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

/**
 * Used when returning information about operations that claimed offers
 */
class ClaimOfferAtom
{
    /**
     * @var AccountId
     */
    protected $seller;

    /**
     * @var int (64-bit)
     */
    protected $offerId;

    /**
     * @var Asset
     */
    protected $assetSold;

    /**
     * @var StellarAmount
     */
    protected $amountSold;

    /**
     * @var Asset
     */
    protected $assetBought;

    /**
     * @var StellarAmount
     */
    protected $amountBought;

    /**
     * @param XdrBuffer $xdr
     * @return ClaimOfferAtom
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new ClaimOfferAtom();

        $model->seller = AccountId::fromXdr($xdr);
        $model->offerId = $xdr->readUnsignedInteger64();

        $model->assetSold = Asset::fromXdr($xdr);
        $model->amountSold = new StellarAmount(new BigInteger($xdr->readInteger64()));

        $model->assetBought = Asset::fromXdr($xdr);
        $model->amountBought = new StellarAmount(new BigInteger($xdr->readInteger64()));

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
     * @return int
     */
    public function getOfferId()
    {
        return $this->offerId;
    }

    /**
     * @return Asset
     */
    public function getAssetSold()
    {
        return $this->assetSold;
    }

    /**
     * @return StellarAmount
     */
    public function getAmountSold()
    {
        return $this->amountSold;
    }

    /**
     * @return Asset
     */
    public function getAssetBought()
    {
        return $this->assetBought;
    }

    /**
     * @return StellarAmount
     */
    public function getAmountBought()
    {
        return $this->amountBought;
    }
}