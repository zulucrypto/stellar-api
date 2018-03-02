<?php


namespace ZuluCrypto\StellarSdk\Horizon;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse;
use ZuluCrypto\StellarSdk\Horizon\Api\PostTransactionResponse;
use ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException;
use ZuluCrypto\StellarSdk\Horizon\Exception\PostTransactionException;
use ZuluCrypto\StellarSdk\Model\Account;
use ZuluCrypto\StellarSdk\Model\AccountMergeOperation;
use ZuluCrypto\StellarSdk\Model\CreateAccountOperation;
use ZuluCrypto\StellarSdk\Model\Effect;
use ZuluCrypto\StellarSdk\Model\Ledger;
use ZuluCrypto\StellarSdk\Model\Operation;
use ZuluCrypto\StellarSdk\Model\PathPayment;
use ZuluCrypto\StellarSdk\Model\Payment;
use ZuluCrypto\StellarSdk\Model\Transaction;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\Hash;
use ZuluCrypto\StellarSdk\Util\Json;
use ZuluCrypto\StellarSdk\Xdr\XdrEncoder;
use ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;

class ApiClient
{
    // Passphrases used when calculating hashes (see hash())
    const NETWORK_PASSPHRASE_PUBLIC = 'Public Global Stellar Network ; September 2015';
    const NETWORK_PASSPHRASE_TEST = 'Test SDF Network ; September 2015';

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var boolean
     */
    private $isTestnet;

    /**
     * @var string
     */
    private $networkPassphrase;

    /**
     * @return ApiClient
     */
    public static function newTestnetClient()
    {
        $apiClient = new ApiClient('https://horizon-testnet.stellar.org/', self::NETWORK_PASSPHRASE_TEST);
        $apiClient->isTestnet = true;

        return $apiClient;
    }

    /**
     * @return ApiClient
     */
    public static function newPublicClient()
    {
        return new ApiClient('https://horizon.stellar.org/', self::NETWORK_PASSPHRASE_PUBLIC);
    }

    /**
     * @param $horizonBaseUrl
     * @param $networkPassphrase
     * @return ApiClient
     */
    public static function newCustomClient($horizonBaseUrl, $networkPassphrase)
    {
        $apiClient = new ApiClient($horizonBaseUrl, $networkPassphrase);
        $apiClient->isTestnet = true;

        return $apiClient;
    }

