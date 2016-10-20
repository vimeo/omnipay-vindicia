<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;
use stdClass;

class NameValueTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->faker = new DataFaker();
    }

    public function testVariables()
    {
        $name = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(1, 10));
        $value = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER . DataFaker::DIGITS, $this->faker->intBetween(1, 10));
        $nameValue = new NameValue($name, $value);

        $this->assertSame($name, $nameValue->name);
        $this->assertSame($value, $nameValue->value);
    }

    public function testValueConversion()
    {
        $nameValue = new NameValue('name', null);
        $this->assertSame('null', $nameValue->value);

        $nameValue = new NameValue('name', true);
        $this->assertSame('true', $nameValue->value);

        $nameValue = new NameValue('name', false);
        $this->assertSame('false', $nameValue->value);

        $integer = $this->faker->intBetween(0, 999);
        $nameValue = new NameValue('name', $integer);
        $this->assertSame(strval($integer), $nameValue->value);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testNameMustNotBeInt()
    {
        $nameValue = new NameValue($this->faker->intBetween(0, 999), 'value');
    }

    /**
     * @expectedException InvalidArgumentException
     * @psalm-suppress InvalidScalarArgument because we're testing to ensure that a bool can't be passed
     */
    public function testNameMustNotBeBool()
    {
        $nameValue = new NameValue((bool) $this->faker->intBetween(0, 1), 'value');
    }

    /**
     * @expectedException InvalidArgumentException
     * @psalm-suppress NullReference because we're testing to ensure that null can't be passed
     */
    public function testNameMustNotBeNull()
    {
        $nameValue = new NameValue(null, 'value');
    }

    /**
     * @expectedException InvalidArgumentException
     * @psalm-suppress InvalidArgument because we're testing to ensure that an array can't be passed
     */
    public function testValueMustNotBeArray()
    {
        $nameValue = new NameValue('name', array());
    }

    /**
     * @expectedException InvalidArgumentException
     * @psalm-suppress InvalidArgument because we're testing to ensure that an object can't be passed
     */
    public function testValueMustNotBeObject()
    {
        $nameValue = new NameValue('name', new stdClass());
    }
}
