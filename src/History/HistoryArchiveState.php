<?php


namespace ZuluCrypto\StellarSdk\History;

use ZuluCrypto\StellarSdk\Util\Json;


/**
 * Models the HistoryArchiveState for a given ledger
 *
 * References:
 *  https://github.com/stellar/stellar-core/blob/master/docs/history.md#history-archive-state-has-files
 */
class HistoryArchiveState
{
    /**
     * File version
     *
     * @var string
     */
    protected $version;

    /**
     * Software that wrote the file
     *
     * @var string
     */
    protected $server;

    /**
     * The ledger number this HAS file describes
     *
     * @var int
     */
    protected $currentLedger;

    /**
     *
     * @var BucketList
     */
    protected $currentBuckets;

    /**
     * @param $raw
     * @return HistoryArchiveState
     */
    public static function fromRaw($raw)
    {
        $object = new HistoryArchiveState();
        $object->version = $raw['version'];
        $object->server = $raw['server'];
        $object->currentLedger = $raw['currentLedger'];

        foreach ($raw['currentBuckets'] as $rawBucketLevel) {
            $object->addBucketFromRaw($rawBucketLevel);
        }

        return $object;
    }

    /**
     * @param $path
     * @return HistoryArchiveState
     */
    public static function fromFile($path)
    {
        try {
            return static::fromRaw(Json::mustDecode(file_get_contents($path)));
        } catch (\InvalidArgumentException $e) {
            throw new \ErrorException(sprintf('Error decoding json in %s: %s', $path, $e->getMessage()));
        }
    }

    public function __construct()
    {
        $this->currentBuckets = new BucketList();
    }

    /**
     * @param      $raw
     * @param null $level
     * @return null|BucketLevel
     */
    public function addBucketFromRaw($raw, $level = null)
    {
        return $this->currentBuckets->addLevelFromRaw($raw, $level);
    }

    /**
     * @return array|string[] sha256 hashes
     */
    public function getUniqueBucketHashes()
    {
        return $this->currentBuckets->getUniqueBucketHashes();
    }
}