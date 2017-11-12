<?php

require '../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\Keypair;


$mnemonic = 'cable spray genius state float twenty onion head street palace net private method loan turn phrase state blanket interest dry amazing dress blast tube';
$passphrase = 'p4ssphr4se'; // can be blank

// The primary account generated from this mnemonic
$primaryAccount = Keypair::newFromMnemonic($mnemonic, $passphrase);

// Pass an index to generate additional accounts from the same mnemonic
$secondaryAccount = Keypair::newFromMnemonic($mnemonic, $passphrase, 1);

// GDAHPZ2NSYIIHZXM56Y36SBVTV5QKFIZGYMMBHOU53ETUSWTP62B63EQ
print "Address: " . $primaryAccount->getPublicKey() . PHP_EOL;
// SAFWTGXVS7ELMNCXELFWCFZOPMHUZ5LXNBGUVRCY3FHLFPXK4QPXYP2X
print "Seed   : " . $primaryAccount->getSecret() . PHP_EOL;