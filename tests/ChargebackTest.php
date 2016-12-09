<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ChargebackTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->chargeback = new Chargeback();
        $this->id = $this->faker->chargebackId();
        $this->reference = $this->faker->chargebackReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $chargeback = new Chargeback(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $chargeback->getId());
        $this->assertSame($this->reference, $chargeback->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->chargeback, $this->chargeback->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->chargeback->getId());
        $this->assertSame($this->reference, $this->chargeback->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->chargeback->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setId($this->id));
        $this->assertSame($this->id, $this->chargeback->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->chargeback, $this->chargeback->setReference($this->reference));
        $this->assertSame($this->reference, $this->chargeback->getReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->chargeback, $this->chargeback->setCurrency($currency));
        $this->assertSame($currency, $this->chargeback->getCurrency());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $amount = $this->faker->monetaryAmount($this->faker->currency());
        $this->assertSame($this->chargeback, $this->chargeback->setAmount($amount));
        $this->assertSame($amount, $this->chargeback->getAmount());
    }

    /**
     * @return void
     */
    public function testStatus()
    {
        $status = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 10));
        $this->assertSame($this->chargeback, $this->chargeback->setStatus($status));
        $this->assertSame($status, $this->chargeback->getStatus());
    }

    /**
     * @return void
     */
    public function testStatusChangedTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->chargeback, $this->chargeback->setStatusChangedTime($time));
        $this->assertSame($time, $this->chargeback->getStatusChangedTime());
    }

    /**
     * @return void
     */
    public function testProcessorReceivedTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->chargeback, $this->chargeback->setProcessorReceivedTime($time));
        $this->assertSame($time, $this->chargeback->getProcessorReceivedTime());
    }

    /**
     * @return void
     */
    public function testTransaction()
    {
        $transaction = new Transaction();
        $this->assertSame($this->chargeback, $this->chargeback->setTransaction($transaction));
        $this->assertSame($transaction, $this->chargeback->getTransaction());
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $transactionId = $this->faker->transactionId();
        $this->assertSame($this->chargeback, $this->chargeback->setTransactionId($transactionId));
        $this->assertSame($transactionId, $this->chargeback->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $transactionReference = $this->faker->transactionReference();
        $this->assertSame($this->chargeback, $this->chargeback->setTransactionReference($transactionReference));
        $this->assertSame($transactionReference, $this->chargeback->getTransactionReference());
    }
}
