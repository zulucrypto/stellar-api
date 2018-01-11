<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\Model\StellarAmount;

class ChangeTrustOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testAddMaxTrustline()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $asset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd')->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addChangeTrustOp($asset, StellarAmount::newMaximum());
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Change Trust: add trustline',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '        Code: ' . $asset->getAssetCode(),
            '      Amount: ' . 'Maximum',
            '      Issuer: ' . $asset->getIssuer()->getAccountIdString(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAddTrustline()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $asset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd')->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addChangeTrustOp($asset, 1000);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Change Trust: add trustline',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '        Code: ' . $asset->getAssetCode(),
            '      Amount: ' . '1000',
            '      Issuer: ' . $asset->getIssuer()->getAccountIdString(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testRemoveTrustline()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $asset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd')->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addChangeTrustOp($asset, 0);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Change Trust: add trustline',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '        Code: ' . $asset->getAssetCode(),
            '      Amount: ' . '0',
            '      Issuer: ' . $asset->getIssuer()->getAccountIdString(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}