<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Authorize a transaction. No money will be transfered during authorization.
 * After authorizing a transaction, call capture to complete the transaction and
 * transfer the money. If you want to do authorize and capture in one step, use the
 * purchase function.
 *
 * A customer is required for any kind of purchase. If the customer does not yet exist,
 * specify a customerId and a new customer will be created by this request.
 *
 * Parameters:
 * - customerId: Your identifier for the customer. Either customerId or customerReference is
 * required. If the customer does not yet exist, specify the customerId and a new customer
 * will be automatically created.
 * - customerReference: The gateway's identifier for the customer. Either customerId or
 * customerReference is required. If the customer does not yet exist, specify the customerId
 * and a new customer will be automatically created.
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
 * - name: If you're creating a new customer with this request, you can use this to specify
 * the customer's name.
 * - email: If you're creating a new customer with this request, you can use this to specify
 * the customer's email.
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
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // authorize the transaction
 *   $authorizeResponse = $gateway->authorize(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2)
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card, which will
 *                                         // be attached to the customer's account
 *       'attributes' => array(
 *           'location' => 'FL'
 *       )
 *   ))->send();
 *
 *   if ($authorizeResponse->isSuccessful()) {
 *       // Note: Your transaction ID begins with a prefix you specified in your initial
 *       // Vindicia configuration. The ID is automatically assigned by Vindicia.
 *       echo "Transaction id: " . $authorizeResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $authorizeResponse->getTransactionReference() . PHP_EOL;
 *       echo "The transaction risk score is: " . $authorizeResponse->getRiskScore();
 *   } else {
 *       // error handling
 *   }
 *
 *   // At this point, no money has been transferred. Now we will capture the transaction to
 *   // complete it and transfer the money.
 *
 *   $captureResponse = $gateway->capture(array(
 *       // You can identify the transaction by the transactionId or transactionReference
 *       // obtained from the authorize response
 *       'transactionId' => $authorizeResponse->getTransactionId(),
 *   ))->send();
 *
 *   if ($captureResponse->isSuccessful()) {
 *       // these are the same as they were on the authorize response, because it is the
 *       // same transaction
 *       echo "Transaction id: " . $captureResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $captureResponse->getTransactionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 * </code>
 */
class AuthorizeRequest extends AbstractRequest
{
    /**
     * @return AuthorizeRequest
     */
    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('minChargebackProbability', $parameters)) {
            $parameters['minChargebackProbability'] = self::DEFAULT_MIN_CHARGEBACK_PROBABILITY;
        }
        parent::initialize($parameters);

        return $this;
    }

    /**
     * The object type on which the API call will be made
     *
     * @return string
     */
    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'auth';
    }

    /**
     * Get data for request
     *
     * @return array<string, mixed>
     */
    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $amount = $this->getAmount();
        $items = $this->getItems();
        if (empty($amount) && empty($items)) {
            throw new InvalidRequestException('Either the amount or items parameter is required.');
        }

        // if it's not an update, the customer must be specified
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$this->isUpdate() && empty($customerId) && empty($customerReference)) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $transaction = $this->buildTransaction($paymentMethodType);

        $data = array();
        $data['transaction'] = $transaction;
        $data['action'] = $this->getFunction();
        // default, all transactions will succeed
        $data['minChargebackProbability'] = $this->getMinChargebackProbability();
        $data['sendEmailNotification'] = false;
        $data['campaignCode'] = null;
        $data['dryrun'] = false;

        return $data;
    }
}
