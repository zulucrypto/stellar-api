<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TimeBounds;

class TimeBoundsTest extends TestCase
{
    public function testTimeBoundsFromXdr()
    {
        $source = new TimeBounds(new \DateTime('2018-01-01 00:00:00'), new \DateTime('2018-01-31 00:00:00'));

        $decoded = TimeBounds::fromXdr(new XdrBuffer($source->toXdr()));

        $this->assertEquals($source->getMinTimestamp(), $decoded->getMinTimestamp());
        $this->assertEquals($source->getMaxTimestamp(), $decoded->getMaxTimestamp());
    }
}