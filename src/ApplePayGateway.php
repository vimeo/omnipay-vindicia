<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia ApplePay Gateway
 * This Apple Pay Gateway provides the functionality to authorize, complete authorize and capture
 * Apple Pay payments for Apple Pay on the web.
 * 
 * Apple Pay is available on all iOS devices with a Secure Element —– an industry-standard, certified
 * chip designed to store payment information safely. On macOS, users must have anApple Pay-capable
 * iPhone or Apple Watch to authorize the payment, or a MacBook Pro with Touch ID.
 *
 * You use the same username and password as you do with the regular Vindicia gateway.
 *
 * Parameters set by this gateway:
 * - pemCertPath: Path to the cert of the split Merchant Identity certification, associated with your
 * merchantID needed to make the call.
 * - keyCertPath: Path to the key cert of the split Merchant Identity certification, associated with
 * your merchantID needed to make the call.
 * - keyCertPassword: Key certification password for the key certification.
 * 
 * For further code examples see the *omnipay-example* repository on github.
 * @see GatewayInterface
 * For more information on Apple Pay JS API visit:
 * @link https://developer.apple.com/documentation/apple_pay_on_the_web
 *
 * Example:
 * In the front end, the user initiates an Apple Pay payment. Then the Apple Pay payment sheet is displayed
 * and partially loaded. The Apple Pay gateway makes the call to Apple's servers using the ApplePayAuthorizeRequest
 * object to retrieve an Apple Pay payment session from Apple's servers. Once the session is retrieved
 * (via the response object) you can pass it to the front end to fully load the payment sheet and accept user payment.
 *
 * After the Apple Pay payment sheet is fully loaded on the frontend. The user can authorize a payment using
 * Touch or Face ID –– this will grant access to the ApplePayPaymentToken. The Apple Pay gateway can authorize 
 * a payment using the ApplePayCompleteAuthorizeRequest with the ApplePayPaymentToken passed in –– no money will 
 * be transferred during this step. If the response is successful, the gateway then makes a capture call using 
 * CaptureRequest to capture a payment and money will be received.
 *
 * <code>
 *    // Setup the gateway with your username and password for Vindicia.
 *    // Include the paths to your Apple Pay Merchant Identity cert, key cert and key cert password.
 *    $gateway = \Omnipay\Omnipay::create('Vindicia_ApplePay');
 *    $gateway->setUsername('your_username');
 *    $gateway->setPassword('y0ur_p4ssw0rd');
 *    $gateway->setPemCertPath('./certs/path_to_cert.crt.pem');
 *    $gateway->setKeyCertPath('./certs/path_to_cert.key.pem');
 *    $gateway->setKeyCertPassword('your_key_cert_password');
 *    $gateway->setTestMode(false);
 *
 *    // User clicks or taps an Apple Pay button. The payment sheet is partially loaded. So now Apple
 *    // can validate your merchant identity and you can receive a session object to fully load the
 *    // payment sheet to accept payment.
 *
 *    // To request the payment session pass the required Apple Pay options to the authorize method
 *    // and send the request. An AbstractResponse object will be returned.
 *    $authorizeResponse = $gateway->authorize(array(
 *        // sandbox validation Url
 *        'validationURL' => 'https://apple-pay-gateway-cert.apple.com/paymentservices/startSession',
 *        'merchantIdentifier' => 'merchant.com.example',
 *        'displayName' => 'MyStore',
 *        'applicationUrl' => 'mystore.com' // initiativeContext: the domain name of your site.
 *    ))->send();
 *
 *    // getApplePayPaymentSessionObject() can then be called to retrieve the ApplePayPaymentSession object.
 *    // You can ensure the response is successful before sending it to the front end. If there is an error the
 *    // response body will be empty.
 *    if ($authorizeResponse->isSuccessful()) {
 *        echo 'Status Code: ' . $authorizeResponse->getMessage() . PHP_EOL;
 *        echo 'Status Reason: ' . $authorizeResponse->getMessage() . PHP_EOL;
 *        echo 'Apple Pay Payment Session Object: ' . $authorizeResponse->getPaymentSessionObject()
 *          . PHP_EOL;
 *        echo 'Response: ' . $authorizeResponse->getResponse() . PHP_EOL;
 *    } else {
 *        // Error handling.
 *        echo 'ERROR: Request not successful.';
 *        echo 'Status Code: ' . $authorizeResponse->getCode() . PHP_EOL;
 *        echo 'Status Message: ' . $authorizeResponse->getMessage() . PHP_EOL;
 *    }
 *
 *    // Pass authorizeResponse back to the client to validate your merchant and continue with an Apple Pay payment.
 *    // If successful, the payment sheet should be fully loaded.
 *    $apple_pay_session = $authorizeResponse->getPaymentSessionObject();
 *
 *     // An opaque Apple Pay Session is returned as a response (expires after 5 mins) and it can be sent to
 *     // the front end to fully load the payment sheet. This allows the user to optionally configure their
 *     // payment option and shipping methods (if needed) and submit their payment.
 *
 *    // After the user authorizes an Apple Pay payment on the payment sheet using Touch or Face ID on the front end, 
 *    // parse the ApplePayPayment object to retrieve the token.
 *    // Pass the extracted 'token' to the 'applePayToken' parameter of the ApplePayCompleteAuthorizeRequest class.
 *    // You may use other fields in the ApplePayPayment object to fill out billing or shipping info.
 *    $completeAuthorizeResponse = $gateway->completeAuthorize(array(
 *        'applePayToken' => $apple_pay_payment_session_object['token'];
 *        // Params needed to authorize a payment can go here as well.
 *        'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2)
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'card' => array(
 *          'address1'   => $data->getAddress1(),
            'address2'   => $data->getAddress2(),
            'city'       => $data->getCity(),
            'state'      => $data->getState(),
            'postcode'   => $data->getPostalCode(),
            'country'    => $data->getCountry()
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card, which will
 *                                         // be attached to the customer's account
 *       'attributes' => array(
 *           'location' => 'FL'
 *       )
 *    ))->send();
 *
 *    if ($completeAuthorizeResponse->isSuccessful()) {
 *        // Note: Your transaction ID begins with a prefix you specified in your initial
 *        // Vindicia configuration. The ID is automatically assigned by Vindicia.
 *        echo "Transaction id: " . $completeAuthorizeResponse->getTransactionId() . PHP_EOL;
 *        echo "Transaction reference: " . $completeAuthorizeResponse->getTransactionReference() . PHP_EOL;
 *        echo "The transaction risk score is: " . $completeAuthorizeResponse->getRiskScore();
 *    } else {
 *       // error handling
 *    }
 *
 *    // If the authorization is successful, you may now capture a payment using the transaction ID.
 *    // Money is transferred during this call.
 *    $captureResponse = $gateway->capture(array(
 *        // You can identify the transaction by the transactionId or transactionReference
 *        // obtained from the authorize response
 *        'transactionId' => $completeAuthorizeResponse->getTransactionId(),
 *    ))->send();
 *
 *   if ($captureResponse->isSuccessful()) {
 *        // these are the same as they were on the authorize response, because it is the
 *        // same transaction
 *        echo "Transaction id: " . $captureResponse->getTransactionId() . PHP_EOL;
 *        echo "Transaction reference: " . $captureResponse->getTransactionReference() . PHP_EOL;
 *    } else {
 *        // error handling
 *    }
 *
 * </code>
 */
class ApplePayGateway extends AbstractVindiciaGateway
{

    /**
     * Get the gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Vindicia ApplePay';
    }

    /**
     * @return null|string
     */
    public function getPemCertPath()
    {
        return $this->getParameter('pemCertPath');
    }

    /**
     * A certificate associated with your merchant ID.
     * Example:
     * setPemCertPath('./certs/path_to_cert.crt.pem');
     * 
     * @param string $value
     * @return static
     */
    public function setPemCertPath($value)
    {
        return $this->setParameter('pemCertPath', $value);
    }

    /**
     * @return null|string
     */
    public function getKeyCertPath()
    {
        return $this->getParameter('keyCertPath');
    }

    /**
     * A key certificate associated with your merchant ID.
     * Example:
     * setKeyCertPath('./certs/path_to_cert.key.pem');
     * 
     * @param string $value
     * @return static
     */
    public function setKeyCertPath($value)
    {
        return $this->setParameter('keyCertPath', $value);
    }

    /**
     * @return null|string
     */
    public function getKeyCertPassword()
    {
        return $this->getParameter('keyCertPassword');
    }

    /**
     * Password associated with your key certificate.
     * Can be omitted if a password isn't set.
     * 
     * @param string $value
     * @return static
     */
    public function setKeyCertPassword($value)
    {
        return $this->setParameter('keyCertPassword', $value);
    }

    /**
     * Makes request to Apple to set up session between Vimeo and Apple.
     * See ApplePayAuthorizeRequest for parameter examples.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\ApplePayAuthorizeRequest
     */
    public function authorize(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\ApplePayAuthorizeRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest', $parameters);
    }

    /**
     * Authorize an Apple Pay purchase.
     * See ApplePayCompleteAuthorizeRequest for parameter examples.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\ApplePayCompleteAuthorizeRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        /**
         * @var  \Omnipay\Vindicia\Message\ApplePayCompleteAuthorizeRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\ApplePayCompleteAuthorizeRequest', $parameters);
    }

    /**
     * Capture an Apple Pay purchase.
     * See CaptureRequest for parameter examples.
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
     * Update a payment method.
     *
     * See Message\CreatePaymentMethodRequest for more details.
     *
     * @param array $parameters
     * @return \Omnipay\Vindicia\Message\CreatePaymentMethodRequest
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        /**
         * @var \Omnipay\Vindicia\Message\CreatePaymentMethodRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $parameters, true);
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

    // see AbstractVindiciaGateway for more functions and documentation
}
