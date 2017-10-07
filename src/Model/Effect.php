<?php


namespace ZuluCrypto\StellarSdk\Model;

/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/effect.html
 */
class Effect extends RestApiModel
{
    /**
     * See TYPE_ constants in Operation
     *
     * @var string
     */
    protected $type;

    /**
     * Stellar operation integer code
     *
     * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html
     *
     * @var int
     */
    protected $typeI;

    /**
     * @var Operation
     */
    protected $operation;

    /**
     * Holds unknown fields in the response object
     *
     * @var array
     */
    protected $extraData;

    /**
     * @param array $rawData
     * @return Effect
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new Effect($rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * Operation type
     *
     * @param $type
     */
    public function __construct($type)
    {
        $this->type = $type;

        $this->extraData = [];
    }

    public function __toString()
    {
        return sprintf('%s:%s', $this->type, $this->id);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        // The fields in the Effect response differ slightly from those available
        // in the Operation, so known fields are stored in this class and everything
        // else goes in extraData
        $knownFields = ['_links', 'id', 'type', 'type_i'];

        if (isset($rawData['type'])) $this->type = $rawData['type'];
        if (isset($rawData['type_i'])) $this->typeI = $rawData['type_i'];

        foreach ($rawData as $key => $value) {
            if (in_array($key, $knownFields)) continue;

            $this->extraData[$key] = $value;
        }
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

    /**
     * @return int
     */
    public function getTypeI()
    {
        return $this->typeI;
    }

    /**
     * @param int $typeI
     */
    public function setTypeI($typeI)
    {
        $this->typeI = $typeI;
    }

    /**
     * @return Operation
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @param Operation $operation
     */
    public function setOperation(Operation $operation)
    {
        $this->operation = $operation;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return $this->extraData;
    }

    /**
     * @param array $extraData
     */
    public function setExtraData($extraData)
    {
        $this->extraData = $extraData;
    }
}