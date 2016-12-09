<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class VindiciaItemBagTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->bag = new VindiciaItemBag();
        $this->faker = new DataFaker();
        $this->name = $this->faker->randomCharacters(
            DataFaker::ALPHABET_LOWER,
            $this->faker->intBetween(3, 15)
        );
    }

    /**
     * @return void
     */
    public function testConstruct()
    {
        $bag = new VindiciaItemBag(array(array('name' => $this->name)));
        $this->assertCount(1, $bag);
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $items = array(new VindiciaItem(), new VindiciaItem());
        $bag = new VindiciaItemBag($items);

        $this->assertSame($items, $bag->all());
    }

    /**
     * @return void
     */
    public function testReplace()
    {
        $items = array(new VindiciaItem(), new VindiciaItem());
        $this->bag->replace($items);

        $this->assertSame($items, $this->bag->all());
    }

    /**
     * @return void
     */
    public function testAddWithItem()
    {
        $item = new VindiciaItem();
        $item->setName($this->name);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertSame($item, $contents[0]);
    }

    /**
     * @return void
     */
    public function testAddWithArray()
    {
        $item = array('name' => $this->name);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\VindiciaItem', $contents[0]);
        $this->assertSame($this->name, $contents[0]->getName());
    }

    /**
     * @return void
     */
    public function testGetIterator()
    {
        $item = new VindiciaItem();
        $item->setName($this->name);
        $this->bag->add($item);

        foreach ($this->bag as $bagItem) {
            $this->assertSame($item, $bagItem);
        }
    }

    /**
     * @return void
     */
    public function testCount()
    {
        $count = $this->faker->intBetween(1, 5);
        for ($i = 0; $i < $count; $i++) {
            $this->bag->add(new VindiciaItem());
        }
        $this->assertSame($count, count($this->bag));
    }
}
