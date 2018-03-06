<?php

namespace ZuluCrypto\StellarSdk\Test\Unit\Transaction;


use phpseclib\Math\BigInteger;
use PHPUnit\Framework\TestCase;
use ZuluCrypto\StellarSdk\Keypair;
use ZuluCrypto\StellarSdk\Transaction\Transaction;
use ZuluCrypto\StellarSdk\Transaction\TransactionBuilder;
use ZuluCrypto\StellarSdk\Util\Debug;
use ZuluCrypto\StellarSdk\Xdr\XdrBuffer;
use ZuluCrypto\StellarSdk\XdrModel\AccountId;
use ZuluCrypto\StellarSdk\XdrModel\Operation\CreateAccountOp;

class TransactionTest extends TestCase
{
    public function testFromXdr()
    {
        $sourceKeypair = Keypair::newFromRandom();

        // Build transaction
        $sourceModel = new TransactionBuilder($sourceKeypair);
        $sourceModel->setSequenceNumber(new BigInteger(123));

        $createAccountOp = new CreateAccountOp(new AccountId(Keypair::newFromRandom()), 100);
        $sourceModel->addOperation($createAccountOp);

        $sourceModel->setLowerTimebound(new \DateTime('2018-01-01 00:00:00'));
        $sourceModel->setUpperTimebound(new \DateTime('2018-12-31 00:00:00'));
        $sourceModel->setTextMemo('test memo');

        // Encode and then parse the resulting XDR
        $parsed = Transaction::fromXdr(new XdrBuffer($sourceModel->toXdr()));
        $parsedOps = $parsed->getOperations();

        $this->assertCount(1, $parsedOps);

        $this->assertEquals($sourceKeypair->getAccountId(), $parsed->getSourceAccountId()->getAccountIdString());
        $this->assertEquals('test memo', $parsed->getMemo()->getValue());
    }
}