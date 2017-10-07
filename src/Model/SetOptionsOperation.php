<?php


namespace ZuluCrypto\StellarSdk\Model;


/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/operation.html#set-options
 */
class SetOptionsOperation extends Operation
{
    /**
     * Public key of the new signer
     *
     * @var string
     */
    protected $signerKey;

    /**
     * @var int (1-255)
     */
    protected $signerWeight;

    /**
     * @var int (1-255)
     */
    protected $masterKeyWeight;

    /**
     * The sum weight for the low threshold.
     *
     * @var int
     */
    protected $lowThreshold;

    /**
     * The sum weight for the medium threshold.
     *
     * @var int
     */
    protected $mediumThreshold;

    /**
     * The sum weight for the high threshold.
     *
     * @var int
     */
    protected $highThreshold;

    /**
     * The home domain used for reverse federation lookup
     *
     * @var string
     */
    protected $homeDomain;

    /**
     * The array of numeric values of flags that has been set in this operation
     *
     * @var array
     */
    protected $setFlagsI;

    /**
     * The array of string values of flags that has been set in this operation
     *
     * @var array
     */
    protected $setFlagsS;

    /**
     * The array of numeric values of flags that has been cleared in this operation
     *
     * @var array
     */
    protected $clearFlagsI;

    /**
     * The array of string values of flags that has been cleared in this operation
     *
     * @var array
     */
    protected $clearFlagsS;

    /**
     * @param array $rawData
     * @return SetOptionsOperation
     */
    public static function fromRawResponseData($rawData)
    {
        $object = new SetOptionsOperation($rawData['id'], $rawData['type']);

        $object->loadFromRawResponseData($rawData);

        return $object;
    }

    /**
     * @param $id
     * @param $type
     */
    public function __construct($id, $type)
    {
        parent::__construct($id, Operation::TYPE_SET_OPTIONS);
    }

    /**
     * @param $rawData
     */
    public function loadFromRawResponseData($rawData)
    {
        parent::loadFromRawResponseData($rawData);

        if (isset($rawData['signer_key'])) $this->signerKey = $rawData['signer_key'];
        if (isset($rawData['signer_weight'])) $this->signerWeight = $rawData['signer_weight'];
        if (isset($rawData['master_key_weight'])) $this->masterKeyWeight = $rawData['master_key_weight'];

        if (isset($rawData['low_threshold'])) $this->lowThreshold = $rawData['low_threshold'];
        if (isset($rawData['med_threshold'])) $this->mediumThreshold = $rawData['med_threshold'];
        if (isset($rawData['high_threshold'])) $this->highThreshold = $rawData['high_threshold'];

        if (isset($rawData['set_flags'])) $this->setFlagsI = $rawData['set_flags'];
        if (isset($rawData['set_flags_s'])) $this->setFlagsS = $rawData['set_flags_s'];
        if (isset($rawData['clear_flags'])) $this->clearFlagsI = $rawData['clear_flags'];
        if (isset($rawData['clear_flags_s'])) $this->clearFlagsS = $rawData['clear_flags_s'];
    }

    /**
     * @return string
     */
    public function getSignerKey()
    {
        return $this->signerKey;
    }

    /**
     * @param string $signerKey
     */
    public function setSignerKey($signerKey)
    {
        $this->signerKey = $signerKey;
    }

    /**
     * @return int
     */
    public function getSignerWeight()
    {
        return $this->signerWeight;
    }

    /**
     * @param int $signerWeight
     */
    public function setSignerWeight($signerWeight)
    {
        $this->signerWeight = $signerWeight;
    }

    /**
     * @return int
     */
    public function getMasterKeyWeight()
    {
        return $this->masterKeyWeight;
    }

    /**
     * @param int $masterKeyWeight
     */
    public function setMasterKeyWeight($masterKeyWeight)
    {
        $this->masterKeyWeight = $masterKeyWeight;
    }

    /**
     * @return int
     */
    public function getLowThreshold()
    {
        return $this->lowThreshold;
    }

    /**
     * @param int $lowThreshold
     */
    public function setLowThreshold($lowThreshold)
    {
        $this->lowThreshold = $lowThreshold;
    }

    /**
     * @return int
     */
    public function getMediumThreshold()
    {
        return $this->mediumThreshold;
    }

    /**
     * @param int $mediumThreshold
     */
    public function setMediumThreshold($mediumThreshold)
    {
        $this->mediumThreshold = $mediumThreshold;
    }

    /**
     * @return int
     */
    public function getHighThreshold()
    {
        return $this->highThreshold;
    }

    /**
     * @param int $highThreshold
     */
    public function setHighThreshold($highThreshold)
    {
        $this->highThreshold = $highThreshold;
    }

    /**
     * @return string
     */
    public function getHomeDomain()
    {
        return $this->homeDomain;
    }

    /**
     * @param string $homeDomain
     */
    public function setHomeDomain($homeDomain)
    {
        $this->homeDomain = $homeDomain;
    }

    /**
     * @return array
     */
    public function getSetFlagsI()
    {
        return $this->setFlagsI;
    }

    /**
     * @param array $setFlagsI
     */
    public function setSetFlagsI($setFlagsI)
    {
        $this->setFlagsI = $setFlagsI;
    }

    /**
     * @return array
     */
    public function getSetFlagsS()
    {
        return $this->setFlagsS;
    }

    /**
     * @param array $setFlagsS
     */
    public function setSetFlagsS($setFlagsS)
    {
        $this->setFlagsS = $setFlagsS;
    }

    /**
     * @return array
     */
    public function getClearFlagsI()
    {
        return $this->clearFlagsI;
    }

    /**
     * @param array $clearFlagsI
     */
    public function setClearFlagsI($clearFlagsI)
    {
        $this->clearFlagsI = $clearFlagsI;
    }

    /**
     * @return array
     */
    public function getClearFlagsS()
    {
        return $this->clearFlagsS;
    }

    /**
     * @param array $clearFlagsS
     */
    public function setClearFlagsS($clearFlagsS)
    {
        $this->clearFlagsS = $clearFlagsS;
    }
}