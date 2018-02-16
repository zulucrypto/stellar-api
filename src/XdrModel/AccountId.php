<?php

namespace ZuluCrypto\StellarSdk\XdrModel;

use ZuluCrypto\StellarSdk\AddressableKey;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrDecoder;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 * This is the same thing as a PublicKey
 *
 * publicKeyTypeEd25519 - 0
 *
 * Example encoding of publicKeyTypeEd25519 / GCN2K2HG53AWX2SP5UHRPMJUUHLJF2XBTGSXROTPWRGAYJCDDP63J2U6
 *
 * Public key is encoded as a Uint256 (32-bytes)
 *
 * 4 bytes: union type
 * 32 bytes: public key
 *
 * 00 00 00 00 9b a5 68 e6  ee c1 6b ea 4f ed 0f 17
 * b1 34 a1 d6 92 ea e1 99  a5 78 ba 6f b4 4c 0c 24
 * 43 1b fd b4
 */
class AccountId implements XdrEncodableInterface
{
    const KEY_TYPE_ED25519 = 0;

    /**
     * Base32-encoded account ID (G...)
     *
     * @var string
     */
    private $accountIdString;

    /**
     * Binary representation of account ID
     *
     * @var string
     */
    private $accountIdBytes;

    /**
     * Always 0
     * @var int
     */
    private $keyType = 0;

    /**
     * @param string|Keypair $accountIdString
     */
    public function __construct($accountIdString)
    {
        if ($accountIdString instanceof Keypair) {
            $accountIdString = $accountIdString->getPublicKey();
        }

        $this->accountIdString = $accountIdString;
        $this->accountIdBytes = AddressableKey::getRawBytesFromBase32AccountId($accountIdString);

        $this->keyType = 0;
    }

    /**
     * @return string|Keypair
     */
    public function getAccountIdString()
    {
        return $this->accountIdString;
    }

    public function toXdr()
    {
        $bytes = "";

        $bytes .= XdrEncoder::unsignedInteger(self::KEY_TYPE_ED25519);
        $bytes .= XdrEncoder::opaqueFixed($this->accountIdBytes);

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return AccountId
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $keyType = $xdr->readUnsignedInteger();
        $accountIdBytes = $xdr->readOpaqueFixed(32);
        $accountIdString = AddressableKey::addressFromRawBytes($accountIdBytes);

        $model = new AccountId($accountIdString);

        $model->keyType = $keyType;

        return $model;
    }

    /**
     * @return int
     */
    public function getKeyType()
    {
        return $this->keyType;
    }
}