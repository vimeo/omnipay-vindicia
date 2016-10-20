<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\AbstractGateway;

/**
 * Defines the functions that are shared between the Vindicia gateways.
 */
abstract class AbstractVindiciaGateway extends AbstractGateway
{
    /**
     * Get the gateway parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'password' => '',
            'testMode' => false,
        );
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Refund a transaction.
     *
     * See Message\RefundRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\RefundRequest', $parameters);
    }

    /**
     * Create a new customer. Customers must be created before purchases can be made.
     *
     * See Message\CreateCustomerRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateCustomerRequest', $parameters);
    }

    /**
     * Update an existing customer.
     *
     * See Message\CreateCustomerRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function updateCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateCustomerRequest', $parameters, true);
    }

    /**
     * Create a new plan. A plan defines the behavior of a subscription, such as how
     * often the customer is billed.
     *
     * See Message\CreatePlanRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createPlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePlanRequest', $parameters);
    }

    /**
     * Update an existing plan.
     *
     * See Message\CreatePlanRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function updatePlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePlanRequest', $parameters, true);
    }

    /**
     * Create a new product.
     *
     * See Message\CreateProductRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateProductRequest', $parameters);
    }

    /**
     * Update an existing product.
     *
     * See Message\CreateProductRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function updateProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateProductRequest', $parameters, true);
    }

    /**
     * Cancel a subscription.
     *
     * See Message\CancelSubscriptionRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function cancelSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CancelSubscriptionRequest', $parameters);
    }

    /**
     * Cancel multiple subscriptions for a user. Can cancel specified subscriptions
     * or all subscriptions.
     *
     * See Message\CancelSubscriptionRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function cancelSubscriptions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia transaction object.
     *
     * See Message\FetchTransactionRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Fetch multiple transactions, either for a customer or for a time window.
     *
     * See Message\FetchTransactionsRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchTransactions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchTransactionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia product object.
     *
     * See Message\FetchProductRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchProductRequest', $parameters);
    }

    /**
     * Fetch a Vindicia subscription object.
     *
     * See Message\FetchSubscriptionRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchSubscriptionRequest', $parameters);
    }

    /**
     * Fetch multiple subscriptions, either for a customer or for a time window.
     *
     * See Message\FetchSubscriptionsRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchSubscriptions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia customer object.
     *
     * See Message\FetchSubscriptionsRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchCustomerRequest', $parameters);
    }

    /**
     * Fetch a Vindicia paymentMethod object.
     *
     * See Message\FetchPaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPaymentMethodRequest', $parameters);
    }

    /**
     * Fetch all payment methods for a customer.
     *
     * See Message\FetchPaymentMethodsRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchPaymentMethods(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPaymentMethodsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia plan object.
     *
     * See Message\FetchPlanRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchPlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPlanRequest', $parameters);
    }

    /**
     * Fetch multiple refunds, either for a transaction or for a time window.
     *
     * See Message\FetchRefundsRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchRefunds(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchRefundsRequest', $parameters);
    }

    /**
     * Fetch multiple chargebacks, either for a transaction or for a time window.
     *
     * See Message\FetchChargebacksRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function fetchChargebacks(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchChargebacksRequest', $parameters);
    }

    /**
     * Calculate the sales tax for a potential transaction.
     *
     * See Message\CalculateSalesTaxRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function calculateSalesTax(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest', $parameters);
    }

    /**
     * Method override to support $isUpdate flag.
     *
     * @param string $class The request class name
     * @param array $parameters
     * @pram bool $isUpdate default false
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    protected function createRequest($class, array $parameters, $isUpdate = false)
    {
        $obj = new $class($this->httpClient, $this->httpRequest, $isUpdate);
        return $obj->initialize(array_replace($this->getParameters(), $parameters));
    }
}
