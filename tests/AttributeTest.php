<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class AttributeTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->attribute = new Attribute();
        $this->faker = new DataFaker();
        $attribute = $this->faker->attribute();
        $this->name = $attribute->getName();
        $this->value = $attribute->getValue();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $attribute = new Attribute(array(
            'name' => $this->name,
            'value' => $this->value
        ));
        $this->assertSame($this->name, $attribute->getName());
        $this->assertSame($this->value, $attribute->getValue());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->attribute, $this->attribute->initialize(array(
            'name' => $this->name,
            'value' => $this->value
        )));
        $this->assertSame($this->name, $this->attribute->getName());
        $this->assertSame($this->value, $this->attribute->getValue());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->attribute, $this->attribute->setName($this->name)->setValue($this->value));
        $this->assertSame(array('name' => $this->name, 'value' => $this->value), $this->attribute->getParameters());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $this->assertSame($this->attribute, $this->attribute->setName($this->name));
        $this->assertSame($this->name, $this->attribute->getName());
    }

    /**
     * @return void
     */
    public function testValue()
    {
        $this->assertSame($this->attribute, $this->attribute->setValue($this->value));
        $this->assertSame($this->value, $this->attribute->getValue());
    }
}
