<?php


namespace ZuluCrypto\StellarSdk\Model;


use phpseclib\Math\BigInteger;

class AssetAmount
{
    const ASSET_TYPE_NATIVE = 'native';
    const ASSET_TYPE_CREDIT_ALPHANUM4 = 'credit_alphanum4';
    const ASSET_TYPE_CREDIT_ALPHANUM12 = 'credit_alphanum12';

    // Balances are represented in most UIs with a decimal point but stored
    // in the XDR structure and this class as signed 64-bit integers
    const ASSET_SCALE = 10000000; // 10 million

    /**
     * Asset type, see ASSET_TYPE_* constants
     *
     * @var string
     */
    private $assetType;

    /**
     * @var StellarAmount
     */
    private $amount;

    /**
     * Asset code as defined by the asset issuer
     *
     * @var string
     */
    private $assetCode;

    /**
     * Public address of the asset issuer
     *
     * @var string
     */
    private $assetIssuerAccountId;

    /**
     * The maximum amount of this asset that can be held
     *
     * @var StellarAmount
     */
    private $limit;

    /**
     * AssetAmount constructor.
     *
     * $unscaledBalance is the balance with a decimal point. For example,
     * 100 lumens would be passed in as '100.0000000'
     *
     * If a BigInteger is passed in, it's assumed to be a scaled balance
     *
     * See: https://www.stellar.org/developers/guides/concepts/assets.html
     *
     * This class stores the balance internally as a StellarAmount
     *
     * @param number|BigInteger $amount
     * @param string $assetType
     */
    public function __construct($amount, $assetType = 'native')
    {
        $this->assetType = $assetType;

        $this->amount = new StellarAmount($amount);
    }

    public function __toString()
    {
        return sprintf("%s %s", strval($this->getBalance()), $this->getAssetCode());
    }

    /**
     * @return bool
     */
    public function isNativeAsset()
    {
        return self::ASSET_TYPE_NATIVE == $this->assetType;
    }

    /**
     * @return StellarAmount
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param number|BigInteger $limit
     */
    public function setLimit($limit)
    {
        $this->limit = new StellarAmount($limit);
    }

    /**
     * @return string
     */
    public function getAssetType()
    {
        return $this->assetType;
    }

    public function getAssetCode()
    {
        if ($this->isNativeAsset()) return 'XLM';

        return $this->assetCode;
    }

    /**
     * @param string $assetType
     */
    public function setAssetType($assetType)
    {
        $this->assetType = $assetType;
    }

    /**
     * @return string
     */
    public function getUnscaledBalance()
    {
        return $this->amount->getUnscaledString();
    }

    /**
     * @return number
     */
    public function getBalance()
    {
        return $this->amount->getScaledValue();
    }

    /**
     * Returns the stroop representation of this AssetAmount
     *
     * @return BigInteger
     */
    public function getBalanceAsStroops()
    {
        return $this->amount->getUnscaledBigInteger();
    }

    /**
     * @param number|BigInteger $amount
     */
    public function setAmount($amount)
    {
        $this->amount = new StellarAmount($amount);
    }

    /**
     * @return string
     */
    public function getAssetIssuerAccountId()
    {
        return $this->assetIssuerAccountId;
    }

    /**
     * @param string $assetIssuerAccountId
     */
    public function setAssetIssuerAccountId($assetIssuerAccountId)
    {
        $this->assetIssuerAccountId = $assetIssuerAccountId;
    }

    /**
     * @param string $assetCode
     */
    public function setAssetCode($assetCode)
    {
        $this->assetCode = $assetCode;
    }
}