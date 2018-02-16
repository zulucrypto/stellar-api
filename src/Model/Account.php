<?php


namespace ZuluCrypto\StellarSdk\Model;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\MathSafety;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;

/**
 * See: https://www.stellar.org/developers/horizon/reference/resources/account.html
 *
 * Account viewer:
 *  https://www.stellar.org/laboratory/#explorer
 */
class Account extends RestApiModel
{
    protected $id;

    private $accountId;

    private $sequence;

    private $subentryCount;

    /**
     * @var array|AssetAmount[]
     */
    private $balances;

    private $thresholds;

    private $signers;

    private $data;

    /**
     * @param HorizonResponse $response
     * @return Account
     */
    public static function fromHorizonResponse(HorizonResponse $response)
    {
        $rawData = $response->getRawData();

        $object = new Account($rawData['id']);

        $object->accountId = $rawData['account_id'];
        $object->sequence = $rawData['sequence'];
        $object->subentryCount = $rawData['subentry_count'];
        $object->thresholds = $rawData['thresholds'];
        $object->data = [];
        if (isset($rawData['data'])) {
            foreach ($rawData['data'] as $key => $value) {
                $object->data[$key] = base64_decode($value);
            }
        }

        if (isset($rawData['balances'])) {
            foreach ($rawData['balances'] as $rawBalance) {
                $balance = new AssetAmount($rawBalance['balance'], $rawBalance['asset_type']);

                if (!$balance->isNativeAsset()) {
                    $balance->setAssetCode($rawBalance['asset_code']);
                    $balance->setAssetIssuerAccountId($rawBalance['asset_issuer']);
                    $balance->setLimit($rawBalance['limit']);
                }

                $object->balances[] = $balance;
            }
        }

        return $object;
    }

    public function __construct($id)
    {
        $this->id = $id;

        $this->balances = [];
    }

    /**
     * @param                 $toAccountId
     * @param                 $amount
     * @param string|string[] $signingKeys
     * @return HorizonResponse
     * @throws \ErrorException
     */
    public function sendNativeAsset($toAccountId, $amount, $signingKeys)
    {
        $payment = Payment::newNativeAssetPayment($toAccountId, $amount, $this->accountId);

        return $this->sendPayment($payment, $signingKeys);
    }

    /**
     * @param Payment $payment
     * @param         $signingKeys
     * @return HorizonResponse
     * @throws \ErrorException
     */
    public function sendPayment(Payment $payment, $signingKeys)
    {
        if ($payment->isNativeAsset()) {
            $paymentOp = PaymentOp::newNativePayment($payment->getDestinationAccountId(), $payment->getAmount()->getBalanceAsStroops());
        }
        else {
            throw new \ErrorException('Not implemented');
        }

        $transaction = (new TransactionBuilder($this->accountId))
            ->setApiClient($this->apiClient)
            ->addOperation(
                $paymentOp
            )
        ;

        return $transaction->submit($signingKeys);
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return Transaction[]
     */
    public function getTransactions($sinceCursor = null, $limit = 50)
    {
        $transactions = [];

        $url = sprintf('/accounts/%s/transactions', $this->accountId);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;
        if ($limit) $params['limit'] = $limit;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->apiClient->get($url);
        $rawTransactions = $response->getRecords();

        foreach ($rawTransactions as $rawTransaction) {
            $transaction = Transaction::fromRawResponseData($rawTransaction);
            $transaction->setApiClient($this->getApiClient());

            $transactions[] = $transaction;
        }

        return $transactions;
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return array
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     */
    public function getEffects($sinceCursor = null, $limit = 50)
    {
        $effects = [];
        $url = sprintf('/accounts/%s/effects', $this->accountId);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;
        if ($limit) $params['limit'] = $limit;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->apiClient->get($url);
        $raw = $response->getRecords();

        foreach ($raw as $rawEffect) {
            $effect = Effect::fromRawResponseData($rawEffect);
            $effect->setApiClient($this->getApiClient());

            $effects[] = $effect;
        }

        return $effects;
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return array|AssetTransferInterface[]|RestApiModel[]
     */
    public function getPayments($sinceCursor = null, $limit = 50)
    {
        $results = [];

        $url = sprintf('/accounts/%s/payments', $this->accountId);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;
        if ($limit) $params['limit'] = $limit;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->apiClient->get($url);
        $rawRecords = $response->getRecords($limit);

        foreach ($rawRecords as $rawRecord) {
            switch ($rawRecord['type']) {
                case 'create_account':
                    $result = CreateAccountOperation::fromRawResponseData($rawRecord);
                    break;
                case 'payment':
                    $result = Payment::fromRawResponseData($rawRecord);
                    break;
                case 'account_merge':
                    $result = AccountMergeOperation::fromRawResponseData($rawRecord);
                    break;
                case 'path_payment':
                    $result = PathPayment::fromRawResponseData($rawRecord);
                    break;
            }

            $result->setApiClient($this->getApiClient());

            $results[] = $result;
        }

        return $results;
    }

    /**
     * See ApiClient::streamPayments
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamPayments($sinceCursor = 'now', callable $callback = null)
    {
        $this->apiClient->streamPayments($sinceCursor, $callback);
    }

    /**
     * Returns a string representing the native balance
     *
     * @return string
     * @throws \ErrorException
     */
    public function getNativeBalance()
    {
        MathSafety::require64Bit();

        foreach ($this->getBalances() as $balance) {
            if ($balance->isNativeAsset()) return $balance->getBalance();
        }

        return 0;
    }

    /**
     * Returns the balance in stroops
     *
     * @return string
     * @throws \ErrorException
     */
    public function getNativeBalanceStroops()
    {
        MathSafety::require64Bit();

        foreach ($this->getBalances() as $balance) {
            if ($balance->isNativeAsset()) return $balance->getUnscaledBalance();
        }

        return "0";
    }

    /**
     * Returns the numeric balance of the given asset
     *
     * @param Asset $asset
     * @return null|string
     */
    public function getCustomAssetBalanceValue(Asset $asset)
    {
        foreach ($this->getBalances() as $balance) {
            if ($balance->getAssetCode() !== $asset->getAssetCode()) continue;
            if ($balance->getAssetIssuerAccountId() != $asset->getIssuer()->getAccountIdString()) continue;

            return $balance->getBalance();
        }

        return null;
    }

    /**
     * Returns an AssetAmount representing the balance of this asset
     *
     * @param Asset $asset
     * @return null|AssetAmount
     */
    public function getCustomAssetBalance(Asset $asset)
    {
        foreach ($this->getBalances() as $balance) {
            if ($balance->getAssetCode() !== $asset->getAssetCode()) continue;
            if ($balance->getAssetIssuerAccountId() != $asset->getIssuer()->getAccountIdString()) continue;

            return $balance;
        }

        return null;
    }

    /**
     * Returns the balance of a custom asset in stroops
     *
     * @param Asset $asset
     * @return null|string
     * @throws \ErrorException
     */
    public function getCustomAssetBalanceStroops(Asset $asset)
    {
        MathSafety::require64Bit();

        foreach ($this->getBalances() as $balance) {
            if ($balance->getAssetCode() !== $asset->getAssetCode()) continue;
            if ($balance->getAssetIssuerAccountId() != $asset->getIssuer()->getAccountIdString()) continue;

            return $balance->getUnscaledBalance();
        }

        return null;
    }

    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @return array|AssetAmount[]
     */
    public function getBalances()
    {
        return $this->balances;
    }

    /**
     * Returns an array of key => value pairs
     *
     * Note that the values have been base64-decoded and may be binary data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}