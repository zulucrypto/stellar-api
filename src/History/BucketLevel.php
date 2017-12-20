<?php


namespace ZuluCrypto\StellarSdk\History;


/**
 * State documentation available at:
 *  https://github.com/stellar/stellar-core/blob/master/docs/history.md#history-archive-state-has-files
 *
 * Overview of the bucket format available at:
 *  https://github.com/stellar/stellar-core/blob/master/src/bucket/BucketList.h
 *
 */
class BucketLevel
{
    const HASH_EMPTY = '0000000000000000000000000000000000000000000000000000000000000000';

    // Future bucket that hasn't been populated yet
    const STATE_PENDING         = 0;
    //
    const STATE_MERGED          = 1;
    const STATE_MERGE_PENDING   = 2;

    /**
     *
     * @var int
     */
    protected $level;

    /**
     * Current bucket for this level
     *
     * @var string sha256
     */
    protected $curr;

    /**
     * "snap" bucket for this level
     *
     * @var string sha256
     */
    protected $snap;

    /**
     * Information about the future bucket for this level. An array with the
     * following keys:
     *  state: see STATE_ constants
     *  output: todo
     *
     * @var string sha256
     */
    protected $next;

    public static function fromRaw($raw, $level)
    {
        $object = new BucketLevel($level);

        // Only "next" buckets have a state
        $object->curr = $raw['curr'];
        $object->snap = $raw['snap'];
        $object->next = $raw['next'];

        return $object;
    }

    public function __construct($level)
    {
        $this->level = $level;
    }

    /**
     * Returns an array of unique sha256 hashes in this bucket level
     *
     * @return array
     */
    public function getUniqueBucketHashes()
    {
        $hashes = [];

        if ($this->curr != self::HASH_EMPTY) $hashes[] = $this->curr;
        if ($this->snap != self::HASH_EMPTY) $hashes[] = $this->snap;

        return array_values(array_unique($hashes));
    }
}