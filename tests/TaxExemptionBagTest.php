<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class TaxExemptionBagTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->bag = new TaxExemptionBag();
        $this->faker = new DataFaker();
        $this->exemptionId = $this->faker->taxExemptionId();
    }

    /**
     * @return void
     */
    public function testConstruct()
    {
        $bag = new TaxExemptionBag(array(array('exemptionId' => $this->exemptionId)));
        $this->assertCount(1, $bag);
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $items = array(new TaxExemption(), new TaxExemption());
        $bag = new TaxExemptionBag($items);

        $this->assertSame($items, $bag->all());
    }

    /**
     * @return void
     */
    public function testReplace()
    {
        $items = array(new TaxExemption(), new TaxExemption());
        $this->bag->replace($items);

        $this->assertSame($items, $this->bag->all());
    }

    /**
     * @return void
     */
    public function testAddWithItem()
    {
        $item = new TaxExemption();
        $item->setExemptionId($this->exemptionId);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertSame($item, $contents[0]);
    }

    /**
     * @return void
     */
    public function testAddWithArray()
    {
        $item = array('exemptionId' => $this->exemptionId);
        $this->bag->add($item);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\TaxExemption', $contents[0]);
        $this->assertSame($this->exemptionId, $contents[0]->getExemptionId());
    }

    /**
     * @return void
     */
    public function testGetIterator()
    {
        $item = new TaxExemption();
        $item->setExemptionId($this->exemptionId);
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
            $this->bag->add(new TaxExemption());
        }
        $this->assertSame($count, count($this->bag));
    }
}
