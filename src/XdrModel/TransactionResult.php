<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class TransactionResult
{
    const SUCCESS               = 0;    // all operations suceeded
    const FAILED                = -1;   // one or more operations failed
    const TOO_EARLY             = -2;   // ledger close time before min timebounds
    const TOO_LATE              = -3;   // ledger close time after max timebounds
    const MISSING_OPERATION     = -4;   // no operations specified
    const BAD_SEQ               = -5;   // sequence number not correct for source account
    const BAD_AUTH              = -6;   // too few valid signatures or wrong network
    const INSUFFICIENT_BALANCE  = -7;   // account would be below the reserve after this tx
    const NO_ACCOUNT            = -8;   // source account not found
    const INSUFFICIENT_FEE      = -9;   // fee was too small
    const BAD_AUTH_EXTRA        = -10;  // included extra signatures
    const INTERNAL_ERROR        = -11;  // unknown error

    /**
     * This value is stored internally as stroops
     * @var StellarAmount
     */
    protected $feeCharged;

    /**
     * See the class constants
     * @var string
     */
    protected $resultCode;

    /**
     * Array of operation results
     *
     * @var OperationResult[]
     */
    protected $operationResults;

    public function __construct()
    {
        $this->operationResults = [];
    }

    /**
     * @param XdrBuffer $xdr
     * @return TransactionResult
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new TransactionResult();

        // This is the fee in stroops
        $model->feeCharged = new StellarAmount($xdr->readInteger64());

        $rawCode = $xdr->readInteger();
        $model->resultCode = $rawCode;

        $numOperations = $xdr->readInteger();
        for ($i=0; $i < $numOperations; $i++) {
            $op = OperationResult::fromXdr($xdr);
            $model->operationResults[] = $op;
        }

        return $model;
    }

    /**
     * Returns true if all operations in this transaction succeeded
     * @return bool
     */
    public function succeeded()
    {
        return $this->resultCode === static::SUCCESS;
    }

    /**
     * Returns true if any operation in the transaction failed
     * @return bool
     */
    public function failed()
    {
        return !$this->succeeded();
    }

    /**
     * Returns a BigInteger representing the fee in stroops
     *
     * @return \phpseclib\Math\BigInteger
     */
    public function getFeeStroops()
    {
        return $this->feeCharged->getUnscaledBigInteger();
    }

    /**
     * Returns the fee in XLM
     *
     * @return float|int
     */
    public function getFee()
    {
        return $this->feeCharged->getScaledValue();
    }

    /**
     * @return OperationResult[]
     */
    public function getOperationResults()
    {
        return $this->operationResults;
    }
}