<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Horizon\ApiClient;
use \ZuluCrypto\StellarSdk\Model\Payment;

$client = ApiClient::newPublicClient();

$client->streamPayments('now', function(Payment $payment) {
    printf('[%s] %s from %s -> %s' . PHP_EOL,
        (new \DateTime())->format('Y-m-d h:i:sa'),
        $payment->getAmount(),
        $payment->getFromAccountId(),
        $payment->getToAccountId()
    );
});

