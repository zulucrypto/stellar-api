<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Horizon\Exception\PostTransactionException;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\InflationOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ManageDataOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ManageOfferOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PathPaymentOp;
use ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;
use ZuluCrypto\StellarSdk\XdrModel\Price;

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
     * Helper method to extract OperationResult XDR for writing other tests / debugging
     *
     * @group requires-integrationnet
     */
    public function testGetXdr()
    {
        $this->markTestSkipped('For debugging');

        $sourceKeypair = $this->fixtureAccounts['basic1'];
        $destinationKeypair = $this->fixtureAccounts['basic2'];
        $usdIssuingKeypair = Keypair::newFromSeed('SBJXZEVYRX244HKDY6L5JZYPWDQW6D3WLEE3PTMQM4CSUKGE37J4AC3W');
        $usdBankKeypair = Keypair::newFromSeed('SDJOXTS4TE3Q3HUIFQK5AQCTRML6HIOUQIXDLCEQHICOFHU5CQN6DBLS');
        $authRequiredIssuingKeypair = Keypair::newFromSeed('SABFYGWPSP3EEJ2EURHQYAIRTNK3SVQPED5PWOHGCWKPZBSCWBV4QGKE');

        $usdAsset = Asset::newCustomAsset('USDTEST', $usdIssuingKeypair->getPublicKey());
        $authRequiredAsset = Asset::newCustomAsset('AUTHREQ', $authRequiredIssuingKeypair->getPublicKey());

        //$op = new ManageOfferOp($usdAsset, Asset::newNativeAsset(), 0, new Price(3, 1), 8);

        $op = new ManageDataOp('asdf', 'jkl');

        $response = $this->horizonServer->buildTransaction($sourceKeypair)
            ->addOperation($op)
            ->submit($sourceKeypair);

        $rawData = $response->getRawData();
        $xdrB64 = $rawData['result_xdr'];
        $xdr = base64_decode($xdrB64);

        $xdr = substr($xdr,8 + 4 + 4);
        print "XDR: \n";
        print base64_encode($xdr);
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