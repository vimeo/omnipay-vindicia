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
        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $plan = new Plan(array(
            'planId' => $this->planId,
            'planReference' => $this->planReference
        ));
        $this->assertSame($this->planId, $plan->getPlanId());
        $this->assertSame($this->planReference, $plan->getPlanReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->plan, $this->plan->initialize(array(
            'planId' => $this->planId,
            'planReference' => $this->planReference
        )));
        $this->assertSame($this->planId, $this->plan->getPlanId());
        $this->assertSame($this->planReference, $this->plan->getPlanReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->plan, $this->plan->setPlanId($this->planId)->setPlanReference($this->planReference));
        $this->assertSame(array('planId' => $this->planId, 'planReference' => $this->planReference), $this->plan->getParameters());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $this->assertSame($this->plan, $this->plan->setPlanId($this->planId));
        $this->assertSame($this->planId, $this->plan->getPlanId());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $this->assertSame($this->plan, $this->plan->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $this->plan->getPlanReference());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->plan, $this->plan->setId($this->planId));
        $this->assertSame($this->planId, $this->plan->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->plan, $this->plan->setReference($this->planReference));
        $this->assertSame($this->planReference, $this->plan->getReference());
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
