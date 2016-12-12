<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Semi-generic representation of a transaction status that could be returned in
 * a transaction's status log.
 */
class TransactionStatus
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new transaction status with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this transaction status with the specified parameters
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
     * @return TransactionStatus
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the status
     *
     * @return null|string
     */
    public function getStatus()
    {
        return $this->getParameter('status');
    }

    /**
     * Set the status
     *
     * @param string $value
     * @return static
     */
    public function setStatus($value)
    {
        return $this->setParameter('status', $value);
    }

    /**
     * Get the time
     *
     * @return null|string
     */
    public function getTime()
    {
        return $this->getParameter('time');
    }

    /**
     * Set the time
     *
     * @param string $value
     * @return static
     */
    public function setTime($value)
    {
        return $this->setParameter('time', $value);
    }

    /**
     * Get the authorization code
     *
     * @return null|string
     */
    public function getAuthorizationCode()
    {
        return $this->getParameter('authorizationCode');
    }

    /**
     * Set the authorization code
     *
     * @param string $value
     * @return static
     */
    public function setAuthorizationCode($value)
    {
        return $this->setParameter('authorizationCode', $value);
    }
}
