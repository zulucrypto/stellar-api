<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ManageDataOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class ManageDataOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceOp = new ManageDataOp('testkey', 'testvalue');

        /** @var ManageDataOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof ManageDataOp);

        $this->assertEquals('testkey', $parsed->getKey());
        $this->assertEquals('testvalue', $parsed->getValue());
    }
}