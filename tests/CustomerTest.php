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
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $customer = new Customer(array(
            'customerId' => $this->customerId,
            'customerReference' => $this->customerReference
        ));
        $this->assertSame($this->customerId, $customer->getCustomerId());
        $this->assertSame($this->customerReference, $customer->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->customer, $this->customer->initialize(array(
            'customerId' => $this->customerId,
            'customerReference' => $this->customerReference
        )));
        $this->assertSame($this->customerId, $this->customer->getCustomerId());
        $this->assertSame($this->customerReference, $this->customer->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->customer, $this->customer->setCustomerId($this->customerId)->setCustomerReference($this->customerReference));
        $this->assertSame(array('customerId' => $this->customerId, 'customerReference' => $this->customerReference), $this->customer->getParameters());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $this->assertSame($this->customer, $this->customer->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $this->customer->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $this->assertSame($this->customer, $this->customer->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $this->customer->getCustomerReference());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testId()
    {
        $this->assertSame($this->customer, $this->customer->setId($this->customerId));
        $this->assertSame($this->customerId, $this->customer->getId());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testReference()
    {
        $this->assertSame($this->customer, $this->customer->setReference($this->customerReference));
        $this->assertSame($this->customerReference, $this->customer->getReference());
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
