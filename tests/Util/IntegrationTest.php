<?php

namespace ZuluCrypto\StellarSdk\Test\Util;

use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Server;
use ZuluCrypto\StellarSdk\XdrModel\Asset;

/**
 * Fixture data is setup in setup-integration-network.
 *
 * This class assumes access to a private network such as https://github.com/zulucrypto/docker-stellar-integration-test-network
 */
abstract class IntegrationTest extends TestCase
{
    /**
     * Base URL for the Horizon API server
     *
     * @var string
     */
    protected $horizonBaseUrl;

    /**
     * Network password
     *
     * @var string
     */
    protected $networkPassword;

    /**
     * Array of Keypairs describing fixture accounts
     *
     * @var Keypair[]
     */
    protected $fixtureAccounts;

    /**
     * @var Asset[]
     */
    protected $fixtureAssets;

    /**
     * Default Server connected to the integrationnet
     *
     * @var Server
     */
    protected $horizonServer;

    public function setUp()
    {
        $this->horizonBaseUrl = getenv('STELLAR_HORIZON_BASE_URL');
        if (!$this->horizonBaseUrl) {
            throw new \InvalidArgumentException('Environment variable STELLAR_HORIZON_BASE_URL must be defined');
        }

        // Public : Public Global Stellar Network ; September 2015
        // Testnet: Test SDF Network ; September 2015
        $this->networkPassword = getenv('STELLAR_NETWORK_PASSWORD');
        if (!$this->networkPassword) {
            $this->networkPassword = 'Integration Test Network ; zulucrypto';
        }

        $this->fixtureAccounts = $this->getFixtureAccounts();
        $this->fixtureAssets = $this->getFixtureAssets();

        $this->horizonServer = Server::customNet($this->horizonBaseUrl, $this->networkPassword);
    }

    /**
     * @return Keypair
     * @throws \ZuluCrypto\StellarSdk\Horizon\Exception\HorizonException
     */
    protected function getRandomFundedKeypair()
    {
        $keypair = Keypair::newFromRandom();
        $this->horizonServer->fundAccount($keypair);

        return $keypair;
    }

    /**
     * These are defined by the docker container, see: https://github.com/zulucrypto/docker-stellar-integration-test-network
     */
    protected function getFixtureAccounts()
    {
        return [
            // GAJCCCRIRXAYEU2ATNQAFYH4E2HKLN2LCKM2VPXCTJKIBVTRSOLEGCJZ
            'basic1' => Keypair::newFromSeed('SDJCZISO5M5XAUV6Y7MZJNN3JZ5BWPXDHV4GXP3MYNACVDNQRQSERXBC'),
            // GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ
            'basic2' => Keypair::newFromSeed('SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E'),
            // GAPSWEVEZVAOTW6AJM26NIVBITCKXNOMGBZAOPFTFDTJGKYCIIPVI4RJ
            'basic3' => Keypair::newFromSeed('SBY7ZNSKQ3CDHH34RUWVIUCMM7UEWWFTCM6ORFT5QTE77JGDFCBGXSU5'),

            // GDJ7OPOMTHEUFEBT6VUR7ANXR6BOHKR754CZ3KMSIMQC43HHBEDVDWVG
            'usdIssuingKeypair' => Keypair::newFromSeed('SBJXZEVYRX244HKDY6L5JZYPWDQW6D3WLEE3PTMQM4CSUKGE37J4AC3W'),
            // GBTO25DHZJ43Z5UI3JDAUQMHKP3SVUKLLBSNN7TFR7MW3PCLPSW3SFQQ
            'usdBankKeypair' => Keypair::newFromSeed('SDJOXTS4TE3Q3HUIFQK5AQCTRML6HIOUQIXDLCEQHICOFHU5CQN6DBLS'),

            // GC5DIPGB56HFCAUTX27K3TENHB65VQ2RNH2DJ3KALEXJHR6STPICMC3Y
            'jpyIssuingKeypair' => Keypair::newFromSeed('SDLK77FFXNCSTLXD6HMVGVR24FAK2GF6KX47I2FYOLRBCKPOD6TRW7V6'),
            // GAFT3ZRCVXDFJLTKNU3C2I3UGW6HVNI65FX7LTMYB5DQDPOM4H7XZAT6
            'jpyBankKeypair' => Keypair::newFromSeed('SCJ7RMMMCOOBTC77J5STNKV5EDAQDXXXPSDAOP66MBIIYLJYT7WZK2UN'),
            // GBMAHYE3L74AKS36LLF3AGQC55AL4AVZMVCCLJZXL32U2WDBMSEOZPQJ
            'jpyMerchantKeypair' => Keypair::newFromSeed('SAR6ZY7XFHBW5YYQGXSORBGIQH2AKSXQP3JPWPHEN3RSRTCMN6JBMYHS'),

            // GC5DIPGB56HFCAUTX27K3TENHB65VQ2RNH2DJ3KALEXJHR6STPICMC3Y
            'eurIssuingKeypair' => Keypair::newFromSeed('SAXU3ZUG3RGQLAQBBPDPHANM4UOO32D7IDLBA57JH3GXYQSJLKYHHMRM'),
            // GAFT3ZRCVXDFJLTKNU3C2I3UGW6HVNI65FX7LTMYB5DQDPOM4H7XZAT6
            'eurBankKeypair' => Keypair::newFromSeed('SAFSNJPNEBAQFPXNVOBPXOVLJMEGRBHPOPHGK565WKLJQ3U6QC4N5H3C'),
        ];
    }

    /**
     * Depends on getFixtureAccounts()
     */
    protected function getFixtureAssets()
    {
        return [
            'usd' => Asset::newCustomAsset('USDTEST', $this->fixtureAccounts['usdIssuingKeypair']->getPublicKey()),
            'jpy' => Asset::newCustomAsset('JPYTEST', $this->fixtureAccounts['jpyIssuingKeypair']->getPublicKey()),
            'eur' => Asset::newCustomAsset('EURTEST', $this->fixtureAccounts['eurIssuingKeypair']->getPublicKey()),
        ];
    }
}