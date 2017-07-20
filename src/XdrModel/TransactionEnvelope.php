<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\Type\VariableArray;

class TransactionEnvelope implements XdrEncodableInterface
{
    const TYPE_SCP  = 1;
    const TYPE_TX   = 2;
    const TYPE_AUTH = 3;

    /**
     * @var TransactionBuilder[]
     */
    private $transaction;

    /**
     * @var VariableArray of DecoratedSignature
     */
    private $signatures;

    public function __construct(TransactionBuilder $transactionBuilder)
    {
        $this->transaction = $transactionBuilder;
        $this->signatures = new VariableArray();

        return $this;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= $this->transaction->toXdr();
        $bytes .= $this->signatures->toXdr();

        return $bytes;
    }

    public function sign($secretKeyStrings)
    {
        if (!is_array($secretKeyStrings)) $secretKeyStrings = [$secretKeyStrings];

        foreach ($secretKeyStrings as $secretKeyString) {
            $transactionHash = $this->transaction->hash();

            $keypair = Keypair::newFromSeed($secretKeyString);

            $decorated = $keypair->signDecorated($transactionHash);
            $this->signatures->append($decorated);
        }

        return $this;
    }
}