<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

/**
 * Initialize a HOA Web Session for an authorize request.
 *
 * Uses the same parameters as a regular authorize request, but you would not provide the
 * card details since those will come from the form.
 *
 * As with a normal authorization, no money will be transfered until capture is called.
 * Use the purchase function to perform an authorization and capture in one request.
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
 *   $authorizeResponse = $gateway->authorize(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2),
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'attributes' => array(
 *           'location' => 'FL'
 *       ),
 *       'returnUrl' => 'http://www.example.com/success',
 *       'errorUrl' => 'http://www.example.com/failure'
 *   ))->send();
 *
 *   if ($authorizeResponse->isSuccessful()) {
 *       echo "Web session reference: " . $authorizeResponse->getWebSessionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... Now the user is filling in the credit card form on your site. They click submit
 *   // and the form is submitted directly to Vindicia to make the authorize request. Vindicia
 *   // returns to your returnUrl, so now we're going to complete the authorize request:
 *
 *   $completeResponse = $gateway->complete(array(
 *       // use the webSessionReference from the authorize response to identify the session to complete
 *       'webSessionReference' => $authorizeResponse->getWebSessionReference()
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // You can check what request was just completed:
 *       echo "Did we just complete an authorize web session? " . $completeResponse->wasAuthorize() . PHP_EOL;
 *       // transaction object:
 *       var_dump($completeResponse->getTransaction());
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
 *   // And now we can capture the transaction, just like a normal authorize
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
class HOAAuthorizeRequest extends AbstractHOARequest
{
    /**
     * @var string
     */
    protected static $REGULAR_REQUEST_CLASS = '\Omnipay\Vindicia\Message\AuthorizeRequest';

    protected function getObjectParamNames()
    {
        return array(self::$TRANSACTION_OBJECT => 'transaction');
    }

    /**
     * @return array<int, NameValue>
     */
    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        $values = array(
            new NameValue('Transaction_Auth_minChargebackProbability', $regularRequestData['minChargebackProbability']),
            new NameValue('Transaction_Auth_sendEmailNotification', $regularRequestData['sendEmailNotification']),
            new NameValue('Transaction_Auth_dryrun', $regularRequestData['dryrun'])
        );

        // adding this param with an empty value makes the web session crash
        if (!empty($regularRequestData['campaignCode'])) {
            $values[] = new NameValue('Transaction_Auth_campaignCode', $regularRequestData['campaignCode']);
        }

        return $values;
    }
}
