<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\XdrModel;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;

class TransactionEnvelopeTest extends TestCase
{
    public function testFromXdr()
    {
        // Envelope with one operation and one signature
        $xdr = new XdrBuffer(base64_decode('AAAAAPiYlW8vAGxCaxMz+HxxWaGYTNZzlDoJsaqN7xu5rjBZAAAAZAB2pQsAAAABAAAAAAAAAAAAAAABAAAAAQAAAAAW54MIqHAc32nFCT9+1gjU50n4hrm/oUrIn3uDhzuVTAAAAAEAAAAA43JoMKC2DLX1LIRM/81O7WXrpcFV6JsmQRVick5x5UQAAAAAAAAAADuaygAAAAAAAAAAAbmuMFkAAABAGIlPtdw9ye1dUM/u60QZ18+7GWfEnddxPdjgXkUy5ovDY+tzHPTimlqv1T7k/39SGoSamX9rUnFjnH6NxrbPBQ=='));

        $parsed = TransactionEnvelope::fromXdr($xdr);

        $signatures = $parsed->getDecoratedSignatures();

        $this->assertCount(1, $signatures);
    }
}