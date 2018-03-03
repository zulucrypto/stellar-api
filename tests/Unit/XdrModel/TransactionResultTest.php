<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\PaymentResult;
use ZuluCrypto\StellarSdk\XdrModel\TransactionResult;

class TransactionResultTest extends TestCase
{
    public function testFromXdrOnePayment()
    {
        // 100 stroop fee, successful, 1 successful payment
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAGQAAAAAAAAAAQAAAAAAAAABAAAAAAAAAAA='));

        $result = TransactionResult::fromXdr($xdr);

        $opResults = $result->getOperationResults();

        $this->assertEquals(TransactionResult::SUCCESS, $result->getResultCode());
        $this->assertCount(1, $opResults);
        $this->assertEquals(100, $result->getFeeCharged()->getUnscaledString());
        $this->assertTrue($result->succeeded());

        $paymentResult = $opResults[0];
        $this->assertTrue($paymentResult instanceof PaymentResult);
        $this->assertTrue($paymentResult->succeeded());
    }
}