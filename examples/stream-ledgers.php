<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Horizon\ApiClient;
use \ZuluCrypto\StellarSdk\Model\Ledger;

$client = ApiClient::newPublicClient();

$client->streamLedgers('now', function(Ledger $ledger) {
    printf('[%s] Closed %s at %s with %s operations' . PHP_EOL,
        (new \DateTime())->format('Y-m-d h:i:sa'),
        $ledger->getId(),
        $ledger->getClosedAt()->format('Y-m-d h:i:sa'),
        $ledger->getOperationCount()
    );
});
