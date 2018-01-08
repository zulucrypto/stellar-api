<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;


use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use ZuluCrypto\StellarSdk\Keypair;
use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PathPaymentOp;

class PathPaymentOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testNativeAssetPaymentNoPaths()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');
        $maxAmount = 200.9999999; // maximum XLM that the sender is willing to pay
        $receivesAmount = 20.0000001; // amount of JPY the receiver gets

        $payWithAsset = Asset::newNativeAsset();
        $receiverGetsAsset = Asset::newCustomAsset('JPY', $jpyIssuerKeypair);

        $operation = new PathPaymentOp(
            $payWithAsset,
            $maxAmount,
            $destKeypair,
            $receiverGetsAsset,
            $receivesAmount
        );

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Path Payment: XLM -> JPY (no paths)',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '       Max Paid: ' . $maxAmount . ' XLM',
            'Amount Received: ' . sprintf('%s %s (%s)', $receivesAmount, $receiverGetsAsset->getAssetCode(), $receiverGetsAsset->getIssuer()->getAccountIdString()),
            '             To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testNativeAssetPaymentOnePath()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $destKeypair = Keypair::newFromMnemonic($this->mnemonic, 'destination');
        $jpyIssuerKeypair = Keypair::newFromMnemonic($this->mnemonic, 'jpy issuer');
        $maxAmount = 200.9999999; // maximum XLM that the sender is willing to pay
        $receivesAmount = 20.0000001; // amount of JPY the receiver gets

        $payWithAsset = Asset::newNativeAsset();
        $receiverGetsAsset = Asset::newCustomAsset('JPY', $jpyIssuerKeypair);

        $pathAsset1 = Asset::newCustomAsset('PTH1', Keypair::newFromMnemonic($this->mnemonic, 'PTH1'));

        $operation = new PathPaymentOp(
            $payWithAsset,
            $maxAmount,
            $destKeypair,
            $receiverGetsAsset,
            $receivesAmount
        );

        $operation->addPath($pathAsset1);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            '   Path Payment: XLM -> JPY (one path)',
            '         Source: ' . $sourceKeypair->getPublicKey(),
            '       Max Paid: ' . $maxAmount . ' XLM',
            'Amount Received: ' . sprintf('%s %s (%s)', $receivesAmount, $receiverGetsAsset->getAssetCode(), $receiverGetsAsset->getIssuer()->getAccountIdString()),
            '             To: ' . $destKeypair->getPublicKey(),
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}