<?php


namespace ZuluCrypto\StellarSdk\Model;


class CreateAccountOperation extends Operation implements AssetTransferInterface
{
    /**
     * The new account ID that was funded
     *
     * @var string
     */
    protected $newAccountId;

    /**
     * The account ID providing the initial funds
     *
     * @var string
     */
    protected $fundingAccountId;

    /**
     * @var AssetAmount
     */
    protected $startingBalance;

    /**
     * @param array $rawData
     * @return CreateAccountOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new CreateAccountOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_CREATE_ACCOUNT);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->newAccountId = $rawData['account'];
        $this->fundingAccountId = $rawData['funder'];

        $this->startingBalance = new AssetAmount($rawData['starting_balance']);
    }

    public function getAssetTransferType()
    {
        return $this->type;
    }

    public function getFromAccountId()
    {
        return $this->fundingAccountId;
    }

    public function getToAccountId()
    {
        return $this->newAccountId;
    }

    public function getAssetAmount()
    {
        return $this->startingBalance;
    }

    /**
     * @return string
     */
    public function getNewAccountId()
    {
        return $this->newAccountId;
    }

    /**
     * @param string $newAccountId
     */
    public function setNewAccountId($newAccountId)
    {
        $this->newAccountId = $newAccountId;
    }

    /**
     * @return string
     */
    public function getFundingAccountId()
    {
        return $this->fundingAccountId;
    }

    /**
     * @param string $fundingAccountId
     */
    public function setFundingAccountId($fundingAccountId)
    {
        $this->fundingAccountId = $fundingAccountId;
    }

    /**
     * @return AssetAmount
     */
    public function getStartingBalance()
    {
        return $this->startingBalance;
    }

    /**
     * @param AssetAmount $startingBalance
     */
    public function setStartingBalance($startingBalance)
    {
        $this->startingBalance = $startingBalance;
    }
}