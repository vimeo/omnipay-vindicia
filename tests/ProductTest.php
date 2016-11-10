<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ProductTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->product = new Product();
        $this->id = $this->faker->productId();
        $this->reference = $this->faker->productReference();
    }

    public function testConstructWithParams()
    {
        $product = new Product(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $product->getId());
        $this->assertSame($this->reference, $product->getReference());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->product, $this->product->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->product->getId());
        $this->assertSame($this->reference, $this->product->getReference());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->product, $this->product->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->product->getParameters());
    }

    public function testId()
    {
        $this->assertSame($this->product, $this->product->setId($this->id));
        $this->assertSame($this->id, $this->product->getId());
    }

    public function testReference()
    {
        $this->assertSame($this->product, $this->product->setReference($this->reference));
        $this->assertSame($this->reference, $this->product->getReference());
    }

    public function testPlan()
    {
        $plan = new Plan();
        $this->assertSame($this->product, $this->product->setPlan($plan));
        $this->assertSame($plan, $this->product->getPlan());
    }

    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->product, $this->product->setPlanId($planId));
        $this->assertSame($planId, $this->product->getPlanId());
    }

    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->product, $this->product->setPlanReference($planReference));
        $this->assertSame($planReference, $this->product->getPlanReference());
    }

    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($this->product, $this->product->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $this->product->getTaxClassification());
    }

    public function testPrices()
    {
        $prices = array($this->faker->price());
        $this->assertSame($this->product, $this->product->setPrices($prices));
        $this->assertSame($prices, $this->product->getPrices());
    }

    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->product, $this->product->setAttributes($attributes));
        $this->assertSame($attributes, $this->product->getAttributes());
    }
}
