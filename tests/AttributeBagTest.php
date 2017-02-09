<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class AttributeBagTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->bag = new AttributeBag();
        $this->faker = new DataFaker();
        $attribute = $this->faker->attribute();
        $this->attributeName = $attribute->getName();
        $this->value = $attribute->getValue();
        $attribute2 = $this->faker->attribute();
        $this->attributeName2 = $attribute2->getName();
        $this->value2 = $attribute2->getValue();
    }

    /**
     * @return void
     */
    public function testConstruct()
    {
        $bag = new AttributeBag(array(array(
            'name' => $this->attributeName,
            'value' => $this->value
        )));
        $this->assertCount(1, $bag);
    }

    /**
     * Make sure all construction syntaxes return the same bag.
     *
     * @return void
     */
    public function testConstructSyntax()
    {
        $bagFromArrays = new AttributeBag(array(
            array(
                'name' => $this->attributeName,
                'value' => $this->value
            ),
            array(
                'name' => $this->attributeName2,
                'value' => $this->value2
            )
        ));
        $bagFromObjects = new AttributeBag(array(
            new Attribute(array(
                'name' => $this->attributeName,
                'value' => $this->value
            )),
            new Attribute(array(
                'name' => $this->attributeName2,
                'value' => $this->value2
            )),
        ));
        $bagFromSimplifiedArray = new AttributeBag(array(
            $this->attributeName => $this->value,
            $this->attributeName2 => $this->value2
        ));

        $this->assertEquals($bagFromArrays, $bagFromObjects);
        $this->assertEquals($bagFromArrays, $bagFromSimplifiedArray);
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $attributes = array(new Attribute(), new Attribute());
        $bag = new AttributeBag($attributes);

        $this->assertSame($attributes, $bag->all());
    }

    /**
     * @return void
     */
    public function testReplace()
    {
        $attributes = array(new Attribute(), new Attribute());
        $this->bag->replace($attributes);

        $this->assertSame($attributes, $this->bag->all());
    }

    /**
     * @return void
     */
    public function testAddWithAttribute()
    {
        $attribute = new Attribute();
        $attribute->setName($this->attributeName);
        $this->bag->add($attribute);

        $contents = $this->bag->all();
        $this->assertSame($attribute, $contents[0]);
    }

    /**
     * @return void
     */
    public function testAddWithArray()
    {
        $attribute = array(
            'name' => $this->attributeName,
            'value' => $this->value
        );
        $this->bag->add($attribute);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $contents[0]);
        $this->assertSame($this->attributeName, $contents[0]->getName());
    }

    /**
     * @return void
     */
    public function testGetIterator()
    {
        $attribute = new Attribute();
        $attribute->setName($this->attributeName);
        $this->bag->add($attribute);

        foreach ($this->bag as $bagAttribute) {
            $this->assertSame($attribute, $bagAttribute);
        }
    }

    /**
     * @return void
     */
    public function testCount()
    {
        $count = $this->faker->intBetween(1, 5);
        for ($i = 0; $i < $count; $i++) {
            $this->bag->add(new Attribute());
        }
        $this->assertSame($count, count($this->bag));
    }
}
