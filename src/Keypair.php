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

    public function __construct($seedString)
    {
        $this->seed = $seedString;
        $this->privateKey = AddressableKey::getRawBytesFromBase32Seed($seedString);
        $this->publicKeyString = AddressableKey::addressFromRawSeed($this->privateKey);
        $this->publicKey = AddressableKey::getRawBytesFromBase32AccountId($this->publicKeyString);
    }

    /**
     * @param $value
     * @return DecoratedSignature
     */
    public function signDecorated($value)
    {
        return new DecoratedSignature(
            $this->getHint(),
            $this->sign($value)
        );
    }

    /**
     * Signs the specified $value with the private key
     *
     * @param $value
     * @return string
     */
    public function sign($value)
    {
        return Ed25519::sign_detached($value, $this->getEd25519SecretKey());
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

    public function getSecret()
    {
        return $this->seed;
    }

    /**
     * @return bool|string
     */
    public function getPrivateKeyBytes()
    {
        return AddressableKey::getRawBytesFromBase32Seed($this->seed);
    }

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

    public function getAccountId()
    {
        return $this->publicKeyString;
    }

    protected function getEd25519SecretKey()
    {
        $pk = '';
        $sk = '';

        Ed25519::seed_keypair($pk, $sk, $this->privateKey);

        return $sk;
    }
}