<?php

namespace ZuluCrypto\StellarSdk\Test\Util;

use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Server;

class IntegrationTest extends TestCase
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
     * Array of arrays with keys:
     *  accountId - public account key
     *  seed - private seed
     *
     * @var array
     */
    protected $fixtureAccounts;

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

        $this->networkPassword = getenv('STELLAR_NETWORK_PASSWORD');
        if (!$this->networkPassword) {
            $this->networkPassword = 'Integration Test Network ; zulucrypto';
        }

        $this->fixtureAccounts = $this->getFixtureAccounts();

        $this->horizonServer = Server::customNet($this->horizonBaseUrl, $this->networkPassword);
    }

    /**
     * These are defined by the docker container, see: https://github.com/zulucrypto/docker-stellar-integration-test-network
     */
    protected function getFixtureAccounts()
    {
        return [
            'basic1' => [
                'accountId' => 'GAJCCCRIRXAYEU2ATNQAFYH4E2HKLN2LCKM2VPXCTJKIBVTRSOLEGCJZ',
                'seed' => 'SDJCZISO5M5XAUV6Y7MZJNN3JZ5BWPXDHV4GXP3MYNACVDNQRQSERXBC',
            ],
            'basic2' => [
                'accountId' => 'GCP6IHMHWRCF5TQ4ZP6TVIRNDZD56W42F42VHYWMVDGDAND75YGAHHBQ',
                'seed' => 'SCEDMZ7DUEOUGRQWEXHXEXISQ2NAWI5IDXRHYWT2FHTYLIQOSUK5FX2E',
            ],
            'basic3' => [
                'accountId' => 'GAPSWEVEZVAOTW6AJM26NIVBITCKXNOMGBZAOPFTFDTJGKYCIIPVI4RJ',
                'seed' => 'SBY7ZNSKQ3CDHH34RUWVIUCMM7UEWWFTCM6ORFT5QTE77JGDFCBGXSU5',
            ],
        ];
    }
}