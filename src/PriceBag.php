<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\Exception\InvalidPriceBagException;

/**
 * Price Bag
 *
 * This class defines a bag (multi element set or array) of prices for a request.
 * Only one price is allowed for each currency.
 *
 * @see Price
 */
class PriceBag implements \IteratorAggregate, \Countable
{
    /**
     * Price storage
     *
     * @var array of Price
     */
    protected $prices;

    /**
     * Tracks which currencies are already in this bag so you can make sure you're
     * not adding a price for an existing currency in linear time
     *
     * @var array of string to bool
     */
    protected $currencies;

    /**
     * Constructor
     *
     * @param array $prices An array of prices
     */
    public function __construct(array $prices = array())
    {
        $this->replace($prices);
    }

    /**
     * Return all the prices
     *
     * @return array An array of prices
     */
    public function all()
    {
        return $this->prices;
    }

    /**
     * Replace the contents of this bag with the specified prices.
     * $prices can be an array of Prices, an array of
     * `['currency' => XXX, 'amount' => XXX]` pairs, or an array of amounts
     * indexed by currency.
     *
     * @param  array $prices An array of prices
     * @throws InvalidPriceBagException if multiple prices have the same currency
     * @return void
     */
    public function replace(array $prices = array())
    {
        $this->prices = array();
        $this->currencies = array();

        foreach ($prices as $key => $value) {
            if (is_array($value) || $value instanceof Price) {
                $this->add($value);
            } else {
                $this->add(array(
                    'currency' => $key,
                    'amount' => $value
                ));
            }
        }
    }

    /**
     * Add an price to the bag
     *
     * @param  Price|array $price An existing price, or associative array of price
     *                             parameters (`['currency' => XXX, 'amount' => XXX]`)
     * @throws InvalidPriceBagException if a price for this currency is already in the bag
     * @return void
     */
    public function add($price)
    {
        if (!$price instanceof Price) {
            $price = new Price($price);
        }

        $currency = $price->getCurrency();
        if (isset($this->currencies[$currency])) {
            throw new InvalidPriceBagException('This currency is already in the PriceBag.');
        }

        // mark this currency as in the price bag
        $this->currencies[$currency] = true;
        $this->prices[] = $price;
    }

    /**
     * Returns an iterator for prices
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->prices);
    }

    /**
     * Returns the number of prices
     *
     * @return int The number of prices
     */
    public function count()
    {
        return count($this->prices);
    }
}
