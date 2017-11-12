<?php


namespace ZuluCrypto\StellarSdk\Derivation;


use ZuluCrypto\StellarSdk\Util\MathSafety;

/**
 * A Hierarchical Deterministic node for use with Stellar
 *
 */
class HdNode
{
    const HARDENED_MINIMUM_INDEX = 0x80000000;

    /**
     * @var string
     */
    protected $privateKeyBytes;

    /**
     * @var string
     */
    protected $chainCodeBytes;

    /**
     * Returns a new master node that can be used to derive subnodes
     *
     * @param $entropy
     * @return HdNode
     */
    public static function newMasterNode($entropy)
    {
        $hmac = hash_hmac('sha512', $entropy, 'ed25519 seed', true);

        return new HdNode(
            substr($hmac, 0, 32),
            substr($hmac, 32, 32)
        );
    }

    /**
     * HdNode constructor.
     *
     * @param $privateKeyBytes (string) 32 bytes of randomly generated data for the private key
     * @param $chainCodeBytes (string) 32 bytes of randomly generated data for deriving additional keys
     */
    public function __construct($privateKeyBytes, $chainCodeBytes)
    {
        MathSafety::require64Bit();

        if (strlen($privateKeyBytes) != 32) throw new \InvalidArgumentException('Private key must be 32 bytes');
        if (strlen($chainCodeBytes) != 32) throw new \InvalidArgumentException('Chain code must be 32 bytes');

        $this->privateKeyBytes = $privateKeyBytes;
        $this->chainCodeBytes = $chainCodeBytes;
    }

    /**
     * @param $index int automatically converted to a hardened index
     * @return HdNode
     */
    public function derive($index)
    {
        $index = intval($index) + intval(static::HARDENED_MINIMUM_INDEX);
        if ($index < static::HARDENED_MINIMUM_INDEX) throw new \InvalidArgumentException('Only hardened indexes are supported');

        // big-endian unsigned long (4 bytes)
        $indexBytes = pack('N', $index);
        $key = pack('C', 0x00) . $this->privateKeyBytes . $indexBytes;

        $hmac = hash_hmac('sha512', $key, $this->chainCodeBytes, true);

        return new HdNode(
            substr($hmac, 0, 32),
            substr($hmac, 32, 32)
        );
    }

    /**
     * Derives a path like m/0'/1'
     * @param $path
     * @return HdNode
     */
    public function derivePath($path)
    {
        $pathParts = $this->parseDerivationPath($path);

        $derived = $this;
        foreach ($pathParts as $index) {
            $derived = $derived->derive($index);
        }

        return $derived;
    }

    /**
     * Takes a path like "m/0'/1'" and returns an array of indexes to derive
     *
     * Note that since this class assumes all indexes are hardened, the returned
     * array for the above example would be:
     *  [0, 1]
     *
     * @param $path
     * @return array
     */
    protected function parseDerivationPath($path)
    {
        $parsed = [];
        $parts = explode('/', $path);
        if (strtolower($parts[0]) != 'm') throw new \InvalidArgumentException('Path must start with "m"');

        // Remove initial 'm' since it refers to the current HdNode
        array_shift($parts);

        // Add each part to the return value
        foreach ($parts as $part) {
            // Each subsequent node must be hardened
            if (strpos($part, "'") != (strlen($part)-1)) throw new \InvalidArgumentException('Path can only contain hardened indexes');
            $part = str_replace("'", '', $part);

            if (!is_numeric($part)) throw new \InvalidArgumentException('Path must be numeric');

            $parsed[] = intval($part);
        }

        return $parsed;
    }

    /**
     * @return string
     */
    public function getPrivateKeyBytes()
    {
        return $this->privateKeyBytes;
    }

    /**
     * @return string
     */
    public function getChainCodeBytes()
    {
        return $this->chainCodeBytes;
    }
}