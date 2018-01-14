<?php


namespace ZuluCrypto\StellarSdk;


use ParagonIE\Sodium\Core\Ed25519;
use ZuluCrypto\StellarSdk\Derivation\Bip39\Bip39;
use ZuluCrypto\StellarSdk\Derivation\HdNode;
use ZuluCrypto\StellarSdk\XdrModel\DecoratedSignature;

/**
 * A public/private keypair for use with the Stellar network
 */
class Keypair
{

    /**
     * Base-32 encoded seed
     *
     * @var string
     */
    private $seed;

    /**
     * Bytes of the private key
     *
     * @var string
     */
    private $privateKey;

    /**
     * Base-32 public key
     *
     * @var string
     */
    private $publicKeyString;

    /**
     * Bytes of the public key
     *
     * @var string
     */
    private $publicKey;

    /**
     * Creates a new random keypair
     *
     * @return Keypair
     */
    public static function newFromRandom()
    {
        return self::newFromRawSeed(random_bytes(32));
    }

    /**
     * Creates a new keypair from a base-32 encoded string (S...)
     *
     * @param $base32String
     * @return Keypair
     */
    public static function newFromSeed($base32String)
    {
        return new Keypair($base32String);
    }

    /**
     * Creates a new keypair from 32 bytes of entropy
     *
     * @param $rawSeed (32-byte string)
     * @return Keypair
     */
    public static function newFromRawSeed($rawSeed)
    {
        $seedString = AddressableKey::seedFromRawBytes($rawSeed);

        return new Keypair($seedString);
    }

    /**
     * @param $base32String
     * @return Keypair
     */
    public static function newFromPublicKey($base32String)
    {
        $keypair = new Keypair();
        $keypair->setPublicKey($base32String);

        return $keypair;
    }

    /**
     * Creates a new keypair from a mnemonic, passphrase (optional) and index (defaults to 0)
     *
     * For more details, see https://github.com/stellar/stellar-protocol/blob/master/ecosystem/sep-0005.md
     *
     * @param        $mnemonic
     * @param string $passphrase
     * @param int    $index
     * @return Keypair
     */
    public static function newFromMnemonic($mnemonic, $passphrase = '', $index = 0)
    {
        $bip39 = new Bip39();
        $seedBytes = $bip39->mnemonicToSeedBytesWithErrorChecking($mnemonic, $passphrase);

        $masterNode = HdNode::newMasterNode($seedBytes);

        $accountNode = $masterNode->derivePath(sprintf("m/44'/148'/%s'", $index));

        return static::newFromRawSeed($accountNode->getPrivateKeyBytes());
    }

    public function __construct($seedString = null)
    {
        if ($seedString) {
            $this->setSeed($seedString);
        }
    }

    /**
     * @param $value
     * @return DecoratedSignature
     */
    public function signDecorated($value)
    {
        $this->requirePrivateKey();

        return new DecoratedSignature(
            $this->getHint(),
            $this->sign($value)
        );
    }

    /**
     * Signs the specified $value with the private key
     *
     * @param $value
     * @return string - raw bytes representing the signature
     */
    public function sign($value)
    {
        $this->requirePrivateKey();

        return Ed25519::sign_detached($value, $this->getEd25519SecretKey());
    }

    /**
     * @param $signature
     * @param $message
     * @return bool
     * @throws \Exception
     */
    public function verifySignature($signature, $message)
    {
        return Ed25519::verify_detached($signature, $message, $this->publicKey);
    }

    /**
     * @param $base32String string GABC...
     */
    public function setPublicKey($base32String)
    {
        // Clear out all private key fields
        $this->privateKey = null;

        $this->publicKey = AddressableKey::getRawBytesFromBase32AccountId($base32String);
        $this->publicKeyString = $base32String;
    }

    /**
     * @param $base32SeedString
     */
    public function setSeed($base32SeedString)
    {
        $this->seed = $base32SeedString;
        $this->privateKey = AddressableKey::getRawBytesFromBase32Seed($base32SeedString);
        $this->publicKeyString = AddressableKey::addressFromRawSeed($this->privateKey);
        $this->publicKey = AddressableKey::getRawBytesFromBase32AccountId($this->publicKeyString);
    }

    /**
     * Returns the last 4 characters of the public key
     *
     * @return string
     */
    public function getHint()
    {
        return substr($this->publicKey, -4);
    }

    /**
     * Returns the base-32 encoded private key (S...)
     * @return string
     * @throws \ErrorException
     */
    public function getSecret()
    {
        $this->requirePrivateKey();

        return $this->seed;
    }

    /**
     * @return bool|string
     * @throws \ErrorException
     */
    public function getPrivateKeyBytes()
    {
        $this->requirePrivateKey();

        return AddressableKey::getRawBytesFromBase32Seed($this->seed);
    }

    /**
     * Returns the base-32 encoded public key (G...)
     * @return string
     */
    public function getPublicKey()
    {
        return $this->publicKeyString;
    }

    /**
     * @return string
     */
    public function getPublicKeyBytes()
    {
        return $this->publicKey;
    }

    /**
     * Returns the base-32 encoded public key (G...)
     * @return string
     */
    public function getAccountId()
    {
        return $this->publicKeyString;
    }

    /**
     * Used to ensure the private key has been specified for this keypair
     *
     * @throws \ErrorException
     */
    protected function requirePrivateKey()
    {
        if (!$this->privateKey) throw new \ErrorException('Private key is required to perform this operation');
    }

    protected function getEd25519SecretKey()
    {
        $this->requirePrivateKey();

        $pk = '';
        $sk = '';

        Ed25519::seed_keypair($pk, $sk, $this->privateKey);

        return $sk;
    }
}