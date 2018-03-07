<?php

/**
 * Demonstrates how to load an existing transaction from XDR and add the signature
 * for another account.
 *
 * In this example, we'll use three accounts:
 *
 * Source Account      Only purpose is to pay the fee for the transaction
 * Payment Account     Is paying 100 XLM to a destination account
 * Destination Account Receives the payment
 *
 * Submitting this transaction will require two signatures: Source Account and Payment Account
 *
 * In the XDR below, one signature (the source account) has already been added.
 *
 * This code demonstrates how to add the second required signature and submit
 * the finished transaction.
 */

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use \ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;

// GD4JRFLPF4AGYQTLCMZ7Q7DRLGQZQTGWOOKDUCNRVKG66G5ZVYYFT76M
$sourceKeypair = Keypair::newFromSeed('SAA2U5UFW65DW3MLVX734BUQIHAWANQNBLTFT47X2NVVBCN7X6QC5AOG');

// GALOPAYIVBYBZX3JYUET67WWBDKOOSPYQ2437IKKZCPXXA4HHOKUZ5OA
$payingKeypair = Keypair::newFromSeed('SBFMSGMYTSAJMCNEJQPPO65BNL5HSUXYKY4HPE2RZWXBP7C745YKYIUC');

// GDRXE2BQUC3AZNPVFSCEZ76NJ3WWL25FYFK6RGZGIEKWE4SOOHSUJUJ6
$destinationKeypair = Keypair::newFromSeed('SBGWSG6BTNCKCOB3DIFBGCVMUPQFYPA2G4O34RMTB343OYPXU5DJDVMN');

// This is the XDR we start with. It already includes the signature for $sourceKeypair
$xdr = base64_decode('AAAAAPiYlW8vAGxCaxMz+HxxWaGYTNZzlDoJsaqN7xu5rjBZAAAAZAB2pQsAAAABAAAAAAAAAAAAAAABAAAAAQAAAAAW54MIqHAc32nFCT9+1gjU50n4hrm/oUrIn3uDhzuVTAAAAAEAAAAA43JoMKC2DLX1LIRM/81O7WXrpcFV6JsmQRVick5x5UQAAAAAAAAAADuaygAAAAAAAAAAAbmuMFkAAABAGIlPtdw9ye1dUM/u60QZ18+7GWfEnddxPdjgXkUy5ovDY+tzHPTimlqv1T7k/39SGoSamX9rUnFjnH6NxrbPBQ==');

$server = Server::testNet();

// Convert from XDR into a TransactionEnvelope
$transactionEnvelope = TransactionEnvelope::fromXdr(new XdrBuffer($xdr));

// Add the paying account's signature
// NOTE: a Server must be passed since the network passphrase is part of what gets
// signed. This prevents transactions on the test network from being valid on
// the public network.
$transactionEnvelope->sign($payingKeypair, $server);


// TransactionEnvelope now has two signatures and can be submitted
$server->submitB64Transaction($transactionEnvelope->toBase64());

print "Done!" . PHP_EOL;