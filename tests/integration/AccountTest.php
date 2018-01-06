<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


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
}