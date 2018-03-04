<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Operation\AccountMergeOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class AccountMergeOpTest extends TestCase
{
    public function testFromXdr()
    {
        $source = new AccountMergeOp(Keypair::newFromRandom());

        /** @var AccountMergeOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($source->toXdr()));

        $this->assertTrue($parsed instanceof AccountMergeOp);
        $this->assertEquals($source->getDestination()->getAccountIdString(), $parsed->getDestination()->getAccountIdString());
    }
}