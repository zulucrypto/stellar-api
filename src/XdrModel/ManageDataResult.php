<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class ManageDataResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L630
    const NOT_SUPPORTED_YET         = 'manage_data_not_supported_yet';  // network hasn't upgraded to the right protocol version yet
    const NAME_NOT_FOUND            = 'manage_data_name_not_found';     // trying to remove a data entry that doesn't exist
    const LOW_RESERVE               = 'manage_data_low_reserve';
    const INVALID_NAME              = 'manage_data_invalid_name';

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new ManageDataResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::NOT_SUPPORTED_YET,
            '-2' => static::NAME_NOT_FOUND,
            '-3' => static::LOW_RESERVE,
            '-4' => static::INVALID_NAME,
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