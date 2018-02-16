<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#account-merge
 */
class AccountMergeOperation extends Operation implements AssetTransferInterface
{
    /**
     * The account that will be removed
     *
     * @var string
     */
    protected $sourceAccountId;

    /**
     * The account that will contain items from the old account
     *
     * @var string
     */
    protected $mergeIntoAccountId;

    /**
     * @param array $rawData
     * @return AccountMergeOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new AccountMergeOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_ACCOUNT_MERGE);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->sourceAccountId = $rawData['account'];
        $this->mergeIntoAccountId = $rawData['into'];
    }

    public function getAssetTransferType()
    {
        return $this->type;
    }

    public function getFromAccountId()
    {
        return $this->sourceAccountId;
    }

    public function getToAccountId()
    {
        return $this->mergeIntoAccountId;
    }

    public function getAssetAmount()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getSourceAccountId()
    {
        return $this->sourceAccountId;
    }

    /**
     * @param string $sourceAccountId
     */
    public function setSourceAccountId($sourceAccountId)
    {
        $this->sourceAccountId = $sourceAccountId;
    }

    /**
     * @return string
     */
    public function getMergeIntoAccountId()
    {
        return $this->mergeIntoAccountId;
    }

    /**
     * @param string $mergeIntoAccountId
     */
    public function setMergeIntoAccountId($mergeIntoAccountId)
    {
        $this->mergeIntoAccountId = $mergeIntoAccountId;
    }
}