<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->product = new Product();
        $this->productId = $this->faker->productId();
        $this->productReference = $this->faker->productReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $product = new Product(array(
            'productId' => $this->productId,
            'productReference' => $this->productReference
        ));
        $this->assertSame($this->productId, $product->getProductId());
        $this->assertSame($this->productReference, $product->getProductReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->product, $this->product->initialize(array(
            'productId' => $this->productId,
            'productReference' => $this->productReference
        )));
        $this->assertSame($this->productId, $this->product->getProductId());
        $this->assertSame($this->productReference, $this->product->getProductReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->product, $this->product->setProductId($this->productId)->setProductReference($this->productReference));
        $this->assertSame(array('productId' => $this->productId, 'productReference' => $this->productReference), $this->product->getParameters());
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $this->assertSame($this->product, $this->product->setProductId($this->productId));
        $this->assertSame($this->productId, $this->product->getProductId());
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $this->assertSame($this->product, $this->product->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $this->product->getProductReference());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testId()
    {
        $this->assertSame($this->product, $this->product->setId($this->productId));
        $this->assertSame($this->productId, $this->product->getId());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testReference()
    {
        $this->assertSame($this->product, $this->product->setReference($this->productReference));
        $this->assertSame($this->productReference, $this->product->getReference());
    }

    /**
     * @return void
     */
    public function testPlan()
    {
        $plan = new Plan();
        $this->assertSame($this->product, $this->product->setPlan($plan));
        $this->assertSame($plan, $this->product->getPlan());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->product, $this->product->setPlanId($planId));
        $this->assertSame($planId, $this->product->getPlanId());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->product, $this->product->setPlanReference($planReference));
        $this->assertSame($planReference, $this->product->getPlanReference());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($this->product, $this->product->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $this->product->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testPrices()
    {
        $prices = array($this->faker->price());
        $this->assertSame($this->product, $this->product->setPrices($prices));
        $this->assertSame($prices, $this->product->getPrices());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->product, $this->product->setAttributes($attributes));
        $this->assertSame($attributes, $this->product->getAttributes());
    }
}
