<?php

namespace Omnipay\VindiciaTest;

use Omnipay\Tests\TestCase;
use Omnipay\VindiciaTest\Mocker;
use Omnipay\Vindicia\Attribute;

class VindiciaCreditCardTest extends TestCase
{
    public function setUp()
    {
        $this->card = Mocker::mock('\Omnipay\Vindicia\VindiciaCreditCard')->makePartial();
        $this->card->initialize();
        $this->faker = new DataFaker();
    }

    public function testAttributesAsBag()
    {
        // $attributes is an AttributeBag
        $attributes = $this->faker->attributes();
        $this->assertSame($this->card, $this->card->setAttributes($attributes));
        $this->assertSame($attributes, $this->card->getAttributes());
    }

    public function testAttributesAsArray()
    {
        // $attributes is an array
        $attributes = $this->faker->attributesAsArray();
        $this->assertSame($this->card, $this->card->setAttributes($attributes));

        $returnedAttributes = $this->card->getAttributes();
        $this->assertInstanceOf('Omnipay\Vindicia\AttributeBag', $returnedAttributes);

        $numAttributes = count($attributes);
        $this->assertSame($numAttributes, $returnedAttributes->count());

        foreach ($returnedAttributes as $i => $returnedAttribute) {
            $this->assertEquals(new Attribute($attributes[$i]), $returnedAttribute);
        }
    }
}
