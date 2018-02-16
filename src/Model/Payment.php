<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#payment
 */
class Payment extends Operation implements AssetTransferInterface
{
    /**
     *
     * @var string
     */
    private $sourceAccountId;

    /**
     * @var string
     */
    private $fromAccountId;

    /**
     * @var string
     */
    private $toAccountId;
    
    /**
     * @var string
     */
    private $transactionHash;

    /**
     * @var AssetAmount
     */
    private $amount;

    /**
     * @param array $rawData
     * @return Payment
     */
    public static function fromRawResponseData($rawData)
    {
        if ($rawData['type'] != Operation::TYPE_PAYMENT) throw new \InvalidArgumentException(sprintf('Attempted to create a Payment from invalid type %s', $rawData['type']));

        $object = new Payment($rawData['id']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $toAccountId
     * @param $amount
     * @return Payment
     */
    public static function newNativeAssetPayment($toAccountId, $amount, $fromAccountId, $sourceAccountId = null)
    {
        $payment = new Payment(null);
        $payment->toAccountId = $toAccountId;
        $payment->amount = new AssetAmount($amount);
        $payment->fromAccountId = $fromAccountId;

        if ($sourceAccountId === null) $payment->sourceAccountId = $fromAccountId;

        return $payment;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id)
    {
        parent::__construct($id, Operation::TYPE_PAYMENT);

        $this->amount = new AssetAmount('0');
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->sourceAccountId = $rawData['source_account'];

        if (isset($rawData['from'])) $this->fromAccountId = $rawData['from'];
        if (isset($rawData['to'])) $this->toAccountId = $rawData['to'];
        if (isset($rawData['transaction_hash'])) $this->transactionHash = $rawData['transaction_hash'];

        $assetAmount = null;
        // Native assets
        if ('native' == $rawData['asset_type']) {
            $assetAmount = new AssetAmount($rawData['amount']);
        }
        // Custom assets
        else {
            $assetAmount = new AssetAmount($rawData['amount'], $rawData['asset_type']);
            $assetAmount->setAssetIssuerAccountId($rawData['asset_issuer']);
            $assetAmount->setAssetCode($rawData['asset_code']);
        }

        $this->amount = $assetAmount;
    }

    public function getAssetTransferType()
    {
        return $this->type;
    }

    public function getAssetAmount()
    {
        return $this->amount;
    }

    /**
     * @return bool
     */
    public function isNativeAsset()
    {
        return $this->amount->isNativeAsset();
    }

    /**
     * @return mixed
     */
    public function getSourceAccountId()
    {
        return $this->sourceAccountId;
    }

    /**
     * @param mixed $sourceAccountId
     */
    public function setSourceAccountId($sourceAccountId)
    {
        $this->sourceAccountId = $sourceAccountId;
    }

    /**
     * @return mixed
     */
    public function getFromAccountId()
    {
        return $this->fromAccountId;
    }

    /**
     * @param mixed $fromAccountId
     */
    public function setFromAccountId($fromAccountId)
    {
        $this->fromAccountId = $fromAccountId;
    }

    /**
     * @return string
     */
    public function getToAccountId()
    {
        return $this->toAccountId;
    }

    /**
     * @return string
     */
    public function getDestinationAccountId()
    {
        return $this->toAccountId;
    }

    /**
     * @param mixed $toAccountId
     */
    public function setToAccountId($toAccountId)
    {
        $this->toAccountId = $toAccountId;
    }

    /**
     * @return AssetAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param AssetAmount $amount
     */
    public function setAmount(AssetAmount $amount)
    {
        $this->amount = $amount;
    }
    
    /**
     * @return string
     */
    public function getTransactionHash()
    {
        return $this->transactionHash;
    }
}
