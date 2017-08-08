<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Creates or updates a subscription (recurring payment), depending on whether it's
 * called from createSubscription or updateSubscription.
 *
 * If you are updating a subscription, you should NOT change its currency.
 *
 * Takes the same parameters as authorize or purchase except it does not use the amount
 * parameter. The amount is determined by the prices on the product plus the currency on
 * the subscription. See Message\Authorize for parameter details.
 *
 * Also uses the following parameters:
 * - subscriptionId: Your identifier to represent this subscription. Unlike in the authorize
 * and purchase requests, Vindicia does not automatically create an ID for the subscription,
 * so you must specify it.
 * - productId: Your identifier for the product that the customer is subscribing to. Either
 * the productId or productReference is required. The product defines the prices.
 * - productReference: The gateway's identifier for the product that the customer is
 * subscribing to. Either the productId or productReference is required.
 * - planId: Your identifier for the plan defining the subscription's characteristics (eg,
 * frequency of billing). A billing plan specified here will take precedence over one specified
 * on the product.
 * - planReference: The gateway's identifier for the plan defining the subscription's
 * characteristics (eg, frequency of billing). A billing plan specified here will take
 * precedence over one specified on the product.
 * - startTimestamp: The time to start billing. If not specified, defaults to the current
 * time. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 * - billingDay: the day of the month when the next charge will be issue, defaults to the day
 * of `startTimeStamp`
 * - shouldAuthorize: Whether an authorization should be performed on the card before creating
 * the subscription. If true and the authorization fails, the subscription creation will fail.
 * Defaults to false.
 *
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a plan to govern the behavior of the subscription (eg billing frequency)
 *   $planResponse = $gateway->createPlan(array(
 *       'planId' => '123456789', // you choose this
 *       'interval' => 'week',
 *       'intervalCount' => 2,
 *       'prices' => array(
 *           'USD' => '9.99',
 *           'CAD' => '12.99'
 *       )
 *   ))->send();
 *
 *   if ($planResponse->isSuccessful()) {
 *       echo "Plan id: " . $planResponse->getPlanId() . PHP_EOL;
 *       echo "Plan reference: " . $planResponse->getPlanReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // create a product using that plan for the user to subscribe to
 *   $productResponse = $gateway->createProduct(array(
 *       'productId' => '123456789', // you choose this
 *       'planId' => $planResponse->getPlanId(), // assign the plan you created above to it
 *       'statementDescriptor' => 'Statement descriptor',
 *       'duplicateBehavior' => CreateProductRequest::BEHAVIOR_SUCCEED_IGNORE,
 *       'prices' => array(
 *           'USD' => '9.99',
 *           'CAD' => '12.99'
 *       )
 *   ))->send();
 *
 *   if ($productResponse->isSuccessful()) {
 *       echo "Product id: " . $productResponse->getProductId() . PHP_EOL;
 *       echo "Product reference: " . $productResponse->getProductReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // finally we can create the subscription!
 *   $subscriptionResponse = $gateway->createSubscription(array(
 *       'currency' => 'GBP',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'subscriptionId' => '111111', // you choose this
 *       'productId' => $productResponse->getProductId(),
 *       'planId' => $planResponse->getPlanId(), // not necessary since it's already on the product
 *       'billingDay' => 15 // Day of the month when user gets charged must be between 1-31
 *   ))->send();
 *
 *   if ($subscriptionResponse->isSuccessful()) {
 *       echo "Subscription id: " . $subscriptionResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $subscriptionResponse->getSubscriptionReference() . PHP_EOL;
 *       echo "The transaction risk score is: " . $subscriptionResponse->getRiskScore();
 *   } else {
 *       // error handling
 *   }
 *
 *   // now maybe we want to update the subscription to switch it to a different card
 *   $updateResponse = $gateway->updateSubscription(array(
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-234567', // this ID will be assigned to the card
 *        // reference the subscription created above. you could also reference it by subscriptionReference:
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId()
 *       'billingDay' => 15 // Day of the month when user gets charged must be between 1-31
 *   ))->send();
 *
 *   if ($updateResponse->isSuccessful()) {
 *       // These are the same as from the original $subscriptionResponse, since it's the same subscription
 *       echo "Subscription id: " . $updateResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $updateResponse->getSubscriptionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // we could also update the subscription to used a saved payment method
 *   // you should not specify a card parameter in this case
 *   $updateResponse2 = $gateway->updateSubscription(array(
 *       'paymentMethodId' => '1234567', // this is the ID of the saved payment method we want to use
 *                                       // it could be a credit card or a PayPal payment method
 *        // reference the subscription created above. you could also reference it by subscriptionReference:
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId()
 *       'billingDay' => 15 // Day of the month when user gets charged must be between 1-31
 *   ))->send();
 *
 *   if ($updateResponse2->isSuccessful()) {
 *       // do stuff
 *   } else {
 *       // error handling
 *   }
 *
 * </code>
 */
class CreateSubscriptionRequest extends AuthorizeRequest
{
    /**
     * Returns whether an authorization will be performed to validate the card
     * before creating the subscription.
     *
     * @return null|bool
     */
    public function getShouldAuthorize()
    {
        return $this->getParameter('shouldAuthorize');
    }

    /**
     * Sets whether an authorization will be performed to validate the card
     * before creating the subscription.
     *
     * @param bool $value
     * @return static
     */
    public function setShouldAuthorize($value)
    {
        return $this->setParameter('shouldAuthorize', $value);
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'update';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData($paymentMethodType = null)
    {
        $subscriptionId = $this->getSubscriptionId();
        $subscriptionReference = $this->getSubscriptionReference();
        if (!$this->isUpdate()) {
            $this->validate('subscriptionId');
        } elseif (!$subscriptionId && !$subscriptionReference) {
            throw new InvalidRequestException(
                'Either the subscriptionId or subscriptionReference parameter is required.'
            );
        }

        $productId = $this->getProductId();
        $productReference = $this->getProductReference();
        if (!$productId && !$productReference) {
            throw new InvalidRequestException('Either the productId or productReference parameter is required.');
        }

        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $subscription = new stdClass();
        $subscription->billingStatementIdentifier = $this->getStatementDescriptor();
        $subscription->currency = $this->getCurrency();
        $subscription->merchantAutoBillId = $subscriptionId;
        $subscription->VID = $subscriptionReference;
        $subscription->sourceIp = $this->getIp();
        $subscription->startTimestamp = $this->getStartTime();
        $subscription->billingDay = $this->getBillingDay();
        $subscription->statementFormat = 'DoNotSend';

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;
        $account->name = $this->getName();
        $account->emailAddress = $this->getEmail();
        $subscription->account = $account;

        $plan = new stdClass();
        $plan->merchantBillingPlanId = $this->getPlanId();
        $plan->VID = $this->getPlanReference();
        $subscription->billingPlan = $plan;

        $product = new stdClass();
        $product->merchantProductId = $productId;
        $product->VID = $productReference;

        $item = new stdClass();
        $item->index = 0; //set the item index
        $item->product = $product;
        $subscription->items = array($item);

        $attributes = $this->getAttributes();
        if ($attributes) {
            $subscription->nameValues = $this->buildNameValues($attributes);
        }

        $subscription->paymentMethod = $this->buildPaymentMethod($paymentMethodType);

        return array(
            'autobill' => $subscription,
            'action' => $this->getFunction(),
            'immediateAuthFailurePolicy' => 'putAutoBillInRetryCycleIfPaymentMethodIsValid',
            'validateForFuturePayment' => $this->getShouldAuthorize() ?: false,
            'ignoreAvsPolicy' => false,
            'ignoreCvnPolicy' => false,
            'campaignCode' => null,
            'dryrun' => false,
            'cancelReasonCode' => null,
            'minChargebackProbability' => $this->getMinChargebackProbability()
        );
    }
}
