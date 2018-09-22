<?php

/**
 * Adds a new signer to an existing account
 */


require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \phpseclib\Math\BigInteger;


$server = Server::testNet();

// GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ
$currentAccount = Keypair::newFromSeed('SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E');


// Submit to the network
$server->buildTransaction($currentAccount->getPublicKey())
    ->bumpSequenceTo(new BigInteger('47061756253569030'))
    ->submit($currentAccount->getSecret());