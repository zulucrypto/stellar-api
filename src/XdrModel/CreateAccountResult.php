<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class CreateAccountResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L364
    const MALFORMED             = 'create_account_malformed';
    const UNDERFUNDED           = 'create_account_underfunded'; // source account does not have enough funds
    const LOW_RESERVE           = 'create_account_low_reserve'; // would create an account below the minimum reserve
    const ALREADY_EXIST         = 'create_account_already_exists';

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new CreateAccountResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::UNDERFUNDED,
            '-3' => static::LOW_RESERVE,
            '-4' => static::ALREADY_EXIST,
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