<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Attribute
 *
 * Definites an attribute--custom additional data that can be added to a request
 * beyond what the gateway API supports. Vindicia calls this a "NameValue".
 */
class Attribute
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new attribute with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this attribute with the specified parameters
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
     * @return Attribute
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the attribute name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getParameter('name');
    }

    /**
     * Set the attribute name
     *
     * @param string $value
     * @return static
     */
    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    /**
     * Get the attribute value
     *
     * @return null|int|float|bool|string
     */
    public function getValue()
    {
        return $this->getParameter('value');
    }

    /**
     * Set the attribute value
     *
     * @param int|float|bool|string $value
     * @return static
     */
    public function setValue($value)
    {
        return $this->setParameter('value', $value);
    }
}
