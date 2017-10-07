<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#create-passive-offer
 */
class CreatePassiveOfferOperation extends ManageOfferOperation
{
    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type = Operation::TYPE_CREATE_PASSIVE_OFFER)
    {
        parent::__construct($id, $type);
    }
}