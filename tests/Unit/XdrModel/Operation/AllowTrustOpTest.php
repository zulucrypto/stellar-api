<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\AllowTrustOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;

class AllowTrustOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceOp = new AllowTrustOp(Asset::newCustomAsset('TST', Keypair::newFromRandom()), new AccountId(Keypair::newFromRandom()));
        $sourceOp->setIsAuthorized(true);

        /** @var AllowTrustOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof AllowTrustOp);

        $this->assertEquals('TST', $parsed->getAsset()->getAssetCode());
        $this->assertEquals($sourceOp->getTrustor()->getAccountIdString(), $parsed->getTrustor()->getAccountIdString());
    }
}