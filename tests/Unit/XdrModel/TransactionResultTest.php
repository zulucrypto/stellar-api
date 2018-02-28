<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TransactionResult;

class TransactionResultTest extends TestCase
{
    public function testFromXdrOnePayment()
    {
        // 100 stroop fee, successful, 1 successful payment
        $xdr = new XdrBuffer(base64_decode('AAAAAAAAAGQAAAAAAAAAAQAAAAAAAAABAAAAAAAAAAA='));

        $result = TransactionResult::fromXdr($xdr);
    }
}