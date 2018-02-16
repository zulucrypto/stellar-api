<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class AssetTest extends TestCase
{
    public function testNativeAssetFromXdr()
    {
        $asset = Asset::newNativeAsset();

        $assetFromXdr = Asset::fromXdr(new XdrBuffer($asset->toXdr()));

        $this->assertEquals($asset->getType(), $assetFromXdr->getType());
    }

    public function testCustomAsset4FromXdr()
    {
        $issuerKeypair = Keypair::newFromRandom();
        $asset = Asset::newCustomAsset('TEST', $issuerKeypair);

        $assetFromXdr = Asset::fromXdr(new XdrBuffer($asset->toXdr()));

        $this->assertEquals($asset->getType(), $assetFromXdr->getType());
        $this->assertEquals('TEST', $assetFromXdr->getAssetCode());
        $this->assertEquals($issuerKeypair->getPublicKey(), $assetFromXdr->getIssuer()->getAccountIdString());
    }

    public function testCustomAsset7FromXdr()
    {
        $issuerKeypair = Keypair::newFromRandom();
        $asset = Asset::newCustomAsset('TESTABC', $issuerKeypair);

        $assetFromXdr = Asset::fromXdr(new XdrBuffer($asset->toXdr()));

        $this->assertEquals($asset->getType(), $assetFromXdr->getType());
        $this->assertEquals('TESTABC', $assetFromXdr->getAssetCode());
        $this->assertEquals($issuerKeypair->getPublicKey(), $assetFromXdr->getIssuer()->getAccountIdString());
    }

    public function testCustomAsset12FromXdr()
    {
        $issuerKeypair = Keypair::newFromRandom();
        $asset = Asset::newCustomAsset('ABCDEFGHIJKL', $issuerKeypair);

        $assetFromXdr = Asset::fromXdr(new XdrBuffer($asset->toXdr()));

        $this->assertEquals($asset->getType(), $assetFromXdr->getType());
        $this->assertEquals('ABCDEFGHIJKL', $assetFromXdr->getAssetCode());
        $this->assertEquals($issuerKeypair->getPublicKey(), $assetFromXdr->getIssuer()->getAccountIdString());
    }
}