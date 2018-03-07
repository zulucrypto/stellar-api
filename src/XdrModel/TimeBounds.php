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
     * @var \DateTime
     */
    private $minTime;

    /**
     * @var \DateTime
     */
    private $maxTime;

    /**
     * @param \DateTime|null $minTime
     * @param \DateTime|null $maxTime
     * @throws \ErrorException
     */
    public function __construct(\DateTime $minTime = null, \DateTime $maxTime = null)
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
        $this->minTime = clone $min;
    }

    /**
     * @param \DateTime $max
     */
    public function setMaxTime(\DateTime $max)
    {
        $this->maxTime = clone $max;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= XdrEncoder::unsignedInteger64($this->getMinTimestamp());
        $bytes .= XdrEncoder::unsignedInteger64($this->getMaxTimestamp());

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
        $model->minTime = \DateTime::createFromFormat('U', $xdr->readUnsignedInteger64());
        $model->maxTime = \DateTime::createFromFormat('U', $xdr->readUnsignedInteger64());

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
     * @return \DateTime
     */
    public function getMinTime()
    {
        return $this->minTime;
    }

    /**
     * @return \DateTime
     */
    public function getMaxTime()
    {
        return $this->maxTime;
    }

    /**
     * @return int
     */
    public function getMinTimestamp()
    {
        return $this->minTime->format('U');
    }

    /**
     * @return int
     */
    public function getMaxTimestamp()
    {
        return $this->maxTime->format('U');
    }
}