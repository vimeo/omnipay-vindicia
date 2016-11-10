<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ChargebackTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->chargeback = new Chargeback();
        $this->id = $this->faker->chargebackId();
        $this->reference = $this->faker->chargebackReference();
    }

    public function testConstructWithParams()
    {
        $chargeback = new Chargeback(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $chargeback->getId());
        $this->assertSame($this->reference, $chargeback->getReference());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->chargeback, $this->chargeback->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->chargeback->getId());
        $this->assertSame($this->reference, $this->chargeback->getReference());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->chargeback->getParameters());
    }

    public function testId()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setId($this->id));
        $this->assertSame($this->id, $this->chargeback->getId());
    }

    public function testReference()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setReference($this->reference));
        $this->assertSame($this->reference, $this->chargeback->getReference());
    }

    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->chargeback, $this->chargeback->setCurrency($currency));
        $this->assertSame($currency, $this->chargeback->getCurrency());
    }

    public function testAmount()
    {
        $amount = $this->faker->monetaryAmount($this->faker->currency());
        $this->assertSame($this->chargeback, $this->chargeback->setAmount($amount));
        $this->assertSame($amount, $this->chargeback->getAmount());
    }

    public function testStatus()
    {
        $status = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 10));
        $this->assertSame($this->chargeback, $this->chargeback->setStatus($status));
        $this->assertSame($status, $this->chargeback->getStatus());
    }

    public function testStatusChangedTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->chargeback, $this->chargeback->setStatusChangedTime($time));
        $this->assertSame($time, $this->chargeback->getStatusChangedTime());
    }

    public function testProcessorReceivedTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->chargeback, $this->chargeback->setProcessorReceivedTime($time));
        $this->assertSame($time, $this->chargeback->getProcessorReceivedTime());
    }

    public function testTransaction()
    {
        $transaction = new Transaction();
        $this->assertSame($this->chargeback, $this->chargeback->setTransaction($transaction));
        $this->assertSame($transaction, $this->chargeback->getTransaction());
    }

    public function testTransactionId()
    {
        $transactionId = $this->faker->transactionId();
        $this->assertSame($this->chargeback, $this->chargeback->setTransactionId($transactionId));
        $this->assertSame($transactionId, $this->chargeback->getTransactionId());
    }

    public function testTransactionReference()
    {
        $transactionReference = $this->faker->transactionReference();
        $this->assertSame($this->chargeback, $this->chargeback->setTransactionReference($transactionReference));
        $this->assertSame($transactionReference, $this->chargeback->getTransactionReference());
    }
}
