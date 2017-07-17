<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class RefundTest extends TestCase
{
    /** @var Refund */
    protected $refund;

    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->refund = new Refund();
        $this->refundId = $this->faker->refundId();
        $this->refundReference = $this->faker->refundReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $refund = new Refund(array(
            'refundId' => $this->refundId,
            'refundReference' => $this->refundReference
        ));
        $this->assertSame($this->refundId, $refund->getRefundId());
        $this->assertSame($this->refundReference, $refund->getRefundReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->refund, $this->refund->initialize(array(
            'refundId' => $this->refundId,
            'refundReference' => $this->refundReference
        )));
        $this->assertSame($this->refundId, $this->refund->getRefundId());
        $this->assertSame($this->refundReference, $this->refund->getRefundReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->refund, $this->refund->setRefundId($this->refundId)->setRefundReference($this->refundReference));
        $this->assertSame(array('refundId' => $this->refundId, 'refundReference' => $this->refundReference), $this->refund->getParameters());
    }

    /**
     * @return void
     */
    public function testRefundId()
    {
        $this->assertSame($this->refund, $this->refund->setRefundId($this->refundId));
        $this->assertSame($this->refundId, $this->refund->getRefundId());
    }

    /**
     * @return void
     */
    public function testRefundReference()
    {
        $this->assertSame($this->refund, $this->refund->setRefundReference($this->refundReference));
        $this->assertSame($this->refundReference, $this->refund->getRefundReference());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testId()
    {
        $this->assertSame($this->refund, $this->refund->setId($this->refundId));
        $this->assertSame($this->refundId, $this->refund->getId());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testReference()
    {
        $this->assertSame($this->refund, $this->refund->setReference($this->refundReference));
        $this->assertSame($this->refundReference, $this->refund->getReference());
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
    public function testReason()
    {
        $reason = $this->faker->refundReason();
        $this->assertSame($this->refund, $this->refund->setReason($reason));
        $this->assertSame($reason, $this->refund->getReason());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we're testing that it still works
     */
    public function testNote()
    {
        $reason = $this->faker->refundReason();
        $this->assertSame($this->refund, $this->refund->setNote($reason));
        $this->assertSame($reason, $this->refund->getNote());
        $this->assertSame($reason, $this->refund->getReason());
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
