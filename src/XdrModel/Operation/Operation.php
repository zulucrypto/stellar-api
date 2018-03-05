<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;

/**
 * Known operation types:
 *
    xdr.enum("OperationType", {
        createAccount: 0,
        payment: 1,
        pathPayment: 2,
        manageOffer: 3,
        createPassiveOffer: 4,
        setOption: 5,
        changeTrust: 6,
        allowTrust: 7,
        accountMerge: 8,
        inflation: 9,
        manageDatum: 10,
    });
 *
 * See: https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x
 *
 */
abstract class Operation implements XdrEncodableInterface
{
    const TYPE_CREATE_ACCOUNT       = 0;
    const TYPE_PAYMENT              = 1;
    const TYPE_PATH_PAYMENT         = 2;
    const TYPE_MANAGE_OFFER         = 3;
    const TYPE_CREATE_PASSIVE_OFFER = 4;
    const TYPE_SET_OPTIONS          = 5;
    const TYPE_CHANGE_TRUST         = 6;
    const TYPE_ALLOW_TRUST          = 7;
    const TYPE_ACCOUNT_MERGE        = 8;
    const TYPE_INFLATION            = 9;
    const TYPE_MANAGE_DATA          = 10;

    /**
     * @var AccountId
     */
    protected $sourceAccount;

    /**
     * Type constants are defined by each subclass
     *
     * A full list can be found at: https://www.stellar.org/developers/guides/concepts/list-of-operations.html
     *
     * @var int
     */
    protected $type;

    /**
     * Operation constructor.
     *
     * @param $type int operation type constant
     * @param $sourceAccountId AccountId if null this will default to the source for the transaction
     * @return Operation
     */
    public function __construct($type, $sourceAccountId = null)
    {
        if ($sourceAccountId instanceof Keypair) {
            $sourceAccountId = new AccountId($sourceAccountId->getPublicKey());
        }
        if (is_string($sourceAccountId)) {
            $sourceAccountId = new AccountId($sourceAccountId);
        }

        $this->sourceAccount = $sourceAccountId;
        $this->type = $type;

        return $this;
    }

    /**
     * Child classes MUST call this method to get the header for the operation
     * and then append their body
     *
     * @return string
     */
    public function toXdr()
    {
        $bytes = '';

        // Source Account
        $bytes .= XdrEncoder::optional($this->sourceAccount);

        // Type
        $bytes .= XdrEncoder::unsignedInteger($this->type);

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        /** @var Operation $model */
        $model = null;
        $hasSourceAccount = $xdr->readBoolean();

        $sourceAccount = null;
        if ($hasSourceAccount) {
            $sourceAccount = AccountId::fromXdr($xdr);
        }

        $type = $xdr->readUnsignedInteger();

        switch ($type) {
            case Operation::TYPE_CREATE_ACCOUNT:
                $model = CreateAccountOp::fromXdr($xdr);
                break;
            case Operation::TYPE_PAYMENT:
                $model = PaymentOp::fromXdr($xdr);
                break;
            case Operation::TYPE_PATH_PAYMENT:
                $model = PathPaymentOp::fromXdr($xdr);
                break;
            case Operation::TYPE_MANAGE_OFFER:
                $model = ManageOfferOp::fromXdr($xdr);
                break;
            case Operation::TYPE_CREATE_PASSIVE_OFFER:
                $model = CreatePassiveOfferOp::fromXdr($xdr);
                break;
            case Operation::TYPE_SET_OPTIONS:
                $model = SetOptionsOp::fromXdr($xdr);
                break;
            case Operation::TYPE_CHANGE_TRUST:
                $model = ChangeTrustOp::fromXdr($xdr);
                break;
            case Operation::TYPE_ALLOW_TRUST:
                $model = AllowTrustOp::fromXdr($xdr);
                break;
            case Operation::TYPE_ACCOUNT_MERGE:
                $model = AccountMergeOp::fromXdr($xdr);
                break;
            case Operation::TYPE_INFLATION:
                $model = new InflationOp(); // no additional XDR to parse
                break;
            case Operation::TYPE_MANAGE_DATA:
                $model = ManageDataOp::fromXdr($xdr);
                break;
            default:
                throw new \InvalidArgumentException(sprintf('unrecognized operation type %s', $type));
        }

        $model->sourceAccount = $sourceAccount;

        return $model;
    }

    /**
     * @return AccountId
     */
    public function getSourceAccount()
    {
        return $this->sourceAccount;
    }

    /**
     * @param AccountId $sourceAccount
     */
    public function setSourceAccount(AccountId $sourceAccount)
    {
        $this->sourceAccount = $sourceAccount;
    }
}