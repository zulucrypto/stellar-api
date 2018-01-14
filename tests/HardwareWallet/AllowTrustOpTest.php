<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;

use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class AllowTrustOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testAllowTrust()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $trustedKeypair = Keypair::newFromMnemonic($this->mnemonic, 'trusted keypair');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');

        $asset = Asset::newCustomAsset('JPY', $jpyIssuerKeypair);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->authorizeTrustline($asset, $trustedKeypair);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '    Allow Trust: authorize',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '          Asset: ' . $asset->getAssetCode(),
            'Trusted Account: ' . $trustedKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testRevokeTrust()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $trustedKeypair = Keypair::newFromMnemonic($this->mnemonic, 'trusted keypair');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');

        $asset = Asset::newCustomAsset('JPY', $jpyIssuerKeypair);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->revokeTrustline($asset, $trustedKeypair);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '    Allow Trust: revoke',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '          Asset: ' . $asset->getAssetCode(),
            'Trusted Account: ' . $trustedKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAllow7CharAssetTrust()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $trustedKeypair = Keypair::newFromMnemonic($this->mnemonic, 'trusted keypair');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');

        $asset = Asset::newCustomAsset('ABCDEFG', $jpyIssuerKeypair);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->authorizeTrustline($asset, $trustedKeypair);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '    Allow Trust: authorize 7-character asset',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '          Asset: ' . $asset->getAssetCode(),
            'Trusted Account: ' . $trustedKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAllow12CharAssetTrust()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $trustedKeypair = Keypair::newFromMnemonic($this->mnemonic, 'trusted keypair');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');

        $asset = Asset::newCustomAsset('ABCDEFGHIJKL', $jpyIssuerKeypair);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->authorizeTrustline($asset, $trustedKeypair);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '    Allow Trust: authorize 12-character asset',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '          Asset: ' . $asset->getAssetCode(),
            'Trusted Account: ' . $trustedKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}