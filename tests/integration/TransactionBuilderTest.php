<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;

class TransactionBuilderTest extends IntegrationTest
{
    /**
     * @group requires-integrationnet
     */
    public function testTransactionResultSingleOp()
    {
        $sourceKeypair = $this->fixtureAccounts['basic1'];
        $destinationKeypair = $this->fixtureAccounts['basic2'];

        $response = $this->horizonServer->buildTransaction($sourceKeypair)
            ->addLumenPayment($destinationKeypair, 3)
            ->submit($sourceKeypair);

        // All operations should have succeeded
        $result = $response->getResult();

        $this->assertTrue($result->succeeded());
        $this->assertCount(1, $result->getOperationResults());
    }
}