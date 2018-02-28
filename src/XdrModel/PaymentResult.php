<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class PaymentResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L387
    const MALFORMED             = 'payment_malformed';
    const UNDERFUNDED           = 'payment_underfunded';
    const SRC_NO_TRUST          = 'payment_src_no_trust';
    const SRC_NOT_AUTHORIZED    = 'payment_src_not_authorized';
    const NO_DESTINATION        = 'payment_no_destination';
    const NO_TRUST              = 'payment_no_trust';
    const NOT_AUTHORIZED        = 'payment_not_authorized';
    const LINE_FULL             = 'payment_line_full';
    const NO_ISSUER             = 'payment_no_issuer';

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new PaymentResult();

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