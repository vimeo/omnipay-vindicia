<?php

namespace Omnipay\Vindicia;

use Symfony\Component\HttpFoundation\ParameterBag;
use Omnipay\Common\Helper;

class TaxExemption
{
    /**
     * Internal storage of all of the exemption parameters.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new TaxExemption object using the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        if (!array_key_exists('active', $parameters)) {
            $parameters['active'] = true;
        }

        $this->initialize($parameters);
    }

    /**
     * Initialize the object with parameters.
     *
     * If any unknown parameters passed, they will be ignored.
     *
     * @param array $parameters An associative array of parameters
     * @return static
     */
    public function initialize($parameters = null)
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * Get all parameters.
     *
     * @return array An associative array of parameters.
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * Get one parameter.
     *
     * @return mixed A single parameter value.
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * Set one parameter.
     *
     * @param string $key Parameter key
     * @param mixed $value Parameter value
     * @return static
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);
        return $this;
    }

    /**
     * Set the ID for the tax exemption, such as the US tax ID
     * or VAT ID.
     *
     * @param string $value
     * @return static
     */
    public function setExemptionId($value)
    {
        return $this->setParameter('exemptionId', $value);
    }

    /**
     * Get the ID for the tax exemption, such as the US tax ID
     * or VAT ID.
     *
     * @return string
     */
    public function getExemptionId()
    {
        return $this->getParameter('exemptionId');
    }

    /**
     * Set the region where the exemption is applicable (two letter country
     * or state code)
     *
     * @param string $value
     * @return static
     */
    public function setRegion($value)
    {
        return $this->setParameter('region', $value);
    }

    /**
     * Get the region where the exemption is applicable (two letter country
     * or state code)
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->getParameter('region');
    }

    /**
     * Set whether this tax exemption is active (true by default)
     *
     * @param bool $value
     * @return static
     */
    public function setActive($value)
    {
        return $this->setParameter('active', $value);
    }

    /**
     * Get whether this tax exemption is active (true by default)
     *
     * @return bool
     */
    public function getActive()
    {
        return $this->getParameter('active');
    }
}
