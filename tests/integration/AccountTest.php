<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;


class AccountTest extends IntegrationTest
{
    /**
     * @group requires-integrationnet
     */
    public function testSendPaymentNativeAsset()
    {
        $paymentAmount = 5;

        // Get basic1 account
        $sourceAccount = $this->horizonServer->getAccount($this->fixtureAccounts['basic1']->getPublicKey());

        $destinationAccountBefore = $this->horizonServer->getAccount($this->fixtureAccounts['basic2']->getPublicKey());

        // Send a payment to basic2
        $response = $sourceAccount->sendNativeAsset(
            // Send to basic2 account
            $this->fixtureAccounts['basic2']->getPublicKey(),
            $paymentAmount,
            // Sign with basic1 seed
            $this->fixtureAccounts['basic1']->getSecret()
        );

        $destinationAccountAfter = $this->horizonServer->getAccount($this->fixtureAccounts['basic2']->getPublicKey());

        // Must be a valid hash
        $this->assertNotEmpty($response->mustGetField('hash'));

        // Balance should have gone up by the paymentAmount
        $this->assertEquals($destinationAccountBefore->getNativeBalance() + $paymentAmount, $destinationAccountAfter->getNativeBalance());
    }

    /**
     * @group requires-integrationnet
     */
    public function testGetPayments()
    {
        // Create a new account to receive the payments and fund via friendbot
        $paymentDestKeypair = $this->getRandomFundedKeypair();

        // Create a payment from a regular account
        $payingKeypair = $this->fixtureAccounts['basic1'];
        $payingAccount = $this->horizonServer->getAccount($payingKeypair->getPublicKey());
        $payingAccount->sendNativeAsset($paymentDestKeypair, 100, $payingKeypair);

        // Merge an account into the destination account
        $mergingKeypair = $this->getRandomFundedKeypair();
        $this->horizonServer->buildTransaction($mergingKeypair)
            ->addMergeOperation($paymentDestKeypair)
            ->submit($mergingKeypair);

        // loading this too fast will miss the last payment
        sleep(1);
        $account = $this->horizonServer->getAccount($paymentDestKeypair->getPublicKey());

        $this->assertCount(2, $account->getPayments());
    }
}