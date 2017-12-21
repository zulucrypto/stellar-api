<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\Type\VariableArray;
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
     * @var int
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
     * @var int
     */
    protected $destinationAmount;

    /**
     * @var Asset[]
     */
    protected $paths;

    public function __construct(
        Asset $sendAsset,
        $sendMax,
        $destinationAccountId,
        Asset $destinationAsset,
        $destinationAmount,
        $sourceAccountId
    ) {
        parent::__construct(Operation::TYPE_PATH_PAYMENT, $sourceAccountId);

        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $this->sendAsset = $sendAsset;
        $this->sendMax = $sendMax;
        $this->destinationAccount = new AccountId($destinationAccountId);
        $this->destinationAsset = $destinationAsset;
        $this->destinationAmount = $destinationAmount;

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
        $bytes .= XdrEncoder::signedInteger64($this->sendMax);

        // Destination account
        $bytes .= $this->destinationAccount->toXdr();

        // Destination asset
        $bytes .= $this->destinationAsset->toXdr();

        // Destination amount
        $bytes .= XdrEncoder::signedInteger64($this->destinationAmount);

        // Paths
        $bytes .= $this->paths->toXdr();

        return $bytes;
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
     * @return int
     */
    public function getSendMax()
    {
        return $this->sendMax;
    }

    /**
     * @param int $sendMax
     */
    public function setSendMax($sendMax)
    {
        $this->sendMax = $sendMax;
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
     * @return int
     */
    public function getDestinationAmount()
    {
        return $this->destinationAmount;
    }

    /**
     * @param int $destinationAmount
     */
    public function setDestinationAmount($destinationAmount)
    {
        $this->destinationAmount = $destinationAmount;
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