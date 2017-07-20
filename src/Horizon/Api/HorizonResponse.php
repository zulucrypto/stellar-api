<?php


namespace ZuluCrypto\StellarSdk\Horizon\Api;


class HorizonResponse
{
    /**
     * Raw json-decoded response body
     *
     * @var array
     */
    private $rawData;

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