<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Horizon\ApiClient;
use \ZuluCrypto\StellarSdk\Model\Effect;

$client = ApiClient::newPublicClient();

$client->streamEffects('now', function(Effect $effect) {
    printf('[%s] %s' . PHP_EOL,
        (new \DateTime())->format('Y-m-d h:i:sa'),
        $effect->getType()
    );
});

