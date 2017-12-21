<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

class AccountMergeOp extends Operation
{
    /**
     * @var AccountId
     */
    protected $destination;

    public function __construct($destinationAccountId, $sourceAccountId = null)
    {
        parent::__construct(Operation::TYPE_ACCOUNT_MERGE, $sourceAccountId);

        if ($destinationAccountId instanceof Keypair) {
            $destinationAccountId = $destinationAccountId->getPublicKey();
        }

        $this->destination = new AccountId($destinationAccountId);
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Destination account
        $bytes .= $this->destination->toXdr();

        return $bytes;
    }

    /**
     * @return AccountId
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param AccountId $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }
}