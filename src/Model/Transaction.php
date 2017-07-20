<?php


namespace ZuluCrypto\StellarSdk\Model;


class Transaction extends RestApiModel
{
    /**
     * ID on the stellar network
     *
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $hash;

    private $memoType;

    private $memo;

    private $pagingToken;

    private $sourceAccountId;

    /**
     * @param array $rawData
     * @return Transaction
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new Transaction($rawData['id'], $rawData['hash']);

        // todo: should be a Memo object?
        if (isset($rawData['memo_type'])) $object->memoType = $rawData['memo_type'];
        if (isset($rawData['memo'])) $object->memo = $rawData['memo'];
        if (isset($rawData['paging_token'])) $object->pagingToken = $rawData['paging_token'];
        if (isset($rawData['source_account'])) $object->sourceAccountId = $rawData['source_account'];

        return $object;
    }

    public function __construct($id, $hash)
    {
        $this->id = $id;
        $this->hash = $hash;
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return array|Payment[]
     */
    public function getPayments($sinceCursor = null, $limit = 50)
    {
        $payments = [];

        $url = sprintf('/transactions/%s/payments', $this->hash);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;
        if ($limit) $params['limit'] = $limit;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->apiClient->get($url);

        $rawRecords = $response->getRecords();

        foreach ($rawRecords as $rawRecord) {
            $payment = Payment::fromRawResponseData($rawRecord);
            $payment->setApiClient($this->getApiClient());

            $payments[] = $payment;
        }

        return $payments;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getMemoType()
    {
        return $this->memoType;
    }

    /**
     * @param mixed $memoType
     */
    public function setMemoType($memoType)
    {
        $this->memoType = $memoType;
    }

    /**
     * @return mixed
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * @param mixed $memo
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;
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
}