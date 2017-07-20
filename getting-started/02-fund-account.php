<?php

require __DIR__ . '/../vendor/autoload.php';

// See 01-create-account.php for where this was generated
$publicAccountId = 'GCFXHS4GXL6BVUCXBWXGTITROWLVYXQKQLF4YH5O5JT3YZXCYPAFBJZB';

// Use the testnet friendbot to add funds:
$response = file_get_contents('https://horizon-testnet.stellar.org/friendbot?addr=' . $publicAccountId);

// After a successful response, the account will have lumens from the testbot
if ($response !== false) {
    print 'Success! Account is now funded.' . PHP_EOL;
}

