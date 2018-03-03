<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Model\StellarAmount;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

class InflationPayout
{
    /**
     * @var AccountId
     */
    protected $destination;

    /**
     * @var StellarAmount
     */
    protected $amount;

    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new InflationPayout();

        $model->destination = AccountId::fromXdr($xdr);
        $model->amount = new StellarAmount($xdr->readInteger64());

        return $model;
    }

    /**
     * @return AccountId
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * @param AccountId $destination
     */
    public function setDestination($destination)
    {
        $this->destination = $destination;
    }

    /**
     * @return StellarAmount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param StellarAmount $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }
}