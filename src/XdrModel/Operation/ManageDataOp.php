<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 * https://github.com/stellar/stellar-core/blob/master/src/xdr/Stellar-transaction.x#L218
 */
class ManageDataOp extends Operation
{
    /**
     * @var string
     */
    protected $key;

    /**
     * Setting this to null will clear an existing value
     *
     * @var string
     */
    protected $value;

    public function __construct($key, $value = null, $sourceAccountId = null)
    {
        parent::__construct(Operation::TYPE_MANAGE_DATA, $sourceAccountId);

        $this->setKey($key);
        $this->setValue($value);
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Key
        $bytes .= XdrEncoder::string($this->key, 64);

        // Value (an optional field)
        if ($this->value) {
            $bytes .= XdrEncoder::boolean(true);
            $bytes .= XdrEncoder::opaqueVariable($this->value);
        }
        else {
            $bytes .= XdrEncoder::boolean(false);
        }

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return ManageDataOp|Operation
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $key = $xdr->readString(64);

        $value = null;
        if ($xdr->readBoolean()) {
            $value = $xdr->readOpaqueVariable(64);
        }

        return new ManageDataOp($key, $value);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        if (strlen($key) > 64) throw new \InvalidArgumentException('key cannot be longer than 64 characters');

        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue($value = null)
    {
        if (strlen($value) > 64) throw new \InvalidArgumentException('value cannot be longer than 64 characters');

        $this->value = $value;
    }
}