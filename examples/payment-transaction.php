<?php
/**
 * Builds a payment transaction
 */


require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;

$server = Server::testNet();

// GAJCCCRIRXAYEU2ATNQAFYH4E2HKLN2LCKM2VPXCTJKIBVTRSOLEGCJZ
$sourceKeypair = Keypair::newFromSeed('SDJCZISO5M5XAUV6Y7MZJNN3JZ5BWPXDHV4GXP3MYNACVDNQRQSERXBC');

// GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ
$destinationKeypair = Keypair::newFromSeed('SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E');

$txEnvelope = $server->buildTransaction($sourceKeypair)
    ->addLumenPayment($destinationKeypair, 10)
    ->getTransactionEnvelope();

$txEnvelope->sign($sourceKeypair);

$b64Tx = base64_encode($txEnvelope->toXdr());

print "Submitting transaction: " . PHP_EOL;
print $b64Tx;
print PHP_EOL;

$server->submitB64Transaction($b64Tx);

