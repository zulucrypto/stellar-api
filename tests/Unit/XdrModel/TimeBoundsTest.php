<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TimeBounds;

class TimeBoundsTest extends TestCase
{
    public function testNullTimeBoundsFromXdr()
    {
        $this->assertNull(TimeBounds::fromXdr(new XdrBuffer("\x00\x00\x00\x00\x11\x22\x33\x44")));
    }

    public function testTimeBoundsFromXdr()
    {
        $source = new TimeBounds(1000, 2000);

        $decoded = TimeBounds::fromXdr(new XdrBuffer($source->toXdr()));

        $this->assertEquals($source->getMinTimestamp(), $decoded->getMinTimestamp());
        $this->assertEquals($source->getMaxTimestamp(), $decoded->getMaxTimestamp());
    }
}