<?php

require __DIR__ . '/../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\PaymentOp;

$server = Server::testNet();

// GAHC2HBHXSRNUT5S3BMKMUMTR3IIHVCARBFAX256NONXYKY65R2C5267
// You may need to fund this account if the testnet has been reset:
// https://www.stellar.org/laboratory/#account-creator?network=test
$sourceKeypair = Keypair::newFromSeed('SDU5L4Q7ZPW7AOKJTHZABI4QVD2YXPR4K2AFO7DD7HBYTO3PQPODGBER');
$destinationAccountId = 'GA2C5RFPE6GCKMY3US5PAB6UZLKIGSPIUKSLRB6Q723BM2OARMDUYEJ5';

// Verify that the destination account exists. This will throw an exception
// if it does not
$destinationAccount = $server->getAccount($destinationAccountId);

// Build the payment transaction
$transaction = \ZuluCrypto\StellarSdk\Server::testNet()
    ->buildTransaction($sourceKeypair->getPublicKey())
    ->addOperation(
        PaymentOp::newNativePayment($destinationAccountId, 1)
    )
;

// Sign and submit the transaction
$response = $transaction->submit($sourceKeypair->getSecret());

print "Response:" . PHP_EOL;
print_r($response->getRawData());

print PHP_EOL;
print 'Payment succeeded!' . PHP_EOL;