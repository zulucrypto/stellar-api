<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;

class CreateAccountOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testCreateAccount()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $newKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCreateAccountOp($newKeypair, 100.0333);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Create Account operation',
            '          Source: ' . $sourceKeypair->getPublicKey(),
            'Creating account: ' . $newKeypair->getPublicKey(),
            ' Initial balance: ' . 100.0333,
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testCreateAccountWithMaximumValue()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $newKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addCreateAccountOp($newKeypair, new BigInteger('9223372036854775807'));
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Create Account operation',
            '          Source: ' . $sourceKeypair->getPublicKey(),
            'Creating account: ' . $newKeypair->getPublicKey(),
            ' Initial balance: ' . '922337203685.4775807',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}