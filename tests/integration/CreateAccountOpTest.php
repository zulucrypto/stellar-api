<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;

class CreateAccountOpTest extends IntegrationTest
{
    /**
     * @group requires-integrationnet
     */
    public function testCreateAccount()
    {
        /** @var Keypair $sourceKeypair */
        $sourceKeypair = $this->fixtureAccounts['basic1'];

        $newKeypair = Keypair::newFromRandom();

        $this->horizonServer->buildTransaction($sourceKeypair->getPublicKey())
            ->addCreateAccountOp($newKeypair->getAccountId(), 100.0333)
            ->submit($sourceKeypair->getSecret());

        // Should then be able to retrieve the account and verify the balance
        $newAccount = $this->horizonServer->getAccount($newKeypair->getPublicKey());

        $this->assertEquals("100.0333", $newAccount->getNativeBalance());
    }
}