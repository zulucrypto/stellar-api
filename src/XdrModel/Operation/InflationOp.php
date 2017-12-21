<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


/**
 * This is an empty operation that triggers the network to calculate inflation
 */
class InflationOp extends Operation
{
    public function __construct()
    {
        parent::__construct(Operation::TYPE_INFLATION);
    }
}