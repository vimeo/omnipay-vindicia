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
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session for a purchase request.
     *
     * See Message\HOAPurchaseRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function purchase(array $parameters = array())
    {
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
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function capture(array $parameters = array())
    {
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
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function void(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\VoidRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to collect new card details.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update card details.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters, true);
    }

    /**
     * Initialize a HOA Web Session to create a subscription.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update a subscription.
     *
     * See Message\HOACreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function updateSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters, true);
    }

    /**
     * Complete a HOA request.
     *
     * See Message\CompleteHOARequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function complete(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CompleteHOARequest', $parameters);
    }

    // see AbstractVindiciaGateway for more functions and documentation
}
