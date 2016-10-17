<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\VindiciaItem;

class VindiciaItemTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->name = $this->faker->randomCharacters(
            DataFaker::ALPHABET_LOWER,
            $this->faker->intBetween(3, 15)
        );
        $this->currency = $this->faker->currency();
        $this->item = $this->faker->item($this->currency);
    }

    public function testConstructWithParams()
    {
        $item = new VindiciaItem(array('name' => $this->name));
        $this->assertSame($this->name, $item->getName());
    }

    public function testInitializeWithParams()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->initialize(array('name' => $this->name)));
        $this->assertSame($this->name, $item->getName());
    }

    public function testGetParameters()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setName($this->name));
        $this->assertSame(array('name' => $this->name), $item->getParameters());
    }

    public function testName()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setName($this->name));
        $this->assertSame($this->name, $item->getName());
    }

    public function testDescription()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setDescription($this->name));
        $this->assertSame($this->name, $item->getDescription());
    }

    public function testQuantity()
    {
        $item = new VindiciaItem();
        $quantity = $this->faker->intBetween(1, 15);
        $this->assertSame($item, $item->setQuantity($quantity));
        $this->assertSame($quantity, $item->getQuantity());
    }

    public function testPrice()
    {
        $item = new VindiciaItem();
        $price = $this->faker->monetaryAmount($this->currency);
        $this->assertSame($item, $item->setPrice($price));
        $this->assertSame($price, $item->getPrice());
    }

    public function testSku()
    {
        $item = new VindiciaItem();
        $sku = $this->faker->sku();
        $this->assertSame($item, $item->setSku($sku));
        $this->assertSame($sku, $item->getSku());
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing name.
     */
    public function testValidateNameRequired()
    {
        $this->item->setName(null);
        $this->item->validate();
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing price.
     */
    public function testValidatePriceRequired()
    {
        $this->item->setPrice(null);
        $this->item->validate();
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing quantity.
     */
    public function testValidateQuantityRequired()
    {
        $this->item->setQuantity(null);
        $this->item->validate();
    }

    /**
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing sku.
     */
    public function testValidateSkuRequired()
    {
        $this->item->setSku(null);
        $this->item->validate();
    }
}
