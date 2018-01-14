<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;

class AccountMergeOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testMergeAccount()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destinationKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addMergeOperation($destinationKeypair);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            'Merge Account: ',
            '       Source: ' . $sourceKeypair->getPublicKey(),
            '  Destination: ' . $destinationKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}