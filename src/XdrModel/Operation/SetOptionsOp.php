<?php


namespace ZuluCrypto\StellarSdk\XdrModel\Operation;


use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Signer;

class SetOptionsOp extends Operation
{
    const FLAG_AUTH_REQUIRED    = 1;
    const FLAG_AUTH_REVOCABLE   = 2;

    /**
     * @var AccountId
     */
    private $inflationDestinationAccount;

    /**
     * @var integer uint32
     */
    private $clearFlags;

    /**
     * @var integer uint32
     */
    private $setFlags;

    /**
     * @var integer uint32
     */
    private $masterWeight;

    /**
     * @var integer uint32
     */
    private $lowThreshold;

    /**
     * @var integer uint32
     */
    private $mediumThreshold;

    /**
     * @var integer uint32
     */
    private $highThreshold;

    /**
     * Base-32 encoded
     * @var string<32>
     */
    private $homeDomain;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * @return SetOptionsOp
     */
    public function __construct($sourceAccountId = null)
    {
        parent::__construct(Operation::TYPE_SET_OPTIONS, $sourceAccountId);

        $this->setFlags = null;
        $this->clearFlags = null;

        return $this;
    }

    public function toXdr()
    {
        $bytes = parent::toXdr();

        // Treat 0 flags as null
        if ($this->setFlags === 0) $this->setFlags = null;
        if ($this->clearFlags === 0) $this->clearFlags = null;

        // inflation destination
        $bytes .= XdrEncoder::optional($this->inflationDestinationAccount);

        // clear flags
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->clearFlags);

