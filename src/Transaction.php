<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a transaction object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Transaction
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new transaction with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this transaction with the specified parameters
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
     * @return Transaction
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the transaction id
     *
     * @return null|string
     */
    public function getId()
    {
        return $this->getParameter('id');
    }

    /**
     * Set the transaction id
     *
     * @param string $value
     * @return static
     */
    public function setId($value)
    {
        return $this->setParameter('id', $value);
    }

    /**
     * Get the transaction reference
     *
     * @return null|string
     */
    public function getReference()
    {
        return $this->getParameter('reference');
    }

    /**
     * Set the transaction reference
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
     * Get the customer
     *
     * @return null|Customer
     */
    public function getCustomer()
    {
        return $this->getParameter('customer');
    }

    /**
     * Set the customer
     *
     * @param Customer $value
     * @return static
     */
    public function setCustomer($value)
    {
        return $this->setParameter('customer', $value);
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
     * Get the payment method
     *
     * @return null|PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    /**
     * Set the payment method
     *
     * @param PaymentMethod $value
     * @return static
     */
    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    /**
     * Get the payment method id
     *
     * @return null|string
     */
    public function getPaymentMethodId()
    {
        return $this->getParameter('paymentMethodId');
    }

    /**
     * Set the payment method id
     *
     * @param string $value
     * @return static
     */
    public function setPaymentMethodId($value)
    {
        return $this->setParameter('paymentMethodId', $value);
    }

    /**
     * Get the payment method reference
     *
     * @return null|string
     */
    public function getPaymentMethodReference()
    {
        return $this->getParameter('paymentMethodReference');
    }

    /**
     * Set the payment method reference
     *
     * @param string $value
     * @return static
     */
    public function setPaymentMethodReference($value)
    {
        return $this->setParameter('paymentMethodReference', $value);
    }

    /**
     * Get the items
     *
     * @return null|array
     */
    public function getItems()
    {
        return $this->getParameter('items');
    }

    /**
     * Set the items
     *
     * @param array $items
     * @return static
     */
    public function setItems($items)
    {
        return $this->setParameter('items', $items);
    }

    /**
     * Get the ip address
     *
     * @return null|string
     */
    public function getIp()
    {
        return $this->getParameter('ip');
    }

    /**
     * Set the ip address
     *
     * @param string $value
     * @return static
     */
    public function setIp($value)
    {
        return $this->setParameter('ip', $value);
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

    /**
     * Get the address verification system response code
     *
     * @return null|string
     */
    public function getAvsCode()
    {
        return $this->getParameter('avsCode');
    }

    /**
     * Set the address verification system response
     *
     * @param string $value
     * @return static
     */
    public function setAvsCode($value)
    {
        return $this->setParameter('avsCode', $value);
    }

    /**
     * Get the CVV verification code
     *
     * @return null|string
     */
    public function getCvvCode()
    {
        return $this->getParameter('cvvCode');
    }

    /**
     * Set the CVV verification code
     *
     * @param string $value
     * @return static
     */
    public function setCvvCode($value)
    {
        return $this->setParameter('cvvCode', $value);
    }

    /**
     * Get the PayPal email
     *
     * @return null|string
     */
    public function getPayPalEmail()
    {
        return $this->getParameter('payPalEmail');
    }

    /**
     * Set the PayPal email
     *
     * @param string $value
     * @return static
     */
    public function setPayPalEmail($value)
    {
        return $this->setParameter('payPalEmail', $value);
    }

    /**
     * Get the PayPal redirect url
     *
     * @return null|string
     */
    public function getPayPalRedirectUrl()
    {
        return $this->getParameter('payPalRedirectUrl');
    }

    /**
     * Set the PayPal redirect url
     *
     * @param string $value
     * @return static
     */
    public function setPayPalRedirectUrl($value)
    {
        return $this->setParameter('payPalRedirectUrl', $value);
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
     * Get the status log
     *
     * @return null|array<TransactionStatus>
     */
    public function getStatusLog()
    {
        return $this->getParameter('statusLog');
    }

    /**
     * Set the status log
     *
     * @param string $value
     * @return static
     */
    public function setStatusLog($value)
    {
        return $this->setParameter('statusLog', $value);
    }

    /**
     * A list of attributes
     *
     * @return null|array
     */
    public function getAttributes()
    {
        return $this->getParameter('attributes');
    }

    /**
     * Set the attributes in this order
     *
     * @param array
     * @return static
     */
    public function setAttributes($attributes)
    {
        return $this->setParameter('attributes', $attributes);
    }
}
