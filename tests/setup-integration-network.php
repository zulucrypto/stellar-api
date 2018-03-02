<?php

/**
 * Sets up the accounts, issuers, assets, etc. on the integration network
 *
 * This script is designed to be run on a private network such as: https://github.com/zulucrypto/docker-stellar-integration-test-network
 *
 * Example URL to view path payment from Alice -> Bob for 500 EURTEST:
 *  /paths?destination_account=GALOPAYIVBYBZX3JYUET67WWBDKOOSPYQ2437IKKZCPXXA4HHOKUZ5OA&source_account=GD4JRFLPF4AGYQTLCMZ7Q7DRLGQZQTGWOOKDUCNRVKG66G5ZVYYFT76M&destination_asset_type=credit_alphanum12&destination_asset_code=EURTEST&destination_asset_issuer=GBOCHOYXRJ7JSN22MLEUC6VPYYYWQBY77YEY2FUPJVUZNW2AMAIA5ISC&destination_amount=500
 *
 */

require_once(__DIR__ . '/../vendor/autoload.php');


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\XdrModel\Asset;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Operation\ManageOfferOp;
use ZuluCrypto\StellarSdk\XdrModel\Price;
use ZuluCrypto\StellarSdk\XdrModel\Operation\SetOptionsOp;


$horizonBaseUrl = getenv('STELLAR_HORIZON_BASE_URL');
if (!$horizonBaseUrl) $horizonBaseUrl = 'http://localhost:8000/';

$networkPassphrase = getenv('STELLAR_NETWORK_PASSPHRASE');
if (!$networkPassphrase) $networkPassphrase = 'Integration Test Network ; zulucrypto';

$server = Server::customNet($horizonBaseUrl, $networkPassphrase);

// ------------------------------------------------------------
// Keypairs

print "Setting up accounts...\n";

// GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG
$usdIssuingKeypair = setupKeypair('SBJXZEVYRX244HKDY6L5JZYPWDQW6D3WLEE3PTMQM4CSUKGE37J4AC3W');
// GBOCHOYXRJ7JSN22MLEUC6VPYYYWQBY77YEY2FUPJVUZNW2AMAIA5ISC
$eurIssuingKeypair = setupKeypair('SAXU3ZUG3RGQLAQBBPDPHANM4UOO32D7IDLBA57JH3GXYQSJLKYHHMRM');
// GC5DIPGB56HFCAUTX27K3TENHB65VQ2RNH2DJ3KALEXJHR6STPICMC3Y
$jpyIssuingKeypair = setupKeypair('SDLK77FFXNCSTLXD6HMVGVR24FAK2GF6KX47I2FYOLRBCKPOD6TRW7V6');
// GAOM2624VSUBOGXTKS6ZVZZRXYUQTFNMUOGTVW6O5UT6JSV4T6F457DA
$authRequiredIssuingKeypair = setupKeypair('SABFYGWPSP3EEJ2EURHQYAIRTNK3SVQPED5PWOHGCWKPZBSCWBV4QGKE');

// GBTO25DHZJ43Z5UI3JDAUQMHKP3SVUKLLBSNN7TFR7MW3PCLPSW3SFQQ
$usdBankKeypair = setupKeypair('SDJOXTS4TE3Q3HUIFQK5AQCTRML6HIOUQIXDLCEQHICOFHU5CQN6DBLS');
// GAU7MRC2T3SAY23F4VYQJNSJNM4S2GWVYBEKC7T2Z7EY5D5N7CBU4QSH
$eurBankKeypair = setupKeypair('SAFSNJPNEBAQFPXNVOBPXOVLJMEGRBHPOPHGK565WKLJQ3U6QC4N5H3C');
// GAFT3ZRCVXDFJLTKNU3C2I3UGW6HVNI65FX7LTMYB5DQDPOM4H7XZAT6
$jpyBankKeypair = setupKeypair('SCJ7RMMMCOOBTC77J5STNKV5EDAQDXXXPSDAOP66MBIIYLJYT7WZK2UN');

// GBMAHYE3L74AKS36LLF3AGQC55AL4AVZMVCCLJZXL32U2WDBMSEOZPQJ
$jpyMerchantKeypair = setupKeypair('SAR6ZY7XFHBW5YYQGXSORBGIQH2AKSXQP3JPWPHEN3RSRTCMN6JBMYHS');


