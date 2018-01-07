<?php


namespace ZuluCrypto\StellarSdk\Signing;

use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\XdrModel\DecoratedSignature;

interface SigningInterface
{
    /**
     * Returns a DecoratedSignature for the given TransactionBuilder
     *
     * @param TransactionBuilder $builder
     * @return DecoratedSignature
     */
    public function signTransaction(TransactionBuilder $builder);
}