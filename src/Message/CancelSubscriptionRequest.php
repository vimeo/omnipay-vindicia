<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Cancel a subscription.
 *
 * Parameters:
 * - subscriptionId: Your identifier for the subscription to be canceled. Either subscriptionId
 * or subscriptionReference is required.
 * - subscriptionReference: The gateway's identifier for the subscription to be canceled. Either
 * subscriptionId or subscriptionReference is required.
 * - cancelReason: The reason that the subscription was canceled (optional). Possible values are
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
 *   $cancelResponse = $gateway->cancelSubscription(array(
 *       // identify the subscription to cancel. could do this by subscriptionReference as well
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId()
 *   ))->send();
 *
 *   if ($cancelResponse->isSuccessful()) {
 *       // same id as when it was created
 *       echo "Subscription id: " . $cancelResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $cancelResponse->getSubscriptionReference() . PHP_EOL;
 *       echo "Subscription status: " . $cancelResponse->getSubscriptionStatus() . PHP_EOL;
 *   }
 *
 * </code>
 */
class CancelSubscriptionRequest extends AbstractRequest
{
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
     * The name of the function to be called in Vindicia's API
     *
     * @vreturn string
     * @return  string
     */
    protected function getFunction()
    {
        return 'cancel';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData()
    {
        $subscription = new stdClass();
        $subscription->merchantAutoBillId = $this->getSubscriptionId();
        $subscription->VID = $this->getSubscriptionReference();

        if (!isset($subscription->merchantAutoBillId) && !isset($subscription->VID)) {
            throw new InvalidRequestException('Either the subscription id or reference is required.');
        }

        return array(
            'autobill' => $subscription,
            'action' => $this->getFunction(),
            'disentitle' => false,
            'force' => true,
            'settle' => false,
            'sendCancellationNotice' => false,
            'cancelReasonCode' => $this->getCancelReason()
        );
    }
}
