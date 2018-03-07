<?php


namespace ZuluCrypto\StellarSdk\Xdr\Type;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

class VariableArray implements XdrEncodableInterface
{
    /**
     * @var XdrEncodableInterface[]
     */
    private $elements;

    public function __construct()
    {
        $this->elements = [];
    }

    public function append(XdrEncodableInterface $element)
    {
        $this->elements[] = $element;
    }

    public function toXdr()
    {
        $bytes = '';

        if (count($this->elements) > (pow(2, 32) - 1)) {
            throw new \ErrorException('Maximum number of elements exceeded');
        }

        $bytes .= XdrEncoder::unsignedInteger(count($this->elements));

        foreach ($this->elements as $element) {
            $bytes .= $element->toXdr();
        }

        return $bytes;
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array_values($this->elements);
    }
}