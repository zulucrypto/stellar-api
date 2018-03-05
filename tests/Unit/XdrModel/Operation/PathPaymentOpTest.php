<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel\Operation;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\Operation;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PathPaymentOp;

class PathPaymentOpTest extends TestCase
{
    public function testFromXdr()
    {
        $sendingAsset = Asset::newCustomAsset('SEND', Keypair::newFromRandom());
        $sendMax = 100.99;
        $destinationKeypair = Keypair::newFromRandom();
        $destinationAsset = Asset::newCustomAsset('DEST', Keypair::newFromRandom());
        $desinationAmount = 500;

        $pathA = Asset::newCustomAsset('PATHA', Keypair::newFromRandom());
        $pathB = Asset::newCustomAsset('PATHB', Keypair::newFromRandom());

        $sourceOp = new PathPaymentOp($sendingAsset, $sendMax, $destinationKeypair, $destinationAsset, $desinationAmount);
        $sourceOp->addPath($pathA);
        $sourceOp->addPath($pathB);

        /** @var PathPaymentOp $parsed */
        $parsed = Operation::fromXdr(new XdrBuffer($sourceOp->toXdr()));

        $this->assertTrue($parsed instanceof PathPaymentOp);

        $this->assertEquals($sourceOp->getDestinationAccount()->getAccountIdString(), $parsed->getDestinationAccount()->getAccountIdString());
        $this->assertEquals($sendMax, $parsed->getSendMax()->getScaledValue());
        $this->assertEquals($destinationKeypair->getAccountId(), $parsed->getDestinationAccount()->getAccountIdString());
        $this->assertEquals($destinationAsset->getAssetCode(), $parsed->getDestinationAsset()->getAssetCode());
        $this->assertEquals($desinationAmount, $parsed->getDestinationAmount()->getScaledValue());
    }
}