<?php
/**
 * Mirrors a history archive to the target directory
 *
 * Example usage:
 *
 * php sync-history.php http://example.com/history/ /tmp/stellar-history/
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use \ZuluCrypto\StellarSdk\History\HttpHistoryArchive;

// Root URL of the history archive
$sourceArchiveUrl = $argv[1];
$storageRoot = $argv[2];

if (!$sourceArchiveUrl) die("Source archive URL is required");
if (!$storageRoot) die("Local directory to mirror to is required");

$archive = new HttpHistoryArchive($sourceArchiveUrl, $storageRoot);

$archive->downloadAll();