// GD4JRFLPF4AGYQTLCMZ7Q7DRLGQZQTGWOOKDUCNRVKG66G5ZVYYFT76M
$userAliceKeypair = setupKeypair('SAA2U5UFW65DW3MLVX734BUQIHAWANQNBLTFT47X2NVVBCN7X6QC5AOG');
// GALOPAYIVBYBZX3JYUET67WWBDKOOSPYQ2437IKKZCPXXA4HHOKUZ5OA
$userBobKeypair = setupKeypair('SBFMSGMYTSAJMCNEJQPPO65BNL5HSUXYKY4HPE2RZWXBP7C745YKYIUC');

// Define test assets
$nativeAsset = Asset::newNativeAsset();
$usdAsset = Asset::newCustomAsset('USDTEST', $usdIssuingKeypair->getPublicKey());
$eurAsset = Asset::newCustomAsset('EURTEST', $eurIssuingKeypair->getPublicKey());
$jpyAsset = Asset::newCustomAsset('JPYTEST', $jpyIssuingKeypair->getPublicKey());
$authRequiredAsset = Asset::newCustomAsset('AUTHREQ', $authRequiredIssuingKeypair->getPublicKey());

// Configure assets
print "Configuring assets...\n";

$op = new SetOptionsOp();
$op->setAuthRevocable(true);
$op->setAuthRequired(true);
$server->buildTransaction($authRequiredIssuingKeypair)
    ->addOperation($op)
    ->submit($authRequiredIssuingKeypair);

// Establish trustlines for banks to assets
print "Establishing trustlines...\n";
$server->buildTransaction($usdBankKeypair)
    ->addChangeTrustOp($usdAsset)
    ->submit($usdBankKeypair);

$server->buildTransaction($eurBankKeypair)
    ->addChangeTrustOp($eurAsset)
    ->submit($eurBankKeypair);

$server->buildTransaction($jpyBankKeypair)
    ->addChangeTrustOp($jpyAsset)
    ->submit($jpyBankKeypair);

// For users
$server->buildTransaction($userAliceKeypair)
    ->addChangeTrustOp($eurAsset)
    ->submit($userAliceKeypair);

$server->buildTransaction($userAliceKeypair)
    ->addChangeTrustOp($authRequiredAsset)
    ->submit($userAliceKeypair);
$server->buildTransaction($authRequiredIssuingKeypair)
    ->authorizeTrustline($authRequiredAsset, $userAliceKeypair)
    ->submit($authRequiredIssuingKeypair);

$server->buildTransaction($userBobKeypair)
    ->addChangeTrustOp($eurAsset)
    ->submit($userBobKeypair);

// Fund the bank accounts
print "Funding Anchor accounts...\n";
$server->buildTransaction($usdIssuingKeypair)
    ->addCustomAssetPaymentOp($usdAsset, 1000000, $usdBankKeypair)
    ->submit($usdIssuingKeypair->getSecret());

$server->buildTransaction($eurIssuingKeypair)
    ->addCustomAssetPaymentOp($eurAsset, 1000000, $eurBankKeypair)
    ->submit($eurIssuingKeypair->getSecret());

$server->buildTransaction($jpyIssuingKeypair)
    ->addCustomAssetPaymentOp($jpyAsset, 1000000, $jpyBankKeypair)
    ->submit($jpyIssuingKeypair->getSecret());

// Anchors have standing offers for their assets
print "Submitting custom asset offers...\n";
$server->buildTransaction($usdBankKeypair)
    ->addOperation(new ManageOfferOp($usdAsset, $nativeAsset, 500000, new Price(100)))
    ->submit($usdBankKeypair->getSecret());

$server->buildTransaction($eurBankKeypair)
    ->addOperation(new ManageOfferOp($eurAsset, $nativeAsset, 500000, new Price(120)))
    ->submit($eurBankKeypair->getSecret());

$server->buildTransaction($jpyBankKeypair)
    ->addOperation(new ManageOfferOp($jpyAsset, $nativeAsset, 500000, new Price(40)))
    ->submit($jpyBankKeypair->getSecret());



/**
 * Funds $secretKey from friendbot
 *
 * @param $secretKey
 * @return Keypair
 * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
 */
function setupKeypair($secretKey)
{
    global $server;

    $keypair = Keypair::newFromSeed($secretKey);

    $account = $server->getAccount($keypair);

    if (!$account) {
        $server->fundAccount($keypair->getPublicKey());
    }

    return $keypair;
}
