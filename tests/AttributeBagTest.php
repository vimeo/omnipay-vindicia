<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\AttributeBag;
use Omnipay\Vindicia\Attribute;

class AttributeBagTest extends TestCase
{
    public function setUp()
    {
        $this->bag = new AttributeBag();
        $this->faker = new DataFaker();
        $attribute = $this->faker->attribute();
        $this->name = $attribute->getName();
        $this->value = $attribute->getValue();
        $attribute2 = $this->faker->attribute();
        $this->name2 = $attribute2->getName();
        $this->value2 = $attribute2->getValue();
    }

    public function testConstruct()
    {
        $bag = new AttributeBag(array(array(
            'name' => $this->name,
            'value' => $this->value
        )));
        $this->assertCount(1, $bag);
    }

    /**
     * Make sure all construction syntaxes return the same bag.
     */
    public function testConstructSyntax()
    {
        $bagFromArrays = new AttributeBag(array(
            array(
                'name' => $this->name,
                'value' => $this->value
            ),
            array(
                'name' => $this->name2,
                'value' => $this->value2
            )
        ));
        $bagFromObjects = new AttributeBag(array(
            new Attribute(array(
                'name' => $this->name,
                'value' => $this->value
            )),
            new Attribute(array(
                'name' => $this->name2,
                'value' => $this->value2
            )),
        ));
        $bagFromSimplifiedArray = new AttributeBag(array(
            $this->name => $this->value,
            $this->name2 => $this->value2
        ));

        $this->assertEquals($bagFromArrays, $bagFromObjects);
        $this->assertEquals($bagFromArrays, $bagFromSimplifiedArray);
    }

    public function testAll()
    {
        $attributes = array(new Attribute(), new Attribute());
        $bag = new AttributeBag($attributes);

        $this->assertSame($attributes, $bag->all());
    }

    public function testReplace()
    {
        $attributes = array(new Attribute(), new Attribute());
        $this->bag->replace($attributes);

        $this->assertSame($attributes, $this->bag->all());
    }

    public function testAddWithAttribute()
    {
        $attribute = new Attribute();
        $attribute->setName($this->name);
        $this->bag->add($attribute);

        $contents = $this->bag->all();
        $this->assertSame($attribute, $contents[0]);
    }

    public function testAddWithArray()
    {
        $attribute = array(
            'name' => $this->name,
            'value' => $this->value
        );
        $this->bag->add($attribute);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $contents[0]);
        $this->assertSame($this->name, $contents[0]->getName());
    }

    public function testGetIterator()
    {
        $attribute = new Attribute();
        $attribute->setName($this->name);
        $this->bag->add($attribute);

        foreach ($this->bag as $bagAttribute) {
            $this->assertSame($attribute, $bagAttribute);
        }
    }

    public function testCount()
    {
        $count = $this->faker->intBetween(1, 5);
        for ($i = 0; $i < $count; $i++) {
            $this->bag->add(new Attribute());
        }
        $this->assertSame($count, count($this->bag));
    }
}
