<?php

require __DIR__ . '/../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Server;

// See 01-create-account.php for where this was generated
$publicAccountId = 'GBCT7H5STV3DCAHJKFEYSDUGMF6RSK6O4V5J6JZT4TAFXIYPDKWD2REB';

$server = Server::testNet();

$account = $server->getAccount($publicAccountId);

print 'Balances for account ' . $publicAccountId . PHP_EOL;

foreach ($account->getBalances() as $balance) {
    printf('  Type: %s, Code: %s, Balance: %s' . PHP_EOL,
        $balance->getAssetType(),
        $balance->getAssetCode(),
        $balance->getBalance()
    );
}

