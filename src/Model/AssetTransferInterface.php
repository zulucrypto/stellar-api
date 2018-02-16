<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * Represents an asset transfer. This could be a payment, account creation, path
 * payment, account merge, etc.
 */
interface AssetTransferInterface
{
    /**
     * Returns a string describing the type of asset transfer (usually the operation
     * code)
     *
     * @return string
     */
    public function getAssetTransferType();

    /**
     * @return string
     */
    public function getFromAccountId();

    /**
     * @return string
     */
    public function getToAccountId();

    /**
     * NOTE: Return value from this method may be null. For example, a merge operation
     *
     * @return AssetAmount|null
     */
    public function getAssetAmount();
}