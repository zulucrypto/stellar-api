<?php

namespace ZuluCrypto\StellarSdk;


use Prophecy\Exception\InvalidArgumentException;
use ZuluCrypto\StellarSdk\Horizon\ApiClient;
use ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException;
use ZuluCrypto\StellarSdk\Model\Account;
use ZuluCrypto\StellarSdk\Model\Payment;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;

class Server
{
    /**
     * @var ApiClient
     */
    private $apiClient;

    /**
     * @var
     */
    private $isTestnet;

    /**
     * @return Server
     */
    public static function testNet()
    {
        $server = new Server(ApiClient::newTestnetClient());
        $server->isTestnet = true;

        return $server;
    }

    /**
     * @return Server
     */
    public static function publicNet()
    {
        $server = new Server(ApiClient::newPublicClient());

        return $server;
    }

    /**
     * Connects to a custom network
     *
     * @param $horizonBaseUrl
     * @param $networkPassphrase
     * @return Server
     */
    public static function customNet($horizonBaseUrl, $networkPassphrase)
    {
        return new Server(ApiClient::newCustomClient($horizonBaseUrl, $networkPassphrase));
    }

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        $this->isTestnet = false;
    }

    /**
     * Returns the Account that matches $accountId or null if the account does
     * not exist
     *
     * @param $accountId Keypair|string the public account ID
     * @return Account|null
     * @throws Horizon\Exception\HorizonException
     */
    public function getAccount($accountId)
    {
        // Cannot be empty
        if (!$accountId) throw new InvalidArgumentException('Empty accountId');

        if ($accountId instanceof Keypair) {
            $accountId = $accountId->getPublicKey();
        }

        try {
            $response = $this->apiClient->get(sprintf('/accounts/%s', $accountId));
        }
        catch (HorizonException $e) {
            // Account not found, return null
            if ($e->getHttpStatusCode() === 404) {
                return null;
            }

            // A problem we can't handle, rethrow
            throw $e;
        }

        $account = Account::fromHorizonResponse($response);
        $account->setApiClient($this->apiClient);

        return $account;
    }

    /**
     * @param $accountId string|Keypair
     * @return TransactionBuilder
     */
    public function buildTransaction($accountId)
    {
        if ($accountId instanceof Keypair) {
            $accountId = $accountId->getPublicKey();
        }

        return (new TransactionBuilder($accountId))
            ->setApiClient($this->apiClient)
        ;
    }

    /**
     * @param $transactionHash
     * @return array|Payment[]
     */
    public function getPaymentsByTransactionHash($transactionHash)
    {
        $url = sprintf('/transactions/%s/payments', $transactionHash);

        $response = $this->apiClient->get($url);

        $payments = [];
        foreach ($response->getRecords() as $rawRecord) {
            $payments[] = Payment::fromRawResponseData($rawRecord);
        }

        return $payments;
    }

    /**
     * @param $accountId
     * @throws Horizon\Exception\HorizonException
     */
    public function fundAccount($accountId)
    {
        $this->apiClient->get(sprintf('/friendbot?addr=%s', $accountId));
    }
}