<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class ChangeTrustResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L539
    const MALFORMED                 = 'change_trust_malformed';
    const NO_ISSUER                 = 'change_trust_no_issuer';
    const INVALID_LIMIT             = 'change_trust_invalid_limit';
    const LOW_RESERVE               = 'change_trust_low_reserve';
    const SELF_NOT_ALLOWED          = 'change_trust_self_not_allowed';

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new ChangeTrustResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::NO_ISSUER,
            '-3' => static::INVALID_LIMIT,
            '-4' => static::LOW_RESERVE,
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