<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;
use Omnipay\Common\CreditCard;

/**
 * Generic representation of a payment method object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class PaymentMethod
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new payment method with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this payment method with the specified parameters
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
     * @return PaymentMethod
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the paymentMethod id
     *
     * @return null|string
     */
    public function getPaymentMethodId()
    {
        return $this->getParameter('paymentMethodId');
    }

    /**
     * Set the paymentMethod id
     *
     * @param string $value
     * @return static
     */
    public function setPaymentMethodId($value)
    {
        return $this->setParameter('paymentMethodId', $value);
    }

    /**
     * Get the paymentMethod reference
     *
     * @return null|string
     */
    public function getPaymentMethodReference()
    {
        return $this->getParameter('paymentMethodReference');
    }

    /**
     * Set the paymentMethod reference
     *
     * @param string $value
     * @return static
     */
    public function setPaymentMethodReference($value)
    {
        return $this->setParameter('paymentMethodReference', $value);
    }

    /**
     * Get the paymentMethod id
     *
     * @return null|string
     * @deprecated see getPaymentMethodId
     */
    public function getId()
    {
        return $this->getPaymentMethodId();
    }

    /**
     * Set the paymentMethod id
     *
     * @param string $value
     * @return static
     * @deprecated see setPaymentMethodId
     */
    public function setId($value)
    {
        return $this->setPaymentMethodId($value);
    }

    /**
     * Get the paymentMethod reference
     *
     * @return null|string
     * @deprecated see getPaymentMethodReference
     */
    public function getReference()
    {
        return $this->getPaymentMethodReference();
    }

    /**
     * Set the paymentMethod reference
     *
     * @param string $value
     * @return static
     * @deprecated see setPaymentMethodReference
     */
    public function setReference($value)
    {
        return $this->setPaymentMethodReference($value);
    }

    /**
     * Get the card
     *
     * @return null|CreditCard
     */
    public function getCard()
    {
        return $this->getParameter('card');
    }

    /**
     * Sets the card.
     *
     * @param CreditCard $value
     * @return static
     */
    public function setCard($value)
    {
        return $this->setParameter('card', $value);
    }

    /**
     * Get the payment method type, either PayPal or CreditCard
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * Sets the payment method type
     *
     * @param string $value
     * @return static
     */
    public function setType($value)
    {
        return $this->setParameter('type', $value);
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

    /**
     * Get the email associated with PayPal account
     *
     * @return null|string
     */
    public function getPayPalEmail()
    {
        return $this->getParameter('payPalEmail');
    }

    /**
     * Sets the payment method payPalEmail
     *
     * @param string $value
     * @return static
     */
    public function setPayPalEmail($value)
    {
        return $this->setParameter('payPalEmail', $value);
    }
}
