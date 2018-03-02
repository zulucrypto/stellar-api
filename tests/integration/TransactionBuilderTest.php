<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Horizon\Exception\PostTransactionException;
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

    /**
     * @group requires-integrationnet
     */
    public function testFailedTransactionResultSingleOp()
    {
        $sourceKeypair = $this->fixtureAccounts['basic1'];
        $destinationKeypair = $this->fixtureAccounts['basic2'];

        // This should fail since the source account doesn't have enough funds
        try {
            $response = $this->horizonServer->buildTransaction($sourceKeypair)
                ->addLumenPayment($destinationKeypair, 99999)
                ->submit($sourceKeypair);

            $this->fail('Exception was expected');
        }
        catch (PostTransactionException $ex) {
            $result = $ex->getResult();
            $opResults = $result->getOperationResults();
            $this->assertCount(1, $opResults);
            $this->assertEquals('payment_underfunded', $opResults[0]->getErrorCode());
        }
    }
}