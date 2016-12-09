<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class RefundTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->refund = new Refund();
        $this->id = $this->faker->refundId();
        $this->reference = $this->faker->refundReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $refund = new Refund(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $refund->getId());
        $this->assertSame($this->reference, $refund->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->refund, $this->refund->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->refund->getId());
        $this->assertSame($this->reference, $this->refund->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->refund, $this->refund->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->refund->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->refund, $this->refund->setId($this->id));
        $this->assertSame($this->id, $this->refund->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->refund, $this->refund->setReference($this->reference));
        $this->assertSame($this->reference, $this->refund->getReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->refund, $this->refund->setCurrency($currency));
        $this->assertSame($currency, $this->refund->getCurrency());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $amount = $this->faker->monetaryAmount($this->faker->currency());
        $this->assertSame($this->refund, $this->refund->setAmount($amount));
        $this->assertSame($amount, $this->refund->getAmount());
    }

    /**
     * @return void
     */
    public function testNote()
    {
        $note = $this->faker->note();
        $this->assertSame($this->refund, $this->refund->setNote($note));
        $this->assertSame($note, $this->refund->getNote());
    }

    /**
     * @return void
     */
    public function testTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->refund, $this->refund->setTime($time));
        $this->assertSame($time, $this->refund->getTime());
    }

    /**
     * @return void
     */
    public function testTransaction()
    {
        $transaction = new Transaction();
        $this->assertSame($this->refund, $this->refund->setTransaction($transaction));
        $this->assertSame($transaction, $this->refund->getTransaction());
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $transactionId = $this->faker->transactionId();
        $this->assertSame($this->refund, $this->refund->setTransactionId($transactionId));
        $this->assertSame($transactionId, $this->refund->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $transactionReference = $this->faker->transactionReference();
        $this->assertSame($this->refund, $this->refund->setTransactionReference($transactionReference));
        $this->assertSame($transactionReference, $this->refund->getTransactionReference());
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $items = array($this->faker->refundItem($this->faker->currency()));
        $this->assertSame($this->refund, $this->refund->setItems($items));
        $this->assertSame($items, $this->refund->getItems());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->refund, $this->refund->setAttributes($attributes));
        $this->assertSame($attributes, $this->refund->getAttributes());
    }
}
