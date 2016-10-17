<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\Price;

class PriceTest extends TestCase
{
    public function setUp()
    {
        $this->price = new Price();
        $this->faker = new DataFaker();
        $price = $this->faker->price();
        $this->currency = $price->getCurrency();
        $this->amount = $price->getAmount();
    }

    public function testConstructWithParams()
    {
        $price = new Price(array(
            'currency' => $this->currency,
            'amount' => $this->amount
        ));
        $this->assertSame($this->currency, $price->getCurrency());
        $this->assertSame($this->amount, $price->getAmount());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->price, $this->price->initialize(array(
            'currency' => $this->currency,
            'amount' => $this->amount
        )));
        $this->assertSame($this->currency, $this->price->getCurrency());
        $this->assertSame($this->amount, $this->price->getAmount());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->price, $this->price->setCurrency($this->currency)->setAmount($this->amount));
        $this->assertSame(array('currency' => $this->currency, 'amount' => $this->amount), $this->price->getParameters());
    }

    public function testCurrency()
    {
        $this->assertSame($this->price, $this->price->setCurrency($this->currency));
        $this->assertSame($this->currency, $this->price->getCurrency());
    }

    public function testAmount()
    {
        $this->assertSame($this->price, $this->price->setAmount($this->amount));
        $this->assertSame($this->amount, $this->price->getAmount());
    }
}
