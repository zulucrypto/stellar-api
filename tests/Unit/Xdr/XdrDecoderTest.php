<?php

namespace ZuluCrypto\StellarSdk\Test\Unit\Xdr;

use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Xdr\XdrDecoder;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

class XdrDecoderTest extends TestCase
{
    public function testUnsignedInteger()
    {
        $this->assertEquals(0, XdrDecoder::unsignedInteger("\x00\x00\x00\x00"));
        $this->assertEquals(4294967295, XdrDecoder::unsignedInteger("\xFF\xFF\xFF\xFF"));

        $this->assertEquals(268435456, XdrDecoder::unsignedInteger(XdrEncoder::unsignedInteger(268435456)));
    }

    public function testSignedInteger()
    {
        $this->assertEquals(0, XdrDecoder::signedInteger("\x00\x00\x00\x00"));
        $this->assertEquals(1, XdrDecoder::signedInteger("\x00\x00\x00\x01"));
        $this->assertEquals(-1, XdrDecoder::signedInteger("\xFF\xFF\xFF\xFF"));

        $this->assertEquals(-1024, XdrDecoder::signedInteger(XdrEncoder::unsignedInteger(-1024)));
    }

    public function testUnsignedInteger64()
    {
        $this->assertEquals(0, XdrDecoder::unsignedInteger64("\x00\x00\x00\x00\x00\x00\x00\x00"));

        $this->assertEquals(4294967295, XdrDecoder::unsignedInteger64("\x00\x00\x00\x00\xFF\xFF\xFF\xFF"));

        $this->assertEquals(72057594000000000, XdrDecoder::unsignedInteger64(XdrEncoder::unsignedInteger64(72057594000000000)));
        $this->assertEquals(9223372036854775807, XdrDecoder::unsignedInteger64(XdrEncoder::unsignedInteger64(9223372036854775807)));
    }

    public function testSignedInteger64()
    {
        $this->assertEquals(0, XdrDecoder::signedInteger64("\x00\x00\x00\x00\x00\x00\x00\x00"));

        $this->assertEquals(4294967295, XdrDecoder::signedInteger64("\x00\x00\x00\x00\xFF\xFF\xFF\xFF"));

        $this->assertEquals(-1, XdrDecoder::signedInteger64(XdrEncoder::signedInteger64(-1)));
        $this->assertEquals(9223372036854775807, XdrDecoder::signedInteger64(XdrEncoder::signedInteger64(9223372036854775807)));
    }

    public function testOpaqueFixed()
    {
        $this->assertBytesEqual('00 11 22', XdrDecoder::opaqueFixed("\x00\x11\x22\x33\x44\x55", 3));
    }

    public function testOpaqueVariable()
    {
        $this->assertBytesEqual('00 11 22', XdrDecoder::opaqueVariable("\x00\x00\x00\x03\x00\x11\x22\x00"));
    }

    public function testBoolean()
    {
        $this->assertEquals(true, XdrDecoder::boolean("\x00\x00\x00\x01"));
        $this->assertEquals(false, XdrDecoder::boolean("\x00\x00\x00\x00"));
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