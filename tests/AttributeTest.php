<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class AttributeTest extends TestCase
{
    public function setUp()
    {
        $this->attribute = new Attribute();
        $this->faker = new DataFaker();
        $attribute = $this->faker->attribute();
        $this->name = $attribute->getName();
        $this->value = $attribute->getValue();
    }

    public function testConstructWithParams()
    {
        $attribute = new Attribute(array(
            'name' => $this->name,
            'value' => $this->value
        ));
        $this->assertSame($this->name, $attribute->getName());
        $this->assertSame($this->value, $attribute->getValue());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->attribute, $this->attribute->initialize(array(
            'name' => $this->name,
            'value' => $this->value
        )));
        $this->assertSame($this->name, $this->attribute->getName());
        $this->assertSame($this->value, $this->attribute->getValue());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->attribute, $this->attribute->setName($this->name)->setValue($this->value));
        $this->assertSame(array('name' => $this->name, 'value' => $this->value), $this->attribute->getParameters());
    }

    public function testName()
    {
        $this->assertSame($this->attribute, $this->attribute->setName($this->name));
        $this->assertSame($this->name, $this->attribute->getName());
    }

    public function testValue()
    {
        $this->assertSame($this->attribute, $this->attribute->setValue($this->value));
        $this->assertSame($this->value, $this->attribute->getValue());
    }
}
