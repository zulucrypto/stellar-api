<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\AccountMergeOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;

class PaymentOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceOp = new PaymentOp();
        $sourceOp->setDestination(new AccountId(Keypair::newFromRandom()->getAccountId()));
        $sourceOp->setAmount(100);
        $sourceOp->setAsset(Asset::newNativeAsset());


        /** @var PaymentOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof PaymentOp);
        $this->assertEquals($sourceOp->getDestination()->getAccountIdString(), $parsed->getDestination()->getAccountIdString());
        $this->assertEquals($sourceOp->getAmount()->getScaledValue(), $parsed->getAmount()->getScaledValue());
        $this->assertEquals($sourceOp->getAsset()->getType(), $parsed->getAsset()->getType());
    }
}