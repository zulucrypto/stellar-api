<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class PaymentOp extends Operation
{
    /**
     * @var AccountId
     */
    private $destination;

    /**
     * @var Asset
     */
    private $asset;

    /**
     * @var BigInteger Int64 (signed)
     */
    private $amount;

    public static function newNativePayment($sourceAccountId, $destinationAccountId, $amount)
    {
        $sourceAccount = null;
        if ($sourceAccountId) $sourceAccount = new AccountId($sourceAccountId);

        $op = new PaymentOp($sourceAccount);
        $op->destination = new AccountId($destinationAccountId);
        $op->asset = Asset::newNativeAsset();
        $op->setAmount($amount);

        return $op;
    }

    /**
     * @param $sourceAccountId
     * @param $destinationAccountId string|Keypair
     * @param $amount
     * @param $assetCode
     * @param $assetIssuerId
     * @return PaymentOp
     */
    public static function newCustomPayment($sourceAccountId, $destinationAccountId, $amount, $assetCode, $assetIssuerId)
    {
        $sourceAccount = null;
        if ($sourceAccountId) $sourceAccount = new AccountId($sourceAccountId);

        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $op = new PaymentOp($sourceAccount);
        $op->destination = new AccountId($destinationAccountId);
        $op->setAmount($amount);
        $op->asset = Asset::newCustomAsset($assetCode, $assetIssuerId);

        return $op;
    }

    public function __construct(AccountId $sourceAccount = null)
    {
        parent::__construct(Operation::TYPE_PAYMENT, $sourceAccount);
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->destination->toXdr();
        $bytes .= $this->asset->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->amount);

        return $bytes;
    }

    /**
     * @param string|BigInteger $scaledAmountOrBigInteger
     */
    public function setAmount($scaledAmountOrBigInteger)
    {
        if (!is_string($scaledAmountOrBigInteger) && !$scaledAmountOrBigInteger instanceof BigInteger) {
            if ($scaledAmountOrBigInteger > PHP_INT_MAX) {
                throw new \InvalidArgumentException(sprintf("you must pass a string to this method since amount is greater than PHP_INT_MAX"));
            }
        }

        // A string or integer
        if (!$scaledAmountOrBigInteger instanceof BigInteger) {
            $scaledAmountOrBigInteger = new BigInteger($scaledAmountOrBigInteger);
            $rawScale = new BigInteger("10000000");
            $this->amount = $scaledAmountOrBigInteger->multiply($rawScale);
        }
        else {
            $this->amount = $scaledAmountOrBigInteger;
        }
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->amount->toString();
    }

    /**
     * @param BigInteger $stroops
     */
    public function setAmountInStroops(BigInteger $stroops)
    {
        $this->amount = $stroops;
    }
}