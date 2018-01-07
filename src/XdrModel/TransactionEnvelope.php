<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Util\Hash;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\Type\VariableArray;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

class TransactionEnvelope implements XdrEncodableInterface
{
    const TYPE_SCP  = 1;
    const TYPE_TX   = 2;
    const TYPE_AUTH = 3;

    /**
     * @var TransactionBuilder[]
     */
    private $transactionBuilder;

    /**
     * @var VariableArray of DecoratedSignature
     */
    private $signatures;

    public function __construct(TransactionBuilder $transactionBuilder)
    {
        $this->transactionBuilder = $transactionBuilder;
        $this->signatures = new VariableArray();

        return $this;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= $this->transactionBuilder->toXdr();
        $bytes .= $this->signatures->toXdr();

        return $bytes;
    }

    /**
     * Returns the hash of the transaction envelope
     *
     * This hash is what is signed
     *
     * @return string
     */
    public function getHash()
    {
        return $this->transactionBuilder->hash();
    }

    public function sign($secretKeyStrings)
    {
        if (!is_array($secretKeyStrings)) $secretKeyStrings = [$secretKeyStrings];

        foreach ($secretKeyStrings as $secretKeyString) {
            $transactionHash = $this->transactionBuilder->hash();

            $keypair = Keypair::newFromSeed($secretKeyString);

            $decorated = $keypair->signDecorated($transactionHash);
            $this->signatures->append($decorated);
        }

        return $this;
    }

    public function addSignature($signatureBytes, $hint = null)
    {
        $decorated = new DecoratedSignature($hint, $signatureBytes);

        $this->signatures->append($decorated);
    }

    /**
     * @param DecoratedSignature $decoratedSignature
     */
    public function addDecoratedSignature(DecoratedSignature $decoratedSignature)
    {
        $this->signatures->append($decoratedSignature);
    }
}