        // set flags
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->setFlags);

        // master weight
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->masterWeight);

        // low threshold
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->lowThreshold);

        // medium threshold
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->mediumThreshold);

        // high threshold
        $bytes .= XdrEncoder::optionalUnsignedInteger($this->highThreshold);

        // home domain
        $bytes .= XdrEncoder::optionalString($this->homeDomain, 32);

        // Signer
        $bytes .= XdrEncoder::optional($this->signer);

        return $bytes;
    }

    /**
     * @deprecated Do not call this directly, instead call Operation::fromXdr()
     * @param XdrBuffer $xdr
     * @return Operation|SetOptionsOp
     * @throws \ErrorException
     */
    public static function fromXdr(XdrBuffer $xdr)
    {
        $model = new SetOptionsOp();

        // inflation destination
        if ($xdr->readBoolean()) {
            $model->inflationDestinationAccount = AccountId::fromXdr($xdr);
        }
        // clear flags
        if ($xdr->readBoolean()) {
            $model->applyClearFlags($xdr->readUnsignedInteger());
        }
        // set flags
        if ($xdr->readBoolean()) {
            $model->applySetFlags($xdr->readUnsignedInteger());
        }
        // master weight
        if ($xdr->readBoolean()) {
            $model->masterWeight = $xdr->readUnsignedInteger();
        }
        // low threshold
        if ($xdr->readBoolean()) {
            $model->lowThreshold = $xdr->readUnsignedInteger();
        }
        // medium threshold
        if ($xdr->readBoolean()) {
            $model->mediumThreshold = $xdr->readUnsignedInteger();
        }
        // high threshold
        if ($xdr->readBoolean()) {
            $model->highThreshold = $xdr->readUnsignedInteger();
        }
        // home domain
        if ($xdr->readBoolean()) {
            $model->homeDomain = $xdr->readString(32);
        }
        // signer
        if ($xdr->readBoolean()) {
            $model->signer = Signer::fromXdr($xdr);
        }

        return $model;
    }

    /**
     * @param $accountId
     * @return SetOptionsOp
     */
    public function setInflationDestination($accountId)
    {
        $this->inflationDestinationAccount = new AccountId($accountId);

        return $this;
    }

    /**
     * If set, TrustLines are created with authorized set to false requiring the issuer to set it for each TrustLine
     *
     * @param $isRequired
     * @return SetOptionsOp
     */
    public function setAuthRequired($isRequired)
    {
        if ($isRequired) {
            $this->setFlags   = $this->setFlags | static::FLAG_AUTH_REQUIRED;
            $this->clearFlags = $this->clearFlags & ~(static::FLAG_AUTH_REQUIRED);
        }
        else {
            $this->setFlags   = $this->setFlags & ~(static::FLAG_AUTH_REQUIRED);
            $this->clearFlags = $this->clearFlags | static::FLAG_AUTH_REQUIRED;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthRequired()
    {
        return boolval($this->setFlags & static::FLAG_AUTH_REQUIRED);
    }

    /**
     * If set, the authorized flag in TrustLines can be cleared. Otherwise, authorization cannot be revoked
     *
     * @param $isRevocable
     * @return SetOptionsOp
     */
    public function setAuthRevocable($isRevocable)
    {
        if ($isRevocable) {
            $this->setFlags   = $this->setFlags | static::FLAG_AUTH_REVOCABLE;
            $this->clearFlags = $this->clearFlags & ~(static::FLAG_AUTH_REVOCABLE);
        }
        else {
            $this->setFlags   = $this->setFlags & ~(static::FLAG_AUTH_REVOCABLE);
            $this->clearFlags = $this->clearFlags | static::FLAG_AUTH_REVOCABLE;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isAuthRevocable()
    {
        return boolval($this->setFlags & static::FLAG_AUTH_REVOCABLE);
    }

    /**
     * Set the weight of $signer to 0 to remove it
     *
     * @param Signer $signer
     * @return SetOptionsOp
     */
    public function updateSigner(Signer $signer)
    {
        $this->signer = $signer;

        return $this;
    }

    /**
     * @param $weight
     * @return SetOptionsOp
     */
    public function setMasterWeight($weight)
    {
        if ($weight > 255 || $weight < 0) throw new \InvalidArgumentException('$weight must be between 0 and 255');

        $this->masterWeight = $weight;

        return $this;
    }

    /**
     * @param $threshold
     * @return $this
     */
    public function setLowThreshold($threshold)
    {
        if ($threshold < 0 || $threshold > 255) throw new \InvalidArgumentException('$threshold must be between 0 and ' . (2^32-1));

        $this->lowThreshold = $threshold;

        return $this;
    }

    /**
     * @param $threshold
     * @return $this
     */
    public function setMediumThreshold($threshold)
    {
        if ($threshold < 0 || $threshold > 255) throw new \InvalidArgumentException('$threshold must be between 0 and ' . (2^32-1));

        $this->mediumThreshold = $threshold;

        return $this;
    }

    /**
     * @param $threshold
     * @return $this
     */
    public function setHighThreshold($threshold)
    {
        if ($threshold < 0 || $threshold > 255) throw new \InvalidArgumentException('$threshold must be between 0 and ' . (2^32-1));

        $this->highThreshold = $threshold;

        return $this;
    }

    /**
     * @param $domain string maximum length 32 characters
     * @return $this
     */
    public function setHomeDomain($domain)
    {
        if (strlen($domain) > 32) throw new \InvalidArgumentException('$domain can not be longer than 32 characters');

        $this->homeDomain = $domain;

        return $this;
    }

    /**
     * @return AccountId
     */
    public function getInflationDestinationAccount()
    {
        return $this->inflationDestinationAccount;
    }

    /**
     * @return int
     */
    public function getClearFlags()
    {
        return $this->clearFlags;
    }

    /**
     * @return int
     */
    public function getSetFlags()
    {
        return $this->setFlags;
    }

    /**
     * @return int
     */
    public function getMasterWeight()
    {
        return $this->masterWeight;
    }

    /**
     * @return int
     */
    public function getLowThreshold()
    {
        return $this->lowThreshold;
    }

    /**
     * @return int
     */
    public function getMediumThreshold()
    {
        return $this->mediumThreshold;
    }

    /**
     * @return int
     */
    public function getHighThreshold()
    {
        return $this->highThreshold;
    }

    /**
     * @return string
     */
    public function getHomeDomain()
    {
        return $this->homeDomain;
    }

    /**
     * @return Signer
     */
    public function getSigner()
    {
        return $this->signer;
    }

    /**
     * @param $clearFlags
     */
    protected function applyClearFlags($clearFlags)
    {
        if ($clearFlags & static::FLAG_AUTH_REQUIRED) {
            $this->setAuthRequired(false);
        }
        if ($clearFlags & static::FLAG_AUTH_REVOCABLE) {
            $this->setAuthRevocable(false);
        }
    }

    /**
     * @param $setFlags
     */
    protected function applySetFlags($setFlags)
    {
        if ($setFlags & static::FLAG_AUTH_REQUIRED) {
            $this->setAuthRequired(true);
        }
        if ($setFlags & static::FLAG_AUTH_REVOCABLE) {
            $this->setAuthRevocable(true);
        }
    }
}