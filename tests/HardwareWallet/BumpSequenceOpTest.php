<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;

class BumpSequenceOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testSimpleBumpSequence()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $bumpTo = new BigInteger(1234567890);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->bumpSequenceTo($bumpTo);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '  Bump Sequence: basic',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '        Bump To: ' . $bumpTo->toString(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testMaxBumpSequence()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);

        $bumpTo = new BigInteger('9223372036854775807');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->bumpSequenceTo($bumpTo);

        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '  Bump Sequence: max',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '        Bump To: ' . $bumpTo->toString(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}