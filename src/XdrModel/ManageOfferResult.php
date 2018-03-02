<?php


namespace ZuluCrypto\StellarSdk\XdrModel;

use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class ManageOfferResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L457
    const MALFORMED             = 'manage_offer_malformed';
    const SELL_NO_TRUST         = 'manage_offer_sell_no_trust';         // no trustline for what's being sold
    const BUY_NO_TRUST          = 'manage_offer_buy_no_trust';
    const SELL_NOT_AUTHORIZED   = 'manage_offer_sell_not_authorized';
    const BUY_NOT_AUTHORIZED    = 'manage_offer_buy_not_authorized';
    const LINE_FULL             = 'manage_offer_line_full';
    const UNDERFUNDED           = 'manage_offer_underfunded';
    const CROSS_SELF            = 'manage_offer_cross_self';
    const SELL_NO_ISSUER        = 'manage_offer_sell_no_issuer';
    const BUY_NO_ISSUER         = 'manage_offer_buy_no_issuer';
    const NOT_FOUND             = 'manage_offer_not_found';             // offer ID doesn't match an existing offer
    const LOW_RESERVE           = 'manage_offer_low_reserve';           // not enough funds to create a new offer

    // Constants for checking ManageOfferEffect
    const EFFECT_CREATED        = 0;
    const EFFECT_UPDATED        = 1;
    const EFFECT_DELETED        = 2;

    /**
     * @var ClaimOfferAtom[]
     */
    protected $claimedOffers = array();

    /**
     * This will be present if the ManageOfferOp resulted in an offer getting
     * created or updated
     *
     * @var OfferEntry
     */
    protected $offer;

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new ManageOfferResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::SELL_NO_TRUST,
            '-3' => static::BUY_NO_TRUST,
            '-4' => static::SELL_NOT_AUTHORIZED,
            '-5' => static::BUY_NOT_AUTHORIZED,
            '-6' => static::LINE_FULL,
            '-7' => static::UNDERFUNDED,
            '-8' => static::CROSS_SELF,
            '-9' => static::SELL_NO_ISSUER,
            '-10' => static::BUY_NO_ISSUER,
            '-11' => static::NOT_FOUND,
            '-12' => static::LOW_RESERVE,
        ];
        if (!isset($errorCodeMap[$rawErrorCode])) {
            throw new \ErrorException(sprintf('Unknown error code %s', $rawErrorCode));
        }

        // Do not store the "success" error code
        if ($errorCodeMap[$rawErrorCode] !== 'success') {
            $model->errorCode = $errorCodeMap[$rawErrorCode];
            // Return immediately if there was an error since there won't be any additional data
            return $model;
        }

        // Populate claimed offers
        $numOffersClaimed = $xdr->readInteger();
        for ($i=0; $i < $numOffersClaimed; $i++) {
            $model->claimedOffers[] = ClaimOfferAtom::fromXdr($xdr);
        }

        $effect = $xdr->readInteger();
        if ($effect === static::EFFECT_CREATED || $effect === static::EFFECT_UPDATED) {
            $model->offer = OfferEntry::fromXdr($xdr);
        }

        return $model;
    }

    /**
     * @return ClaimOfferAtom[]
     */
    public function getClaimedOffers()
    {
        return $this->claimedOffers;
    }

    /**
     * @param ClaimOfferAtom[] $claimedOffers
     */
    public function setClaimedOffers($claimedOffers)
    {
        $this->claimedOffers = $claimedOffers;
    }

    /**
     * @return OfferEntry
     */
    public function getOffer()
    {
        return $this->offer;
    }

    /**
     * @param OfferEntry $offer
     */
    public function setOffer($offer)
    {
        $this->offer = $offer;
    }
}