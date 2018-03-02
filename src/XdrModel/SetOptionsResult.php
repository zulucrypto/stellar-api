<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class SetOptionsResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L513
    const LOW_RESERVE               = 'set_options_low_reserve';
    const TOO_MANY_SIGNERS          = 'set_options_too_many_signers';
    const BAD_FLAGS                 = 'set_options_bad_flags';
    const INVALID_INFLATION         = 'set_options_invalid_inflation';  // inflation account does not exist
    const CANT_CHANGE               = 'set_options_cant_change';        // option can no longer be changed
    const UNKNOWN_FLAG              = 'set_options_unknown_flag';
    const THRESHOLD_OUT_OF_RANGE    = 'set_options_threshold_out_of_range'; // bad value for weight or threshold
    const BAD_SIGNER                = 'set_options_bad_signer';         // signer cannot be master key
    const INVALID_HOME_DOMAIN       = 'set_options_invalid_home_domain';// malformed home domain

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new SetOptionsResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::LOW_RESERVE,
            '-2' => static::TOO_MANY_SIGNERS,
            '-3' => static::BAD_FLAGS,
            '-4' => static::INVALID_INFLATION,
            '-5' => static::CANT_CHANGE,
            '-6' => static::UNKNOWN_FLAG,
            '-7' => static::THRESHOLD_OUT_OF_RANGE,
            '-8' => static::BAD_SIGNER,
            '-9' => static::INVALID_HOME_DOMAIN,
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