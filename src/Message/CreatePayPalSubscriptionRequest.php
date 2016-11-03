<?php

namespace Omnipay\Vindicia\Message;

/**
 * Create a subscription through PayPal. A redirect URL will be returned to send
 * the user to PayPal's website.
 *
 * Uses all the same parameters as a regular create subscription request. A card does not need to
 * be specified, but you may choose to in order to provide address or customer name information.
 * Providing a card number is meaningless and it will be ignored. See Message\CreateSubscriptionRequest
 * for parameter details.
 *
 * Requires two additional parameters:
 * - returnUrl: The URL to which the user should be redirected after they complete the
 * purchase on PayPal's site.
 * - cancelUrl: The URL to which the user should be redirected if they cancel their purchase
 * on PayPal's site.
 *
 * This can also be used to update a subscription if called via updateSubscription. See
 * Message\CreateSubscriptionRequest for the differences between a create and update and an example.
 * Remember, you should NOT change the currency of an existing subscription.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_PayPal');
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
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'subscriptionId' => '111111', // you choose this
 *       'productId' => $productResponse->getProductId(),
 *       'planId' => $planResponse->getPlanId(), // not necessary since it's already on the product
 *       'returnUrl' => 'http://www.example.com/order_summary,
 *       'cancelUrl' => 'http://www.example.com/cart
 *   ))->send();
 *
 *   if ($subscriptionResponse->isSuccessful()) {
 *       echo "Subscription id: " . $subscriptionResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $subscriptionResponse->getSubscriptionReference() . PHP_EOL;
 *       echo "Redirecting to: " . $subscriptionResponse->getRedirectUrl();
 *       $subscriptionResponse->redirect();
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... now the user is filling out info on PayPal's site. Then they get redirected back
 *   // to your site, where you will complete the subscription ...
 *
 *   // get the PayPal transaction reference from the URL
 *   $payPalTransactionReference = $_GET['vindicia_vid'];
 *
 *   // complete the subscription
 *   $completeResponse = $gateway->completeCreateSubscription(array(
 *       'success' => true,
 *       'payPalTransactionReference' => $payPalTransactionReference
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // these are the same subscription ids you saw after the purchase call
 *       echo "Subscription id: " . $completeResponse->getSubscriptionId() . PHP_EOL;
 *       echo "Subscription reference: " . $completeResponse->getSubscriptionReference() . PHP_EOL;
 *   }
 *
 */
class CreatePayPalSubscriptionRequest extends CreateSubscriptionRequest
{
    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $this->validate('returnUrl', 'cancelUrl');

        return parent::getData(self::PAYMENT_METHOD_PAYPAL);
    }

    /**
     * Overriding to provide a more precise return type
     * @return CreatePayPalSubscriptionResponse
     */
    public function send()
    {
        /**
         * @var CreatePayPalSubscriptionResponse
         */
        return parent::send();
    }

    /**
     * Use a special response object for PayPal subscription requests.
     *
     * @param object $response
     * @return CreatePayPalSubscriptionResponse
     */
    protected function buildResponse($response)
    {
        return new CreatePayPalSubscriptionResponse($this, $response);
    }
}
