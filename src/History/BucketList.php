<?php


namespace ZuluCrypto\StellarSdk\History;


/**
 * See: https://github.com/stellar/stellar-core/blob/master/src/bucket/BucketList.h
 */
class BucketList
{
    /**
     * @var BucketLevel[]
     */
    protected $bucketLevels;

    public function __construct()
    {
        $this->bucketLevels = [];
    }

    /**
     * @param      $raw
     * @param null $level defaults to the next unoccupied level
     * @return null|BucketLevel
     */
    public function addLevelFromRaw($raw, $level = null)
    {
        if ($level === null) {
            $level = count($this->bucketLevels);
        }

        $bucketLevel = BucketLevel::fromRaw($raw, $level);

        $this->bucketLevels[] = $bucketLevel;

        return $bucketLevel;
    }

    /**
     * @return array|string[] sha256 bucket hashes
     */
    public function getUniqueBucketHashes()
    {
        $hashes = [];

        foreach ($this->bucketLevels as $bucketLevel) {
            $hashes = array_merge($hashes, $bucketLevel->getUniqueBucketHashes());
        }

        return array_values(array_unique($hashes));
    }
}