<?php

require __DIR__ . '/../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;

$server = Server::testNet();

$sourceKeypair = Keypair::newFromSeed('SCZANGBA5YHTNYVVV4C3U252E2B6P6F5T3U6MM63WBSBZATAQI3EBTQ4');
$destinationAccountId = 'GA2C5RFPE6GCKMY3US5PAB6UZLKIGSPIUKSLRB6Q723BM2OARMDUYEJ5';

// Verify that the destination account exists. This will throw an exception
// if it does not
$destinationAccount = $server->getAccount($destinationAccountId);

// Build the payment transaction
$transaction = \ZuluCrypto\StellarSdk\Server::testNet()
    ->buildTransaction($sourceKeypair->getPublicKey())
    ->addOperation(
        PaymentOp::newNativePayment($sourceKeypair->getPublicKey(), $destinationAccountId, "10")
    )
;

// Sign and submit the transaction
$response = $transaction->submit($sourceKeypair->getSecret());

print "Response:" . PHP_EOL;
print_r($response->getRawData());