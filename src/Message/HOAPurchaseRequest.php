<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

/**
 * Initialize a HOA Web Session for an purchase request.
 *
 * Uses the same parameters as a regular purchase request, but you would not provide the
 * card details since those will come from the form.
 *
 * Additional parameters for the Web Session:
 * - returnUrl: The URL the customer should be sent to after form submission and a
 * successful authorization. Required.
 * - errorUrl: The URL the customer should be sent to after form submission if the
 * authorization fails. Defaults the the returnUrl.
 * - hoaAttributes: The attributes parameter will specify attributes on the transaction,
 * so if you wish to specify attributes on the Web Session, you can use hoaAttributes.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_HOA');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a customer (unlike many gateways, Vindicia requires a customer exist
 *   // before a transaction can occur)
 *   $customerResponse = $gateway->createCustomer(array(
 *       'name' => 'Test Customer',
 *       'email' => 'customer@example.com',
 *       'customerId' => '123456789'
 *   ))->send();
 *
 *   if ($customerResponse->isSuccessful()) {
 *       echo "Customer id: " . $customerResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $customerResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   $purchaseResponse = $gateway->purchase(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2),
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456789',
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'attributes' => array(
 *           'location' => 'FL'
 *       ),
 *       'returnUrl' => 'http://www.example.com/success',
 *       'errorUrl' => 'http://www.example.com/failure'
 *   ))->send();
 *
 *   if ($purchaseResponse->isSuccessful()) {
 *       echo "Web session reference: " . $purchaseResponse->getWebSessionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... Now the user is filling in the credit card form on your site. They click submit
 *   // and the form is submitted directly to Vindicia to make the purchase request. Vindicia
 *   // returns to your returnUrl, so now we're going to complete the purchase request:
 *
 *   $completeResponse = $gateway->complete(array(
 *       // use the webSessionReference from the authorize response to identify the session to complete
 *       'webSessionReference' => $purchaseResponse->getWebSessionReference()
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // @todo Haven't tested what's available yet
 *   } else {
 *       if ($completeResponse->getFailureType() === CompleteHOAResponse::REQUEST_FAILURE) {
 *           echo 'The HOA request itself failed!' . PHP_EOL;
 *       } else {
 *           // This case, identified by CompleteHOAResponse::METHOD_FAILURE, means that
 *           // although the HOA request succeeded, the method it called, such as authorize
 *           // or purchase, had an error.
 *           echo 'The HOA request succeeded, but the method it called failed!' . PHP_EOL;
 *       }
 *       echo 'Error message: ' . $completeResponse->getMessage() . PHP_EOL;
 *       // error handling
 *   }
 *
 *   // ta da!
 *
 * </code>
 */
class HOAPurchaseRequest extends HOAAuthorizeRequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\PurchaseRequest';

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue(
                'Transaction_AuthCapture_minChargebackProbability',
                $regularRequestData['minChargebackProbability']
            ),
            new NameValue(
                'Transaction_AuthCapture_sendEmailNotification',
                $regularRequestData['sendEmailNotification']
            ),
            new NameValue('Transaction_AuthCapture_campaignCode', $regularRequestData['campaignCode']),
            new NameValue('Transaction_AuthCapture_dryrun', $regularRequestData['dryrun']),
            new NameValue('Transaction_AuthCapture_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
            new NameValue('Transaction_AuthCapture_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy'])
        );
    }
}
