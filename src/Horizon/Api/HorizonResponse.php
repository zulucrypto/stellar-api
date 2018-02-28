<?php


namespace ZuluCrypto\StellarSdk\Horizon\Api;


class HorizonResponse
{
    /**
     * Raw json-decoded response body
     *
     * @var array
     */
    protected $rawData;

    public function __construct($jsonEncodedData)
    {
        $this->rawData = @json_decode($jsonEncodedData, true);

        if (null === $this->rawData && json_last_error() != JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(sprintf("Error in json_decode: %s", json_last_error_msg()));
        }
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * Returns the value of $fieldName or null if $fieldName is not in the response
     *
     * @param $fieldName
     * @return mixed|null
     */
    public function getField($fieldName)
    {
        if (!isset($this->rawData[$fieldName])) return null;

        return $this->rawData[$fieldName];
    }

    /**
     * Throws an exception if $fieldName is not present in the response
     *
     * @param $fieldName
     * @throws \InvalidArgumentException
     * @return mixed|null
     */
    public function mustGetField($fieldName)
    {
        if (!isset($this->rawData[$fieldName])) throw new \InvalidArgumentException(sprintf("Field '%s' not present in response", $fieldName));

        return $this->rawData[$fieldName];
    }

    public function getRecords($limit = 100)
    {
        // todo: support paging

        $records = [];
        foreach ($this->rawData['_embedded']['records'] as $rawRecord) {
            $record = $rawRecord;
            unset($record['_links']);

            $records[] = $record;
        }

        return $records;
    }
}