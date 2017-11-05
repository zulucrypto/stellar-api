<?php


namespace ZuluCrypto\StellarSdk\Transaction;

use ZuluCrypto\StellarSdk\Horizon\ApiClient;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\Type\VariableArray;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Memo;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;
use ZuluCrypto\StellarSdk\XdrModel\TimeBounds;
use ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;


/**
 * todo: rename to Transaction
 * Helper class to build a transaction on the Stellar network
 *
 * References:
 *  Debugging / testing:
 *      https://www.stellar.org/laboratory/
 *
 *  Retrieve fee information from:
 *      https://www.stellar.org/developers/horizon/reference/endpoints/ledgers-single.html
 *      https://www.stellar.org/developers/horizon/reference/resources/ledger.html
 *
 * Notes:
 *  - Per-operation fee is 100 stroops (0.00001 XLM)
 *  - Base reserve is 10 XLM
 *      - Minimum balance for an account is base reserve * 2
 *      - Each additional trustline, offer, signer, and data entry requires another 10 XLM
 *
 *
 * Format of a transaction:
 *  Source Address (AddressId)
 *      type
 *      address
 *  Fee (Uint32)
 *  Next sequence number (SequenceNumber - uint64)
 *      ...
 *  Time bounds (TimeBounds)
 *  Memo (Memo)
 *  Operations (Operation[])
 *  ext (TransactionExt) - extra? currently is a union with no arms
 */
class TransactionBuilder implements XdrEncodableInterface
{
    /**
     * Base-32 account ID
     *
     * @var AccountId
     */
    private $accountId;

    /**
     * @var TimeBounds
     */
    private $timeBounds;

    /**
     * @var Memo
     */
    private $memo;

    /**
     * @var VariableArray[]
     */
    private $operations;

    /**
     * Horizon API client, used for retrieving sequence numbers and validating
     * transaction
     *
     * @var ApiClient
     */
    private $apiClient;

    /**
     * TransactionBuilder constructor.
     *
     * @param $sourceAccountId
     * @return TransactionBuilder
     */
    public function __construct($sourceAccountId)
    {
        $this->accountId = new AccountId($sourceAccountId);

        $this->timeBounds = new TimeBounds();
        $this->memo = new Memo(Memo::MEMO_TYPE_NONE);
        $this->operations = new VariableArray();

        return $this;
    }

    /**
     * @return TransactionEnvelope
     */
    public function getTransactionEnvelope()
    {
        return new TransactionEnvelope($this);
    }

    /**
     * @param $secretKeyString
     * @return TransactionEnvelope
     */
    public function sign($secretKeyString)
    {
        return (new TransactionEnvelope($this))->sign($secretKeyString);
    }

    public function hash()
    {
        return $this->apiClient->hash($this);
    }

    public function getHashAsString()
    {
        return $this->apiClient->getHashAsString($this);
    }

    /**
     * @param $secretKeyString
     * @return \ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse
     */
    public function submit($secretKeyString)
    {
        return $this->apiClient->submitTransaction($this, $secretKeyString);
    }

    public function getFee()
    {
        // todo: calculate real fee
        return 100;
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = '';

        // Account ID
        $bytes .= $this->accountId->toXdr();
        // Fee
        $bytes .= XdrEncoder::unsignedInteger($this->getFee());
        // Sequence number
        $bytes .= XdrEncoder::unsignedInteger64($this->generateSequenceNumber());
        // Time Bounds
        $bytes .= $this->timeBounds->toXdr();
        // Memo
        $bytes .= $this->memo->toXdr();

        // Operations
        $bytes .= $this->operations->toXdr();

        // TransactionExt (not used? encoded as an empty union)
        $bytes .= XdrEncoder::unsignedInteger(0);

        return $bytes;
    }

    /**
     * @param $operation
     * @return TransactionBuilder
     */
    public function addOperation($operation)
    {
        $this->operations->append($operation);

        return $this;
    }

    protected function generateSequenceNumber()
    {
        $this->ensureApiClient();

        return $this->apiClient
                ->getAccount($this->accountId->getAccountIdString())
                ->getSequence() + 1
        ;
    }

    protected function ensureApiClient()
    {
        if (!$this->apiClient) throw new \ErrorException("An API client is required, call setApiClient before using this method");
    }

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     * @return TransactionBuilder
     */
    public function setApiClient($apiClient)
    {
        $this->apiClient = $apiClient;

        return $this;
    }
}