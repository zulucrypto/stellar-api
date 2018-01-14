<?php


namespace ZuluCrypto\StellarSdk\Test\Util;


use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Signing\PrivateKeySigner;
use ZuluCrypto\StellarSdk\Signing\TrezorSigner;

/**
 * ## Executing Tests
 *
 * 1. Initialize the hardware wallet with the test mnemonic: illness spike retreat truth genius clock brain pass fit cave bargain toe
 * 2. Execute the tests/run-hardware-wallet.sh test script
 *
 * As tests are run, information will appear in the terminal so you can verify
 * the correct information is displayed on the hardware wallet.
 *
 * Each test will also assert that the PHP library's signature matches the one
 * returned by the hardware wallet.
 *
 * ## Hints
 *
 * Run a specific test via:
 *  $ tests/run-hardware-wallet.sh --filter testCustomAsset12Payment
 */
abstract class HardwareWalletIntegrationTest extends IntegrationTest
{
    /**
     * @var string
     */
    protected $mnemonic;

    /**
     * A PrivateKeySigner created from the mnemonic and using the default account
     *
     * @var PrivateKeySigner
     */
    protected $privateKeySigner;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // GDRXE2BQUC3AZNPVFSCEZ76NJ3WWL25FYFK6RGZGIEKWE4SOOHSUJUJ6
        // SBGWSG6BTNCKCOB3DIFBGCVMUPQFYPA2G4O34RMTB343OYPXU5DJDVMN
        $this->mnemonic = 'illness spike retreat truth genius clock brain pass fit cave bargain toe';
        $this->privateKeySigner = new PrivateKeySigner(Keypair::newFromMnemonic($this->mnemonic));
    }

    public function setUp()
    {
        parent::setUp();

        $signingProvider = getenv('STELLAR_SIGNING_PROVIDER');
        if (!$signingProvider) {
            printf('STELLAR_SIGNING_PROVIDER must be defined' . PHP_EOL);
            printf('For example: ' . PHP_EOL);
            printf('export STELLAR_SIGNING_PROVIDER=trezor' . PHP_EOL);
            die();
        }

        if ($signingProvider == 'trezor') {
            $signer = new TrezorSigner();
            $trezorBinPath = getenv('TREZOR_BIN_PATH');
            if ($trezorBinPath) {
                $signer->setTrezorBinPath($trezorBinPath);
            }

            // Set the public key of the signer to the default to prevent
            // unnecessary calls to the hardware wallet to retrieve the public key
            $signer->setPublicKey(Keypair::newFromMnemonic($this->mnemonic));

            $this->horizonServer->setSigningProvider($signer);
        }
        else {
            die('Unsupported STELLAR_SIGNING_PROVIDER');
        }
    }

    /**
     * Immediately writes output so the tester can verify the correct information
     * is displayed on the hardware wallet
     *
     * @param $message
     */
    public function manualVerificationOutput($message)
    {
        print PHP_EOL . $message . PHP_EOL;

        ob_flush();
    }
}