<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class PlanTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->plan = new Plan();
        $this->id = $this->faker->planId();
        $this->reference = $this->faker->planReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $plan = new Plan(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $plan->getId());
        $this->assertSame($this->reference, $plan->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->plan, $this->plan->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->plan->getId());
        $this->assertSame($this->reference, $this->plan->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->plan, $this->plan->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->plan->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->plan, $this->plan->setId($this->id));
        $this->assertSame($this->id, $this->plan->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->plan, $this->plan->setReference($this->reference));
        $this->assertSame($this->reference, $this->plan->getReference());
    }

    /**
     * @return void
     */
    public function testInterval()
    {
        $interval = $this->faker->billingInterval();
        $this->assertSame($this->plan, $this->plan->setInterval($interval));
        $this->assertSame($interval, $this->plan->getInterval());
    }

    /**
     * @return void
     */
    public function testIntervalCount()
    {
        $intervalCount = $this->faker->billingIntervalCount();
        $this->assertSame($this->plan, $this->plan->setIntervalCount($intervalCount));
        $this->assertSame($intervalCount, $this->plan->getIntervalCount());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($this->plan, $this->plan->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $this->plan->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testPrices()
    {
        $prices = array($this->faker->price());
        $this->assertSame($this->plan, $this->plan->setPrices($prices));
        $this->assertSame($prices, $this->plan->getPrices());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->plan, $this->plan->setAttributes($attributes));
        $this->assertSame($attributes, $this->plan->getAttributes());
    }
}
