<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\InflationOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class InflationOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceOp = new InflationOp();

        /** @var InflationOpTest $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof InflationOp);
    }
}