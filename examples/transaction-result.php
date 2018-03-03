<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\Horizon\Exception\PostTransactionException;

$server = Server::testNet();

// GD4JRFLPF4AGYQTLCMZ7Q7DRLGQZQTGWOOKDUCNRVKG66G5ZVYYFT76M
$sourceKeypair = Keypair::newFromSeed('SAA2U5UFW65DW3MLVX734BUQIHAWANQNBLTFT47X2NVVBCN7X6QC5AOG');

$newAccount1 = Keypair::newFromRandom();
$newAccount2 = Keypair::newFromRandom();

// ---------------------------------------------------------
// create two new accounts
$response = $server->buildTransaction($sourceKeypair)
    ->addCreateAccountOp($newAccount1, 5)
    ->addCreateAccountOp($newAccount2, 5)
    ->submit($sourceKeypair);

/*
 * Get information on the overall result of the transaction
 */
/** @var \ZuluCrypto\StellarSdk\XdrModel\TransactionResult $result */
$result = $response->getResult();

print "Fee charged: " . $result->getFeeCharged()->getScaledValue() . " XLM" . PHP_EOL;


/*
 * Each operation within the transaction has its own result
 */
$operationResults = $result->getOperationResults();

/*
 * Each result will be a child class of OperationResult depending on what the
 * original operation was.
 *
 * See these classes for additional details that can be retrieved for each type
 * of result
 */
foreach ($operationResults as $operationResult) {
    print "Operation result is a: " . get_class($operationResult) . PHP_EOL;
}

/*
 * Exception handling
 */

// This transaction will fail because there aren't enough lumens in the source
// account
try {
    $response = $server->buildTransaction($sourceKeypair)
        ->addLumenPayment($newAccount1, 9999999)
        ->submit($sourceKeypair);
} catch (PostTransactionException $e) {
    // Details operation information can be retrieved from this exception
    $operationResults = $e->getResult()->getOperationResults();

    foreach ($operationResults as $result) {
        // Skip through the ones that worked
        if ($result->succeeded()) continue;

        // Print out the first failed one
        print "Operation failed with code: " . $result->getErrorCode() . PHP_EOL;
    }
}
