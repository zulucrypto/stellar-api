<?php


namespace ZuluCrypto\StellarSdk\Derivation\Bip39;


use ZuluCrypto\StellarSdk\Util\Debug;

/**
 * Minimal BIP-39 implementation (only what's necessary to generate seeds for
 * Stellar wallets)
 *
 * The default wordlist is english. To use a different wordlist, pass its path
 * to the constructor. The wordlist must be formatted with one word on each
 * line. See: https://github.com/bitcoin/bips/blob/master/bip-0039/bip-0039-wordlists.md
 */
class Bip39
{
    protected $words;

    /**
     * Returns the hex-encoded checksum for the raw bytes in the entropy
     *
     * @param $entropyBytes
     * @return string
     */
    public static function getEntropyChecksumHex($entropyBytes)
    {
        $checksumLengthBits = (strlen($entropyBytes)*8) / 32;
        $hashBytes = hash('sha256', $entropyBytes, true);

        // base_convert can only handle up to 64 bits, so we have to reduce the
        // length of data that gets sent to it
        $checksumLengthBytes = ceil($checksumLengthBits / 8);
        $reducedBytesToChecksum = substr($hashBytes, 0, $checksumLengthBytes);

        $reducedChecksumHex = bin2hex($reducedBytesToChecksum);
        $reducedChecksumBits = str_pad(base_convert($reducedChecksumHex, 16, 2), $checksumLengthBytes * 8, '0', STR_PAD_LEFT);

        $checksumBitstring = substr($reducedChecksumBits, 0, $checksumLengthBits);
        $checksumHex = static::bitstringToHex($checksumBitstring);

        return $checksumHex;
    }

    /**
     * Utility method to convert a bitstring to hex.
     *
     * Primarily a workaround to avoid requiring a real math library
     *
     * @param $bitstring
     * @return string
     */
    public static function bitstringToHex($bitstring)
    {
        $chunkSizeBits = 8;

        // If the string is shorter than the chunk size it can be 0-padded
        if (strlen($bitstring) < $chunkSizeBits) {
            $bitstring = str_pad($bitstring, $chunkSizeBits, '0', STR_PAD_LEFT);
        }

        if (strlen($bitstring) % $chunkSizeBits !== 0) throw new \InvalidArgumentException(sprintf('Got bitstring of length %s, but it must be divisible by %s', strlen($bitstring), $chunkSizeBits));

        $finalHex = '';
        for ($i=0; $i < strlen($bitstring); $i += $chunkSizeBits) {
            $bitstringPart = substr($bitstring, $i, $chunkSizeBits);
            $hex = base_convert($bitstringPart, 2, 16);
            // Ensure hex is always two characters
            $hex = str_pad($hex, 2, '0', STR_PAD_LEFT);

            $finalHex .= $hex;
        }

        return $finalHex;
    }

    /**
     * Bip39 constructor.
     *
     * @param null $wordlistPath
     */
    public function __construct($wordlistPath = null)
    {
        if (null === $wordlistPath) $wordlistPath = __DIR__ . '/wordlists/en.txt';

        $this->words = $this->loadWordlist($wordlistPath);
    }

    /**
     * Converts a mnemonic to raw bytes
     *
     * NOTE: this is NOT the raw bytes used for a Stellar key! See mnemonicToSeedBytes
     *
     * @param $mnenomic
     * @return bool|string
     */
    public function mnemonicToEntropy($mnenomic)
    {
        $bitstring = $this->parseMnemonic($mnenomic);

        // Calculate expected lengths
        $numChecksumBits = strlen($bitstring) / 33;
        $numEntropyBits = strlen($bitstring) - $numChecksumBits;

        // Get checksum bits from the end of the string
        $checksumBits = substr($bitstring, -1 * $numChecksumBits);
        $checksumHex = static::bitstringToHex($checksumBits);

        // Remaining bits are the entropy
        $entropyBits = substr($bitstring, 0, $numEntropyBits);
        $entropyHex = static::bitstringToHex($entropyBits);

        $entropyBytes = hex2bin($entropyHex);

        if ($checksumHex !== static::getEntropyChecksumHex($entropyBytes)) {
            throw new \InvalidArgumentException('Invalid checksum');
        }

        return $entropyBytes;
    }

    /**
     * Converts a mnemonic and optional passphrase to a 64-byte string for use
     * as entropy.
     *
     * Note that this is specific to the wordlist being used and is NOT portable
     * across wordlists.
     *
     * In most cases, mnemonicToSeedBytesWithErrorChecking should be used since
     * it will fail if there's a checksum error in the mnemonic
     *
     * @param        $mnemonic
     * @param string $passphrase
     * @return string
     */
    public function mnemonicToSeedBytes($mnemonic, $passphrase = '')
    {
        $salt = 'mnemonic' . $passphrase;
        return hash_pbkdf2("sha512", $mnemonic, $salt, 2048, 64, true);
    }

    /**
     * Converts $mnemonic to seed bytes suitable for creating a new HDNode
     *
     * If the mnemonic is invalid, an exception is thrown
     *
     * @param        $mnemonic
     * @param string $passphrase
     * @return string raw bytes
     */
    public function mnemonicToSeedBytesWithErrorChecking($mnemonic, $passphrase = '')
    {
        // This will throw an exception if the embedded checksum is incorrect
        $this->mnemonicToEntropy($mnemonic);

        return $this->mnemonicToSeedBytes($mnemonic, $passphrase);
    }

    /**
     * Parses a string of words and returns a string representing the binary
     * encoding of the mnemonic (including checksum)
     *
     * Note that this is a literal string of "101101110" and not raw bytes!
     *
     * @param $mnemonic
     * @return string
     */
    protected function parseMnemonic($mnemonic)
    {
        $words = explode(' ', $mnemonic);
        if (count($words) %3 !== 0) throw new \InvalidArgumentException('Invalid mnemonic (number of words must be a multiple of 3)');

        $wordBitstrings = [];
        foreach ($words as $word) {
            $wordIdx = $this->getWordIndex($word);

            // Convert $wordIdx to an 11-bit number (preserving 0s)
            $wordBitstrings[] = str_pad(decbin($wordIdx), 11, '0', STR_PAD_LEFT);
        }

        // Return a string representing each bit
        return join('', $wordBitstrings);
    }

    /**
     * @param $word
     * @return int
     * @throws \InvalidArgumentException
     */
    protected function getWordIndex($word)
    {
        $index = 0;

        foreach ($this->words as $wordInList) {
            if ($wordInList === $word) return $index;

            $index++;
        }

        throw new \InvalidArgumentException(sprintf('Word "%s" not found in wordlist', $word));
    }

    /**
     * @param $wordlistPath
     * @return array
     */
    protected function loadWordlist($wordlistPath)
    {
        $this->words = [];
        if (!file_exists($wordlistPath)) throw new \InvalidArgumentException('Cannot load wordlist from "%s"', $wordlistPath);

        foreach (file($wordlistPath) as $word) {
            $this->words[] = trim($word);
        }

        return $this->words;
    }
}