<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 */
class Payment extends RestApiModel
{
    const TYPE_CREATE_ACCOUNT = 'create_account';
    const TYPE_PAYMENT = 'payment';

    /**
     * The payment ID as assigned by the stellar network
     *
     * @var string
     */
    private $id;

    /**
     * 64-bit Stellar ID
     *
     * @var string
     */
    private $stellarId;

    private $pagingToken;

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
     * @var AssetAmount
     */
    private $amount;

    /**
     * The type of payment. See TYPE_* constants
     *
     * @var string
     */
    private $type;

    /**
     * @param array $rawData
     * @return Payment
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new Payment();

        $object->id = $rawData['id'];
        $object->stellarId = $rawData['id'];
        $object->pagingToken = $rawData['paging_token'];
        $object->sourceAccountId = $rawData['source_account'];
        $object->type = $rawData['type'];

        if (isset($rawData['from'])) $object->fromAccountId = $rawData['from'];
        if (isset($rawData['to'])) $object->toAccountId = $rawData['to'];

        if ('payment' == $rawData['type']) {
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

            $object->amount = $assetAmount;
        }

        return $object;
    }


    public function __construct()
    {
        $this->amount = new AssetAmount('0');
    }

    public function isNativeAsset()
    {
        return $this->amount->isNativeAsset();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
    public function getStellarId()
    {
        return $this->stellarId;
    }

    /**
     * @param string $stellarId
     */
    public function setStellarId($stellarId)
    {
        $this->stellarId = $stellarId;
    }

    /**
     * @return mixed
     */
    public function getPagingToken()
    {
        return $this->pagingToken;
    }

    /**
     * @param mixed $pagingToken
     */
    public function setPagingToken($pagingToken)
    {
        $this->pagingToken = $pagingToken;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}