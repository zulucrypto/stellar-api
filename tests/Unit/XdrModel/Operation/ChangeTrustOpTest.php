<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\AccountMergeOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ChangeTrustOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class ChangeTrustOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceOp = new ChangeTrustOp(Asset::newCustomAsset('TRUST', Keypair::newFromRandom()), 8888);

        /** @var ChangeTrustOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof ChangeTrustOp);

        $this->assertEquals('TRUST', $parsed->getAsset()->getAssetCode());
        $this->assertEquals(8888, $parsed->getLimit()->getScaledValue());
    }
}