<?php


namespace ZuluCrypto\StellarSdk\Horizon\Api;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TransactionResult;

class PostTransactionResponse extends HorizonResponse
{
    /**
     * @var TransactionResult
     */
    protected $result;

    /**
     * @param HorizonResponse $response
     * @return PostTransactionResponse
     */
    public static function fromApiResponse(HorizonResponse $response)
    {
        return new PostTransactionResponse(json_encode($response->rawData));
    }

    public function __construct($jsonEncodedData)
    {
        parent::__construct($jsonEncodedData);

        $this->parseRawData($this->rawData);
    }

    /**
     * Parses the raw response data and populates this object
     *
     * @param $rawData
     * @throws \ErrorException
     */
    protected function parseRawData($rawData)
    {
        if (!$rawData) return;

        if (!empty($rawData['result_xdr'])) {
            $xdr = new XdrBuffer(base64_decode($rawData['result_xdr']));
            $this->result = TransactionResult::fromXdr($xdr);
        }
    }

    /**
     * @return TransactionResult
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * @param TransactionResult $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}