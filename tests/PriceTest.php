<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class PriceTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->price = new Price();
        $this->faker = new DataFaker();
        $price = $this->faker->price();
        $this->currency = $price->getCurrency();
        $this->amount = $price->getAmount();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $price = new Price(array(
            'currency' => $this->currency,
            'amount' => $this->amount
        ));
        $this->assertSame($this->currency, $price->getCurrency());
        $this->assertSame($this->amount, $price->getAmount());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->price, $this->price->initialize(array(
            'currency' => $this->currency,
            'amount' => $this->amount
        )));
        $this->assertSame($this->currency, $this->price->getCurrency());
        $this->assertSame($this->amount, $this->price->getAmount());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->price, $this->price->setCurrency($this->currency)->setAmount($this->amount));
        $this->assertSame(array('currency' => $this->currency, 'amount' => $this->amount), $this->price->getParameters());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $this->assertSame($this->price, $this->price->setCurrency($this->currency));
        $this->assertSame($this->currency, $this->price->getCurrency());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $this->assertSame($this->price, $this->price->setAmount($this->amount));
        $this->assertSame($this->amount, $this->price->getAmount());
    }
}
