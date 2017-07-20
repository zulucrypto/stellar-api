<?php

namespace ZuluCrypto\StellarSdk;


use ZuluCrypto\StellarSdk\Horizon\ApiClient;
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

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param $accountId string the public account ID
     * @return Account
     */
    public function getAccount($accountId)
    {
        $response = $this->apiClient->get(sprintf('/accounts/%s', $accountId));

        $account = Account::fromHorizonResponse($response);
        $account->setApiClient($this->apiClient);

        return $account;
    }

    /**
     * @param $accountId
     * @return TransactionBuilder
     */
    public function buildTransaction($accountId)
    {
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
}