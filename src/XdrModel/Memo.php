<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 *
 * Union with fields:
 *  memoType (enum)
 *  value:
 *      none: void
 *      text: string(28)
 *      id: uint64
 *      hash: Hash
 *      return: Hash
 */
class Memo
{
    const MEMO_TYPE_NONE = 0;
    const MEMO_TYPE_TEXT = 1;
    const MEMO_TYPE_ID = 2;
    const MEMO_TYPE_HASH = 3;
    const MEMO_TYPE_RETURN = 4;

    // Text memos can be up to 28 characters
    const VALUE_TEXT_MAX_SIZE = 28;

    /**
     * See the MEMO_TYPE constants
     *
     * @var int
     */
    private $type;

    /**
     * Various, encoded to XDR based on the value of $this->type
     *
     * @var string
     */
    private $value;

    public function __construct($type, $value = null)
    {
        $this->type = $type;
        $this->value = $value;

        $this->validate();
    }

    public function validate()
    {
        if ($this->type == static::MEMO_TYPE_NONE) return;
        if ($this->type == static::MEMO_TYPE_TEXT) {
            // Verify length does not exceed max
            if (strlen($this->value) > static::VALUE_TEXT_MAX_SIZE) {
                throw new \ErrorException(sprintf('memo text is greater than the maximum of %s bytes', static::VALUE_TEXT_MAX_SIZE));
            }
        }
        if ($this->type == static::MEMO_TYPE_ID) {
            if ($this->value < 0) throw new \ErrorException('value cannot be negative');
            if ($this->value > PHP_INT_MAX) throw new \ErrorException(sprintf('value cannot be larger than %s', PHP_INT_MAX));
        }
        if ($this->type == static::MEMO_TYPE_HASH || $this->type == static::MEMO_TYPE_RETURN) {
            if (strlen($this->value) != 32) throw new \InvalidArgumentException(sprintf('hash values must be 32 bytes, got %s bytes', strlen($this->value)));
        }
    }

    public function toXdr()
    {
        $this->validate();
        $bytes = '';

        // Type
        $bytes .= XdrEncoder::unsignedInteger($this->type);

        // Value
        if ($this->type == static::MEMO_TYPE_NONE) {
            // no-op
        }
        if ($this->type == static::MEMO_TYPE_TEXT) {
            $bytes .= XdrEncoder::string($this->value, static::VALUE_TEXT_MAX_SIZE);
        }
        if ($this->type == static::MEMO_TYPE_ID) {
            $bytes .= XdrEncoder::unsignedInteger64($this->value);
        }
        if ($this->type == static::MEMO_TYPE_HASH) {
            $bytes .= XdrEncoder::opaqueFixed($this->value, 32);
        }
        if ($this->type == static::MEMO_TYPE_RETURN) {
            $bytes .= XdrEncoder::opaqueFixed($this->value, 32);
        }

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return Memo
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $type = $xdr->readUnsignedInteger();

        $memo = new Memo($type);

        if ($memo->type == static::MEMO_TYPE_TEXT) {
            $memo->value = $xdr->readString(static::VALUE_TEXT_MAX_SIZE);
        }
        if ($memo->type == static::MEMO_TYPE_ID) {
            $memo->value = $xdr->readBigInteger()->toString();
        }
        if ($memo->type == static::MEMO_TYPE_HASH
        || $memo->type == static::MEMO_TYPE_RETURN) {
            $memo->value = $xdr->readOpaqueFixed(32);
        }

        return $memo;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}