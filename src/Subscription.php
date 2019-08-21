<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a subscription object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Subscription
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new subscription with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this subscription with the specified parameters
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
     * @return Subscription
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the subscription id
     *
     * @return null|string
     */
    public function getSubscriptionId()
    {
        return $this->getParameter('subscriptionId');
    }

    /**
     * Set the subscription id
     *
     * @param string $value
     * @return static
     */
    public function setSubscriptionId($value)
    {
        return $this->setParameter('subscriptionId', $value);
    }

    /**
     * Get the subscription reference
     *
     * @return null|string
     */
    public function getSubscriptionReference()
    {
        return $this->getParameter('subscriptionReference');
    }

    /**
     * Set the subscription reference
     *
     * @param string $value
     * @return static
     */
    public function setSubscriptionReference($value)
    {
        return $this->setParameter('subscriptionReference', $value);
    }

    /**
     * Get the subscription id
     *
     * @return null|string
     * @deprecated see getSubscriptionId
     */
    public function getId()
    {
        return $this->getSubscriptionId();
    }

    /**
     * Set the subscription id
     *
     * @param string $value
     * @return static
     * @deprecated see setSubscriptionId
     */
    public function setId($value)
    {
        return $this->setSubscriptionId($value);
    }

    /**
     * Get the subscription reference
     *
     * @return null|string
     * @deprecated see getSubscriptionReference
     */
    public function getReference()
    {
        return $this->getSubscriptionReference();
    }

    /**
     * Set the subscription reference
     *
     * @param string $value
     * @return static
     * @deprecated see setSubscriptionReference
     */
    public function setReference($value)
    {
        return $this->setSubscriptionReference($value);
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
     * Get the product
     *
     * @return null|Product
     */
    public function getProduct()
    {
        return $this->getParameter('product');
    }

    /**
     * Set the product
     *
     * @param Product $value
     * @return static
     */
    public function setProduct($value)
    {
        return $this->setParameter('product', $value);
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
     * Get the start tume
     *
     * @return null|string
     */
    public function getStartTime()
    {
        return $this->getParameter('startTime');
    }

    /**
     * Set the start time
     *
     * @param string $value
     * @return static
     */
    public function setStartTime($value)
    {
        return $this->setParameter('startTime', $value);
    }

    /**
     * Get the end tume
     *
     * @return null|string
     */
    public function getEndTime()
    {
        return $this->getParameter('endTime');
    }

    /**
     * Set the end time
     *
     * @param string $value
     * @return static
     */
    public function setEndTime($value)
    {
        return $this->setParameter('endTime', $value);
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
     * Get the billing state
     *
     * @return null|string
     */
    public function getBillingState()
    {
        return $this->getParameter('billingState');
    }

    /**
     * Set the billing state
     *
     * @param string $value
     * @return static
     */
    public function setBillingState($value)
    {
        return $this->setParameter('billingState', $value);
    }

    /**
     * Get the billing day (day of the month when subscription was charge) of the subscription.
     *
     * @return int|null
     */
    public function getBillingDay()
    {
        return $this->getParameter('billingDay');
    }

    /**
     * Set the billing day
     *
     * @param int $value
     * @return static
     */
    public function setBillingDay($value)
    {
        return $this->setParameter('billingDay', $value);
    }

    /**
     * Gets the reason the subscription was canceled
     *
     * @return null|string
     */
    public function getCancelReason()
    {
        return $this->getParameter('cancelReason');
    }

    /**
     * Set the reason the subscription was canceled
     *
     * @param string $value
     * @return static
     */
    public function setCancelReason($value)
    {
        return $this->setParameter('cancelReason', $value);
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
