<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class InflationResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L606
    const NOT_TIME                  = 'inflation_not_time';

    /**
     * @var InflationPayout[]
     */
    protected $payouts = array();

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new InflationResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::NOT_TIME,
        ];
        if (!isset($errorCodeMap[$rawErrorCode])) {
            throw new \ErrorException(sprintf('Unknown error code %s', $rawErrorCode));
        }

        // Do not store the "success" error code
        if ($errorCodeMap[$rawErrorCode] !== 'success') {
            $model->errorCode = $errorCodeMap[$rawErrorCode];
            return $model;
        }

        $numPayouts = $xdr->readUnsignedInteger();
        for ($i=0; $i < $numPayouts; $i++) {
            $model->payouts[] = InflationPayout::fromXdr($xdr);
        }

        return $model;
    }
}