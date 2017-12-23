<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\Model;


use phpseclib\Math\BigInteger;
use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Model\StellarAmount;

class StellarAmountTest extends TestCase
{
    /**
     * Verify passing PHP integers and floats works as expected
     */
    public function testValues()
    {
        $this->assertEquals(1, (new StellarAmount('1'))->getScaledValue());
        $this->assertEquals(20.1, (new StellarAmount(20.1))->getScaledValue());
        $this->assertEquals(0.005, (new StellarAmount(0.005))->getScaledValue());
        $this->assertEquals(922337203685.1, (new StellarAmount(922337203685.1))->getScaledValue());
        $this->assertEquals(0, (new StellarAmount(0))->getScaledValue());
        $this->assertEquals(10103.0150003, (new StellarAmount(10103.0150003))->getScaledValue());
        $this->assertEquals(10103.0150003, (new StellarAmount('10103.0150003'))->getScaledValue());
    }

    public function testBigIntegers()
    {
        // Create with the maximum amount
        new StellarAmount(new BigInteger('9223372036854775807'));
        // Create with the minimum amount
        new StellarAmount(new BigInteger('0'));

        $this->assertEquals(0, (new StellarAmount(new BigInteger('0')))->getScaledValue());
        $this->assertEquals(1, (new StellarAmount(new BigInteger('10000000')))->getScaledValue());
        $this->assertEquals(1.1, (new StellarAmount(new BigInteger('11000000')))->getScaledValue());
        $this->assertEquals(1.0000001, (new StellarAmount(new BigInteger('10000001')))->getScaledValue());
        $this->assertEquals(922337203685.4775807, (new StellarAmount(new BigInteger('9223372036854775807')))->getScaledValue());
        $this->assertEquals(922337203685.4775807, (StellarAmount::newMaximum())->getScaledValue());
    }

    public function testSmallValues()
    {
        $this->assertEquals(0.00001, (new StellarAmount(0.00001))->getScaledValue());
        // 1 stroop
        $this->assertEquals(0.0000001, (new StellarAmount(0.0000001))->getScaledValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetScaledValueTooBig()
    {
        // Maximum number of XLM that can be held is 922337203685.4775807
        $amount = new StellarAmount(922337203686);

        // With throw an exception
        $amount->getScaledValue();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionWhenNegative()
    {
        $amount = new StellarAmount(-1);
    }
}