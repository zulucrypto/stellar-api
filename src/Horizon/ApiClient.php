<?php


namespace ZuluCrypto\StellarSdk\Horizon;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use ZuluCrypto\StellarSdk\Horizon\Api\HorizonResponse;
use ZuluCrypto\StellarSdk\Model\Account;
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
     * @return ApiClient
     */
    public static function newTestnetClient()
    {
        $apiClient = new ApiClient('https://horizon-testnet.stellar.org/');
        $apiClient->isTestnet = true;

        return $apiClient;
    }

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = new Client([
            'base_uri' => $baseUrl,
        ]);
    }

    public function hash(TransactionBuilder $transactionBuilder)
    {
        $passphrase = ($this->isTestnet) ? self::NETWORK_PASSPHRASE_TEST : self::NETWORK_PASSPHRASE_PUBLIC;
        $hashedValue = '';

        $hashedValue .= Hash::generate($passphrase);
        $hashedValue .= XdrEncoder::unsignedInteger(TransactionEnvelope::TYPE_TX);
        $hashedValue .= $transactionBuilder->toXdr();

        return Hash::generate($hashedValue);
    }

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

    public function getAndStream($relativeUrl, $callback)
    {
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
        catch (ClientException $e) {
            print "Client error:\n";
            print $e->getResponse()->getBody() . "\n";
        }
    }
}