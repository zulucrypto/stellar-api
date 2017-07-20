<?php

require __DIR__ . '/../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\Model\Payment;

$server = Server::testNet();

$account = $server->getAccount('GA2C5RFPE6GCKMY3US5PAB6UZLKIGSPIUKSLRB6Q723BM2OARMDUYEJ5');

$account->streamPayments(null, function(Payment $payment) {
    printf('[%s] Amount: %s From %s' . PHP_EOL,
        $payment->getType(),
        $payment->getAmount(),
        $payment->getSourceAccountId()
    );
});