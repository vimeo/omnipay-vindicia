<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia Gateway
 *
 * Example setup:
 * <code>
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 * </code>
 */
class Gateway extends AbstractVindiciaGateway
{
    /**
     * Get the gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Vindicia';
    }

    /**
     * Authorize a transaction. No money will be transfered until the transaction is captured.
     *
     * Parameters:
     * - customerId: Your identifier for the customer. The customer must be created before the
     * authorize request, so either customerId or customerReference is required.
     * - customerReference: The gateway's identifier for the customer. The customer must be
     * created before the authorize request, so either customerId or customerReference is required.
     * - amount: The amount of the transaction. Either the amount or items parameter is required.
     * If both are provided, the sum of the items must equal the amount.
     * - items: Line-items for the transaction. Either the amount or items parameter is required.
     * Each item must contain a sku, price, quantity, and name. A description is optional.
     * - currency: The three letter (capitalized) currency code for the transaction, eg) 'USD'
     * If not specified, the default will be left up to Vindicia, which will probably be USD for
     * most users.
     * - card: The card you wish to charge. If a payment method is not already attached to the
     * customer's account, the card, paymentMethodId, and/or paymentMethodReference parameter
     * must be provided. The card supports all basic Omnipay card fields, but not shipping
     * addresses at this time.
     * - paymentMethodId: Your identifier for the payment method. This can reference a saved
     * payment method or can be used to assign an ID to the card passed in the card parameter.
     * If a payment method is not already attached to the customer's account, the card,
     * paymentMethodId, and/or paymentMethodReference parameter is required.
     * must be provided.
     * - paymentMethodReference: The gateway's identifier for the payment method. This references
     * a saved payment method. If a payment method is not already attached to the customer's account,
     * the card, paymentMethodId, and/or paymentMethodReference parameter is required. Since the
     * reference is created by the gateway, assigning both this parameter and the card parameter
     * is meaningless.
     * - minChargebackProbability: If chargeback probabilty from risk scoring is greater than the
     * this value, the transaction will fail. If the value is 100, all transactions will succeed.
     * 100 is the default.
     * - taxClassification: The tax classification of the transaction. Values may vary depending
     * on your tax engine, consult with Vindicia to learn what values are available to you.
     * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
     * - ip: The customer's IP address. Optional.
     * - statementDescriptor: The description shown on the customers billing statement from the bank
     * This field’s value and its format are constrained by your payment processor; consult with
     * Vindicia Client Services before setting the value.
     * - attributes: Custom values you wish to have stored with the transaction. They have
     * no affect on anything.
     *
     * Example:
     * <code>
     *   $response = $gateway->authorize(array(
     *       'items' => array(
     *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
     *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2)
     *       ),
     *       'amount' => '23.48', // not necessary since items are provided
     *       'currency' => 'USD',
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
     *           'location' => 'FL'
     *       )
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       // Note: Your transaction ID begins with a prefix you specified in your initial
     *       // Vindicia configuration. The ID is automatically assigned by Vindicia.
     *       echo "Transaction id: " . $response->getTransactionId() . PHP_EOL;
     *       echo "Transaction reference: " . $response->getTransactionReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Purchase something! Money will be transferred. Calling purchase is the equivalent of
     * calling authorize and then calling capture.
     *
     * Parameters and usage are the same as for authorize.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\PurchaseRequest', $parameters);
    }

    /**
     * Capture a previously authorized transaction.
     *
     * Parameters:
     * - transactionId: Your identifier to represent this transaction. Either the transactionId
     * or transactionReference is required to specify what transaction should be captured.
     * - transactionReference: The gateway's identifier to represent this transaction. Either the
     * transactionId or transactionReference is required to specify what transaction should be
     * captured.
     *
     * Example:
     * <code>
     *   $response = $gateway->capture(array(
     *       'transactionId' => 'XYZ12345678',
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Transaction id: " . $response->getTransactionId() . PHP_EOL;
     *       echo "Transaction reference: " . $response->getTransactionReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function capture(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CaptureRequest', $parameters);
    }

    /**
     * Voids, or cancels, a previously authorized transaction. Will not work if the transaction
     * has already been captured, either by the capture function or purchase function.
     *
     * Parameters and usage are the same as for capture.
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\VoidRequest', $parameters);
    }

    /**
     * Creates a subscription (recurring payment).
     *
     * Takes the same parameters as authorize or purchase except it does not use the amount
     * parameter. The amount is determined by the prices on the product plus the currency on
     * the subscription. Also uses the following parameters:
     * - subscriptionId: Your identifier to represent this subscription. Unlike in the authorize
     * and purchase requests, Vindicia does not automatically create an ID for the subscription,
     * so you must specify it.
     * - productId: Your identifier for the product that the customer is subscribing to. Either
     * the productId or productReference is required. The product defines the prices.
     * - productReference: The gateway's identifier for the product that the customer is
     * subscribing to. Either the productId or productReference is required.
     * - planId: Your identifier for the plan defining the subscription's characteristics (eg,
     * frequency of billing). Either the planId or planReference is required. A billing plan
     * specified here will take precedence over one specified on the product.
     * - planReference: The gateway's identifier for the plan defining the subscription's
     * characteristics (eg, frequency of billing). Either the planId or planReference is required.
     *  A billing plan specified here will take precedence over one specified on the product.
     * - startTimestamp: The time to start billing. If not specified, defaults to the current
     * time. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
     *
     * Example:
     * <code>
     *   $response = $gateway->createSubscription(array(
     *       'amount' => '29.95',
     *       'currency' => 'GBP',
     *       'customerId' => '123456789',
     *       'card' => array(
     *           'number' => '5555555555554444',
     *           'expiryMonth' => '01',
     *           'expiryYear' => '2020',
     *           'cvv' => '123',
     *           'postcode' => '12345'
     *       ),
     *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
     *       'subscriptionId' => '111111',
     *       'productId' => '123123',
     *       'planId' => '654321'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Subscription id: " . $response->getSubscriptionId() . PHP_EOL;
     *       echo "Subscription reference: " . $response->getSubscriptionReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateSubscriptionRequest', $parameters);
    }

    /**
     * Update an existing subscription.
     *
     * Parameters and usage are the same as for createSubscription, except that the
     * subscription can be referenced by the subscriptionId or the subscriptionReference
     * (the gateway's identifier for this subscription).
     *
     * You should NOT change the currency of an existing subscription.
     */
    public function updateSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreateSubscriptionRequest', $parameters, true);
    }

    /**
     * Create a new payment method.
     *
     * Parameters:
     * - customerId: Your identifier for the customer to whom this payment method will belong.
     * Either customerId or customerReference is required.
     * - customerReference: The gateway's identifier for the customer to whom this payment method
     * will belong. Either customerId or customerReference is required.
     * - card: The card details you're adding. Required. Attributes can also be specified on the
     * card.
     * - paymentMethodId: Your identifier for the payment method. Required.
     *
     * Example:
     * <code>
     *   $response = $gateway->createPaymentMethod(array(
     *       'customerId' => '123456789',
     *       'card' => array(
     *           'number' => '5555555555554444',
     *           'expiryMonth' => '01',
     *           'expiryYear' => '2020',
     *           'cvv' => '123',
     *           'postcode' => '12345',
     *           'attributes' => array(
     *               'color' => 'blue'
     *           )
     *       ),
     *       'paymentMethodId' => 'cc-123456'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Payment method id: " . $response->getPaymentMethodId() . PHP_EOL;
     *       echo "Payment method reference: " . $response->getPaymentMethodReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function createPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $parameters);
    }

    /**
     * Update a payment method.
     *
     * Parameters and usage are the same as for createPaymentMethod, except that the
     * payment method can be referenced by the paymentMethodId or the paymentMethodReference
     * (the gateway's identifier for this payment method).
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $parameters, true);
    }

    // see AbstractVindiciaGateway for more functions and documentation
}
