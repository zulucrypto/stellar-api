<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\AddressableKey;
use ZuluCrypto\StellarSdk\Util\MathSafety;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 * Represents a time range
 *
 * Optional Struct with fields:
 *  minTime Uint64
 *  maxTime Uint64
 *
 */
class TimeBounds
{
    /**
     * @var int 64-bit unsigned
     */
    private $minTime;

    /**
     * @var int 64-bit unsigned
     */
    private $maxTime;

    /**
     * @param null|number $minTime 64-bit unix timestamp
     * @param null|number $maxTime 64-bit unix timestamp
     * @throws \ErrorException
     */
    public function __construct($minTime = null, $maxTime = null)
    {
        MathSafety::require64Bit();

        $this->minTime = $minTime;
        $this->maxTime = $maxTime;
    }

    /**
     * @param \DateTime $min
     */
    public function setMinTime(\DateTime $min)
    {
        $this->minTime = $min->format('U');
    }

    /**
     * @param \DateTime $max
     */
    public function setMaxTime(\DateTime $max)
    {
        $this->maxTime = $max->format('U');
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= XdrEncoder::unsignedInteger64($this->minTime);
        $bytes .= XdrEncoder::unsignedInteger64($this->maxTime);

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return null|TimeBounds
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new TimeBounds();
        $model->minTime = $xdr->readUnsignedInteger64();
        $model->maxTime = $xdr->readUnsignedInteger64();

        return $model;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return $this->minTime === null && $this->maxTime === null;
    }

    /**
     * @return int
     */
    public function getMinTimestamp()
    {
        return $this->minTime;
    }

    /**
     * @return int
     */
    public function getMaxTimestamp()
    {
        return $this->maxTime;
    }
}