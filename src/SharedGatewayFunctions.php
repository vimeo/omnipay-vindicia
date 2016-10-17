<?php

namespace Omnipay\Vindicia;

/**
 * Defines the functions that are shared between the Vindicia and Vindicia_PayPal gateways.
 */
trait SharedGatewayFunctions
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
     * Parameters:
     * - transactionId: Your identifier to represent the transaction that should be refunded.
     * Either the transactionId or transactionReference is required.
     * - transactionReference: The gateway's identifier to represent the transaction that should
     * be refunded. Either the transactionId or transactionReference is required.
     * - amount: The amount to refund. If neither amount or items is provided, the remaining
     * balance will be refunded. If both are provided, the sum of the items must equal the amount.
     * The amount can be up to the remaining non-refunded balance of the transaction (ie, the total
     * amount of the transaction if it has not been refunded before). After a non-itemized refund
     * has been issued on a transaction, an itemized refund cannot be issued for the same
     * transaction.
     * - items: Line-items for the transaction. If neither amount or items is provided, the remaining
     * balance will be refunded. If both are provided, the sum of the items must equal the amount.
     * The amount can be up to the remaining non-refunded balance of the transaction (ie, the total
     * amount of the transaction if it has not been refunded before). After a non-itemized refund
     * has been issued on a transaction, an itemized refund cannot be issued for the same
     * transaction. Each item must contain a sku, amount, and transactionItemIndexNumber.
     * transactionItemIndex number is the position of the item in the original transaction, indexed
     * starting at 1. A taxOnly parameter can be set to true if only the tax should be refunded.
     * Refunding by items may not work if you are using Vindicia's old tax engine.
     * - note: A note to attach to the refund. Optional.
     * - attributes: Custom values you wish to have stored with the refund. They have
     * no affect on anything.
     *
     * Example:
     * <code>
     *   $response = $gateway->refund(array(
     *       'transactionId' => 'XYZ12345678',
     *       'items' => array(
     *           array('transactionItemIndexNumber' => '1', 'sku' => '1', 'amount' => '3.50'),
     *           array('transactionItemIndexNumber' => '2', 'sku' => '2', 'amount' => '19.98')
     *       ),
     *       'amount' => '23.48', // not necessary since items are provided
     *       'note' => 'A note about the refund'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Refund id: " . $response->getRefundId() . PHP_EOL;
     *       echo "Refund reference: " . $response->getRefundReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function refund(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\RefundRequest', $parameters);
    }

    /**
     * Create a new customer. Customers must be created before purchases can be made.
     *
     * Parameters:
     * - customerId: Your identifier to represent the customer. Required.
     * - name: The customer's name. Optional.
     * - email: The customer's email address. Optional.
     * - card: A new card to be added for the customer. If card is specified, paymentMethodId
     * should be as well.
     * - paymentMethodId: Your identifier for the payment method being added. This can reference
     * an existing saved payment method or can be used to assign an ID to the card passed in the
     * card parameter.
     * - paymentMethodReference: The gateway's identifier for the payment method. This would only
     * be used to attach an existing payment method to this new customer, and in this case can be
     * used in place of paymentMethodId if you wish.
     * - attributes: Custom values you wish to have stored with the customer. They have
     * no affect on anything.
     *
     * Example:
     * <code>
     *   $response = $gateway->createCustomer(array(
     *       'name' => 'Test Customer',
     *       'email' => 'customer@example.com',
     *       'customerId' => '123456789',
     *       'card' => array(
     *           'number' => '5555555555554444',
     *           'expiryMonth' => '01',
     *           'expiryYear' => '2020',
     *           'cvv' => '123',
     *           'postcode' => '12345'
     *       ),
     *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
     *       'attributes' => array(
     *           'hasMustache' => false
     *       )
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Customer id: " . $response->getCustomerId() . PHP_EOL;
     *       echo "Customer reference: " . $response->getCustomerReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function createCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateCustomerRequest', $parameters);
    }

    /**
     * Update an existing customer.
     *
     * Parameters and usage are the same as for createCustomer, except that the customer
     * can be referenced by the customerId or the customerReference (the gateway's identifier
     * for this customer).
     */
    public function updateCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateCustomerRequest', $parameters, true);
    }

    /**
     * Create a new plan. A plan defines the behavior of a subscription, such as how
     * often the customer is billed.
     *
     * It is rare that you will need many different plans since you can attach the same
     * plan to multiple different subscriptions. You may find it easier to create the
     * plans through Vindicia's web portal, which will also give you more control over
     * their features.
     *
     * Parameters:
     * - planId: Your identifier to represent this plan. Required.
     * - interval: The units of the billing period (day, week, month, year). Required.
     * - intervalCount: The frequency to bill, using the units specified by interval. (eg,
     * if interval is week and intervalCount is 2, the customer will be billed every two
     * weeks. Required.
     * - prices: Prices in multiple currencies associated with the billing plan. These
     * are a fallback in case the product does not specify prices. Optional.
     * - taxClassification: The tax classification of the plan. Values may vary depending
     * on your tax engine, consult with Vindicia to learn what values are available to you.
     * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
     * - statementDescriptor: The description shown on the customers billing statement from the bank
     * This field’s value and its format are constrained by your payment processor; consult with
     * Vindicia Client Services before setting the value.
     * - attributes: Custom values you wish to have stored with the plan. They have
     * no affect on anything.
     *
     * Example:
     * <code>
     *   $response = $gateway->createPlan(array(
     *       'planId' => '123456789',
     *       'interval' => 'week',
     *       'intervalCount' => 2,
     *       'prices' => array(
     *           'USD' => '9.99',
     *           'CAD' => '12.99'
     *       )
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Plan id: " . $response->getPlanId() . PHP_EOL;
     *       echo "Plan reference: " . $response->getPlanReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function createPlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePlanRequest', $parameters);
    }

    /**
     * Update an existing plan.
     *
     * Parameters and usage are the same as for createPlan, except that the plan
     * can be referenced by the planId or the planReference (the gateway's identifier
     * for this plan).
     */
    public function updatePlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePlanRequest', $parameters, true);
    }

    /**
     * Create a new product.
     *
     * Products are attached to subscriptions to define what the customer is purchasing
     * and what the price is in different currencies.
     *
     * Parameters:
     * - productId: Your identifier to represent the product.
     * - planId: Your identifier to represent the default billing plan for this product.
     * - planReference: The gateway's identifier to represent the default billing plan for this
     * product.
     * - prices: Prices in multiple currencies for this product. If provided, this will override
     * the prices specified by a plan.
     * - taxClassification: The tax classification of the plan. Values may vary depending
     * on your tax engine, consult with Vindicia to learn what values are available to you.
     * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
     * - taxClassification: The tax classification of the plan. Values may vary depending
     * on your tax engine, consult with Vindicia to learn what values are available to you.
     * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
     * - statementDescriptor: The description shown on the customers billing statement from the bank
     * This field’s value and its format are constrained by your payment processor; consult with
     * Vindicia Client Services before setting the value.
     * - attributes: Custom values you wish to have stored with the plan. They have
     * no affect on anything.
     * - duplicateBehavior: The behavior when the exact same product is submitted twice
     * with no id or reference. Options:
     *   CreateProductRequest::BEHAVIOR_DUPLICATE: Create the product as normal, resulting in two
     *       identical products
     *   CreateProductRequest::BEHAVIOR_FAIL: Does nothing and returns failure
     *   CreateProductRequest::BEHAVIOR_SUCCEED_IGNORE: Does nothing and returns success (default)
     *
     * Example:
     * <code>
     *   $response = $gateway->createProduct(array(
     *       'productId' => '123456789',
     *       'planId' => '123',
     *       'duplicateBehavior' => CreateProductRequest::BEHAVIOR_SUCCEED_IGNORE,
     *       'statementDescriptor' => 'Statement descriptor',
     *       'prices' => array(
     *           'USD' => '9.99',
     *           'CAD' => '12.99'
     *       )
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Product id: " . $response->getProductId() . PHP_EOL;
     *       echo "Product reference: " . $response->getProductReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function createProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateProductRequest', $parameters);
    }

    /**
     * Update an existing product.
     *
     * Parameters and usage are the same as for createProduct, except that the product
     * can be referenced by the productId or the productReference (the gateway's identifier
     * for this product).
     */
    public function updateProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateProductRequest', $parameters, true);
    }

    /**
     * Cancel a subscription.
     *
     * Parameters:
     * - subscriptionId: Your identifier for the subscription to be canceled. Either subscriptionId
     * or subscriptionReference is required.
     * - subscriptionReference: The gateway's identifier for the subscription to be canceled. Either
     * subscriptionId or subscriptionReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->cancelSubscription(array(
     *       'subscriptionId' => '12345'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Subscription id: " . $response->getSubscriptionId() . PHP_EOL;
     *       echo "Subscription reference: " . $response->getSubscriptionReference() . PHP_EOL;
     *       echo "Subscription status: " . $response->getSubscriptionStatus() . PHP_EOL;
     *   }
     * </code>
     */
    public function cancelSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CancelSubscriptionRequest', $parameters);
    }

    /**
     * Cancel multiple subscriptions for a user. Can cancel specified subscriptions
     * or all subscriptions.
     *
     * Parameters:
     * - customerId: Your identifier for the customer whose subscriptions should be canceled.
     * Either customerId or customerReference is required.
     * - customerReference: The gateway's identifier for the customer whose subscriptions should
     * be canceled. Either customerId or customerReference is required.
     * - subscriptionIds: Array of ids (your identifiers) of the subscriptions to be canceled.
     * If neither subscriptionIds nor subscriptionReferences are provided, ALL of the user's
     * subscriptions will be canceled.
     * - subscriptionReferences: Array of references (the gateway's identifiers) of the
     * subscriptions to be canceled. If neither subscriptionIds nor subscriptionReferences are
     * provided, ALL of the user's subscriptions will be canceled.
     *
     * Example:
     * <code>
     *   $response = $gateway->cancelSubscriptions(array(
     *       'customerId' => '54321',
     *       'subscriptionIds' => array('12345', '23456', '34567')
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Customer id: " . $response->getCustomerId() . PHP_EOL;
     *       echo "Customer reference: " . $response->getCustomerReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function cancelSubscriptions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia transaction object.
     *
     * Parameters:
     * - transactionId: Your identifier for the transaction to be fetched. Either transactionId
     * or transactionReference is required.
     * - transactionReference: The gateway's identifier for the transaction to be fetched. Either
     * transactionId or transactionReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchTransaction(array(
     *       'transactionId' => 'XYZ123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getTransaction());
     *   }
     * </code>
     */
    public function fetchTransaction(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchTransactionRequest', $parameters);
    }

    /**
     * Fetch multiple transactions, either for a customer or for a time window.
     *
     * Parameters:
     * - customerId: Your identifier for the customer whose transactions should be fetched.
     * Either customerId or customerReference is required if you're fetching by customer.
     * - customerReference: The gateway's identifier for the customer whose transactions should
     * be fetched. Either customerId or customerReference is required if you're fetching by customer.
     * - startTime: The beginning of the date range for which transactions should be fetched.
     * If fetching by date range, startTime and endTime are required and a customer cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     * - endTime: The end of the date range for which transactions should be fetched.
     * If fetching by date range, startTime and endTime are required and a customer cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     *
     * Example by customer:
     * <code>
     *   $response = $gateway->fetchTransactions(array(
     *       'customerId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getTransactions());
     *   }
     * </code>
     *
     * Example by date range:
     * <code>
     *   $response = $gateway->fetchTransactions(array(
     *       'startTime' => '2016-06-01T12:30:00-04:00',
     *       'endTime' => '2016-07-01T12:30:00-04:00'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getTransactions());
     *   }
     * </code>
     */
    public function fetchTransactions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchTransactionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia product object.
     *
     * Parameters:
     * - productId: Your identifier for the product to be fetched. Either productId
     * or productReference is required.
     * - productReference: The gateway's identifier for the product to be fetched. Either
     * productId or productReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchProduct(array(
     *       'productId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getProduct());
     *   }
     * </code>
     */
    public function fetchProduct(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchProductRequest', $parameters);
    }

    /**
     * Fetch a Vindicia subscription object.
     *
     * Parameters:
     * - subscriptionId: Your identifier for the subscription to be fetched. Either subscriptionId
     * or subscriptionReference is required.
     * - subscriptionReference: The gateway's identifier for the subscription to be fetched. Either
     * subscriptionId or subscriptionReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchSubscription(array(
     *       'subscriptionId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getSubscription());
     *   }
     * </code>
     */
    public function fetchSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchSubscriptionRequest', $parameters);
    }

    /**
     * Fetch multiple subscriptions, either for a customer or for a time window.
     *
     * Parameters:
     * - customerId: Your identifier for the customer whose subscriptions should be fetched.
     * Either customerId or customerReference is required if you're fetching by customer.
     * - customerReference: The gateway's identifier for the customer whose subscriptions should
     * be fetched. Either customerId or customerReference is required if you're fetching by customer.
     * - startTime: The beginning of the date range for which subscriptions should be fetched.
     * If fetching by date range, startTime and endTime are required and a customer cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     * - endTime: The end of the date range for which subscriptions should be fetched.
     * If fetching by date range, startTime and endTime are required and a customer cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     *
     * Example by customer:
     * <code>
     *   $response = $gateway->fetchSubscriptions(array(
     *       'customerId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getSubscriptions());
     *   }
     * </code>
     *
     * Example by date range:
     * <code>
     *   $response = $gateway->fetchSubscriptions(array(
     *       'startTime' => '2016-06-01T12:30:00-04:00',
     *       'endTime' => '2016-07-01T12:30:00-04:00'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getSubscriptions());
     *   }
     * </code>
     */
    public function fetchSubscriptions(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia customer object.
     *
     * Parameters:
     * - customerId: Your identifier for the customer to be fetched. Either customerId
     * or customerReference is required.
     * - customerReference: The gateway's identifier for the customer to be fetched. Either
     * customerId or customerReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchCustomer(array(
     *       'customerId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getCustomer());
     *   }
     * </code>
     */
    public function fetchCustomer(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchCustomerRequest', $parameters);
    }

    /**
     * Fetch a Vindicia paymentMethod object.
     *
     * Parameters:
     * - paymentMethodId: Your identifier for the paymentMethod to be fetched. Either paymentMethodId
     * or paymentMethodReference is required.
     * - paymentMethodReference: The gateway's identifier for the paymentMethod to be fetched. Either
     * paymentMethodId or paymentMethodReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchPaymentMethod(array(
     *       'paymentMethodId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getPaymentMethod());
     *   }
     * </code>
     */
    public function fetchPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPaymentMethodRequest', $parameters);
    }

    /**
     * Fetch all payment methods for a customer.
     *
     * Parameters:
     * - customerId: Your identifier for the customer whose payment methods should be fetched.
     * Either customerId or customerReference is required.
     * - customerReference: The gateway's identifier for the customer whose paymentMethods should
     * be fetched. Either customerId or customerReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchPaymentMethods(array(
     *       'customerId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getPaymentMethods());
     *   }
     * </code>
     */
    public function fetchPaymentMethods(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPaymentMethodsRequest', $parameters);
    }

    /**
     * Fetch a Vindicia plan object.
     *
     * Parameters:
     * - planId: Your identifier for the plan to be fetched. Either planId
     * or planReference is required.
     * - planReference: The gateway's identifier for the plan to be fetched. Either
     * planId or planReference is required.
     *
     * Example:
     * <code>
     *   $response = $gateway->fetchPlan(array(
     *       'planId' => '123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getPlan());
     *   }
     * </code>
     */
    public function fetchPlan(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchPlanRequest', $parameters);
    }

    /**
     * Fetch multiple refunds, either for a transaction or for a time window.
     *
     * Parameters:
     * - transactionId: Your identifier for the transaction whose refunds should be fetched.
     * Either transactionId or transactionReference is required if you're fetching by transaction.
     * - transactionReference: The gateway's identifier for the transaction whose refunds should
     * be fetched. Either transactionId or transactionReference is required if you're fetching by
     * transaction.
     * - startTime: The beginning of the date range for which refunds should be fetched.
     * If fetching by date range, startTime and endTime are required and a transaction cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     * - endTime: The end of the date range for which refunds should be fetched.
     * If fetching by date range, startTime and endTime are required and a transaction cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     *
     * Example by transaction:
     * <code>
     *   $response = $gateway->fetchRefunds(array(
     *       'transactionId' => 'XYZ123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getRefunds());
     *   }
     * </code>
     *
     * Example by date range:
     * <code>
     *   $response = $gateway->fetchRefunds(array(
     *       'startTime' => '2016-06-01T12:30:00-04:00',
     *       'endTime' => '2016-07-01T12:30:00-04:00'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getRefunds());
     *   }
     * </code>
     */
    public function fetchRefunds(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchRefundsRequest', $parameters);
    }

    /**
     * Fetch multiple chargebacks, either for a transaction or for a time window.
     *
     * Parameters:
     * - transactionId: Your identifier for the transaction whose chargebacks should be fetched.
     * Either transactionId or transactionReference is required if you're fetching by transaction.
     * - transactionReference: The gateway's identifier for the transaction whose chargebacks should
     * be fetched. Either transactionId or transactionReference is required if you're fetching by
     * transaction.
     * - startTime: The beginning of the date range for which chargebacks should be fetched.
     * If fetching by date range, startTime and endTime are required and a transaction cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     * - endTime: The end of the date range for which chargebacks should be fetched.
     * If fetching by date range, startTime and endTime are required and a transaction cannot be
     * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     *
     * Example by transaction:
     * <code>
     *   $response = $gateway->fetchChargebacks(array(
     *       'transactionId' => 'XYZ123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getChargebacks());
     *   }
     * </code>
     *
     * Example by date range:
     * <code>
     *   $response = $gateway->fetchChargebacks(array(
     *       'startTime' => '2016-06-01T12:30:00-04:00',
     *       'endTime' => '2016-07-01T12:30:00-04:00'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       var_dump($response->getChargebacks());
     *   }
     * </code>
     */
    public function fetchChargebacks(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\FetchChargebacksRequest', $parameters);
    }

    /**
     * Calculate the sales tax for a potential transaction.
     *
     * Parameters:
     * - taxClassification: The tax classification to use for calculation. Values may vary depending
     * on your tax engine, consult with Vindicia to learn what values are available to you.
     * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
     * - amount: The amount to calculate tax based on. Required.
     * - currency: The three letter (capitalized) currency code for the amount, eg) 'USD'
     * If not specified, the default will be left up to Vindicia, which will probably be USD for
     * most users.
     * - card: Card details are not necessary, but this parameter can be used to provide the address
     * details to determine where the tax will be applied and what rate should be used.
     *
     * Example:
     * <code>
     *   $response = $gateway->calculateSalesTax(array(
     *       'amount' => '10.00',
     *       'currency' => 'USD',
     *       'card' => array(
     *           'country' => 'FR'
     *       ),
     *       'taxClassification' => 'OtherTaxable'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Sales tax: " . $response->getSalesTax() . PHP_EOL;
     *   }
     * </code>
     */
    public function calculateSalesTax(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest', $parameters);
    }
}