    /**
     * ApiClient constructor.
     *
     * @param $baseUrl string root URL of the horizon server, such as https://horizon-testnet.stellar.org/
     * @param $networkPassphrase string Passphrase used when signing transactions on the network
     */
    public function __construct($baseUrl, $networkPassphrase)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = new Client([
            'base_uri' => $baseUrl,
        ]);
        $this->networkPassphrase = $networkPassphrase;
    }

    /**
     * todo: rename to getHashAsBytes
     *
     * @param TransactionBuilder $transactionBuilder
     * @return string
     */
    public function hash(TransactionBuilder $transactionBuilder)
    {
        return Hash::generate($this->getTransactionEnvelope($transactionBuilder));
    }

    public function getTransactionEnvelope(TransactionBuilder $transactionBuilder)
    {
        $transactionBytes = '';

        $transactionBytes .= Hash::generate($this->networkPassphrase);
        $transactionBytes .= XdrEncoder::unsignedInteger(TransactionEnvelope::TYPE_TX);
        $transactionBytes .= $transactionBuilder->toXdr();

        return $transactionBytes;
    }

    /**
     * @param TransactionBuilder $transactionBuilder
     * @return string
     */
    public function getHashAsString(TransactionBuilder $transactionBuilder)
    {
        return Hash::asString($this->getTransactionEnvelope($transactionBuilder));
    }

    /**
     * Submits the transaction contained in the TransactionBuilder to the network
     *
     * @param TransactionBuilder $transactionBuilder
     * @param                    $signingAccountSeedString
     * @return PostTransactionResponse
     */
    public function submitTransaction(TransactionBuilder $transactionBuilder, $signingAccountSeedString)
    {
        $transactionEnvelope = $transactionBuilder->sign($signingAccountSeedString);

        return $this->submitB64Transaction(base64_encode($transactionEnvelope->toXdr()));
    }

    /**
     * @param $base64TransactionEnvelope
     * @return PostTransactionResponse
     */
    public function submitB64Transaction($base64TransactionEnvelope)
    {
        return $this->postTransaction(
            sprintf('/transactions'),
            [
                'tx' => $base64TransactionEnvelope,
            ]
        );
    }

    /**
     * @param $accountId
     * @return Account
     */
    public function getAccount($accountId)
    {
        $account = Account::fromHorizonResponse($this->get(sprintf("/accounts/%s", $accountId)));
        $account->setApiClient($this);

        return $account;
    }

    /**
     * @param $relativeUrl
     * @return HorizonResponse
     * @throws HorizonException
     */
    public function get($relativeUrl)
    {
        try {
            $res = $this->httpClient->get($relativeUrl);
        }
        catch (ClientException $e) {
            // If the response can be json-decoded then it can be converted to a HorizonException
            $decoded = null;
            if ($e->getResponse()) {
                $decoded = Json::mustDecode($e->getResponse()->getBody());
                throw HorizonException::fromRawResponse($relativeUrl, 'GET', $decoded);
            }
            // No response, something else went wrong
            else {
                throw $e;
            }
        }

        return new HorizonResponse($res->getBody());
    }

    /**
     * @param       $relativeUrl
     * @param array $parameters
     * @return HorizonResponse
     */
    public function post($relativeUrl, $parameters = array())
    {
        $apiResponse = null;

        try {
            $apiResponse = $this->httpClient->post($relativeUrl, [ 'form_params' => $parameters ]);
        }
        catch (ClientException $e) {
              // If the response can be json-decoded then it can be converted to a HorizonException
            $decoded = null;
            if ($e->getResponse()) {
                $decoded = Json::mustDecode($e->getResponse()->getBody());
                throw HorizonException::fromRawResponse($relativeUrl, 'POST', $decoded);
            }
            // No response, something else went wrong
            else {
                throw $e;
            }
        }

        return new HorizonResponse($apiResponse->getBody());
    }

    /**
     * Streams Effect objects to $callback
     *
     * $callback should have arguments:
     *  Effect
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamEffects('now', function(Effect $effect) {
            printf('Effect type: %s' . PHP_EOL, $effect->getType());
        });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamEffects($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/effects');
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->getAndStream($url, function($rawData) use ($callback) {
            $parsedObject = Effect::fromRawResponseData($rawData);
            $parsedObject->setApiClient($this);

            $callback($parsedObject);
        });
    }

    /**
     * Streams Ledger objects to $callback
     *
     * $callback should have arguments:
     *  Ledger
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamLedgers('now', function(Ledger $ledger) {
            printf('[%s] Closed %s at %s with %s operations' . PHP_EOL,
                (new \DateTime())->format('Y-m-d h:i:sa'),
                $ledger->getId(),
                $ledger->getClosedAt()->format('Y-m-d h:i:sa'),
                $ledger->getOperationCount()
            );
        });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamLedgers($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/ledgers');
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->getAndStream($url, function($rawData) use ($callback) {
            $parsedObject = Ledger::fromRawResponseData($rawData);
            $parsedObject->setApiClient($this);

            $callback($parsedObject);
        });
    }

    /**
     * Streams Operation objects to $callback
     *
     * $callback should have arguments:
     *  Operation
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamOperations('now', function(Operation $operation) {
            printf('Effect type: %s' . PHP_EOL, $effect->getType());
        });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamOperations($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/operations');
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->getAndStream($url, function($rawData) use ($callback) {
            $parsedObject = Operation::fromRawResponseData($rawData);
            $parsedObject->setApiClient($this);

            $callback($parsedObject);
        });
    }

    /**
     * Streams Payment or CreateAccount objects to $callback
     *
     * $callback should have arguments:
     *  AssetTransferInterface
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamPayments('now', function(AssetTransferInterface $payment) {
            printf('Payment: from %s to %s' . PHP_EOL, $payment->getFromAccountId(), $payment->getToAccountId());
        });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamPayments($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/payments');
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->getAndStream($url, function($rawData) use ($callback) {
            switch ($rawData['type']) {
                case 'create_account':
                    $parsedObject = CreateAccountOperation::fromRawResponseData($rawData);
                    break;
                case 'payment':
                    $parsedObject = Payment::fromRawResponseData($rawData);
                    break;
                case 'account_merge':
                    $parsedObject = AccountMergeOperation::fromRawResponseData($rawData);
                    break;
                case 'path_payment':
                    $parsedObject = PathPayment::fromRawResponseData($rawData);
                    break;
            }

            $parsedObject->setApiClient($this);

            $callback($parsedObject);
        });
    }

    /**
     * Streams Transaction objects to $callback
     *
     * $callback should have arguments:
     *  Transaction
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamTransactions('now', function(Transaction $transaction) {
            printf('Transaction id %s' . PHP_EOL, $transaction->getId());
        });
     *
     * @param null $sinceCursor
     * @param callable $callback
     */
    public function streamTransactions($sinceCursor = 'now', callable $callback = null)
    {
        $url = sprintf('/transactions');
        $params = [];

        if ($sinceCursor) $params['cursor'] = $sinceCursor;

        if ($params) {
            $url .= '?' . http_build_query($params);
        }

        $this->getAndStream($url, function($rawData) use ($callback) {
            $parsedObject = Transaction::fromRawResponseData($rawData);
            $parsedObject->setApiClient($this);

            $callback($parsedObject);
        });
    }

    /**
     * @param $relativeUrl
     * @param $callback
     * @param $retryOnServerException bool If true, ignore ServerException errors and retry
     */
    public function getAndStream($relativeUrl, $callback, $retryOnServerException = true)
    {
        while (true) {
            try {
                $response = $this->httpClient->get($relativeUrl, [
                    'stream' => true,
                    'read_timeout' => null,
                    'headers' => [
                        'Accept' => 'text/event-stream',
                    ]
                ]);

                $body = $response->getBody();

                while (!$body->eof()) {
                    $line = '';

                    $char = null;
                    while ($char != "\n") {
                        $line .= $char;
                        $char = $body->read(1);
                    }

                    // Ignore empty lines
                    if (!$line) continue;

                    // Ignore "data: hello" handshake
                    if (strpos($line, 'data: "hello"') === 0) continue;

                    // Ignore lines that don't start with "data: "
                    $sentinel = 'data: ';
                    if (strpos($line, $sentinel) !== 0) continue;

                    // Remove sentinel prefix
                    $json = substr($line, strlen($sentinel));

                    $decoded = json_decode($json, true);
                    if ($decoded) {
                        $callback($decoded);
                    }
                }

            }
            catch (ServerException $e) {
                if (!$retryOnServerException) throw $e;

                // Delay for a bit before trying again
                sleep(10);
            }
        }
    }

    /**
     * Special handling for the /transaction endpoint since we expect additional
     * transaction-related fields to come back
     *
     * @param       $relativeUrl
     * @param array $parameters
     * @return PostTransactionResponse
     * @throws HorizonException
     */
    protected function postTransaction($relativeUrl, $parameters = array())
    {
        $apiResponse = null;

        try {
            $apiResponse = $this->httpClient->post($relativeUrl, [ 'form_params' => $parameters ]);
        }
        catch (ClientException $e) {
            // If the response can be json-decoded then it can be converted to a HorizonException
            $decoded = null;
            if ($e->getResponse()) {
                $decoded = Json::mustDecode($e->getResponse()->getBody());
                throw PostTransactionException::fromRawResponse($relativeUrl, 'POST', $decoded);
            }
            // No response, something else went wrong
            else {
                throw $e;
            }
        }

        return new PostTransactionResponse($apiResponse->getBody());
    }

    /**
     * @return string
     */
    public function getNetworkPassphrase()
    {
        return $this->networkPassphrase;
    }

    /**
     * @param string $networkPassphrase
     */
    public function setNetworkPassphrase($networkPassphrase)
    {
        $this->networkPassphrase = $networkPassphrase;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }
}