<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\Xdr;


use phpseclib\Math\BigInteger;
use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

class XdrEncoderTest extends TestCase
{
    public function testSignedBigInteger64()
    {
        // 100
        $this->assertBytesEqual('00 00 00 00 00 00 00 64', XdrEncoder::signedBigInteger64(new BigInteger("100")));

        // -1
        $this->assertBytesEqual('ff ff ff ff ff ff ff ff', XdrEncoder::signedBigInteger64(new BigInteger("-1")));

        // MAX_INT
        $this->assertBytesEqual('00 00 00 00 ff ff ff ff', XdrEncoder::signedBigInteger64(new BigInteger("4294967295")));

        // MAX_INT + 1
        $this->assertBytesEqual('00 00 00 01 00 00 00 00', XdrEncoder::signedBigInteger64(new BigInteger("4294967296")));

        // max positive signed int64
        $this->assertBytesEqual('7f ff ff ff ff ff ff ff', XdrEncoder::signedBigInteger64(new BigInteger("9223372036854775807")));

        // max negative signed int64
        $this->assertBytesEqual('80 00 00 00 00 00 00 00', XdrEncoder::signedBigInteger64(new BigInteger("-9223372036854775808")));
    }

    public function testOpaqueVariable()
    {
        // Test padding is applied when characters are not a multiple of 4
        $this->assertBytesEqual('00 00 00 05 31 32 33 34 35 00 00 00', XdrEncoder::opaqueVariable('12345'));
    }


    protected function assertBytesEqual($expectedString, $actualBytes)
    {
        $expectedBytes = '';
        $rawExpected = explode(' ', $expectedString);
        foreach ($rawExpected as $raw) {
            $expectedBytes .= hex2bin($raw);
        }

        $this->assertEquals(hash('sha256', $expectedBytes), hash('sha256', $actualBytes));
    }
}