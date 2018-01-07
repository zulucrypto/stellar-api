<?php


namespace ZuluCrypto\StellarSdk\Signing;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\XdrModel\DecoratedSignature;

class TrezorSigner implements SigningInterface
{
    /**
     * Account index to use when deriving the public key and signing
     *
     * @var int
     */
    protected $accountIndex;

    /**
     * Path to the trezorctl binary, defaults to 'trezorctl
     *
     * @var string
     */
    protected $trezorBinPath;

    /**
     * If known, the bytes of the public key
     *
     * This is used to generate the decorated signature and prevents an additional
     * request for the user's public key
     *
     * @var string
     */
    protected $publickKeyBytes;

    /**
     * @param int $accountIndex
     */
    public function __construct($accountIndex = 0)
    {
        $this->accountIndex = $accountIndex;

        $this->trezorBinPath = 'trezorctl';
    }

    public function signTransaction(TransactionBuilder $builder)
    {
        $xdr = $builder
            ->getTransactionEnvelope()
            ->toXdr();

        $networkPassphrase = $builder->getApiClient()->getNetworkPassphrase();

        $cmd = sprintf('%s stellar_sign_transaction -a %s -n %s %s',
            $this->trezorBinPath,
            escapeshellarg(($this->accountIndex + 1)),
            escapeshellarg($networkPassphrase),
            escapeshellarg(base64_encode($xdr))
        );

        $output = [];
        $retval = null;
        $signatureb64 = exec($cmd, $output, $retval);

        // convert signature to raw bytes
        $signatureBytes = base64_decode($signatureb64);

        // Convert to DecoratedSignature
        $hint = substr($this->publickKeyBytes, -4);
        return new DecoratedSignature($hint, $signatureBytes);
    }

    public function setPublicKey(Keypair $keypair)
    {
        $this->publickKeyBytes = $keypair->getPublicKeyBytes();
    }

    /**
     * @return string
     */
    public function getTrezorBinPath()
    {
        return $this->trezorBinPath;
    }

    /**
     * @param string $trezorBinPath
     */
    public function setTrezorBinPath($trezorBinPath)
    {
        $this->trezorBinPath = $trezorBinPath;
    }
}