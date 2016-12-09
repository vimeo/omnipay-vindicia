<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class CustomerTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->customer = new Customer();
        $this->id = $this->faker->customerId();
        $this->reference = $this->faker->customerReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $customer = new Customer(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $customer->getId());
        $this->assertSame($this->reference, $customer->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->customer, $this->customer->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->customer->getId());
        $this->assertSame($this->reference, $this->customer->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->customer, $this->customer->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->customer->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->customer, $this->customer->setId($this->id));
        $this->assertSame($this->id, $this->customer->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->customer, $this->customer->setReference($this->reference));
        $this->assertSame($this->reference, $this->customer->getReference());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $name = $this->faker->name();
        $this->assertSame($this->customer, $this->customer->setName($name));
        $this->assertSame($name, $this->customer->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $email = $this->faker->email();
        $this->assertSame($this->customer, $this->customer->setEmail($email));
        $this->assertSame($email, $this->customer->getEmail());
    }

    /**
     * @return void
     */
    public function testTaxExemptions()
    {
        $taxExemptions = array($this->faker->taxExemption());
        $this->assertSame($this->customer, $this->customer->setTaxExemptions($taxExemptions));
        $this->assertSame($taxExemptions, $this->customer->getTaxExemptions());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->customer, $this->customer->setAttributes($attributes));
        $this->assertSame($attributes, $this->customer->getAttributes());
    }
}
