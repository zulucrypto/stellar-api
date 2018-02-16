<?php


namespace ZuluCrypto\StellarSdk\XdrModel;


use ZuluCrypto\StellarSdk\Xdr\Iface\XdrEncodableInterface;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;

/**
 * XDR union info:
 *  native: void
 *  alphanum4: AssetAlphaNum4
 *      assetCode: opaque<4>
 *      issuer: AccountId
 *  alphanum12: AssetAlphaNum12
 *      assetCode: opaque<12>
 *      issuer: AccountId
 */
class Asset implements XdrEncodableInterface
{
    const TYPE_NATIVE       = 0;
    const TYPE_ALPHANUM_4   = 1;
    const TYPE_ALPHANUM_12  = 2;

    /**
     * See the TYPE_ constants
     *
     * @var int
     */
    private $type;

    /**
     * Either 4 or 12 bytes describing the asset code (depending on the value of $type)
     *
     * @var string
     */
    private $assetCode;

    /**
     * @var AccountId
     */
    private $issuer;

    /**
     * @return Asset
     */
    public static function newNativeAsset()
    {
        return new Asset(Asset::TYPE_NATIVE);
    }

    /**
     * @param $code
     * @param $issuerId
     * @return Asset
     */
    public static function newCustomAsset($code, $issuerId)
    {
        // Default to 4-character alphanum
        $type = Asset::TYPE_ALPHANUM_4;
        $codeLen = strlen($code);

        // todo: additional validation
        if (!$codeLen || $codeLen > 12) throw new \InvalidArgumentException('Invalid code length (must be >=1 and <= 12');

        if ($codeLen > 4) $type = Asset::TYPE_ALPHANUM_12;

        $asset = new Asset($type);
        $asset->assetCode = $code;
        $asset->issuer = new AccountId($issuerId);

        return $asset;
    }

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function toXdr()
    {
        $bytes = '';

        $bytes .= XdrEncoder::unsignedInteger($this->type);

        if ($this->type == self::TYPE_NATIVE) {
            // no additional content for native types
        }
        elseif ($this->type == self::TYPE_ALPHANUM_4) {
            $bytes .= XdrEncoder::opaqueFixed($this->assetCode, 4, true);
            $bytes .= $this->issuer->toXdr();
        }
        elseif ($this->type == self::TYPE_ALPHANUM_12) {
            $bytes .= XdrEncoder::opaqueFixed($this->assetCode, 12, true);
            $bytes .= $this->issuer->toXdr();
        }

        return $bytes;
    }

    /**
     * @param XdrBuffer $xdr
     * @return Asset
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $type = $xdr->readUnsignedInteger();

        $model = new Asset($type);

        if ($type == static::TYPE_ALPHANUM_4) {
            $model->assetCode = $xdr->readOpaqueFixedString(4);
            $model->issuer = AccountId::fromXdr($xdr);
        }
        if ($type == static::TYPE_ALPHANUM_12) {
            $model->assetCode = $xdr->readOpaqueFixedString(12);
            $model->issuer = AccountId::fromXdr($xdr);
        }

        return $model;
    }

    /**
     * @return bool
     */
    public function isNative()
    {
        return $this->type === static::TYPE_NATIVE;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getAssetCode()
    {
        return $this->assetCode;
    }

    /**
     * @param string $assetCode
     */
    public function setAssetCode($assetCode)
    {
        $this->assetCode = $assetCode;
    }

    /**
     * @return AccountId
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * @param AccountId $issuer
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;
    }
}