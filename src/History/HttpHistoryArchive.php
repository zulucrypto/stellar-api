<?php


namespace ZuluCrypto\StellarSdk\History;


use GuzzleHttp\Client;
use Symfony\Component\Filesystem\Filesystem;
use ZuluCrypto\StellarSdk\Util\Json;

/**
 * Manages downloading from a HTTP history archive
 *
 * References:
 *  https://github.com/stellar/stellar-core/blob/master/docs/history.md
 */
class HttpHistoryArchive
{
    /**
     * @var string
     */
    protected $rootUrl;

    /**
     * @var string
     */
    protected $storageRoot;

    /**
     * @var Client
     */
    protected $httpClient;

    /**
     * https://github.com/stellar/stellar-core/blob/master/docs/history.md#root-has
     *
     * @var array
     */
    protected $rootHas;

    public function __construct($rootUrl, $storageRoot)
    {
        // Root URL must end in a slash
        if (substr($rootUrl, -1, 1) != '/') $rootUrl .= '/';
        // Storage url must end in the appropriate directory separator
        if (substr($storageRoot, -1, 1) != DIRECTORY_SEPARATOR) $storageRoot .= DIRECTORY_SEPARATOR;

        $this->rootUrl = $rootUrl;
        $this->storageRoot = $storageRoot;

        $this->httpClient = new Client([
            'base_uri' => $rootUrl,
        ]);
    }

    public function downloadAll()
    {
        $this->syncRootHas();

        $startingLedger = 64-1;
        $ledgerIncrement = 64;
        $currLedger = $startingLedger;
        $percentComplete = 0;
        while ($currLedger < $this->rootHas['currentLedger']) {
            $ledgerHex = sprintf("%x", $currLedger);
            $ledgerHex = str_pad($ledgerHex, 8, '0', STR_PAD_LEFT);

            // Get HAS file
            $hasPath = $this->getHashedLedgerSubdirectory('history.json', $ledgerHex);
            $localHasPath = $this->syncFile($hasPath);

            $archiveState = HistoryArchiveState::fromFile($localHasPath);

            // Bucket files
            foreach ($archiveState->getUniqueBucketHashes() as $bucketHash) {
                $bucketPath = $this->getHashedBucketSubdirectory($bucketHash);
                $this->syncFile($bucketPath);
            }

            // Other files
            $this->syncFile($this->getHashedLedgerSubdirectory('ledger.xdr.gz', $ledgerHex));
            $this->syncFile($this->getHashedLedgerSubdirectory('transactions.xdr.gz', $ledgerHex));
            $this->syncFile($this->getHashedLedgerSubdirectory('results.xdr.gz', $ledgerHex));
            $this->syncFile($this->getHashedLedgerSubdirectory('scp.xdr.gz', $ledgerHex), false, true);

            $currLedger += $ledgerIncrement;
            $percentComplete = round(($currLedger / $this->rootHas['currentLedger']) * 100);
        }
    }

    public function syncRootHas()
    {
        $response = $this->httpClient->get('.well-known/stellar-history.json');

        $this->rootHas = Json::mustDecode($response->getBody());
    }

    /**
     * Downloads $relativeUrl to the corresponding location on the local filesystem
     *
     * @param      $relativeUrl
     * @param bool $forceDownload force download even if the file is present locally
     * @param bool $allowMissing allow 404 responses
     * @return string
     * @throws \ErrorException
     */
    public function syncFile($relativeUrl, $forceDownload = false, $allowMissing = false)
    {
        $fs = new Filesystem();

        // Target file to save to will match the relativeUrl being requested, but
        // with the correct directory separator
        $targetFile = str_replace('/', DIRECTORY_SEPARATOR, $relativeUrl);
        $targetFile = $this->storageRoot . $targetFile;

        // Files won't change and are only written after a successful download, so
        // if it exists there's no need to download it again
        if (!$forceDownload && $fs->exists($targetFile)) return $targetFile;

        $tmpFile = $fs->tempnam(sys_get_temp_dir(), 'stellar-sync-tmp');
        $response = $this->httpClient->request('GET', $relativeUrl, [
            'sink' => $tmpFile,
            'http_errors' => false,
        ]);

        // Response succeeded
        if ($response->getStatusCode() == 200) {
            // 200 is always OK
        }
        // 404, but missing files are ok
        elseif ($allowMissing && $response->getStatusCode() == 404) {
            // this is OK
        }
        // Error response or 404 with $allowMissing false
        else {
            throw new \ErrorException(sprintf('HTTP %s: %s', $response->getStatusCode(), $response->getBody()));
        }

        // Move temporary file into its final location
        $fs->mkdir(dirname($targetFile), 0700);
        $fs->rename($tmpFile, $targetFile);

        // Basic rate limiting
        usleep(20000);
        return $targetFile;
    }

    protected function getHashedLedgerSubdirectory($filename, $prefixHex)
    {
        // Parse the filename (eg: ledger.xdr.gz) into category (ledger) and extension (.xdr.gz)
        $parts = explode(".", $filename);
        $category = array_shift($parts);
        $extension = join('.', $parts);
        $subdir = $category;

        // Three levels of subdirectories
        $subdir .= '/' . substr($prefixHex, 0, 2);
        $subdir .= '/' . substr($prefixHex, 2, 2);
        $subdir .= '/' . substr($prefixHex, 4, 2);

        // Then the filename + hex + extension
        return sprintf('%s/%s-%s.%s',
            $subdir,
            $category, $prefixHex, $extension
        );
    }

    protected function getHashedBucketSubdirectory($prefixHex)
    {
        $subdir = 'bucket';

        // Three levels of subdirectories
        $subdir .= '/' . substr($prefixHex, 0, 2);
        $subdir .= '/' . substr($prefixHex, 2, 2);
        $subdir .= '/' . substr($prefixHex, 4, 2);

        return sprintf('%s/bucket-%s.xdr.gz',
            $subdir,
            $prefixHex
        );
    }
}