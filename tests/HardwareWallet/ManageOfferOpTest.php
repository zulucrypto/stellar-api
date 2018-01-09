<?php


namespace ZuluCrypto\StellarSdk\Test\HardwareWallet;

use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Test\Util\HardwareWalletIntegrationTest;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ManageOfferOp;
use ZuluCrypto\StellarSdk\XdrModel\Price;

class ManageOfferOpTest extends HardwareWalletIntegrationTest
{
    /**
     * @group requires-hardwarewallet
     */
    public function testNewOffer()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $sellingAsset = Asset::newNativeAsset();
        $buyingAsset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd issuer'));

        $amount = 200;
        $price = new Price(674614, 1000000);

        $operation = new ManageOfferOp($sellingAsset, $buyingAsset, $amount, $price);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            base64_encode($transaction->toXdr()),
            'Manage Offer: new offer',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '     Selling: ' . $amount . 'XLM',
            '      Buying: ' . 'For ' . $price . ' per ' . $buyingAsset->getAssetCode() . ' (' . $buyingAsset->getIssuer()->getAccountIdString() . ')',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testNewMaxAmountOffer()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $sellingAsset = Asset::newNativeAsset();
        $buyingAsset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd issuer'));

        $amount = 100.99;
        $price = new Price(4294967295, 1);

        $operation = new ManageOfferOp($sellingAsset, $buyingAsset, $amount, $price);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            base64_encode($transaction->toXdr()),
            'Manage Offer: new offer',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '     Selling: ' . $amount . 'XLM',
            '      Buying: ' . 'For ' . $price . ' per ' . $buyingAsset->getAssetCode() . ' (' . $buyingAsset->getIssuer()->getAccountIdString() . ')',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testNewMinAmountOffer()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $sellingAsset = Asset::newNativeAsset();
        $buyingAsset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd issuer'));

        $amount = 100.99;
        $price = new Price(1, 4294967295);

        $operation = new ManageOfferOp($sellingAsset, $buyingAsset, $amount, $price);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            base64_encode($transaction->toXdr()),
            'Manage Offer: new offer',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '     Selling: ' . number_format($amount) . 'XLM',
            '      Buying: ' . 'For ' . $price . ' per ' . $buyingAsset->getAssetCode() . ' (' . $buyingAsset->getIssuer()->getAccountIdString() . ')',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testUpdateOffer()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $sellingAsset = Asset::newNativeAsset();
        $buyingAsset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd issuer'));

        $amount = 200;
        $price = new Price(674614, 1000000);
        $offerId = 15528655;

        $operation = new ManageOfferOp($sellingAsset, $buyingAsset, $amount, $price, $offerId);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            base64_encode($transaction->toXdr()),
            'Manage Offer: update offer',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '    Offer ID: ' . $offerId,
            '     Selling: ' . $amount . 'XLM',
            '      Buying: ' . 'For ' . $price . ' per ' . $buyingAsset->getAssetCode() . ' (' . $buyingAsset->getIssuer()->getAccountIdString() . ')',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }

    /**
     * @group requires-hardwarewallet
     */
    public function testRemoveOffer()
    {
        $sourceKeypair = Keypair::newFromMnemonic($this->mnemonic);
        $sellingAsset = Asset::newNativeAsset();
        $buyingAsset = Asset::newCustomAsset('USD', Keypair::newFromMnemonic($this->mnemonic, 'usd issuer'));

        $amount = 0;
        $price = new Price(674614, 1000000);
        $offerId = 15528655;

        $operation = new ManageOfferOp($sellingAsset, $buyingAsset, $amount, $price, $offerId);

        $transaction = $this->horizonServer
            ->buildTransaction($sourceKeypair)
            ->setSequenceNumber(new BigInteger(4294967296))
            ->addOperation($operation);
        $knownSignature = $transaction->signWith($this->privateKeySigner);

        $this->manualVerificationOutput(join(PHP_EOL, [
            base64_encode($transaction->toXdr()),
            'Manage Offer: remove offer',
            '      Source: ' . $sourceKeypair->getPublicKey(),
            '    Offer ID: ' . $offerId,
            '     Selling: ' . $amount . 'XLM',
            '      Buying: ' . 'For ' . $price . ' per ' . $buyingAsset->getAssetCode() . ' (' . $buyingAsset->getIssuer()->getAccountIdString() . ')',
        ]));
        $hardwareSignature = $transaction->signWith($this->horizonServer->getSigningProvider());

        $this->assertEquals($knownSignature->toBase64(), $hardwareSignature->toBase64());
    }
}