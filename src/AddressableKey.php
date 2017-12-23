<?php


namespace ZuluCrypto\StellarSdk;

use Base32\Base32;
use ParagonIE\Sodium\Core\Ed25519;
use ZuluCrypto\StellarSdk\Util\Checksum;
use ZuluCrypto\StellarSdk\Util\Debug;


/**
 * Implements a string key that can be used in various locations in the Stellar
 * network.
 *
 * See class constants for items that can be referenced
 */
class AddressableKey
{
    // Encoded stellar addresses (base-32 encodes to 'G...')
    const VERSION_BYTE_ACCOUNT_ID = 6 << 3;

    // Encoded stellar seeds (base-32 encodes to 'S...')
    const VERSION_BYTE_SEED = 18 << 3;

    // Encoded stellar hashTx (base-32 encodes to 'T...')
    const VERSION_BYTE_HASH_TX = 19 << 3;

    // Encoded stellar hashX (base-32 encodes to 'X...')
    const VERSION_BYTE_HASH_X = 23 << 3;

    public static function seedFromRawBytes($rawBytes)
    {
        if (strlen($rawBytes) != 32) throw new \InvalidArgumentException('$rawBytes must be 32 bytes');

        // Must be interpreted as a byte
        $version = pack('C', self::VERSION_BYTE_SEED);

        $payload = $rawBytes;

        $checksum = Checksum::generate($version . $payload);

        $seedString = Base32::encode($version . $payload . $checksum);

        return $seedString;
    }

    public static function addressFromRawSeed($rawBytes)
    {
        $version = pack('C', self::VERSION_BYTE_ACCOUNT_ID);

        $payload = Ed25519::publickey_from_secretkey($rawBytes);

        $checksum = Checksum::generate($version . $payload);

        return Base32::encode($version . $payload . $checksum);
    }

    /**
     * Converts $rawBytes into a public account ID (G...)
     * 
     * @param $rawBytes
     * @return string
     */
    public static function addressFromRawBytes($rawBytes)
    {
        $version = pack('C', self::VERSION_BYTE_ACCOUNT_ID);

        $payload = $rawBytes;

        $checksum = Checksum::generate($version . $payload);

        return Base32::encode($version . $payload . $checksum);
    }

    public static function getRawBytesFromBase32Seed($base32Seed)
    {
        $decoded = Base32::decode($base32Seed);

        // Unpack version byte
        $unpacked = unpack('Cversion', substr($decoded, 0, 1));
        $version = $unpacked['version'];

        $payload = substr($decoded, 1, strlen($decoded) - 3);
        $checksum = substr($decoded, -2);

        // Verify version
        if ($version != self::VERSION_BYTE_SEED) {
            throw new \InvalidArgumentException('Invalid version');
        }
        // Verify checksum of version + payload
        if (!Checksum::verify($checksum, substr($decoded, 0, -2))) {
            throw new \InvalidArgumentException('Invalid checksum');
        }

        return $payload;
    }

    public static function getRawBytesFromBase32AccountId($base32AccountId)
    {
        $decoded = Base32::decode($base32AccountId);

        // Unpack version byte
        $unpacked = unpack('Cversion', substr($decoded, 0, 1));
        $version = $unpacked['version'];

        $payload = substr($decoded, 1, strlen($decoded) - 3);
        $checksum = substr($decoded, -2);

        // Verify version
        if ($version != self::VERSION_BYTE_ACCOUNT_ID) {
            $msg  = 'Invalid account ID version.';
            if ($version == self::VERSION_BYTE_SEED) {
                $msg .= ' Got a private key and expected a public account ID';
            }
            throw new \InvalidArgumentException($msg);
        }
        // Verify checksum of version + payload
        if (!Checksum::verify($checksum, substr($decoded, 0, -2))) {
            throw new \InvalidArgumentException('Invalid checksum');
        }

        return $payload;
    }
}