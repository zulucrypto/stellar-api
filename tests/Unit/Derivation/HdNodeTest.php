<?php

namespace ZuluCrypto\StellarSdk\Test\Unit\Slip0010;


use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Derivation\HdNode;

class HdNodeTest extends TestCase
{
    public function testNewMasterNode()
    {
        // https://github.com/satoshilabs/slips/blob/master/slip-0010.md#test-vector-1-for-ed25519
        $entropy = hex2bin('000102030405060708090a0b0c0d0e0f');
        $node = HdNode::newMasterNode($entropy);

        $this->assertEquals(
            '2b4be7f19ee27bbf30c667b642d5f4aa69fd169872f8fc3059c08ebae2eb19e7',
            bin2hex($node->getPrivateKeyBytes()));

        $this->assertEquals(
            '90046a93de5380a72b5e45010748567d5ea02bbf6522f979e05c0d8d8ca9fffb',
            bin2hex($node->getChainCodeBytes()));
    }

    public function testMasterNodeDerives()
    {
        $entropy = hex2bin('000102030405060708090a0b0c0d0e0f');
        $node = HdNode::newMasterNode($entropy);

        // m/0'
        $derived = $node->derive(0);
        $this->assertEquals(
            '68e0fe46dfb67e368c75379acec591dad19df3cde26e63b93a8e704f1dade7a3',
            bin2hex($derived->getPrivateKeyBytes()));
        $this->assertEquals(
            '8b59aa11380b624e81507a27fedda59fea6d0b779a778918a2fd3590e16e9c69',
            bin2hex($derived->getChainCodeBytes()));

        // m/0'/1'
        $derived = $derived->derive(1);
        $this->assertEquals(
            'b1d0bad404bf35da785a64ca1ac54b2617211d2777696fbffaf208f746ae84f2',
            bin2hex($derived->getPrivateKeyBytes()));
        $this->assertEquals(
            'a320425f77d1b5c2505a6b1b27382b37368ee640e3557c315416801243552f14',
            bin2hex($derived->getChainCodeBytes()));;
    }

    public function testMasterNodeDerivesPath()
    {
        $entropy = hex2bin('000102030405060708090a0b0c0d0e0f');
        $node = HdNode::newMasterNode($entropy);

        $derived = $node->derivePath("m/0'/1'/2'");
        $this->assertEquals(
            '92a5b23c0b8a99e37d07df3fb9966917f5d06e02ddbd909c7e184371463e9fc9',
            bin2hex($derived->getPrivateKeyBytes()));
        $this->assertEquals(
            '2e69929e00b5ab250f49c3fb1c12f252de4fed2c1db88387094a0f8c4c9ccd6c',
            bin2hex($derived->getChainCodeBytes()));
    }

    private function debugNode(HdNode $node)
    {
        print "\n";
        print "private  : " . bin2hex($node->getPrivateKeyBytes()) . "\n";
        print "chaincode: " . bin2hex($node->getChainCodeBytes()) . "\n";
    }
}