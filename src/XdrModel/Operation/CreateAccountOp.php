<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;

use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

/**
 * Creates and funds a new account
 *
 * See: https://www.stellar.org/developers/guides/concepts/list-of-operations.html#create-account
 */
class CreateAccountOp extends Operation
{
    /**
     * @var AccountId
     */
    protected $newAccount;

    /**
     * @var StellarAmount
     */
    protected $startingBalance;

    /**
     * @param AccountId      $newAccount
     * @param int|BigInteger $startingBalance int representing lumens or BigInteger representing stroops
     * @param null|string|Keypair|AccountId $sourceAccountId
     * @throws \ErrorException
     */
    public function __construct(AccountId $newAccount, $startingBalance, $sourceAccount = null)
    {
        if ($sourceAccount instanceof Keypair) {
            $sourceAccount = new AccountId($sourceAccount->getPublicKey());
        }
        if (is_string($sourceAccount)) {
            $sourceAccount = new AccountId($sourceAccount);
        }

        parent::__construct( Operation::TYPE_CREATE_ACCOUNT, $sourceAccount);

        $this->newAccount = $newAccount;
        $this->startingBalance = new StellarAmount($startingBalance);
    }

    /**
     * @return string XDR bytes
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        $bytes .= $this->newAccount->toXdr();
        $bytes .= XdrEncoder::signedBigInteger64($this->startingBalance->getUnscaledBigInteger());

        return $bytes;
    }
}