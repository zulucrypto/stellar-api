<?php


namespace ZuluCrypto\StellarSdk\Model;


use phpseclib\Math\BigInteger;
use ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
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
        $object->data = $rawData['data'];

        if (isset($rawData['balances'])) {
            foreach ($rawData['balances'] as $rawBalance) {
                $balance = new AssetAmount($rawBalance['balance'], $rawBalance['asset_type']);

                if (!$balance->isNativeAsset()) {
                    $balance->setAssetCode($rawBalance['asset_code']);
                    $balance->setAssetIssuerAccountId($rawBalance['asset_issuer']);
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
     * @param $toAccountId
     * @param $amount
     * @param string|string[] $signingKeys
     * @return HorizonResponse
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
            $paymentOp = PaymentOp::newNativePayment($this->accountId, $payment->getDestinationAccountId(), $payment->getAmount()->getUnscaledBalance());
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

    public function getEffects($sinceCursor = null, $limit = 50)
    {
        $url = sprintf('/accounts/%s/effects', $this->accountId);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;
        if ($limit) $params['limit'] = $limit;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->apiClient->get($url);

        print_r($response->getRawData());
    }

    /**
     * @param null $sinceCursor
     * @param int  $limit
     * @return array|Payment[]
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
            $result = Payment::fromRawResponseData($rawRecord);
            $result->setApiClient($this->getApiClient());

            $results[] = $result;
        }

        return $results;
    }

    /**
     * Streams Payment objects to $callback
     *
     * $callback should have arguments:
     *  Payment
     *
     * For example:
     *
            $account->streamPayments(null, function(Payment $payment) {
                printf('[%s] Amount: %s From %s' . PHP_EOL,
                    $payment->getType(),
                    $payment->getAmount(),
                    $payment->getSourceAccountId()
                );
            });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamPayments($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/accounts/%s/payments', $this->accountId);
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->apiClient->getAndStream($url, function($rawData) use ($callback) {
            $payment = Payment::fromRawResponseData($rawData);

            $callback($payment);
        });
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
}