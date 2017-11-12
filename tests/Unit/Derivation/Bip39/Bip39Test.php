<?php


namespace ZuluCrypto\StellarSdk\Test\Unit\Derivation\Bip39;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Derivation\Bip39\Bip39;

/**
 * Test vectors:
 *
 * https://raw.githubusercontent.com/trezor/python-mnemonic/master/vectors.json
 *  Array is in the format:
 *      entropy
 *      mnemonic
 *      seed bytes (hex encoded)
 *
 *
 * todo: add more test vectors
 */
class Bip39Test extends TestCase
{
    /**
     * @dataProvider mnemonicToEntropyProvider
     */
    public function testMnemonicToEntropy($mnemonic, $entropyHex, $seedHex)
    {
        $bip39 = new Bip39();

        $this->assertEquals(
            bin2hex($bip39->mnemonicToEntropy($mnemonic)),
            $entropyHex
        );

        $this->assertEquals(
            bin2hex($bip39->mnemonicToSeedBytesWithErrorChecking($mnemonic, 'TREZOR')),
            $seedHex
        );
    }
    public function mnemonicToEntropyProvider()
    {
        return [
            [
                'mnemonic'   => 'abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon abandon about',
                'entropyHex' => '00000000000000000000000000000000',
                'seedHex'    => 'c55257c360c07c72029aebc1b53c05ed0362ada38ead3e3e9efa3708e53495531f09a6987599d18264c1e1c92f2cf141630c7a3c4ab7c81b2f001698e7463b04',
            ],
            [
                'mnemonic'   => 'legal winner thank year wave sausage worth useful legal winner thank yellow',
                'entropyHex' => '7f7f7f7f7f7f7f7f7f7f7f7f7f7f7f7f',
                'seedHex'    => '2e8905819b8723fe2c1d161860e5ee1830318dbf49a83bd451cfb8440c28bd6fa457fe1296106559a3c80937a1c1069be3a3a5bd381ee6260e8d9739fce1f607',
            ],
            [
                'mnemonic'   => 'legal winner thank year wave sausage worth useful legal winner thank yellow',
                'entropyHex' => '7f7f7f7f7f7f7f7f7f7f7f7f7f7f7f7f',
                'seedHex'    => '2e8905819b8723fe2c1d161860e5ee1830318dbf49a83bd451cfb8440c28bd6fa457fe1296106559a3c80937a1c1069be3a3a5bd381ee6260e8d9739fce1f607',
            ],
            [
                'mnemonic'   => 'letter advice cage absurd amount doctor acoustic avoid letter advice cage above',
                'entropyHex' => '80808080808080808080808080808080',
                'seedHex'    => 'd71de856f81a8acc65e6fc851a38d4d7ec216fd0796d0a6827a3ad6ed5511a30fa280f12eb2e47ed2ac03b5c462a0358d18d69fe4f985ec81778c1b370b652a8',
            ],

            [
                'mnemonic'   => 'all hour make first leader extend hole alien behind guard gospel lava path output census museum junior mass reopen famous sing advance salt reform',
                'entropyHex' => '066dca1a2bb7e8a1db2832148ce9933eea0f3ac9548d793112d9a95c9407efad',
                'seedHex'    => '26e975ec644423f4a4c4f4215ef09b4bd7ef924e85d1d17c4cf3f136c2863cf6df0a475045652c57eb5fb41513ca2a2d67722b77e954b4b3fc11f7590449191d',
            ],
        ];
    }
}