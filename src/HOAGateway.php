<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\AbstractGateway;

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
 * Example setup:
 * <code>
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_HOA');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 * </code>
 */
class HOAGateway extends AbstractGateway
{
    use SharedGatewayFunctions;

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
     * Uses the same parameters as a regular authorize request, but you would not provide the
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
     *   $response = $gateway->initializeHOAAuthorize(array(
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
     *   if ($response->isSuccessful()) {
     *       echo "Web session reference: " . $response->getWebSessionReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function authorize(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session for a purchase request.
     *
     * See the documentation for regular purchase and HOA authorize.
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOAPurchaseRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to collect new card details.
     *
     * See the documentation for regular createPaymentMethod. Uses the additional HOA parameters
     * specified in the documentation for HOA authorize.
     */
    public function createPaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update card details.
     *
     * See the documentation for regular updatePaymentMethod. Uses the additional HOA parameters
     * specified in the documentation for HOA authorize.
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $parameters, true);
    }

    /**
     * Initialize a HOA Web Session to create a subscription.
     *
     * See the documentation for createSubscription. Uses the additional HOA parameters
     * specified in the documentation for initializeHOAAuthorize.
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters);
    }

    /**
     * Initialize a HOA Web Session to update a subscription.
     *
     * See the documentation for regular updateSubscription. Uses the additional HOA parameters
     * specified in the documentation for HOA authorize.
     *
     * You should NOT change the currency of an existing subscription.
     */
    public function updateSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $parameters, true);
    }

    /**
     * Complete a HOA request.
     *
     * After you call one of the initializeHOA functions, this function should be called
     * on the page the user is returned to (specified by the returnUrl) to complete the request.
     *
     * Parameters:
     * - webSessionReference: The gateway's identifier for the Web Session. This is provided
     * by the response for an initializeHOA call and is used to specify which Web Session should
     * be completed. Required.
     *
     * Example:
     * <code>
     *   $response = $gateway->complete(array(
     *       'webSessionReference' => '1234567890abcdef1234567890abcdef'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       // @todo Haven't tested what's available yet
     *   } else {
     *       if ($response->getFailureType() === CompleteHOAResponse::REQUEST_FAILURE) {
     *           echo 'The HOA request itself failed!' . PHP_EOL;
     *       } else {
     *           // This case, identified by CompleteHOAResponse::METHOD_FAILURE, means that
     *           // although the HOA request succeeded, the method it called, such as authorize
     *           // or purchase, had an error.
     *           echo 'The HOA request succeeded, but the method it called failed!' . PHP_EOL;
     *       }
     *       echo 'Error message: ' . $response->getMessage() . PHP_EOL;
     *   }
     * </code>
     */
    public function complete(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CompleteHOARequest', $parameters);
    }
}
