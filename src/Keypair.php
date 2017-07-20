<?php


namespace ZuluCrypto\StellarSdk;


use ParagonIE\Sodium\Core\Ed25519;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\XdrModel\DecoratedSignature;

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

    public static function newFromRandom()
    {
        return self::newFromRawSeed(random_bytes(32));
    }

    public static function newFromSeed($base32String)
    {
        return new Keypair($base32String);
    }

    public static function newFromRawSeed($rawSeed)
    {
        $seedString = AddressableKey::seedFromRawBytes($rawSeed);

        return new Keypair($seedString);
    }

    public function __construct($seedString)
    {
        $this->seed = $seedString;
        $this->privateKey = AddressableKey::getRawBytesFromBase32Seed($seedString);
        $this->publicKeyString = AddressableKey::addressFromRawSeed($this->privateKey);
        $this->publicKey = AddressableKey::getRawBytesFromBase32AccountId($this->publicKeyString);
    }

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

    public function getPublicKey()
    {
        return $this->publicKeyString;
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