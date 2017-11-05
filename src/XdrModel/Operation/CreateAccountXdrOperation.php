<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

class CreateAccountXdrOperation extends Operation
{
    /**
     * @var AccountId
     */
    protected $newAccount;

    /**
     * @var int
     */
    protected $startingBalance;

    /**
     * @param AccountId $newAccount
     * @param           $startingBalance
     */
    public function __construct(AccountId $sourceAccount, AccountId $newAccount, $startingBalance)
    {
        parent::__construct( Operation::TYPE_CREATE_ACCOUNT, $sourceAccount);

        $this->newAccount = $newAccount;
        $this->startingBalance = $startingBalance;
    }

    /**
     * @return string XDR bytes
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->newAccount->toXdr();
        $bytes .= XdrEncoder::integer64RawBytes($this->startingBalance);

        return $bytes;
    }
}