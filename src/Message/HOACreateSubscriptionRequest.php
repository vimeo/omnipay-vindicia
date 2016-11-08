<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

/**
 * Initialize a HOA Web Session to create a subscription.
 *
 * Uses the same parameters as a regular create subscription request, but you would not
 * provide the card details since those will come from the form.
 *
 * This can also be used to update a subscription if called via updateSubscription.
 * If you're updating part of the subscription other than the card details, you could
 * just use the normal gateway and skip the Web Session. Remember, you should NOT change
 * the currency of an existing subscription.
 *
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_HOA');
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
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'subscriptionId' => '111111', // you choose this
 *       'productId' => $productResponse->getProductId(),
 *       'planId' => $planResponse->getPlanId(), // not necessary since it's already on the product
 *       'returnUrl' => 'http://www.example.com/success',
 *       'errorUrl' => 'http://www.example.com/failure'
 *   ))->send();
 *
 *   if ($subscriptionResponse->isSuccessful()) {
 *       echo "Web session reference: " . $subscriptionResponse->getWebSessionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... Now the user is filling in the credit card form on your site. They click submit
 *   // and the form is submitted directly to Vindicia to create the subscription. Vindicia
 *   // returns to your returnUrl, so now we're going to complete the create subscription request:
 *
 *   $completeResponse = $gateway->complete(array(
 *       // use the webSessionReference from the authorize response to identify the session to complete
 *       'webSessionReference' => $purchaseResponse->getWebSessionReference()
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // You can check what request was just completed:
 *       echo "Did we just complete a create subscription web session? "
 *            . $completeResponse->wasCreateSubscription() . PHP_EOL;
 *       // subscription object:
 *       var_dump($completeResponse->getSubscription());
 *       // values that were passed in the form:
 *       var_dump($completeResponse->getFormValues());
 *       echo "The transaction risk score is: " . $authorizeResponse->getRiskScore();
 *   } else {
 *       if ($completeResponse->isRequestFailure()) {
 *           echo 'The HOA request itself failed!' . PHP_EOL;
 *       } else {
 *           // This case means that although the HOA request succeeded, the method it called,
 *           // such as authorize or purchase, had an error. Also identifiable by
 *           // $completeResponse->isMethodFailure()
 *           echo 'The HOA request succeeded, but the method it called failed!' . PHP_EOL;
 *       }
 *       echo 'Error message: ' . $completeResponse->getMessage() . PHP_EOL;
 *       // error handling
 *   }
 *
 * </code>
 */
class HOACreateSubscriptionRequest extends AbstractHOARequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\CreateSubscriptionRequest';

    protected function getObjectParamNames()
    {
        return array(self::$SUBSCRIPTION_OBJECT => 'autobill');
    }

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue(
                'AutoBill_Update_immediateAuthFailurePolicy',
                $regularRequestData['immediateAuthFailurePolicy']
            ),
            new NameValue('AutoBill_Update_validateForFuturePayment', $regularRequestData['validateForFuturePayment']),
            new NameValue('AutoBill_Update_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
            new NameValue('AutoBill_Update_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy']),
            new NameValue('AutoBill_Update_campaignCode', $regularRequestData['campaignCode']),
            new NameValue('AutoBill_Update_dryrun', $regularRequestData['dryrun']),
            new NameValue('AutoBill_Update_cancelReasonCode', $regularRequestData['cancelReasonCode']),
            new NameValue('AutoBill_Update_minChargebackProbability', $regularRequestData['minChargebackProbability'])
        );
    }
}
