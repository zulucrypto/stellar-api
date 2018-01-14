<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Util\Debug;

class ManageDataOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testSetDataValue()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $dataKey = 'test data';
        $dataValue = 'asdf';

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->setAccountData($dataKey, $dataValue);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Manage Data: set data',
            '     Source: ' . $sourceKeypair->getPublicKey(),
            '        Key: ' . $dataKey,
            ' Value Hash: ' . hash('sha256', $dataValue),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testSetMaximumLengthDataValue()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $dataKey = join('', [
            'abcdefghijklmnop',
            'abcdefghijklmnop',
            'abcdefghijklmnop',
            'abcdefghijklmnop',
        ]);

        $dataValue = random_bytes(64);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->setAccountData($dataKey, $dataValue);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Manage Data: set data (maximum length key and value)',
            '     Source: ' . $sourceKeypair->getPublicKey(),
            '        Key: ' . $dataKey,
            ' Value Hash: ' . hash('sha256', $dataValue),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testClearDataValue()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $dataKey = 'test data';
        $dataValue = null;

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->setAccountData($dataKey, $dataValue);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Manage Data: clear data',
            '     Source: ' . $sourceKeypair->getPublicKey(),
            '        Key: ' . $dataKey,
            ' Value Hash: ' . hash('sha256', $dataValue),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}