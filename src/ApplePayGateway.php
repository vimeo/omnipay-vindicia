<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia ApplePay Gateway
 * This Apple Pay Gateway provides the functionality to retrieve an opaque payment session object from Apple.
 * 
 * Apple Pay on the web:
 * Apple Pay is available on all iOS devices with a Secure Element—an industry-standard, certified chip
 * designed to store payment information safely. On macOS, users must have an Apple Pay-capable iPhone or Apple Watch
 * to authorize the payment, or a MacBook Pro with Touch ID.
 *
 * Business Chat:
 * Business Chat let's customers chat directly with your business using the Messages app on their device.
 * through their CSP provider that implments a REST API to enables you to request a payment through Apple Pay.
 *
 * You use the same username and password as you do with the regular Vindicia
 * gateway.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_ApplePay');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setPemCertPath('./certs/path_to_cert.crt.pem');
 *   $gateway->setKeyCertPath('./certs/path_to_cert.key.pem');
 *   $gateway->setKeyCertPassword('your_key_cert_password');
 *   $gateway->setTestMode(false);
 *
 *   // now we start the authorize process
 *   $authorizeResponse = $gateway->authorize(array(
 *      'validationURL' => 'validationurl_from_frontend',
 *      'displayName' => 'name_of_product',
 *      'merchantIdentifier' => 'merchant.com.example',
 *      'applicationType' => 'web',
 *      'applicationUrl' => 'mystore.com'
 *   ))->send();
 *
 *   // The Apple Pay Payment Session session (expires after 5 mins)
 *   if ($authorizeResponse->isSuccessful()) {
 *       echo "Status Reason: " . $authorizeResponse->getReason() . PHP_EOL;
 *       echo "Status Code: " . $authorizeResponse->getStatusCode() . PHP_EOL;
 *       echo "Expiration time: " . $authorizeResponse->getApplePaySessionExpirationTimeStamp() . PHP_EOL;
 *       echo "Apple Pay Payment Session Object: " . $authorizeResponse->getApplePayPaymentSessionObject(). PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 * For further code examples see the *omnipay-example* repository on github.
 *
 * @see GatewayInterface
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
