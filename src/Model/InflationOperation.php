<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#inflation
 */
class InflationOperation extends Operation
{
    /**
     * @param array $rawData
     * @return InflationOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new InflationOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_INFLATION);
    }
}