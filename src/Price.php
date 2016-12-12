<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Price
 *
 * Definites a price--a combination of a currency and monetary amount
 * Omnipay 3 will use moneyphp so this might not be necessary anymore
 */
class Price
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new price with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this price with the specified parameters
     *
     * @param array $parameters An array of parameters to set on this object
     * @return static
     */
    public function initialize($parameters = array())
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * @return mixed
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * @return Price
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the currency
     *
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    /**
     * Set the currency
     *
     * @param string $value
     * @return static
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Get the monetary amount
     *
     * @return null|int|float|bool|string
     */
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    /**
     * Set the monetary amount
     *
     * @param int|float|bool|string $value
     * @return static
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }
}
