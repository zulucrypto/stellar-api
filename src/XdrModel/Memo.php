<?php

namespace ZuluCrypto\StellarSdk\XdrModel;


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
 *
 * todo: implement the rest of the types
 */
class Memo
{
    const MEMO_TYPE_NONE = 0;
    const MEMO_TYPE_TEXT = 1;
    const MEMO_TYPE_ID = 2;
    const MEMO_TYPE_HASH = 3;
    const MEMO_TYPE_RETURN = 4;

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
    }

    public function toXdr()
    {
        $bytes = '';

        // Type
        $bytes .= XdrEncoder::unsignedInteger(self::MEMO_TYPE_NONE);

        // Value
        // todo

        return $bytes;
    }
}