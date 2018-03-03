<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class PathPaymentResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L414
    const MALFORMED             = 'path_payment_malformed';
    const UNDERFUNDED           = 'path_payment_underfunded';
    const SRC_NO_TRUST          = 'path_payment_src_no_trust';
    const SRC_NOT_AUTHORIZED    = 'path_payment_src_not_authorized';
    const NO_DESTINATION        = 'path_payment_no_destination';
    const NO_TRUST              = 'path_payment_no_trust';
    const NOT_AUTHORIZED        = 'path_payment_not_authorized';
    const LINE_FULL             = 'path_payment_line_full';
    const NO_ISSUER             = 'path_payment_no_issuer';
    const TOO_FEW_OFFERS        = 'path_payment_too_few_offers';
    const OFFER_CROSS_SELF      = 'path_payment_offer_cross_self';
    const OVER_SENDMAX          = 'path_payment_over_sendmax';

    /**
     * @var ClaimOfferAtom[]
     */
    protected $claimedOffers;

    /**
     * @var AccountId
     */
    protected $destination;

    /**
     * @var Asset
     */
    protected $paidAsset;

    /**
     * @var StellarAmount
     */
    protected $paidAmount;

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new PathPaymentResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::UNDERFUNDED,
            '-3' => static::SRC_NO_TRUST,
            '-4' => static::SRC_NOT_AUTHORIZED,
            '-5' => static::NO_DESTINATION,
            '-6' => static::NO_TRUST,
            '-7' => static::NOT_AUTHORIZED,
            '-8' => static::LINE_FULL,
            '-9' => static::NO_ISSUER,
            '-10' => static::TOO_FEW_OFFERS,
            '-11' => static::OFFER_CROSS_SELF,
            '-12' => static::OVER_SENDMAX,
        ];
        if (!isset($errorCodeMap[$rawErrorCode])) {
            throw new \ErrorException(sprintf('Unknown error code %s', $rawErrorCode));
        }

        // Do not store the "success" error code
        if ($errorCodeMap[$rawErrorCode] !== 'success') {
            $model->errorCode = $errorCodeMap[$rawErrorCode];
        }

        // special case: a "no issuer" error code means we need to parse a different
        // value from the XDR
        if ($errorCodeMap[$rawErrorCode] == self::NO_ISSUER) {
            $model->paidAsset = Asset::fromXdr($xdr);
        }
        // Normal XDR parsing https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L441
        else {
            $numOffersClaimed = $xdr->readUnsignedInteger();
            for ($i=0; $i < $numOffersClaimed; $i++) {
                $model->claimedOffers[] = ClaimOfferAtom::fromXdr($xdr);
            }

            // These fields are combined into a SimplePaymentResult in the XDR spec,
            // but it only appears to be used in one place so I'm leaving them as
            // separate fields for now
            $model->destination = AccountId::fromXdr($xdr);
            $model->paidAsset = Asset::fromXdr($xdr);
            $model->paidAmount = new StellarAmount(new BigInteger($xdr->readInteger64()));
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
     * @return AccountId
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param AccountId $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return Asset
     */
    public function getPaidAsset()
    {
        return $this->paidAsset;
    }

    /**
     * @param Asset $paidAsset
     */
    public function setPaidAsset($paidAsset)
    {
        $this->paidAsset = $paidAsset;
    }

    /**
     * @return StellarAmount
     */
    public function getPaidAmount()
    {
        return $this->paidAmount;
    }

    /**
     * @param StellarAmount $paidAmount
     */
    public function setPaidAmount($paidAmount)
    {
        $this->paidAmount = $paidAmount;
    }
}