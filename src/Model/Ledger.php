<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/ledger.html
 */
class Ledger extends RestApiModel
{
    /**
     * A hex-encoded SHA-256 hash of the ledger’s XDR-encoded form.
     *
     * @var string
     */
    protected $hash;

    /**
     * The hash of the ledger that chronologically came before this one.
     *
     * @var string
     */
    protected $previousHash;

    /**
     * Sequence number of this ledger, suitable for use as the as the :id parameter for url templates that require a ledger number.
     *
     * @var string
     */
    protected $sequence;

    /**
     * The number of transactions in this ledger.
     *
     * @var int
     */
    protected $transactionCount;

    /**
     * The number of operations in this ledger.
     *
     * @var int
     */
    protected $operationCount;

    /**
     * When this ledger was closed
     *
     * @var \DateTime
     */
    protected $closedAt;

    /**
     * The total number of lumens in circulation.
     *
     * @var string
     */
    protected $totalCoins;

    /**
     * The sum of all transaction fees (in lumens) since the last inflation operation. They are redistributed during inflation.
     *
     * @var string
     */
    protected $feePool;

    /**
     * The fee (in Stroops) the network charges per operation in a transaction.
     *
     * @var int
     */
    protected $baseFee;

    /**
     * The reserve (in Lumens) the network uses when calculating an account’s minimum balance.
     *
     * @var string
     */
    protected $baseReserve;

    /**
     * The maximum number of transactions validators have agreed to process in a given ledger.
     *
     * @var int
     */
    protected $maxTransactionSetSize;

    /**
     * @var int
     */
    protected $protocolVersion;

    /**
     * @param array $rawData
     * @return Ledger
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new Ledger();

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        if (isset($rawData['hash'])) $this->hash = $rawData['hash'];
        if (isset($rawData['prev_hash'])) $this->previousHash = $rawData['prev_hash'];
        if (isset($rawData['sequence'])) $this->sequence = strval($rawData['sequence']);
        if (isset($rawData['transaction_count'])) $this->transactionCount = $rawData['transaction_count'];
        if (isset($rawData['operation_count'])) $this->operationCount = $rawData['operation_count'];
        if (isset($rawData['closed_at'])) $this->closedAt = \DateTime::createFromFormat(DATE_ISO8601, $rawData['closed_at']);
        if (isset($rawData['total_coins'])) $this->totalCoins = strval($rawData['total_coins']);
        if (isset($rawData['fee_pool'])) $this->feePool = $rawData['fee_pool'];
        if (isset($rawData['base_fee'])) $this->baseFee = $rawData['base_fee'];
        if (isset($rawData['base_reserve'])) $this->baseReserve = $rawData['base_reserve'];
        if (isset($rawData['max_tx_set_size'])) $this->maxTransactionSetSize = $rawData['max_tx_set_size'];
        if (isset($rawData['protocol_version'])) $this->protocolVersion = $rawData['protocol_version'];
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
     * @return string
     */
    public function getPreviousHash()
    {
        return $this->previousHash;
    }

    /**
     * @param string $previousHash
     */
    public function setPreviousHash($previousHash)
    {
        $this->previousHash = $previousHash;
    }

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @param string $sequence
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    }

    /**
     * @return int
     */
    public function getTransactionCount()
    {
        return $this->transactionCount;
    }

    /**
     * @param int $transactionCount
     */
    public function setTransactionCount($transactionCount)
    {
        $this->transactionCount = $transactionCount;
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
     * @return \DateTime
     */
    public function getClosedAt()
    {
        return $this->closedAt;
    }

    /**
     * @param \DateTime $closedAt
     */
    public function setClosedAt($closedAt)
    {
        $this->closedAt = $closedAt;
    }

    /**
     * @return string
     */
    public function getTotalCoins()
    {
        return $this->totalCoins;
    }

    /**
     * @param string $totalCoins
     */
    public function setTotalCoins($totalCoins)
    {
        $this->totalCoins = $totalCoins;
    }

    /**
     * @return string
     */
    public function getFeePool()
    {
        return $this->feePool;
    }

    /**
     * @param string $feePool
     */
    public function setFeePool($feePool)
    {
        $this->feePool = $feePool;
    }

    /**
     * @return int
     */
    public function getBaseFee()
    {
        return $this->baseFee;
    }

    /**
     * @param int $baseFee
     */
    public function setBaseFee($baseFee)
    {
        $this->baseFee = $baseFee;
    }

    /**
     * @return string
     */
    public function getBaseReserve()
    {
        return $this->baseReserve;
    }

    /**
     * @param string $baseReserve
     */
    public function setBaseReserve($baseReserve)
    {
        $this->baseReserve = $baseReserve;
    }

    /**
     * @return int
     */
    public function getMaxTransactionSetSize()
    {
        return $this->maxTransactionSetSize;
    }

    /**
     * @param int $maxTransactionSetSize
     */
    public function setMaxTransactionSetSize($maxTransactionSetSize)
    {
        $this->maxTransactionSetSize = $maxTransactionSetSize;
    }

    /**
     * @return int
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * @param int $protocolVersion
     */
    public function setProtocolVersion($protocolVersion)
    {
        $this->protocolVersion = $protocolVersion;
    }
}