<?php


namespace ZuluCrypto\StellarSdk\Model;

/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/transaction.html
 */
class Transaction extends RestApiModel
{
    /**
     * A hex-encoded SHA-256 hash of the transaction’s XDR-encoded form.
     *
     * @var string
     */
    protected $hash;

    /**
     * Sequence number of the ledger in which this transaction was applied.
     *
     * @var string
     */
    protected $ledger;

    /**
     * @var string
     */
    protected $sourceAccountId;

    /**
     * @var string
     */
    protected $sourceAccountSequence;

    /**
     * The fee paid by the source account of this transaction when the transaction was applied to the ledger.
     *
     * @var int
     */
    protected $feePaid;

    /**
     * The number of operations that are contained within this transaction.
     *
     * @var int
     */
    protected $operationCount;

    /**
     * The numeric result code for this transaction
     *
     * @var int
     */
    protected $resultCodeI;

    /**
     * The string result code for this transaction
     *
     * @var string
     */
    protected $resultCodeS;

    /**
     * @var
     */
    protected $memoType;

    /**
     * @var string
     */
    protected $memo;

    /**
     * A base64 encoded string of the raw TransactionEnvelope xdr struct for this transaction
     * @var string
     */
    protected $envelopeXdr;

    /**
     * A base64 encoded string of the raw TransactionResultPair xdr struct for this transaction
     *
     * @var string
     */
    protected $resultXdr;

    /**
     * A base64 encoded string of the raw TransactionMeta xdr struct for this transaction
     *
     * @var string
     */
    protected $resultMetaXdr;

    /**
     * A base64 encoded string of the raw LedgerEntryChanges xdr struct produced by taking fees for this transaction.
     *
     * @var string
     */
    protected $feeMetaXdr;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @param array $rawData
     * @return Transaction
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new Transaction($rawData['id'], $rawData['hash']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $hash
     */
    public function __construct($id, $hash)
    {
        $this->id = $id;
        $this->hash = $hash;
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        // todo: should be a Memo object?
        if (isset($rawData['ledger'])) $this->ledger = $rawData['ledger'];
        if (isset($rawData['created_at'])) $this->createdAt = \DateTime::createFromFormat(DATE_ISO8601, $rawData['created_at']);
        if (isset($rawData['source_account'])) $this->sourceAccountId = $rawData['source_account'];
        if (isset($rawData['source_account_sequence'])) $this->sourceAccountSequence = $rawData['source_account_sequence'];
        if (isset($rawData['fee_paid'])) $this->feePaid = $rawData['fee_paid'];
        if (isset($rawData['operation_count'])) $this->operationCount = $rawData['operation_count'];
        if (isset($rawData['envelope_xdr'])) $this->envelopeXdr = $rawData['envelope_xdr'];
        if (isset($rawData['result_xdr'])) $this->resultXdr = $rawData['result_xdr'];
        if (isset($rawData['result_meta_xdr'])) $this->resultMetaXdr = $rawData['result_meta_xdr'];
        if (isset($rawData['fee_meta_xdr'])) $this->feeMetaXdr = $rawData['fee_meta_xdr'];
        if (isset($rawData['result_code'])) $this->resultCodeI = $rawData['result_code'];
        if (isset($rawData['result_code_s'])) $this->resultCodeS = $rawData['result_code_s'];
        if (isset($rawData['memo_type'])) $this->memoType = $rawData['memo_type'];
        if (isset($rawData['memo'])) $this->memo = $rawData['memo'];
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return array|AssetTransferInterface[]|RestApiModel[]
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
            switch ($rawRecord['type']) {
                case 'create_account':
                    $result = CreateAccountOperation::fromRawResponseData($rawRecord);
                    break;
                case 'payment':
                    $result = Payment::fromRawResponseData($rawRecord);
                    break;
                case 'account_merge':
                    $result = AccountMergeOperation::fromRawResponseData($rawRecord);
                    break;
                case 'path_payment':
                    $result = PathPayment::fromRawResponseData($rawRecord);
                    break;
            }

            $result->setApiClient($this->getApiClient());

            $payments[] = $result;
        }

        return $payments;
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
     * @return string
     */
    public function getLedger()
    {
        return $this->ledger;
    }

    /**
     * @param string $ledger
     */
    public function setLedger($ledger)
    {
        $this->ledger = $ledger;
    }

    /**
     * @return string
     */
    public function getSourceAccountSequence()
    {
        return $this->sourceAccountSequence;
    }

    /**
     * @param string $sourceAccountSequence
     */
    public function setSourceAccountSequence($sourceAccountSequence)
    {
        $this->sourceAccountSequence = $sourceAccountSequence;
    }

    /**
     * @return int
     */
    public function getFeePaid()
    {
        return $this->feePaid;
    }

    /**
     * @param int $feePaid
     */
    public function setFeePaid($feePaid)
    {
        $this->feePaid = $feePaid;
    }

    /**
     * @return int
     */
    public function getOperationCount()
    {
        return $this->operationCount;
    }

    /**
     * @param int $operationCount
     */
    public function setOperationCount($operationCount)
    {
        $this->operationCount = $operationCount;
    }

    /**
     * @return int
     */
    public function getResultCodeI()
    {
        return $this->resultCodeI;
    }

    /**
     * @param int $resultCodeI
     */
    public function setResultCodeI($resultCodeI)
    {
        $this->resultCodeI = $resultCodeI;
    }

    /**
     * @return string
     */
    public function getResultCodeS()
    {
        return $this->resultCodeS;
    }

    /**
     * @param string $resultCodeS
     */
    public function setResultCodeS($resultCodeS)
    {
        $this->resultCodeS = $resultCodeS;
    }

    /**
     * @return string
     */
    public function getEnvelopeXdr()
    {
        return $this->envelopeXdr;
    }

    /**
     * @param string $envelopeXdr
     */
    public function setEnvelopeXdr($envelopeXdr)
    {
        $this->envelopeXdr = $envelopeXdr;
    }

    /**
     * @return string
     */
    public function getResultXdr()
    {
        return $this->resultXdr;
    }

    /**
     * @param string $resultXdr
     */
    public function setResultXdr($resultXdr)
    {
        $this->resultXdr = $resultXdr;
    }

    /**
     * @return string
     */
    public function getResultMetaXdr()
    {
        return $this->resultMetaXdr;
    }

    /**
     * @param string $resultMetaXdr
     */
    public function setResultMetaXdr($resultMetaXdr)
    {
        $this->resultMetaXdr = $resultMetaXdr;
    }

    /**
     * @return string
     */
    public function getFeeMetaXdr()
    {
        return $this->feeMetaXdr;
    }

    /**
     * @param string $feeMetaXdr
     */
    public function setFeeMetaXdr($feeMetaXdr)
    {
        $this->feeMetaXdr = $feeMetaXdr;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}