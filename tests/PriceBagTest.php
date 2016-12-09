<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;
use Omnipay\Common\Currency;

class PriceBagTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->bag = new PriceBag();
        $this->faker = new DataFaker();
        $this->price = $this->faker->price();
        $this->currency = $this->price->getCurrency();
        $this->amount = $this->price->getAmount();

        // make sure they have different currencies
        do {
            $this->price2 = $this->faker->price();
            $this->currency2 = $this->price2->getCurrency();
            $this->amount2 = $this->price2->getAmount();
        } while ($this->currency === $this->currency2);
    }

    /**
     * @return void
     */
    public function testConstruct()
    {
        $bag = new PriceBag(array(array(
            'currency' => $this->currency,
            'amount' => $this->amount
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
        $bagFromArrays = new PriceBag(array(
            array(
                'currency' => $this->currency,
                'amount' => $this->amount
            ),
            array(
                'currency' => $this->currency2,
                'amount' => $this->amount2
            )
        ));
        $bagFromObjects = new PriceBag(array(
            new Price(array(
                'currency' => $this->currency,
                'amount' => $this->amount
            )),
            new Price(array(
                'currency' => $this->currency2,
                'amount' => $this->amount2
            )),
        ));
        $bagFromSimplifiedArray = new PriceBag(array(
            $this->currency => $this->amount,
            $this->currency2 => $this->amount2
        ));

        $this->assertEquals($bagFromArrays, $bagFromObjects);
        $this->assertEquals($bagFromArrays, $bagFromSimplifiedArray);
    }

    /**
     * @return void
     */
    public function testAll()
    {
        $prices = array($this->price, $this->price2);
        $bag = new PriceBag($prices);

        $this->assertSame($prices, $bag->all());
    }

    /**
     * @return void
     */
    public function testReplace()
    {
        $prices = array($this->price, $this->price2);
        $this->bag->replace($prices);

        $this->assertSame($prices, $this->bag->all());
    }

    /**
     * @return void
     */
    public function testAddWithPrice()
    {
        $price = new Price();
        $price->setCurrency($this->currency);
        $this->bag->add($price);

        $contents = $this->bag->all();
        $this->assertSame($price, $contents[0]);
    }

    /**
     * @return void
     */
    public function testAddWithArray()
    {
        $price = array(
            'currency' => $this->currency,
            'amount' => $this->amount
        );
        $this->bag->add($price);

        $contents = $this->bag->all();
        $this->assertInstanceOf('\Omnipay\Vindicia\Price', $contents[0]);
        $this->assertSame($this->currency, $contents[0]->getCurrency());
    }

    /**
     * @return void
     */
    public function testGetIterator()
    {
        $price = new Price();
        $price->setCurrency($this->currency);
        $this->bag->add($price);

        foreach ($this->bag as $bagPrice) {
            $this->assertSame($price, $bagPrice);
        }
    }

    /**
     * @return void
     */
    public function testCount()
    {
        $currencies = array_keys(Currency::all());

        $count = $this->faker->intBetween(1, 5);
        for ($i = 0; $i < $count; $i++) {
            $currency = $currencies[$i];
            $this->bag->add(new Price(array(
                'currency' => $currency,
                'amount' => $this->faker->monetaryAmount($currency)
            )));
        }
        $this->assertSame($count, count($this->bag));
    }
}
