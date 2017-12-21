<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;
use \ZuluCrypto\StellarSdk\Server;

$server = Server::testNet();

// GD7NEI36IWVR3EZWPMGRUPJ6LQQGDOZY4CYAKV4D5TMZTLKA5BZM2LKL
$fromKeypair = Keypair::newFromSeed('SBNA7H2S6FTL5C5YKQKRDSQGBAWQLLSUEK4TQAOJ45FWHMNTJZFF5JZI');

// GAJJAADYPQQC36ST5GICFHEZMHE6YPYEJJLN7L3E5FRPMKZXQZCWJESW
$destinationKeypair = Keypair::newFromSeed('SBUZRQQAGYMMIRK7D2IGWIPSW3ORKVEURSZKVTR5ICSLEQOC6HEL47J4');

// Both accounts must be funded for this test
$server->fundAccount($fromKeypair);
$server->fundAccount($destinationKeypair);

// Transfer balance from $fromKeypair to $destinationKeypair

$server->buildTransaction($fromKeypair)
    ->addMergeOperation($destinationKeypair)
    ->submit($fromKeypair);