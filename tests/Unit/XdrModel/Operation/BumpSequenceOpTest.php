<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use phpseclib\Math\BigInteger;
use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Operation\BumpSequenceOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class BumpSequenceOpTest extends TestCase
{
    public function testFromXdr()
    {
        $source = new BumpSequenceOp(new BigInteger('1234567890'));

        /** @var BumpSequenceOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($source->toXdr()));

        $this->assertTrue($parsed instanceof BumpSequenceOp);
        $this->assertEquals($source->getBumpTo()->toString(), $parsed->getBumpTo()->toString());
    }
}