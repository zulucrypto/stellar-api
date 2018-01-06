<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;

$server = Server::testNet();

// GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG
$issuingKeypair = Keypair::newFromSeed('SBJXZEVYRX244HKDY6L5JZYPWDQW6D3WLEE3PTMQM4CSUKGE37J4AC3W');

// GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ
$receivingKeypair = Keypair::newFromSeed('SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E');

$asset = Asset::newCustomAsset('USDTEST', $issuingKeypair->getPublicKey());


// First, the receiving account must add a trustline for the issuer
$server->buildTransaction($receivingKeypair)
    ->addChangeTrustOp($asset) // this will default to the maximum value
    ->submit($receivingKeypair);

// Then, the issuing account can transfer assets
$server->buildTransaction($issuingKeypair)
    ->addCustomAssetPaymentOp($asset, 50, $receivingKeypair->getPublicKey())
    ->submit($issuingKeypair->getSecret());


// ------------------------------------
// An asset that requires authorization

// GDYW5Y5PCHC3RGUPME4MIFBQCDLFMCSFEB6EAA7P2PJRAKGPGUDZX64Q
$issuingKeypair = Keypair::newFromSeed('SC5AQ5K332ZIZB5MWG7FA64JURJXOR4B7VAIDIV7ORIEV7LSGNYFNPL3');

$asset = Asset::newCustomAsset('AUTHTEST', $issuingKeypair->getPublicKey());

// Issuing keypair indicates that authorization is required and revocable
$accountOptions = new SetOptionsOp();
$accountOptions->setAuthRequired(true);
$accountOptions->setAuthRevocable(true); // This is optional

$server->buildTransaction($issuingKeypair)
    ->addOperation($accountOptions)
    ->submit($issuingKeypair);

// Receiving account adds a trustline
$server->buildTransaction($receivingKeypair->getPublicKey())
    ->addChangeTrustOp($asset)
    ->submit($receivingKeypair->getSecret());

// Issuing account indicates the receiving account is authorized
$server->buildTransaction($issuingKeypair->getPublicKey())
    ->authorizeTrustline($asset, $receivingKeypair)
    ->submit($issuingKeypair);

// Issuing account can now transfer to receiving account
$server->buildTransaction($issuingKeypair)
    ->addCustomAssetPaymentOp($asset, 75, $receivingKeypair->getPublicKey())
    ->submit($issuingKeypair->getSecret());