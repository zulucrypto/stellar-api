<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\CreateAccountResult;
use ZuluCrypto\StellarSdk\XdrModel\OperationResult;
use ZuluCrypto\StellarSdk\XdrModel\PathPaymentResult;
use ZuluCrypto\StellarSdk\XdrModel\PaymentResult;

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
}