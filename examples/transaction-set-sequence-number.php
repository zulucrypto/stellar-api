<?php

require '../vendor/autoload.php';

use \phpseclib\Math\BigInteger;

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use \ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;

// GD4JRFLPF4AGYQTLCMZ7Q7DRLGQZQTGWOOKDUCNRVKG66G5ZVYYFT76M
$sourceKeypair = Keypair::newFromSeed('SAA2U5UFW65DW3MLVX734BUQIHAWANQNBLTFT47X2NVVBCN7X6QC5AOG');

// GDRXE2BQUC3AZNPVFSCEZ76NJ3WWL25FYFK6RGZGIEKWE4SOOHSUJUJ6
$destinationKeypair = Keypair::newFromSeed('SBGWSG6BTNCKCOB3DIFBGCVMUPQFYPA2G4O34RMTB343OYPXU5DJDVMN');


$builder = Server::testNet()
    ->buildTransaction($sourceKeypair)
    ->addLumenPayment($destinationKeypair, 100);

$builder->setSequenceNumber(new BigInteger(123));

print "Sequence number: " . $builder->getSequenceNumber() . "\n";