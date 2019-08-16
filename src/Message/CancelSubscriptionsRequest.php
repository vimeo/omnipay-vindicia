<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Cancel multiple subscriptions for a user. Can cancel specified subscriptions
 * or all subscriptions.
 *
 * If you only need to cancel one subscription, you can use the CancelSubscriptionRequest.
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
 * - cancelReason: The reason that the subscriptions were canceled (optional). Possible values are
 * documented by Vindicia here. Only the reason code needs to be specified:
 * https://www.vindicia.com/documents/1800ProgGuideHTML5/Default.htm#ProgGuide/Canceling_AutoBills\
 * _with.htm%3FTocPath%3DCashBox1800ProgGuide%7C5%2520Working%2520with%2520AutoBills%7C5.3%2520\
 * Canceling%2520AutoBills%7C_____1
 *
 * Example:
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
 *       'planId' => $planResponse->getPlanId() // not necessary since it's already on the product
 *   ))->send();
 *
 *   if ($subscriptionResponse->isSuccessful()) {
 *       echo "Subscription id: " . $subscriptionResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $subscriptionResponse->getSubscriptionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now we want to cancel the subscription
 *   $response = $gateway->cancelSubscriptions(array(
 *       // the customer we're canceling subscriptions for
 *       'customerId' => $customerResponse->getCustomerId(),
 *       // If they have multiple subscriptions, any number of them can be specified in this array.
 *       // Or this parameter can be left off entirely to cancel all of their subscriptions.
 *       'subscriptionIds' => array($subscriptionResponse->getSubscriptionId())
 *   ))->send();
 *
 *   if ($response->isSuccessful()) {
 *       echo "Customer id: " . $response->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $response->getCustomerReference() . PHP_EOL;
 *   }
 *
 * </code>
 */
class CancelSubscriptionsRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @vreturn string
     * @return  string
     */
    protected function getFunction()
    {
        return 'stopAutoBilling';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    /**
     * Get the subscription ids to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @return array<string>|null
     */
    public function getSubscriptionIds()
    {
        return $this->getParameter('subscriptionIds');
    }

    /**
     * Set the subscription ids to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @param array<string>|null $value
     * @return static
     */
    public function setSubscriptionIds($value)
    {
        return $this->setParameter('subscriptionIds', $value);
    }

    /**
     * Get the subscription references to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @return array<string>|null
     */
    public function getSubscriptionReferences()
    {
        return $this->getParameter('subscriptionReferences');
    }

    /**
     * Set the subscription references to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @param array<string>|null $value
     * @return static
     */
    public function setSubscriptionReferences($value)
    {
        return $this->setParameter('subscriptionReferences', $value);
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

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;

        $subscriptions = null;
        $subscriptionIds = $this->getSubscriptionIds();
        $subscriptionReferences = $this->getSubscriptionReferences();
        if (!empty($subscriptionIds)) {
            $subscriptions = array();
            foreach ($subscriptionIds as $subscriptionId) {
                $subscription = new stdClass();
                $subscription->merchantAutoBillId = $subscriptionId;
                $subscriptions[] = $subscription;
            }
        } elseif (!empty($subscriptionReferences)) {
            $subscriptions = array();
            foreach ($subscriptionReferences as $subscriptionReference) {
                $subscription = new stdClass();
                $subscription->VID = $subscriptionReference;
                $subscriptions[] = $subscription;
            }
        }

        return array(
            'account' => $account,
            'autobills' => $subscriptions,
            'action' => $this->getFunction(),
            'force' => true,
            'disentitle' => false,
            'cancelReasonCode' => $this->getCancelReason()
        );
    }
}
