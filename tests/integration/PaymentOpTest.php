<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

class PaymentOpTest extends IntegrationTest
{
    /**
     * @group requires-integrationnet
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     * @throws \ErrorException
     */
    public function testNativePayment()
    {
        $sourceKeypair = $this->fixtureAccounts['basic1'];
        $destinationKeypair = $this->fixtureAccounts['basic2'];

        $prevBalance = $this->horizonServer
            ->getAccount($destinationKeypair)
            ->getNativeBalance();

        $this->horizonServer->buildTransaction($sourceKeypair)
            ->addLumenPayment($destinationKeypair, 10.0000001)
            ->submit($sourceKeypair);

        // Get updated balance
        $newBalance = $this->horizonServer
            ->getAccount($destinationKeypair)
            ->getNativeBalance();

        $this->assertEquals($prevBalance + 10.0000001, $newBalance);
    }

    /**
     * NOTE: Due to how PHP uses floating point numbers, it is important to always
     *  work in stroops when adding or subtracting balances.
     *
     * See test for an example
     *
     * @group requires-integrationnet
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     * @throws \ErrorException
     */
    public function testCustomAssetPayment()
    {
        // Floating point failure example:
        /*
        $oldBalance = 1.605;
        $newBalance = 1.61;

        var_dump($oldBalance + 0.005);
        var_dump($newBalance);
        if ($oldBalance + 0.005 === $newBalance) {
            print "Equal\n";
        }
        else {
            print "Not Equal\n";
        }
        */
        // Output:
        // float(1.61)
        // float(1.61)
        // Not Equal

        $usdIssuingKeypair = $this->fixtureAccounts['usdIssuingKeypair'];
        $usdAsset = $this->fixtureAssets['usd'];

        $usdBankKeypair = $this->fixtureAccounts['usdBankKeypair'];

        // Transfer from the issuer to the bank
        $prevBalance = $this->horizonServer
            ->getAccount($usdBankKeypair)
            ->getCustomAssetBalanceStroops($usdAsset);

        $this->horizonServer->buildTransaction($usdIssuingKeypair)
            ->addCustomAssetPaymentOp($usdAsset, 10.005, $usdBankKeypair)
            ->submit($usdIssuingKeypair);

        $newBalance = $this->horizonServer
            ->getAccount($usdBankKeypair)
            ->getCustomAssetBalanceStroops($usdAsset);

        $this->assertEquals($prevBalance + (10.005 * StellarAmount::STROOP_SCALE), $newBalance);
    }

    /**
     * @group requires-integrationnet
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     * @throws \ErrorException
     */
    public function testLargestCustomAssetPayment()
    {
        $transferTo = $this->getRandomFundedKeypair();
        $usdIssuingKeypair = $this->fixtureAccounts['usdIssuingKeypair'];
        $usdAsset = $this->fixtureAssets['usd'];

        // Add trustline for the maximum amount
        $this->horizonServer->buildTransaction($transferTo)
            ->addChangeTrustOp($usdAsset, StellarAmount::newMaximum())
            ->submit($transferTo);

        // Transfer maximum amount
        $this->horizonServer->buildTransaction($usdIssuingKeypair)
            ->addCustomAssetPaymentOp($usdAsset, new BigInteger('9223372036854775807'), $transferTo)
            ->submit($usdIssuingKeypair);

        $newBalance = $this->horizonServer
            ->getAccount($transferTo)
            ->getCustomAssetBalanceValue($usdAsset);

        $this->assertEquals(922337203685.4775807, $newBalance);
    }
}