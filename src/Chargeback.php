<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a chargeback object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Chargeback
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new chargeback with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this chargeback with the specified parameters
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
     * @return Chargeback
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the chargeback id (not supported by Vindicia)
     *
     * @return null|string
     */

    public function getId()
    {
        return $this->getParameter('id');
    }

    /**
     * Set the chargeback id (not supported by Vindicia)
     *
     * @param string $value
     * @return static
     */
    public function setId($value)
    {
        return $this->setParameter('id', $value);
    }

    /**
     * Get the chargeback reference
     *
     * @return null|string
     */
    public function getReference()
    {
        return $this->getParameter('reference');
    }

    /**
     * Set the chargeback reference
     *
     * @param string $value
     * @return static
     */
    public function setReference($value)
    {
        return $this->setParameter('reference', $value);
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
     * @return null|string
     */
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    /**
     * Set the monetary amount
     *
     * @param string $value
     * @return static
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
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
     * Get the time the status changed
     *
     * @return null|string
     */
    public function getStatusChangedTime()
    {
        return $this->getParameter('statusChangedTime');
    }

    /**
     * Set the time the status changed
     *
     * @param string $value
     * @return static
     */
    public function setStatusChangedTime($value)
    {
        return $this->setParameter('statusChangedTime', $value);
    }

    /**
     * Get the time the payment processor received the chargeback
     *
     * @return null|string
     */
    public function getProcessorReceivedTime()
    {
        return $this->getParameter('processorReceivedTime');
    }

    /**
     * Set the time the payment processor received the chargeback
     *
     * @param string $value
     * @return static
     */
    public function setProcessorReceivedTime($value)
    {
        return $this->setParameter('processorReceivedTime', $value);
    }

    /**
     * Get the transaction (not provided by vindicia)
     *
     * @return null|Transaction
     */
    public function getTransaction()
    {
        return $this->getParameter('transaction');
    }

    /**
     * Set the transaction
     *
     * @param Transaction $value
     * @return static
     */
    public function setTransaction($value)
    {
        return $this->setParameter('transaction', $value);
    }

    /**
     * Get the transaction id
     *
     * @return null|string
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Set the transaction id
     *
     * @param string $value
     * @return static
     */
    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * Get the transaction reference (not provided by vindicia)
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    /**
     * Set the transaction reference
     *
     * @param string $value
     * @return static
     */
    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
    }
}
