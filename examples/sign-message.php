<?php
/**
 * Demonstrates how to sign and verify a message using Stellar keypairs
 */

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;

$message = 'test stellar message';

// Sign the message: private key is required

// GDRXE2BQUC3AZNPVFSCEZ76NJ3WWL25FYFK6RGZGIEKWE4SOOHSUJUJ6
$signingKeypair = Keypair::newFromSeed('SBGWSG6BTNCKCOB3DIFBGCVMUPQFYPA2G4O34RMTB343OYPXU5DJDVMN');

$signatureBytes = $signingKeypair->sign($message);

printf("Signed (base-64 encoded): " . base64_encode($signatureBytes) . PHP_EOL);


// Verify the signature
// Note that only the public key is required
$verifyingKeypair = Keypair::newFromPublicKey('GDRXE2BQUC3AZNPVFSCEZ76NJ3WWL25FYFK6RGZGIEKWE4SOOHSUJUJ6');

$isVerified = $verifyingKeypair->verifySignature($signatureBytes, $message);
printf(PHP_EOL);
printf("Verified? %s" . PHP_EOL, ($isVerified) ? 'Yes' : 'No');