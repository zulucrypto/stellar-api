<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class AccountMergeResult extends OperationResult
{
    // https://github.com/stellar/stellar-core/blob/3c4e356803175f6c2645e4437881cf07522df94d/src/xdr/Stellar-transaction.x#L585
    const MALFORMED                 = 'account_merge_malformed';
    const NO_ACCOUNT                = 'account_merge_no_account';       // destination account does not exist
    const IMMUTABLE_SET             = 'account_merge_immutable_set';    // source account has AUTH_IMMUTABLE set
    const HAS_SUB_ENTRIES           = 'account_merge_has_sub_entries';  // account has trust lines, offers, etc.

    /**
     * Amount of XLM transferred from the source account to the destination account
     *
     * @var StellarAmount
     */
    protected $transferredBalance;

    /**
     * @deprecated Do not call this method directly. Instead, use OperationResult::fromXdr
     *
     * @param XdrBuffer $xdr
     * @return OperationResult|PaymentResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new AccountMergeResult();

        $rawErrorCode = $xdr->readInteger();
        $errorCodeMap = [
            '0' => 'success',
            '-1' => static::MALFORMED,
            '-2' => static::NO_ACCOUNT,
            '-3' => static::IMMUTABLE_SET,
            '-4' => static::HAS_SUB_ENTRIES,
        ];
        if (!isset($errorCodeMap[$rawErrorCode])) {
            throw new \ErrorException(sprintf('Unknown error code %s', $rawErrorCode));
        }

        // Do not store the "success" error code
        if ($errorCodeMap[$rawErrorCode] !== 'success') {
            $model->errorCode = $errorCodeMap[$rawErrorCode];
            return $model;
        }

        $model->transferredBalance = new StellarAmount(new BigInteger($xdr->readInteger64()));

        return $model;
    }

    /**
     * @return StellarAmount
     */
    public function getTransferredBalance()
    {
        return $this->transferredBalance;
    }

    /**
     * @param StellarAmount $transferredBalance
     */
    public function setTransferredBalance($transferredBalance)
    {
        $this->transferredBalance = $transferredBalance;
    }
}