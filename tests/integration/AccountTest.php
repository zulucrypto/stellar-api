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
        // Get basic1 account
        $sourceAccount = $this->horizonServer->getAccount($this->fixtureAccounts['basic1']['accountId']);

        // Send a payment to basic2
        $response = $sourceAccount->sendNativeAsset(
            // Send to basic2 account
            $this->fixtureAccounts['basic2']['accountId'],
            5,
            // Sign with basic1 seed
            $this->fixtureAccounts['basic1']['seed']
        );

        $this->assertNotEmpty($response->mustGetField('hash'));
    }
}