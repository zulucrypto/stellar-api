<?php


namespace ZuluCrypto\StellarSdk\Xdr;

use phpseclib\Math\BigInteger;


/**
 * Enables easy iteration through a blob of XDR data
 */
class XdrBuffer
{
    /**
     * @var string
     */
    protected $xdrBytes;

    /**
     * Current position within the bytes
     *
     * @var int
     */
    protected $position;

    public function __construct($xdrBytes)
    {
        $this->xdrBytes = $xdrBytes;

        $this->position = 0;
        $this->size = strlen($xdrBytes);
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function readUnsignedInteger()
    {
        $dataSize = 4;
        $this->assertBytesRemaining($dataSize);

        $data = XdrDecoder::unsignedInteger(substr($this->xdrBytes, $this->position, $dataSize));
        $this->position += $dataSize;

        return $data;
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function readUnsignedInteger64()
    {
        $dataSize = 8;
        $this->assertBytesRemaining($dataSize);

        $data = XdrDecoder::unsignedInteger64(substr($this->xdrBytes, $this->position, $dataSize));
        $this->position += $dataSize;

        return $data;
    }

    /**
     * @return BigInteger
     * @throws \ErrorException
     */
    public function readBigInteger()
    {
        $dataSize = 8;
        $this->assertBytesRemaining($dataSize);

        $bigInteger = new BigInteger(substr($this->xdrBytes, $this->position, $dataSize), 256);
        $this->position += $dataSize;

        return $bigInteger;
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function readInteger()
    {
        $dataSize = 4;
        $this->assertBytesRemaining($dataSize);

        $data = XdrDecoder::signedInteger(substr($this->xdrBytes, $this->position, $dataSize));
        $this->position += $dataSize;

        return $data;
    }

    /**
     * @return int
     * @throws \ErrorException
     */
    public function readInteger64()
    {
        $dataSize = 8;
        $this->assertBytesRemaining($dataSize);

        $data = XdrDecoder::signedInteger64(substr($this->xdrBytes, $this->position, $dataSize));
        $this->position += $dataSize;

        return $data;
    }

    /**
     * @param $length
     * @return bool|string
     * @throws \ErrorException
     */
    public function readOpaqueFixed($length)
    {
        $this->assertBytesRemaining($length);

        $data = XdrDecoder::opaqueFixed(substr($this->xdrBytes, $this->position), $length);
        $this->position += $length;

        return $data;
    }

    /**
     * @param $length
     * @return string
     * @throws \ErrorException
     */
    public function readOpaqueFixedString($length)
    {
        $this->assertBytesRemaining($length);

        $data = XdrDecoder::opaqueFixedString(substr($this->xdrBytes, $this->position), $length);
        $this->position += $length;

        return $data;
    }

    /**
     * @return bool|string
     * @throws \ErrorException
     */
    public function readOpaqueVariable($maxLength = null)
    {
        $length = $this->readUnsignedInteger();
        $paddedLength = $this->roundTo4($length);

        if ($maxLength !== null && $length > $maxLength) {
            throw new \InvalidArgumentException(sprintf('length of %s exceeds max length of %s', $length, $maxLength));
        }

        $this->assertBytesRemaining($paddedLength);

        $data = XdrDecoder::opaqueFixed(substr($this->xdrBytes, $this->position), $length);
        $this->position += $paddedLength;

        return $data;
    }

    /**
     * @param null $maxLength
     * @return bool|string
     * @throws \ErrorException
     */
    public function readString($maxLength = null)
    {
        $strLen = $this->readUnsignedInteger();
        $paddedLength = $this->roundTo4($strLen);
        if ($strLen > $maxLength) throw new \InvalidArgumentException(sprintf('maxLength of %s exceeded (string is %s bytes)', $maxLength, $strLen));

        $this->assertBytesRemaining($paddedLength);

        $data = XdrDecoder::opaqueFixed(substr($this->xdrBytes, $this->position), $strLen);
        $this->position += $paddedLength;

        return $data;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function readBoolean()
    {
        $dataSize = 4;
        $this->assertBytesRemaining($dataSize);

        $data = XdrDecoder::boolean(substr($this->xdrBytes, $this->position, $dataSize));
        $this->position += $dataSize;

        return $data;
    }

    /**
     * @param $numBytes
     * @throws \ErrorException
     */
    protected function assertBytesRemaining($numBytes)
    {
        if ($this->position + $numBytes > $this->size) {
            throw new \ErrorException('Unexpected end of XDR data');
        }
    }

    /**
     * rounds $number up to the nearest value that's a multiple of 4
     *
     * @param $number
     * @return int
     */
    protected function roundTo4($number)
    {
        $remainder = $number % 4;
        if (!$remainder) return $number;

        return $number + (4 - $remainder);
    }
}