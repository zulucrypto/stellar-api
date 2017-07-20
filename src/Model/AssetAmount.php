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
     * @var BigInteger
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
     * AssetAmount constructor.
     *
     * $unscaledBalance is the balance with a decimal point. For example,
     * 100 lumens would be passed in as '100.0000000'
     *
     * If a BigInteger is passed in, it's assumed to be a scaled balance
     *
     * See: https://www.stellar.org/developers/guides/concepts/assets.html
     *
     * This class stores the balance internally as a BigInteger
     *
     * @param string $scaledAmountOrUnscaledBigInteger
     * @param string $assetType
     */
    public function __construct($scaledAmountOrUnscaledBigInteger, $assetType = 'native')
    {
        $this->assetType = $assetType;

        // todo: move this to setAmount()
        // If the amount is not a BigInteger it needs to be converted to one
        if (!$scaledAmountOrUnscaledBigInteger instanceof BigInteger) {
            $parts = explode('.', $scaledAmountOrUnscaledBigInteger);
            $unscaledAmount = new BigInteger('0');

            // Everything to the left of the decimal point
            if ($parts[0]) {
                $unscaledAmountLeft = (new BigInteger($parts[0]))->multiply(new BigInteger(self::ASSET_SCALE));
                $unscaledAmount = $unscaledAmount->add($unscaledAmountLeft);
            }

            // Add everything to the right of the decimal point
            if (count($parts) == 2 && str_replace('0', '', $parts[1]) != '') {
                // Should be a total of 7 decimal digits to the right of the decimal
                $unscaledAmountRight = str_pad($parts[1], 7, STR_PAD_RIGHT);
                $unscaledAmount = $unscaledAmount->add(new BigInteger($unscaledAmountRight));
            }

            $this->amount = $unscaledAmount;
        }
        else {
            $this->amount = $scaledAmountOrUnscaledBigInteger;
        }
    }

    public function __toString()
    {
        return sprintf("%s %s", $this->getBalanceString(), $this->getAssetCode());
    }

    /**
     * @return bool
     */
    public function isNativeAsset()
    {
        return self::ASSET_TYPE_NATIVE == $this->assetType;
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
     * @return BigInteger
     */
    public function getUnscaledBalance()
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getBalanceString()
    {
        list($quotient, $remainder) = $this->amount->divide(new BigInteger(self::ASSET_SCALE));

        return ($remainder->value) ? sprintf('%s.%s', $quotient, $remainder) : $quotient;
    }

    /**
     * @param BigInteger $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
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