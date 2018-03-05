<?php


namespace ZuluCrypto\StellarSdk\Model;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Util\MathSafety;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

/**
 * Helper class for working with stellar values
 *
 * Although displayed to the user as 1 XLM, this value is stored in the protocol
 * as 10000000 stroops since all amounts are integers.
 *
 * Note that although the methods are named after Lumens and Stroops all custom
 * assets work the same way.
 */
class StellarAmount
{
    const STROOP_SCALE = 10000000; // 10 million, 7 zeroes

    /**
     * @var BigInteger
     */
    protected $stroops;

    /**
     * @var BigInteger
     */
    protected $stroopScaleBignum;

    /**
     * The largest amount of stroops that can fit in a signed int64
     * @var BigInteger
     */
    protected $maxSignedStroops64;

    /**
     * Returns the maximum supported amount
     *
     * @return StellarAmount
     */
    public static function newMaximum()
    {
        return new StellarAmount(new BigInteger('9223372036854775807'));
    }

    /**
     * Reads a StellarAmount from a SIGNED 64-bit integer
     *
     * @param XdrBuffer $xdr
     * @return StellarAmount
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        return new StellarAmount(new BigInteger($xdr->readInteger64()));
    }

    /**
     * StellarAmount constructor.
     *
     * @param $lumensOrBigIntegerStroops
     * @throws \ErrorException
     */
    public function __construct($lumensOrBigIntegerStroops)
    {
        // This class assumes 64-bit support
        MathSafety::require64Bit();

        $this->stroopScaleBignum = new BigInteger(static::STROOP_SCALE);
        $this->maxSignedStroops64 = new BigInteger('9223372036854775807');

        // Store everything as a BigInteger representing stroops
        if ($lumensOrBigIntegerStroops instanceof BigInteger) {
            $this->stroops = $lumensOrBigIntegerStroops;
        }
        // Can also pass in another StellarAmount
        else if ($lumensOrBigIntegerStroops instanceof StellarAmount) {
            $this->stroops = clone $lumensOrBigIntegerStroops->getUnscaledBigInteger();
        }
        else {
            $lumensOrBigIntegerStroops = number_format($lumensOrBigIntegerStroops, 7, '.', '');
            $parts = explode('.', $lumensOrBigIntegerStroops);
            $unscaledAmount = new BigInteger('0');

            // Everything to the left of the decimal point
            if ($parts[0]) {
                $unscaledAmountLeft = (new BigInteger($parts[0]))->multiply(new BigInteger(static::STROOP_SCALE));
                $unscaledAmount = $unscaledAmount->add($unscaledAmountLeft);
            }

            // Add everything to the right of the decimal point
            if (count($parts) == 2 && str_replace('0', '', $parts[1]) != '') {
                // Should be a total of 7 decimal digits to the right of the decimal
                $unscaledAmountRight = str_pad($parts[1], 7, '0',STR_PAD_RIGHT);
                $unscaledAmount = $unscaledAmount->add(new BigInteger($unscaledAmountRight));
            }

            $this->stroops = $unscaledAmount;
        }

        // Ensure amount of stroops doesn't exceed the maximum
        $compared = $this->stroops->compare($this->maxSignedStroops64);
        if ($compared > 0) {
            throw new \InvalidArgumentException('Maximum value exceeded. Value cannot be larger than 9223372036854775807 stroops (922337203685.4775807 XLM)');
        }

        // Ensure amount is not negative
        $zero = new BigInteger('0');
        $compared = $this->stroops->compare($zero);
        if ($compared < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    /**
     * @return float|int
     */
    public function getScaledValue()
    {
        /** @var $quotient BigInteger */
        /** @var $remainder BigInteger */
        list($quotient, $remainder) = $this->stroops->divide($this->stroopScaleBignum);

        $number = intval($quotient->toString()) + (intval($remainder->toString()) / intval($this->stroopScaleBignum->toString()));
        return number_format($number, 7, '.', '');
    }

    /**
     * Returns the raw value in stroops as a string
     *
     * @return string
     */
    public function getUnscaledString()
    {
        return $this->stroops->toString();
    }

    /**
     * Returns the raw value in stroops
     *
     * @return BigInteger
     */
    public function getUnscaledBigInteger()
    {
        return $this->stroops;
    }
}