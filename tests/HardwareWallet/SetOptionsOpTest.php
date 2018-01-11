<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;
use ZuluCrypto\StellarSdk\XdrModel\Signer;
use ZuluCrypto\StellarSdk\XdrModel\SignerKey;

class SetOptionsOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testSetInflationDestination()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $inflationDestination = Keypair::newFromMnemonic($this->mnemonic, 'inflation destination');

        $operation = new SetOptionsOp();
        $operation->setInflationDestination($inflationDestination);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '      Set Option: inflation destination',
            '          Source: ' . $sourceKeypair->getPublicKey(),
            'Inf. Destination: ' . $inflationDestination->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testClearOneFlag()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setAuthRevocable(false);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '      Set Option: clear one flag',
            '          Source: ' . $sourceKeypair->getPublicKey(),
            '   Flags Cleared: ' . 'AUTH_REVOCABLE'
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testClearAllFlags()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setAuthRevocable(false);
        $operation->setAuthRequired(false);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '      Set Option: clear all flags',
            '          Source: ' . $sourceKeypair->getPublicKey(),
            '   Flags Cleared: ' . 'AUTH_REVOCABLE, AUTH_REQUIRED'
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetOneFlag()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setAuthRevocable(true);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Set Option: set one flag',
            '    Source: ' . $sourceKeypair->getPublicKey(),
            ' Flags Set: ' . 'AUTH_REVOCABLE'
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetAllFlags()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setAuthRevocable(true);
        $operation->setAuthRequired(true);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Set Option: set all flags',
            '    Source: ' . $sourceKeypair->getPublicKey(),
            ' Flags Set: ' . 'AUTH_REVOCABLE, AUTH_REQUIRED'
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetMasterWeight()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setMasterWeight(128);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: master weight',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            'Master Weight: ' . $operation->getMasterWeight(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetAllThresholds()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setMasterWeight(255);
        $operation->setLowThreshold(128);
        $operation->setMediumThreshold(16);
        $operation->setHighThreshold(0);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: all thresholds',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Master: ' . $operation->getMasterWeight(),
            '          Low: ' . $operation->getLowThreshold(),
            '       Medium: ' . $operation->getMediumThreshold(),
            '         High: ' . $operation->getHighThreshold(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetHomeDomain()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setHomeDomain('example.com');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: home domain',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Domain: ' . $operation->getHomeDomain(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetMaxLengthHomeDomain()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $operation = new SetOptionsOp();
        $operation->setHomeDomain('abcdefghijklmnopqrstuvwxyzabcdef');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: home domain (max length)',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Domain: ' . $operation->getHomeDomain(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAddAccountSigner()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $signerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'new signer');

        $signerKey = SignerKey::fromKeypair($signerKeypair);
        $signer = new Signer($signerKey, 100);

        $operation = new SetOptionsOp();
        $operation->updateSigner($signer);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: add signer (account)',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Signer: ' . $signerKeypair->getPublicKey(),
            '       Weight: ' . $signer->getWeight(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testRemoveAccountSigner()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $signerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'new signer');

        $signerKey = SignerKey::fromKeypair($signerKeypair);
        $signer = new Signer($signerKey, 0);

        $operation = new SetOptionsOp();
        $operation->updateSigner($signer);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: add signer (account)',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Signer: ' . $signerKeypair->getPublicKey(),
            '       Weight: ' . $signer->getWeight(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAddPreAuthHashSigner()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $hashedValue = 'asdf';
        $signerKey = SignerKey::fromPreauthorizedHash(hash('sha256', $hashedValue, true));
        $signer = new Signer($signerKey, 1);

        $operation = new SetOptionsOp();
        $operation->updateSigner($signer);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: add signer (account)',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Signer: ' . hash('sha256', $hashedValue),
            '       Weight: ' . $signer->getWeight(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testAddHashXSigner()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $hashedValue = 'asdf';
        $signerKey = SignerKey::fromHashX($hashedValue);
        $signer = new Signer($signerKey, 1);

        $operation = new SetOptionsOp();
        $operation->updateSigner($signer);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Set Option: add signer (account)',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '       Signer: ' . hash('sha256', $hashedValue),
            '       Weight: ' . $signer->getWeight(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}