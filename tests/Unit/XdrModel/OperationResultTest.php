<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\AccountMergeResult;
use ZuluCrypto\StellarSdk\XdrModel\AllowTrustResult;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\ChangeTrustResult;
use ZuluCrypto\StellarSdk\XdrModel\CreateAccountResult;
use ZuluCrypto\StellarSdk\XdrModel\InflationResult;
use ZuluCrypto\StellarSdk\XdrModel\ManageDataResult;
use ZuluCrypto\StellarSdk\XdrModel\ManageOfferResult;
use ZuluCrypto\StellarSdk\XdrModel\OperationResult;
use ZuluCrypto\StellarSdk\XdrModel\PathPaymentResult;
use ZuluCrypto\StellarSdk\XdrModel\PaymentResult;
use ZuluCrypto\StellarSdk\XdrModel\SetOptionsResult;

class OperationResultTest extends TestCase
{
    public function testPaymentOperationResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAEAAAAAAAAAAQAAAAAAAAAA='));

        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof PaymentResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
        $this->assertTrue($result->succeeded());
        $this->assertFalse($result->failed());
    }

    public function testCreateAccountOperationResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAAAAAAAAAAAAA=='));

        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof CreateAccountResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testPathPaymentOperationResultSuccess()
    {
        // https://www.stellar.org/laboratory/#xdr-viewer?input=AAAAAAAAAAIAAAAAAAAAAQAAAAAp9kRanuQMa2XlcQS2SWs5LRrVwEihfnrPyY6PrfiDTgAAAAAAAAACAAAAAkVVUlRFU1QAAAAAAAAAAABcI7sXin6ZN1piyUF6r8YxaAcf%2FgmNFo9NaZbbQGAQDgAAAAAAmJaAAAAAAAAAAABHhowAAAAAABbngwiocBzfacUJP37WCNTnSfiGub%2BhSsife4OHO5VMAAAAAkVVUlRFU1QAAAAAAAAAAABcI7sXin6ZN1piyUF6r8YxaAcf%2FgmNFo9NaZbbQGAQDgAAAAAAmJaAAAAAAA%3D%3D&type=OperationResult&network=test
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAIAAAAAAAAAAQAAAAAp9kRanuQMa2XlcQS2SWs5LRrVwEihfnrPyY6PrfiDTgAAAAAAAAACAAAAAkVVUlRFU1QAAAAAAAAAAABcI7sXin6ZN1piyUF6r8YxaAcf/gmNFo9NaZbbQGAQDgAAAAAAmJaAAAAAAAAAAABHhowAAAAAABbngwiocBzfacUJP37WCNTnSfiGub+hSsife4OHO5VMAAAAAkVVUlRFU1QAAAAAAAAAAABcI7sXin6ZN1piyUF6r8YxaAcf/gmNFo9NaZbbQGAQDgAAAAAAmJaAAAAAAA=='));

        /** @var PathPaymentResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof PathPaymentResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());

        $this->assertEquals('GALOPAYIVBYBZX3JYUET67WWBDKOOSPYQ2437IKKZCPXXA4HHOKUZ5OA', $result->getDestination()->getAccountIdString());
        $this->assertEquals(1, $result->getPaidAmount()->getScaledValue());

        $this->assertEquals('EURTEST', $result->getPaidAsset()->getAssetCode());
        $this->assertEquals('GBOCHOYXRJ7JSN22MLEUC6VPYYYWQBY77YEY2FUPJVUZNW2AMAIA5ISC', $result->getPaidAsset()->getIssuer()->getAccountIdString());

        // There should be one matched offer
        $claimedOffers = $result->getClaimedOffers();
        $this->assertCount(1, $claimedOffers);

        $offer = $claimedOffers[0];

        $this->assertEquals('GAU7MRC2T3SAY23F4VYQJNSJNM4S2GWVYBEKC7T2Z7EY5D5N7CBU4QSH', $offer->getSeller()->getAccountIdString());
        $this->assertNotEmpty($offer->getOfferId());

        $this->assertEquals('EURTEST', $offer->getAssetSold()->getAssetCode());
        $this->assertEquals('GBOCHOYXRJ7JSN22MLEUC6VPYYYWQBY77YEY2FUPJVUZNW2AMAIA5ISC', $offer->getAssetSold()->getIssuer()->getAccountIdString());
        $this->assertEquals(1, $offer->getAmountSold()->getScaledValue());

        $this->assertEquals(Asset::TYPE_NATIVE, $offer->getAssetBought()->getType());
        $this->assertEquals(120, $offer->getAmountBought()->getScaledValue());
    }

    public function testManageOfferOperationResultCreate()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAMAAAAAAAAAAAAAAAAAAAAAZu10Z8p5vPaI2kYKQYdT9yrRS1hk1v5lj9ltvEt8rbkAAAAAAAAACAAAAAJVU0RURVNUAAAAAAAAAAAA0/c9zJnJQpAz9WkfgbePguOqP+8FnamSQyAubOcJB1EAAAAAAAAAAACYloAAAAACAAAAAQAAAAAAAAAAAAAAAA=='));

        /** @var ManageOfferResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof ManageOfferResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());

        // Should be 0 offers claimed since this is a "create"
        $this->assertCount(0, $result->getClaimedOffers());
        $this->assertNotEmpty($result->getOffer()->getOfferId());

        $this->assertEquals('GBTO25DHZJ43Z5UI3JDAUQMHKP3SVUKLLBSNN7TFR7MW3PCLPSW3SFQQ', $result->getOffer()->getSeller()->getAccountIdString());
        $this->assertEquals('GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG', $result->getOffer()->getSellingAsset()->getIssuer()->getAccountIdString());

        $this->assertEquals(Asset::TYPE_NATIVE, $result->getOffer()->getBuyingAsset()->getType());
        $this->assertEquals(1, $result->getOffer()->getSellingAmount()->getScaledValue());
        $this->assertEquals(2, $result->getOffer()->getPrice()->toFloat());
    }

    public function testManageOfferOperationResultUpdate()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAMAAAAAAAAAAAAAAAEAAAAAZu10Z8p5vPaI2kYKQYdT9yrRS1hk1v5lj9ltvEt8rbkAAAAAAAAACAAAAAJVU0RURVNUAAAAAAAAAAAA0/c9zJnJQpAz9WkfgbePguOqP+8FnamSQyAubOcJB1EAAAAAAAAAAACYloAAAAADAAAAAQAAAAAAAAAAAAAAAA=='));

        /** @var ManageOfferResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof ManageOfferResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());

        // Should be 0 offers claimed since this is a "create"
        $this->assertCount(0, $result->getClaimedOffers());
        $this->assertEquals(8, $result->getOffer()->getOfferId());

        $this->assertEquals('GBTO25DHZJ43Z5UI3JDAUQMHKP3SVUKLLBSNN7TFR7MW3PCLPSW3SFQQ', $result->getOffer()->getSeller()->getAccountIdString());
        $this->assertEquals('GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG', $result->getOffer()->getSellingAsset()->getIssuer()->getAccountIdString());

        $this->assertEquals(Asset::TYPE_NATIVE, $result->getOffer()->getBuyingAsset()->getType());
        $this->assertEquals(1, $result->getOffer()->getSellingAmount()->getScaledValue());
        $this->assertEquals(3, $result->getOffer()->getPrice()->toFloat());
    }

    public function testManageOfferOperationResultDelete()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAMAAAAAAAAAAAAAAAIAAAAA'));

        /** @var ManageOfferResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof ManageOfferResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testSetOptionsResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAUAAAAAAAAAAA=='));

        /** @var SetOptionsResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof SetOptionsResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testChangeTrustResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAYAAAAAAAAAAA=='));

        /** @var ChangeTrustResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof ChangeTrustResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testAllowTrustResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAcAAAAAAAAAAA=='));

        /** @var AllowTrustResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof AllowTrustResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testAccountMergeResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAgAAAAAAAAAAAHJwxwAAAAA'));

        /** @var AccountMergeResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof AccountMergeResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());

        // Balance is 3 XLM - fee
        $this->assertEquals(2.99999, $result->getTransferredBalance()->getScaledValue());
    }

    public function testInflationResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAkAAAAAAAAAAAAAAAA='));

        /** @var InflationResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof InflationResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }

    public function testManageDataResultSuccess()
    {
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAAoAAAAAAAAAAA=='));

        /** @var ManageDataResult $result */
        $result = OperationResult::fromXdr($xdr);

        $this->assertTrue($result instanceof ManageDataResult, 'Incorrect class returned');
        $this->assertEquals(null, $result->getErrorCode());
    }
}