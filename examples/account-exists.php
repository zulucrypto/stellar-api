<?php

/**
 * Adds a new signer to an existing account
 */


require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \phpseclib\Math\BigInteger;


$server = Server::testNet();

// Check if an account exists
try {
    $exists = $server->accountExists('GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ');

    if ($exists) {
        print "Account exists!" . PHP_EOL;
    }
    else {
        print "Account does not exist." . PHP_EOL;
    }
}
// If there's an exception it could be a temporary error, like a connection issue
// to Horizon, so we cannot tell for sure if the account exists or not
catch (\Exception $e) {
    print "Error: " . $e->getMessage() . PHP_EOL;
}
