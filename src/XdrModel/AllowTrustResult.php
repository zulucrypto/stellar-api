<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class AllowTrustResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L562
    const MALFORMED                 = 'allow_trust_malformed';
    const NO_TRUST_LINE             = 'allow_trust_no_trust_line';
    const TRUST_NOT_REQUIRED        = 'allow_trust_trust_not_required';
    const CANT_REVOKE               = 'allow_trust_trust_cant_revoke';
    const SELF_NOT_ALLOWED          = 'allow_trust_trust_self_not_allowed';

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new AllowTrustResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::NO_TRUST_LINE,
            '-3' => static::TRUST_NOT_REQUIRED,
            '-4' => static::CANT_REVOKE,
            '-5' => static::SELF_NOT_ALLOWED,
        ];
        if (!isset($errorCodeMap[$rawErrorCode])) {
            throw new \ErrorException(sprintf('Unknown error code %s', $rawErrorCode));
        }

        // Do not store the "success" error code
        if ($errorCodeMap[$rawErrorCode] !== 'success') {
            $model->errorCode = $errorCodeMap[$rawErrorCode];
        }

        return $model;
    }
}