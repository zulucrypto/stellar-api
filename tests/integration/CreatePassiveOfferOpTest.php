<?php


namespace ZuluCrypto\StellarSdk\Test\Integration;


use ZuluCrypto\StellarSdk\Test\Util\IntegrationTest;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\CreatePassiveOfferOp;
use ZuluCrypto\StellarSdk\XdrModel\Price;

class CreatePassiveOfferOpTest extends IntegrationTest
{
    /**
     * @group requires-integrationnet
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     * @throws \ErrorException
     */
    public function testSubmitPassiveOffer()
    {
        $usdBankKeypair = $this->fixtureAccounts['usdBankKeypair'];
        $usdAsset = $this->fixtureAssets['usd'];

        // Sell 100 USDTEST for 0.0005 XLM
        $xlmPrice = new Price(5, 10000);
        $offerOp = new CreatePassiveOfferOp($usdAsset, Asset::newNativeAsset(), 100, $xlmPrice);

        $response = $this->horizonServer->buildTransaction($usdBankKeypair)
            ->addOperation($offerOp)
            ->submit($usdBankKeypair);

        // todo: add support for viewing offers on an account and verify here
        // todo: verify canceling an offer works correctly
    }
}