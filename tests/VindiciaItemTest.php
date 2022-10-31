<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class VindiciaItemTest extends TestCase
{
    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $item = new VindiciaItem(array('name' => $this->name));
        $this->assertSame($this->name, $item->getName());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->initialize(array('name' => $this->name)));
        $this->assertSame($this->name, $item->getName());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setName($this->name));
        $this->assertSame(array('name' => $this->name), $item->getParameters());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setName($this->name));
        $this->assertSame($this->name, $item->getName());
    }

    /**
     * @return void
     */
    public function testDescription()
    {
        $item = new VindiciaItem();
        $this->assertSame($item, $item->setDescription($this->name));
        $this->assertSame($this->name, $item->getDescription());
    }

    /**
     * @return void
     */
    public function testQuantity()
    {
        $item = new VindiciaItem();
        $quantity = $this->faker->intBetween(1, 15);
        $this->assertSame($item, $item->setQuantity($quantity));
        $this->assertSame($quantity, $item->getQuantity());
    }

    /**
     * @return void
     */
    public function testPrice()
    {
        $item = new VindiciaItem();
        $price = $this->faker->monetaryAmount($this->currency);
        $this->assertSame($item, $item->setPrice($price));
        $this->assertSame($price, $item->getPrice());
    }

    /**
     * @return void
     */
    public function testSku()
    {
        $item = new VindiciaItem();
        $sku = $this->faker->sku();
        $this->assertSame($item, $item->setSku($sku));
        $this->assertSame($sku, $item->getSku());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $item = new VindiciaItem();
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($item, $item->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $item->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testAutoBillItemVid()
    {
        $item = new VindiciaItem();
        $autobillItemVid = $this->faker->autobillItemVid();
        $this->assertSame($item, $item->setAutoBillItemVid($autobillItemVid));
        $this->assertSame($autobillItemVid, $item->getAutoBillItemVid());
    }

    /**
     * @expectedException        \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing name.
     * @return                   void
     */
    public function testValidateNameRequired()
    {
        $this->item->setName(null);
        $this->item->validate();
    }

    /**
     * @expectedException        \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing price.
     * @return                   void
     */
    public function testValidatePriceRequired()
    {
        $this->item->setPrice(null);
        $this->item->validate();
    }

    /**
     * @expectedException        \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing quantity.
     * @return                   void
     */
    public function testValidateQuantityRequired()
    {
        $this->item->setQuantity(null);
        $this->item->validate();
    }

    /**
     * @expectedException        \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Item is missing sku.
     * @return                   void
     */
    public function testValidateSkuRequired()
    {
        $this->item->setSku(null);
        $this->item->validate();
    }
}
