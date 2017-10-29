<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
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
        $op = new PaymentOp(new AccountId($sourceAccountId));
        $op->destination = new AccountId($destinationAccountId);
        $op->asset = Asset::newNativeAsset();
        $op->setAmount($amount);

        return $op;
    }

    public static function newCustomPayment($sourceAccountId, $destinationAccountId, $amount, $assetCode, $assetIssuerId)
    {
        $op = new PaymentOp(new AccountId($sourceAccountId));
        $op->destination = new AccountId($destinationAccountId);
        $op->setAmount($amount);
        $op->asset = Asset::newCustomAsset($assetCode, $assetIssuerId);

        return $op;
    }

    public function __construct(AccountId $sourceAccount)
    {
        parent::__construct(Operation::TYPE_PAYMENT, $sourceAccount);
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->destination->toXdr();
        $bytes .= $this->asset->toXdr();
        $bytes .= XdrEncoder::integer64RawBytes($this->amount->toBytes(true));

        return $bytes;
    }

    /**
     * @param string|BigInteger $scaledAmountOrBigInteger
     */
    public function setAmount($scaledAmountOrBigInteger)
    {
        if (!is_string($scaledAmountOrBigInteger) && !$scaledAmountOrBigInteger instanceof BigInteger) {
            throw new \InvalidArgumentException(sprintf("For type safety you must pass a string to this method"));
        }

        // A scaled amount as a string
        if (is_string($scaledAmountOrBigInteger)) {
            $scaledAmountOrBigInteger = new BigInteger($scaledAmountOrBigInteger);
            $rawScale = new BigInteger("10000000");
            $this->amount = $scaledAmountOrBigInteger->multiply($rawScale);
        }
        else {
            $this->amount = $scaledAmountOrBigInteger;
        }
    }
}