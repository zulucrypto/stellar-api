<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Horizon\ApiClient;
use \ZuluCrypto\StellarSdk\Model\Transaction;

$client = ApiClient::newPublicClient();

$client->streamTransactions('now', function(Transaction $transaction) {
    printf('[%s] Transaction #%s with memo %s' . PHP_EOL,
        (new \DateTime())->format('Y-m-d h:i:sa'),
        $transaction->getId(),
        $transaction->getMemo()
    );
});

