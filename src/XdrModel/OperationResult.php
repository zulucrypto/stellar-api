<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class OperationResult
{
    const BAD_AUTH      = 'bad_auth';   // too few signatures or wrong network
    const NO_ACCOUNT    = 'no_account'; // source account doesn't exist

    /**
     * A string describing the error
     * @var string
     */
    protected $errorCode;

    /**
     * Generic error codes: https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L651
     *
     * @param XdrBuffer $xdr
     * @return OperationResult
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $opResultCode = $xdr->readInteger();

        // Early return for operations that failed
        if ($opResultCode === -1) {
            $opResult = new OperationResult();
            $opResult->errorCode = static::BAD_AUTH;

            return $opResult;
        }
        if ($opResultCode === -2) {
            $opResult = new OperationResult();
            $opResult->errorCode = static::NO_ACCOUNT;

            return $opResult;
        }

        // Past this point there can be a variety of operation results
        $opType = $xdr->readInteger();
        switch ($opType) {
            case Operation::TYPE_CREATE_ACCOUNT:
                return CreateAccountResult::fromXdr($xdr);
                break;
            case Operation::TYPE_PAYMENT:
                return PaymentResult::fromXdr($xdr);
                break;
            case Operation::TYPE_PATH_PAYMENT;
                return PathPaymentResult::fromXdr($xdr);
                break;
            case Operation::TYPE_MANAGE_OFFER:
                return ManageOfferResult::fromXdr($xdr);
                break;
            case Operation::TYPE_SET_OPTIONS:
                return SetOptionsResult::fromXdr($xdr);
                break;
            case Operation::TYPE_CHANGE_TRUST:
                return ChangeTrustResult::fromXdr($xdr);
                break;
            case Operation::TYPE_ALLOW_TRUST:
                return AllowTrustResult::fromXdr($xdr);
                break;
            case Operation::TYPE_ACCOUNT_MERGE:
                return AccountMergeResult::fromXdr($xdr);
                break;
            case Operation::TYPE_INFLATION:
                return InflationResult::fromXdr($xdr);
                break;
            case Operation::TYPE_MANAGE_DATA:
                return ManageDataResult::fromXdr($xdr);
                break;
            default:
                throw new \ErrorException(sprintf('Unknown operation type: %s', $opType));
        }
    }

    public function __construct()
    {
        $this->errorCode = null;
    }

    /**
     * Returns true if this operation succeeded
     *
     * @return bool
     */
    public function succeeded()
    {
        return $this->errorCode === null;
    }

    /**
     * Returns true if this operation failed
     *
     * @return bool
     */
    public function failed()
    {
        return !$this->succeeded();
    }

    /**
     * @return null|string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }
}