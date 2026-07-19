<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class VindiciaRefundItemBagTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->bag = new VindiciaRefundItemBag();
        $this->faker = new DataFaker();
        $this->sku = $this->faker->sku();
    }

    /**
     * @return void
     */
    public function testConstruct()
    {
        $bag = new VindiciaRefundItemBag(array(array('sku' => $this->sku)));
        $this->assertCount(1, $bag);
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $items = array(new VindiciaRefundItem(), new VindiciaRefundItem());
        $bag = new VindiciaRefundItemBag($items);

        $this->assertSame($items, $bag->all());
    }

    /**
     * @return void
     */
    public function testReplace()
    {
        $items = array(new VindiciaRefundItem(), new VindiciaRefundItem());
        $this->bag->replace($items);

        $this->assertSame($items, $this->bag->all());
    }

    /**
     * @return void
     */
    public function testAddWithItem()
    {
        $item = new VindiciaRefundItem();
        $item->setSku($this->sku);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertSame($item, $contents[0]);
    }

    /**
     * @return void
     */
    public function testAddWithArray()
    {
        $item = array('sku' => $this->sku);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\VindiciaRefundItem', $contents[0]);
        $this->assertSame($this->sku, $contents[0]->getSku());
    }

    /**
     * @return void
     */
    public function testGetIterator()
    {
        $item = new VindiciaRefundItem();
        $item->setSku($this->sku);
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
            $this->bag->add(new VindiciaRefundItem());
        }
        $this->assertSame($count, count($this->bag));
    }
}
