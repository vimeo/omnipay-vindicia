<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia HOA Gateway
 *
 * Hosted Order Automation (HOA) is Vindicia's solution for preventing credit card
 * details from ever hitting your server, thereby reducing your PCI compliance
 * oblications. In short, a "Web Session" is created and the form on your page is
 * redirected to post directly to Vindicia's servers. Special input names on the form
 * tell Vindicia what each input contains. Read more about it in Vindcia's documentation.
 *
 * The calls for this gateway work similarly to an offsite gateway, but they all use
 * the same "complete" function: Call authorize/purchase/etc to initialize the Web
 * Session, and then call complete on the page the user is returned to to complete
 * the Web Session.
 *
 * You use the same username and password as you do with the regular Vindicia
 * gateway.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_HOA');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   $purchaseResponse = $gateway->purchase(array(
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
 *   // ta da!
 *
 * </code>
 */
class HOAGateway extends AbstractVindiciaGateway
{
    /**
     * Get the gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Vindicia HOA';
    }

    /**
     * Initialize a HOA Web Session for an authorize request.
     *
     * See Message\HOAAuthorizeRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOAAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOAAuthorizeRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session for a purchase request.
     *
     * See Message\HOAPurchaseRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOAPurchaseRequest
     */
    public function purchase(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOAPurchaseRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOAPurchaseRequest', $parameters);
    }

    /**
     * Capture a previously authorized transaction. Since no card details are needed for a
     * capture, this is identical to a regular capture request.
     *
     * See Message\CaptureRequest for more details and Message\HOAAuthorizeRequest for an
     * example in HOA context.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\CaptureRequest
     */
    public function capture(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\CaptureRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\CaptureRequest', $parameters);
    }

    /**
     * Voids, or cancels, a previously authorized transaction. Will not work if the transaction
     * has already been captured, either by the capture function or purchase function. This is
     * identical to a regular void request.
     *
     * See Message\VoidRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\VoidRequest
     */
    public function void(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\VoidRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\VoidRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to collect new card details.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest
     */
    public function createPaymentMethod(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update card details.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters, true);
    }

    /**
     * Initialize a HOA Web Session to create a subscription.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOACreateSubscriptionRequest
     */
    public function createSubscription(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOACreateSubscriptionRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update a subscription.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\HOACreateSubscriptionRequest
     */
    public function updateSubscription(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\HOACreateSubscriptionRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters, true);
    }

    /**
     * Complete a HOA request.
     *
     * See Message\CompleteHOARequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\CompleteHOARequest
     */
    public function complete(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\CompleteHOARequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\CompleteHOARequest', $parameters);
    }

    // see AbstractVindiciaGateway for more functions and documentation
}
