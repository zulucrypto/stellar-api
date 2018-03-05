<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\Type\VariableArray;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

/**
 * https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L72
 */
class PathPaymentOp extends Operation
{
    /**
     * @var Asset
     */
    protected $sendAsset;

    /**
     * maximum amount of $sendAsset to send (excluding fees)
     *
     * @var StellarAmount
     */
    protected $sendMax;

    /**
     * @var AccountId
     */
    protected $destinationAccount;

    /**
     * @var Asset
     */
    protected $destinationAsset;

    /**
     * @var StellarAmount
     */
    protected $destinationAmount;

    /**
     * @var Asset[]
     */
    protected $paths;

    /**
     * PathPaymentOp constructor.
     *
     * @param Asset $sendAsset
     * @param number|BigInteger      $sendMax number of XLM or BigInteger representing stroops
     * @param       $destinationAccountId
     * @param Asset $destinationAsset
     * @param number|BigInteger      $destinationAmount
     * @param       $sourceAccountId
     */
    public function __construct(
        Asset $sendAsset,
        $sendMax,
        $destinationAccountId,
        Asset $destinationAsset,
        $destinationAmount,
        $sourceAccountId = null
    ) {
        parent::__construct(Operation::TYPE_PATH_PAYMENT, $sourceAccountId);

        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $this->sendAsset = $sendAsset;
        $this->sendMax = new StellarAmount($sendMax);
        $this->destinationAccount = new AccountId($destinationAccountId);
        $this->destinationAsset = $destinationAsset;
        $this->destinationAmount = new StellarAmount($destinationAmount);

        $this->paths = new VariableArray();
    }

    /**
     * @return string
     * @throws \ErrorException
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Sending asset
        $bytes .= $this->sendAsset->toXdr();

        // sendMax
        $bytes .= XdrEncoder::signedBigInteger64($this->sendMax->getUnscaledBigInteger());

        // Destination account
        $bytes .= $this->destinationAccount->toXdr();

        // Destination asset
        $bytes .= $this->destinationAsset->toXdr();

        // Destination amount
        $bytes .= XdrEncoder::signedBigInteger64($this->destinationAmount->getUnscaledBigInteger());

        // Paths
        $bytes .= $this->paths->toXdr();

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return Operation|PathPaymentOp
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $sendingAsset = Asset::fromXdr($xdr);
        $sendMax = StellarAmount::fromXdr($xdr);
        $destinationAccount = AccountId::fromXdr($xdr);
        $destinationAsset = Asset::fromXdr($xdr);
        $destinationAmount = StellarAmount::fromXdr($xdr);

        $model = new PathPaymentOp($sendingAsset, $sendMax->getUnscaledBigInteger(), $destinationAccount->getAccountIdString(), $destinationAsset, $destinationAmount->getUnscaledBigInteger());

        $numPaths = $xdr->readUnsignedInteger();
        for ($i=0; $i < $numPaths; $i++) {
            $model->paths->append(Asset::fromXdr($xdr));
        }

        return $model;
    }

    /**
     * @param Asset $path
     * @return $this
     */
    public function addPath(Asset $path)
    {
        // a maximum of 5 paths are supported
        if (count($this->paths) >= 5) throw new \InvalidArgumentException('Too many paths: PathPaymentOp can contain a maximum of 5 paths');

        $this->paths->append($path);

        return $this;
    }

    /**
     * @return Asset
     */
    public function getSendAsset()
    {
        return $this->sendAsset;
    }

    /**
     * @param Asset $sendAsset
     */
    public function setSendAsset($sendAsset)
    {
        $this->sendAsset = $sendAsset;
    }

    /**
     * @return StellarAmount
     */
    public function getSendMax()
    {
        return $this->sendMax;
    }

    /**
     * @param number|BigInteger $sendMax number of XLM or BigInteger representing stroops
     */
    public function setSendMax($sendMax)
    {
        $this->sendMax = new StellarAmount($sendMax);
    }

    /**
     * @return AccountId
     */
    public function getDestinationAccount()
    {
        return $this->destinationAccount;
    }

    /**
     * @param AccountId $destinationAccount
     */
    public function setDestinationAccount($destinationAccount)
    {
        $this->destinationAccount = $destinationAccount;
    }

    /**
     * @return Asset
     */
    public function getDestinationAsset()
    {
        return $this->destinationAsset;
    }

    /**
     * @param Asset $destinationAsset
     */
    public function setDestinationAsset($destinationAsset)
    {
        $this->destinationAsset = $destinationAsset;
    }

    /**
     * @return StellarAmount
     */
    public function getDestinationAmount()
    {
        return $this->destinationAmount;
    }

    /**
     * @param number|BigInteger $destinationAmount number of XLM or BigInteger representing stroops
     */
    public function setDestinationAmount($destinationAmount)
    {
        $this->destinationAmount = new StellarAmount($destinationAmount);
    }

    /**
     * @return Asset[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * @param Asset[] $paths
     */
    public function setPaths($paths)
    {
        $this->paths = $paths;
    }
}