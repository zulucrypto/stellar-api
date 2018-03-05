<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
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
     * @var StellarAmount
     */
    private $amount;

    /**
     * @param $destinationAccountId
     * @param $amount int|BigInteger int representing lumens or BigInteger representing stroops
     * @param null|string|Keypair $sourceAccountId
     * @return PaymentOp
     */
    public static function newNativePayment($destinationAccountId, $amount, $sourceAccountId = null)
    {
        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $op = new PaymentOp($sourceAccountId);
        $op->destination = new AccountId($destinationAccountId);
        $op->asset = Asset::newNativeAsset();
        $op->setAmount($amount);

        return $op;
    }

    /**
     * @param $destinationAccountId string|Keypair
     * @param $amount
     * @param $assetCode
     * @param $assetIssuerId
     * @param $sourceAccountId
     * @return PaymentOp
     */
    public static function newCustomPayment($destinationAccountId, $amount, $assetCode, $assetIssuerId, $sourceAccountId = null)
    {
        $op = new PaymentOp($sourceAccountId);
        $op->destination = new AccountId($destinationAccountId);
        $op->setAmount($amount);
        $op->asset = Asset::newCustomAsset($assetCode, $assetIssuerId);

        return $op;
    }

    /**
     * PaymentOp constructor.
     *
     * @param null|string|Keypair $sourceAccount
     */
    public function __construct($sourceAccount = null)
    {
        if (is_string($sourceAccount)) {
            $sourceAccount = new AccountId($sourceAccount);
        }
        if ($sourceAccount instanceof Keypair) {
            $sourceAccount = new AccountId($sourceAccount->getPublicKey());
        }

        parent::__construct(Operation::TYPE_PAYMENT, $sourceAccount);
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->destination->toXdr();
        $bytes .= $this->asset->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->amount->getUnscaledBigInteger());

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return Operation|PaymentOp
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $op = new PaymentOp();

        $op->destination = AccountId::fromXdr($xdr);
        $op->asset = Asset::fromXdr($xdr);
        $op->amount = StellarAmount::fromXdr($xdr);

        return $op;
    }

    /**
     * @param int|BigInteger $amount int representing lumens or BigInteger representing stroops
     */
    public function setAmount($amount)
    {
        $this->amount = new StellarAmount($amount);
    }

    /**
     * @return StellarAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param BigInteger $stroops
     */
    public function setAmountInStroops(BigInteger $stroops)
    {
        $this->amount = new StellarAmount($stroops);
    }

    /**
     * @return AccountId
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param AccountId $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param Asset $asset
     */
    public function setAsset($asset)
    {
        $this->asset = $asset;
    }
}