<?php


namespace ZuluCrypto\StellarSdk\Horizon;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse;
use ZuluCrypto\StellarSdk\Model\Account;
use ZuluCrypto\StellarSdk\Model\Effect;
use ZuluCrypto\StellarSdk\Model\Ledger;
use ZuluCrypto\StellarSdk\Model\Operation;
use ZuluCrypto\StellarSdk\Model\Payment;
use ZuluCrypto\StellarSdk\Model\Transaction;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\Hash;
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
     * @param TransactionBuilder $transactionBuilder
     * @return string
     */
    public function hash(TransactionBuilder $transactionBuilder)
    {
        $hashedValue = '';

        $hashedValue .= Hash::generate($this->networkPassphrase);
        $hashedValue .= XdrEncoder::unsignedInteger(TransactionEnvelope::TYPE_TX);
        $hashedValue .= $transactionBuilder->toXdr();

        return Hash::generate($hashedValue);
    }

    /**
     * Submits the transaction contained in the TransactionBuilder to the network
     *
     * @param TransactionBuilder $transactionBuilder
     * @param                    $signingAccountSeedString
     * @return HorizonResponse
     */
    public function submitTransaction(TransactionBuilder $transactionBuilder, $signingAccountSeedString)
    {
        $transactionEnvelope = $transactionBuilder->sign($signingAccountSeedString);

        return $this->post(
            sprintf('/transactions'),
            [
                'tx' => base64_encode($transactionEnvelope->toXdr()),
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
     * todo: better error handling
     * @param $relativeUrl
     * @return HorizonResponse
     */
    public function get($relativeUrl)
    {
        try {
            $res = $this->httpClient->get($relativeUrl);
        }
        catch (ClientException $e) {
            // todo: Make HorizonException class and use that instead
            print "Client error:\n";
            print $e->getResponse()->getBody() . "\n";
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
            print "Client error:\n";
            print $e->getResponse()->getBody() . "\n";
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
        $client->streamEffects(null, function(Effect $effect) {
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
        $client->streamLedgers(null, function(Ledger $ledger) {
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
        $client->streamOperations(null, function(Operation $operation) {
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
     * Streams Payment objects to $callback
     *
     * $callback should have arguments:
     *  Payment
     *
     * For example:

        $client = ApiClient::newPublicClient();
        $client->streamPayments(null, function(Payment $payment) {
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
            $parsedObject = Payment::fromRawResponseData($rawData);
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
        $client->streamTransactions(null, function(Transaction $transaction) {
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
}