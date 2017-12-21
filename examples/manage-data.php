<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;

$server = Server::testNet();

// GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ
$keypair = Keypair::newFromSeed('SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E');

// Build transaction to update account data
$server->buildTransaction($keypair)
    ->setAccountData('color', 'blue')
    ->submit($keypair);


printf("Data updated." . PHP_EOL);
printf("View account at: %saccounts/%s" . PHP_EOL, $server->getHorizonBaseUrl(), $keypair->getPublicKey());