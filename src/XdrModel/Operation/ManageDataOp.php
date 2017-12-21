<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Util\Debug;
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

        if (strlen($key) > 64) throw new \InvalidArgumentException('$key cannot be greater than 64 characters long');
        if (strlen($value) > 64) throw new \InvalidArgumentException('$key cannot be greater than 64 characters long');

        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Key
        $bytes .= XdrEncoder::string($this->key, 64);

        // Value
        $bytes .= XdrEncoder::optionalString($this->value, 64);

        return $bytes;
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
    public function setValue($value)
    {
        $this->value = $value;
    }
}