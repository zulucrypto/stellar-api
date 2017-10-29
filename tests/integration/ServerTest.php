<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;


class ServerTest extends IntegrationTest
{
    /**
     * @group integration
     */
    public function testConnectToCustomNetwork()
    {
        $server = Server::customNet($this->horizonBaseUrl, $this->networkPassword);

        // Verify one of the fixture accounts can be retrieved
        $account = $server->getAccount($this->fixtureAccounts['basic1']['accountId']);

        // Account should have at least one balance
        $this->assertNotEmpty($account->getBalances());
    }
}