<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;
use \ZuluCrypto\StellarSdk\XdrModel\Asset;

$server = Server::testNet();

// GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG
$issuingKeypair = Keypair::newFromSeed('SBJXZEVYRX244HKDY6L5JZYPWDQW6D3WLEE3PTMQM4CSUKGE37J4AC3W');

// GBTO25DHZJ43Z5UI3JDAUQMHKP3SVUKLLBSNN7TFR7MW3PCLPSW3SFQQ
$offeringKeypair = Keypair::newFromSeed('SDJOXTS4TE3Q3HUIFQK5AQCTRML6HIOUQIXDLCEQHICOFHU5CQN6DBLS');

$usdtestAsset = Asset::newCustomAsset('USDTEST', $issuingKeypair->getPublicKey());
$nativeAsset = Asset::newNativeAsset();

// Offer 5,000 XLM @ 100 XLM per 1 USDTEST
$server->buildTransaction($offeringKeypair->getPublicKey())
    ->addOperation(
        new \ZuluCrypto\StellarSdk\XdrModel\Operation\ManageOfferOp(
            $usdtestAsset,
            $nativeAsset,
            5000,
            new \ZuluCrypto\StellarSdk\XdrModel\Price(100, 1)
        )
    )
    ->submit($offeringKeypair->getSecret());

// Passive offer of 1,000 XLM @ 50 XLM per 1 USDTEST
$server->buildTransaction($offeringKeypair->getPublicKey())
    ->addOperation(
        new \ZuluCrypto\StellarSdk\XdrModel\Operation\CreatePassiveOfferOp(
            $usdtestAsset,
            $nativeAsset,
            1000,
            new \ZuluCrypto\StellarSdk\XdrModel\Price(50, 1)
        )
    )
    ->submit($offeringKeypair->getSecret());