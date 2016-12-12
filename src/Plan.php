<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a plan object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Plan
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new plan with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this plan with the specified parameters
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
     * @return Plan
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the plan id
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getParameter('id');
    }

    /**
     * Set the plan id
     *
     * @param string $value
     * @return static
     */
    public function setId($value)
    {
        return $this->setParameter('id', $value);
    }

    /**
     * Get the plan reference
     *
     * @return null|string
     */
    public function getReference()
    {
        return $this->getParameter('reference');
    }

    /**
     * Set the plan reference
     *
     * @param string $value
     * @return static
     */
    public function setReference($value)
    {
        return $this->setParameter('reference', $value);
    }

    /**
     * Get the plan interval
     *
     * @return null|string
     */
    public function getInterval()
    {
        return $this->getParameter('interval');
    }

    /**
     * Set the plan interval
     *
     * @param string $value
     * @return static
     */
    public function setInterval($value)
    {
        return $this->setParameter('interval', $value);
    }

    /**
     * Get the plan interval count
     *
     * @return null|string
     */
    public function getIntervalCount()
    {
        return $this->getParameter('intervalCount');
    }

    /**
     * Set the plan interval count
     *
     * @param string $value
     * @return static
     */
    public function setIntervalCount($value)
    {
        return $this->setParameter('intervalCount', $value);
    }

    /**
     * Get the plan tax classification
     *
     * @return null|string
     */
    public function getTaxClassification()
    {
        return $this->getParameter('taxClassification');
    }

    /**
     * Set the plan tax classification
     *
     * @param string $value
     * @return static
     */
    public function setTaxClassification($value)
    {
        return $this->setParameter('taxClassification', $value);
    }

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
     * @param array $prices
     * @return static
     * @throws InvalidPriceBagException if multiple prices have the same currency
     */
    public function setPrices($prices)
    {
        return $this->setParameter('prices', $prices);
    }

    /**
     * A list of attributes
     *
     * @return AttributeBag|null
     */
    public function getAttributes()
    {
        return $this->getParameter('attributes');
    }

    /**
     * Set the attributes in this order
     *
     * @param array $attributes
     * @return static
     */
    public function setAttributes($attributes)
    {
        return $this->setParameter('attributes', $attributes);
    }
}
