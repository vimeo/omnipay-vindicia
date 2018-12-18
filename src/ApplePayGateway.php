<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia ApplePay Gateway
 * This Apple Pay Gateway provides the functionality to authorize, complete authorize and capture
 * Apple Pay payments for Apple Pay on the web.
 * 
 * // TODO: Add documentation about functionality for capture and completeAuthorize methods to capture and
 * // authorize payments.
 * 
 * Apple Pay is available on all iOS devices with a Secure Element—an industry-standard, certified
 * chip designed to store payment information safely. On macOS, users must have anApple Pay-capable
 * iPhone or Apple Watch to authorize the payment, or a MacBook Pro
 * with Touch ID.
 *
 * You use the same username and password as you do with the regular Vindicia gateway.
 *
 * Example:
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
 *    // To request the payment session pass the required Apple Pay options to the authorize method and send it.
 *    $authorizeResponse = $gateway->authorize(array(
 *        'validationURL' => 'validationURL_from_frontend',
 *        'displayName' => 'name_of_product',
 *        'merchantIdentifier' => 'merchant.com.example',
 *        'applicationType' => 'web',
 *        'applicationUrl' => 'mystore.com'
 *    ))->send();
 *
 *     // You can ensure the response is successful before sending it to the front end.
 *    if ($authorizeResponse->isSuccessful()) {
 *        echo "Status Reason: " . $authorizeResponse->getReason() . PHP_EOL;
 *        echo "Status Code: " . $authorizeResponse->getStatusCode() . PHP_EOL;
 *        echo "Expiration time: " . $authorizeResponse->getApplePaySessionExpirationTimeStamp() . PHP_EOL;
 *        echo "Apple Pay Payment Session Object: " . $authorizeResponse->getApplePayPaymentSessionObject() 
 *          . PHP_EOL;
 *    } else {
 *        // Error handling.
 *        echo "ERROR: Request not successful.";
 *        echo "Status Code: " . $authorizeResponse->getStatusCode() . PHP_EOL;
 *        echo "Status Reason: " . $authorizeResponse->getReason() . PHP_EOL;
 *    }
 *
 *     // TODO: Add functionality for completeAuthorize() and capture().
 *     // An opaque Apple Pay Session is returned as a response (expires after 5 mins) and it can be sent to
 *     // the front end to fully load the payment sheet. This allows the user to optionally configure their
 *     // payment option and shipping methods (if needed) and submit their payment.
 *     $apple_pay_session = $authorizeResponse->getPaymentSessionObject();
 * </code>
 *
 *
 * For further code examples see the *omnipay-example* repository on github.
 * @see GatewayInterface
 * For more information on Apple Pay JS API visit:
 * @link https://developer.apple.com/documentation/apple_pay_on_the_web
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
     * @param string $value
     * @return static
     */
    public function setKeyCertPassword($value)
    {
        return $this->setParameter('keyCertPassword', $value);
    }

    /**
     * Makes request to Apple to set up session between Vimeo and Apple.
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
     *
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function completeAuthorize(array $parameters = array())
    {
        /**
         * @var  \Omnipay\Vindicia\Message\AuthorizeRequest
         */
        return $this->createRequest('\Omnipay\Vindicia\Message\AuthorizeRequest', $parameters);
    }

    /**
     * Capture an Apple Pay purchase.
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

    // see AbstractVindiciaGateway for more functions and documentation
}
