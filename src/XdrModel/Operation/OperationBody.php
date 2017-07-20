<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


/**
 * Union
 *  createAccount todo
 *  payment todo
 *  pathPayment todo
 *  manageOffer todo
 *  createPassiveOffer todo
 *  setOption SetOptionsOp
 *  changeTrust todo
 *  allowTrust todo
 *  accountMerge AccountId todo
 *  inflation void todo
 *  manageDatum todo
 */
class OperationBody
{
    /**
     * @var OperationType
     */
    private $type;

    /**
     * Depends on the value of $this->type
     *
     * @var mixed
     */
    private $value;

}