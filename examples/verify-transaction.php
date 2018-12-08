<?php
require '../vendor/autoload.php';

use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\XdrModel\TransactionEnvelope;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;

/*
 * 	Public: GDG3PLZ5EKTMA3DOUGSLSUR7EZ3V3TPL4IBIRK3QYHYFADP5AFS5NW5D
 *  Secret: SDAP46Z3PXPZLZELRBLRCMS72C23LHHDCGJ2U3D6K5DXUN6A4KARNUNT
 */
use \ZuluCrypto\StellarSdk\Server;


$xdr = 'AAAAAM23rz0ipsBsbqGkuVI/JnddzeviAoircMHwUA39AWXWAAAAZAAQorIAAAABAAAAAQAAAAAAAAAAAAAAAAAAAAAAAAABAAAAAjQyAAAAAAABAAAAAAAAAAoAAAADZm9vAAAAAAEAAAADYmFyAAAAAAAAAAAB/QFl1gAAAED+nsTh3lM19FR3TBPqvswrcM7fM3mBj29fKVPKIdBhMR3SI0sbJiUVNcWwdGv5IveKOKsNYAnOdjRk9dJ8ebYC';
//$supposedSignersPublicKey = 'GBJPAGO7O4XKVZNBNKDPFPDRT47PHFA7DG24YZ677VQRZJFKZR7UN53Y';
$supposedSignersPublicKey = 'GDG3PLZ5EKTMA3DOUGSLSUR7EZ3V3TPL4IBIRK3QYHYFADP5AFS5NW5D';

$server = Server::testNet();

echo 'This transaction was ';
if (wasSignedBy($xdr, $supposedSignersPublicKey, $server)) {
    echo "This transaction was signed by the supposed signer.\n";
} else {
    echo "This transaction was not signed by the supposed signer.\n";
}


function wasSignedBy($xdr, $publicKey, $server) {

    $supposedSigner = Keypair::newFromPublicKey($publicKey);
    $envelope = TransactionEnvelope::fromXdr(new XdrBuffer(base64_decode($xdr)), $server);
    $hash = $envelope->getHash();

    foreach ($envelope->getDecoratedSignatures() as $signature) {
        if ($supposedSigner->verifySignature( $signature->getRawSignature(), $hash)) {
            return true;
        }
    }

    return false;
}

