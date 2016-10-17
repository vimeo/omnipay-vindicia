<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Vindicia\PriceBag;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Trait that defines behavior for a request that, instead of having just one
 * currency and price, can have a list of currencies and their prices. For example,
 * a product or plan can specify prices in any currency.
 */
trait HasPrices
{
    abstract function getParameter($param);

    // all requests that implement HasPrices should also support getAmount and getCurrency
    // for ease of use on single currency applications
    abstract function getAmount();
    abstract function getCurrency();

    /**
     * A list of prices (currency and amount)
     *
     * @return PriceBag|null
     */
    public function getPrices()
    {
        return $this->getParameter('prices');
    }

    /**
     * Set the prices (currency and amount)
     * If you only need a price for one currency, you can also use setAmount and setCurrency.
     *
     * @param PriceBag|array $prices
     * @return AbstractRequest
     * @throws InvalidPriceBagException if multiple prices have the same currency
     */
    public function setPrices($prices)
    {
        if ($prices && !$prices instanceof PriceBag) {
            $prices = new PriceBag($prices);
        }

        return $this->setParameter('prices', $prices);
    }

    /**
     * Builds the value for Vindicia's prices field
     *
     * @return array of stdClass
     */
    protected function makePricesForVindicia()
    {
        $prices = $this->getPrices();
        $amount = $this->getAmount();
        $currency = $this->getCurrency();
        if (!empty($prices) && (isset($amount) || isset($currency))) {
            throw new InvalidRequestException(
                'The amount and currency parameters cannot be set if the prices parameter is set.'
            );
        }

        $vindiciaPrices = array();
        if (!empty($prices)) {
            foreach ($prices as $price) {
                $vindiciaPrice = new stdClass();
                $vindiciaPrice->amount = $price->getAmount();
                $vindiciaPrice->currency = $price->getCurrency();
                $vindiciaPrices[] = $vindiciaPrice;
            }
        } else {
            if (isset($amount)) {
                $price = new stdClass();
                $price->amount = $amount;
                $price->currency = $currency;
                $vindiciaPrices[] = $price;
            }
        }

        return $vindiciaPrices;
    }
}
