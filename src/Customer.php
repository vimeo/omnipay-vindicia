<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a customer object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Customer
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new customer with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this customer with the specified parameters
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
     * @return Customer
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the customer id
     *
     * @return null|string
     */
    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    /**
     * Set the customer id
     *
     * @param string $value
     * @return static
     */
    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    /**
     * Get the customer reference
     *
     * @return null|string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * Set the customer reference
     *
     * @param string $value
     * @return static
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Get the customer id
     *
     * @return null|string
     * @deprecated see getCustomerId
     */
    public function getId()
    {
        return $this->getCustomerId();
    }

    /**
     * Set the customer id
     *
     * @param string $value
     * @return static
     * @deprecated see setCustomerId
     */
    public function setId($value)
    {
        return $this->setCustomerId($value);
    }

    /**
     * Get the customer reference
     *
     * @return null|string
     * @deprecated see getCustomerReference
     */
    public function getReference()
    {
        return $this->getCustomerReference();
    }

    /**
     * Set the customer reference
     *
     * @param string $value
     * @return static
     * @deprecated see setCustomerReference
     */
    public function setReference($value)
    {
        return $this->setCustomerReference($value);
    }

    /**
     * Get the customer name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getParameter('name');
    }

    /**
     * Set the customer name
     *
     * @param string $value
     * @return static
     */
    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }


    /**
     * Get the customer email
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Set the customer email
     *
     * @param string $value
     * @return static
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the customer's tax exemptions
     *
     * @return null|array
     */
    public function getTaxExemptions()
    {
        return $this->getParameter('taxExemptions');
    }

    /**
     * Set the customer's tax exemptions
     *
     * @param array $value
     * @return static
     */
    public function setTaxExemptions($value)
    {
        return $this->setParameter('taxExemptions', $value);
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
