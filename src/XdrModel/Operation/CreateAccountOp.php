<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;

use ZuluCrypto\StellarSdk\Model\AssetAmount;
use ZuluCrypto\StellarSdk\Util\MathSafety;
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
     * @var int
     */
    protected $startingBalance;

    /**
     * @param AccountId      $newAccount
     * @param                $startingBalance
     * @param AccountId|null $sourceAccount
     * @throws \ErrorException
     */
    public function __construct(AccountId $newAccount, $startingBalance, AccountId $sourceAccount = null)
    {
        MathSafety::require64Bit();

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
        $bytes .= XdrEncoder::signedInteger64($this->startingBalance * AssetAmount::ASSET_SCALE);

        return $bytes;
    }
}