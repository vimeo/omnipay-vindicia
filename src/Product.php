<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a product object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Product
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new product with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this product with the specified parameters
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
     * @return Product
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the product id
     *
     * @return null|string
     */
    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    /**
     * Set the product id
     *
     * @param string $value
     * @return static
     */
    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    /**
     * Get the product reference
     *
     * @return null|string
     */
    public function getProductReference()
    {
        return $this->getParameter('productReference');
    }

    /**
     * Set the product reference
     *
     * @param string $value
     * @return static
     */
    public function setProductReference($value)
    {
        return $this->setParameter('productReference', $value);
    }

    /**
     * Get the product id
     *
     * @return null|string
     * @deprecated see getProductId
     */
    public function getId()
    {
        return $this->getProductId();
    }

    /**
     * Set the product id
     *
     * @param string $value
     * @return static
     * @deprecated see setProductId
     */
    public function setId($value)
    {
        return $this->setProductId($value);
    }

    /**
     * Get the product reference
     *
     * @return null|string
     * @deprecated see getProductReference
     */
    public function getReference()
    {
        return $this->getProductReference();
    }

    /**
     * Set the product reference
     *
     * @param string $value
     * @return static
     * @deprecated see setProductReference
     */
    public function setReference($value)
    {
        return $this->setProductReference($value);
    }

    /**
     * Get the plan
     *
     * @return null|Plan
     */
    public function getPlan()
    {
        return $this->getParameter('plan');
    }

    /**
     * Set the plan
     *
     * @param Plan $value
     * @return static
     */
    public function setPlan($value)
    {
        return $this->setParameter('plan', $value);
    }

    /**
     * Get the plan id
     *
     * @return null|string
     */
    public function getPlanId()
    {
        return $this->getParameter('planId');
    }

    /**
     * Set the plan id
     *
     * @param string $value
     * @return static
     */
    public function setPlanId($value)
    {
        return $this->setParameter('planId', $value);
    }

    /**
     * Get the plan reference
     *
     * @return null|string
     */
    public function getPlanReference()
    {
        return $this->getParameter('planReference');
    }

    /**
     * Set the plan reference
     *
     * @param string $value
     * @return static
     */
    public function setPlanReference($value)
    {
        return $this->setParameter('planReference', $value);
    }

    /**
     * Get the product tax classification
     *
     * @return null|string
     */
    public function getTaxClassification()
    {
        return $this->getParameter('taxClassification');
    }

    /**
     * Set the product tax classification
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
