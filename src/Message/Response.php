<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Exception\InvalidResponseException;

class Response extends AbstractResponse
{
    const SUCCESS_CODE = 200;

    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isSuccessful()
    {
        return intval($this->getCode()) === self::SUCCESS_CODE;
    }

    /**
     * Get the response message from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        if (isset($this->data->return)) {
            return $this->data->return->returnString;
        }
        throw new InvalidResponseException('Response has no message.');
    }

    /**
     * Get the response code from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getCode()
    {
        if (isset($this->data->return)) {
            return $this->data->return->returnCode;
        }
        throw new InvalidResponseException('Response has no code.');
    }

    public function getTransaction()
    {
        if (isset($this->data->transaction)) {
            return $this->data->transaction;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this transaction
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        if ($this->getTransaction()) {
            return $this->getTransaction()->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this transaction
     * NOTE: When you create a new transaction, Vindicia automatically
     * generates this value with the prefix you specified during initial
     * configuration.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        if ($this->getTransaction()) {
            return $this->getTransaction()->merchantTransactionId;
        }
        return null;
    }

    public function getCustomer()
    {
        if (isset($this->data->account)) {
            return $this->data->account;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this customer.
     *
     * @return string|null
     */
    public function getCustomerReference()
    {
        if ($this->getCustomer()) {
            return $this->getCustomer()->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this customer.
     * This ID must be provided when creating a purchase/authorize request.
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        if ($this->getCustomer()) {
            return $this->getCustomer()->merchantAccountId;
        }
        return null;
    }

    public function getPlan()
    {
        if (isset($this->data->billingPlan)) {
            return $this->data->billingPlan;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this plan.
     *
     * @return string|null
     */
    public function getPlanReference()
    {
        if ($this->getPlan()) {
            return $this->getPlan()->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this plan.
     *
     * @return string|null
     */
    public function getPlanId()
    {
        if ($this->getPlan()) {
            return $this->getPlan()->merchantBillingPlanId;
        }
        return null;
    }

    public function getProduct()
    {
        if (isset($this->data->product)) {
            return $this->data->product;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this product.
     *
     * @return string|null
     */
    public function getProductReference()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this product.
     *
     * @return string|null
     */
    public function getProductId()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->merchantProductId;
        }
        return null;
    }

    public function getSubscription()
    {
        if (isset($this->data->autobill)) {
            return $this->data->autobill;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this subscription.
     *
     * @return string|null
     */
    public function getSubscriptionReference()
    {
        if ($this->getSubscription()) {
            return $this->getSubscription()->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this subscription.
     *
     * @return string|null
     */
    public function getSubscriptionId()
    {
        if ($this->getSubscription()) {
            return $this->getSubscription()->merchantAutoBillId;
        }
        return null;
    }

    /**
     * Get the status of the subscription.
     *
     * @return string|null
     */
    public function getSubscriptionStatus()
    {
        if (isset($this->data->autobill)) {
            return $this->data->autobill->status;
        }
        return null;
    }

    public function getPaymentMethod()
    {
        if (isset($this->data->paymentMethod)) {
            return $this->data->paymentMethod;
        }

        // sometimes it's in the account object
        if (!isset($this->data->account) || !isset($this->data->account->paymentMethods)) {
            return null;
        }

        // Vindicia returns all of the payment methods for this account, so we have to find
        // the one that we added in the request. Theoretically we could just return the id
        // that was added in the request, but this way we ensure it is actually returned
        // in the response
        foreach ($this->data->account->paymentMethods as $paymentMethod) {
            if ($paymentMethod->merchantPaymentMethodId === $this->request->getPaymentMethodId()) {
                return $paymentMethod;
            }
        }

        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this payment method.
     * This ID must be provided when creating a create payment method request.
     *
     * @return string|null
     */
    public function getPaymentMethodId()
    {
        if ($this->getPaymentMethod()) {
            return $this->getPaymentMethod()->merchantPaymentMethodId;
        }

        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this payment method.
     *
     * @return string|null
     */
    public function getPaymentMethodReference()
    {
        if ($this->getPaymentMethod()) {
            return $this->getPaymentMethod()->VID;
        }

        return null;
    }

    public function getRefunds()
    {
        if (isset($this->data->refunds)) {
            return $this->data->refunds;
        }
        return null;
    }

    public function getTransactions()
    {
        if (isset($this->data->transactions)) {
            return $this->data->transactions;
        }
        return null;
    }

    public function getSubscriptions()
    {
        if (isset($this->data->autobills)) {
            return $this->data->autobills;
        }
        return null;
    }

    public function getPaymentMethods()
    {
        if (isset($this->data->paymentMethods)) {
            return $this->data->paymentMethods;
        }
        return null;
    }

    public function getChargebacks()
    {
        if (isset($this->data->chargebacks)) {
            return $this->data->chargebacks;
        }
        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this HOA Web Session.
     * NOTE: Web sessions do not have IDs, only references.
     *
     * @return string|null
     */
    public function getWebSessionReference()
    {
        if (isset($this->data->session)) {
            return $this->data->session->VID;
        }
        return null;
    }

    /**
     * Returns the total sales tax. For use after a CalculateSalesTax request
     *
     * @return float
     */
    public function getSalesTax()
    {
        if (isset($this->data->totalTax)) {
            return $this->data->totalTax;
        }
        return null;
    }
}
