<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#manage-data
 */
class ManageDataOperation extends Operation
{
    /**
     * The account that data is being updated on
     *
     * @var string
     */
    protected $sourceAccountId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param array $rawData
     * @return ManageDataOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new ManageDataOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_MANAGE_DATA);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        $this->sourceAccountId = $rawData['source_account'];
        $this->name = $rawData['name'];
        $this->value = $rawData['value'];
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
}