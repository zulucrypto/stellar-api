<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use ZuluCrypto\StellarSdk\Keypair;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class PaymentOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testNativeAssetPayment()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $amount = 50.0111;

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addLumenPayment($destKeypair, $amount);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Payment: native asset',
            'Source: ' . $sourceKeypair->getPublicKey(),
            '   Pay: ' . $amount . ' XLM',
            '    To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testCustomAsset1Payment()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $amount = 50.0111;

        $issuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'issuer');
        $asset = Asset::newCustomAsset('X', $issuerKeypair->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCustomAssetPaymentOp($asset, $amount, $destKeypair);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Payment: custom asset (1-char)',
            'Source: ' . $sourceKeypair->getPublicKey(),
            '   Pay: ' . sprintf('%s %s (%s)', $amount, $asset->getAssetCode(), $asset->getIssuer()->getAccountIdString()),
            '    To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testCustomAsset4Payment()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $amount = 50.0111;

        $issuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'issuer');
        $asset = Asset::newCustomAsset('TEST', $issuerKeypair->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCustomAssetPaymentOp($asset, $amount, $destKeypair);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Payment: custom asset (4-char)',
            'Source: ' . $sourceKeypair->getPublicKey(),
            '   Pay: ' . sprintf('%s %s (%s)', $amount, $asset->getAssetCode(), $asset->getIssuer()->getAccountIdString()),
            '    To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testCustomAsset7Payment()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $amount = 50.0111;

        $issuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'issuer');
        $asset = Asset::newCustomAsset('SEVENXX', $issuerKeypair->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCustomAssetPaymentOp($asset, $amount, $destKeypair);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Payment: custom asset (7-char)',
            'Source: ' . $sourceKeypair->getPublicKey(),
            '   Pay: ' . sprintf('%s %s (%s)', $amount, $asset->getAssetCode(), $asset->getIssuer()->getAccountIdString()),
            '    To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testCustomAsset12Payment()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $amount = 50.0111;

        $issuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'issuer');
        $asset = Asset::newCustomAsset('ABCDEFGHIJKL', $issuerKeypair->getPublicKey());

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCustomAssetPaymentOp($asset, $amount, $destKeypair);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Payment: custom asset (12-char)',
            'Source: ' . $sourceKeypair->getPublicKey(),
            '   Pay: ' . sprintf('%s %s (%s)', $amount, $asset->getAssetCode(), $asset->getIssuer()->getAccountIdString()),
            '    To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}