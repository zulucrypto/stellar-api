<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;
use \ZuluCrypto\StellarSdk\XdrModel\SignerKey;
use \ZuluCrypto\StellarSdk\XdrModel\Signer;

$server = Server::testNet();

// GAPSWEVEZVAOTW6AJM26NIVBITCKXNOMGBZAOPFTFDTJGKYCIIPVI4RJ
$keypair = Keypair::newFromSeed('SBY7ZNSKQ3CDHH34RUWVIUCMM7UEWWFTCM6ORFT5QTE77JGDFCBGXSU5');

// Options operation covers several cases
$optionsOperation = new SetOptionsOp();

// Set inflation destination
$optionsOperation->setInflationDestination('GBLGN5K633LO5BMWEKSVHKXTPZHBVVBNSXRGXRDGZUANXWQ4LBWES3BK');

// Set auth required
$optionsOperation->setAuthRequired(true);

// Set auth revokable
$optionsOperation->setAuthRevocable(true);

// Add a new account as a signer (GAJCCCRIRXAYEU2ATNQAFYH4E2HKLN2LCKM2VPXCTJKIBVTRSOLEGCJZ)
$newSignerKeypair = Keypair::newFromSeed('SDJCZISO5M5XAUV6Y7MZJNN3JZ5BWPXDHV4GXP3MYNACVDNQRQSERXBC');
$signerKey = SignerKey::fromKeypair($newSignerKeypair);
$newAccountSigner = new Signer($signerKey, 5);

$optionsOperation->updateSigner($newAccountSigner);

// Set weight of the master key
$optionsOperation->setMasterWeight(255);

// Set threshold values
$optionsOperation->setLowThreshold(1);
$optionsOperation->setMediumThreshold(2);
$optionsOperation->setHighThreshold(3);

// Set home domain
$optionsOperation->setHomeDomain('example.com');


// Submit options to the network
$server->buildTransaction($keypair->getPublicKey())
    ->addOperation($optionsOperation)
    ->submit($keypair->getSecret());