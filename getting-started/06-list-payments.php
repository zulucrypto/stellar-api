<?php

require __DIR__ . '/../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Server;

$server = Server::testNet();

$account = $server->getAccount('GA2C5RFPE6GCKMY3US5PAB6UZLKIGSPIUKSLRB6Q723BM2OARMDUYEJ5');

$currentCursor = null;
while (true) {
    $resultsPerPage = 10;
    $payments = $account->getPayments(null, $resultsPerPage);

    $seenResults = 0;
    foreach ($payments as $payment) {
        // If the same cursor shows up twice, we're repeating results and should exit
        if ($payment->getPagingToken() == $currentCursor) break 2;

        printf('[%s] Amount: %s From %s' . PHP_EOL,
            $payment->getAssetTransferType(),
            $payment->getAssetAmount(),
            $payment->getFromAccountId()
        );

        $currentCursor = $payment->getPagingToken();
    }

    // Immediate exit if there aren't enough results to fill the page
    if ($seenResults < $resultsPerPage) break;
}