<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Message\ResponseInterface;

/**
 * Retrieve an Apple Pay Payment Session object.
 *
 * Post a request to Apple Pay server's Payment Session endpoint using two-way TLS.
 * The response to this request is an Apple Pay session object – it expires after five minutes.
 * - for Apple Pay on the web, you pass the session object to the completion message
 * completeMerchantValidation.
 * - for Apple Pay in Business Chat, you pass the session object to your Customer Service Platform
 * (CSP), which handles communicating with Business Chat on your behalf.
 *
 * Parameters:
 * // Set by the gateway.
 * - pemCertPath: Path to the cert of the split Merchant Identity certification,
 * associated with your merchantID needed to make the call.
 * - keyCertPath: Path to the key cert of the split Merchant Identity certification,
 * associated with your merchantID needed to make the call.
 * - keyCertPassword: Key certification password for the key certification.
 * // Set by this request.
 * - validationUrl: Validation URL received from session.onvalidatemerchant on the client.
 * - merchantIdentifier: Your merchant ID provided by Apple.
 * - displayName: A name for your store, suitable for display, appears in the touch bar.
 * - applicationType: The "initiative" parameter depending on the type of application:
 *   For Apple Pay on the web, use "web". For Business Chat, use "messaging".
 * - applicationUrl: The initiative context is based on the value supplied for the applicationType/initiative:
 *   For Apple Pay on the web, provide your fully qualified domain name associated with your
 *   Apple Pay Merchant Identity Certificate. For Business Chat, pass your payment gateway URL.
 *   Example: $this->setApplicationUrl('example.com');
 *
 *
 * See @link for more details.
 *
 * Example:
 *
 * <code>
 *   // In your Javascript frontend code:
 *      // Validating merchant and retieving validationUrl.
 *      session.onvalidatemerchant = (event) => {
 *      const validationURL = event.validationURL;
 *      // pass your validationUrl to the backend in a function here.
 *      getApplePaySessionObject(event.validationURL).then(function(response) {
 *           session.completeMerchantValidation(response);
 *       });
 *
 *   // In your backend getApplePaySessionObject function.
 *   $gateway = \Omnipay\Omnipay::create('Vindicia ApplePay');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setPemCertPath('./certs/path_to_cert.crt.pem');
 *   $gateway->setKeyCertPath('./certs/path_to_key_cert.key.pem');
 *   $gateway->setKeyCerPassword('y0ur_key_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // authorize the request and get an Apple Pay Session object.
 *   $ApplePaySessionObject = $gateway->authorize(array(
 *       // example validation Url
 *       "validationUrl" => "https://apple-pay-gateway-cert.apple.com/paymentservices/startSession",
 *       "merchantIdentifier" => "merchant.com.example",
 *       "displayName" => "MyStore",
 *       "applicationType" => "type", // initiative: "web" or "messaging"
 *       "applicationUrl" => "mystore.example.com" // initiativeContext:  domain name or payment gateway URL
 *       )
 *   ))->send();
 *
 * Pass this object back to the client to validate your merchant and continue with an Apple Pay payment.
 * </code>
 *
 * @see \Omnipay\Stripe\Gateway
 * @link https://developer.apple.com/documentation/apple_pay_on_the_web/apple_pay_js_api
 */
class ApplePayAuthorizeRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\ApplePayAuthorizeResponse';

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
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
     * @return Omnipay\Common\Message\AbstractRequest
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
     * A certificate associated with your merchant ID.
     * Example:
     * setPemCertPath('./certs/path_to_cert.key.pem');
     *
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
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
     * Password associated with your key certificate, if applicable.
     *
     * @param string $url
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setKeyCertPassword($value)
    {
        return $this->setParameter('keyCertPassword', $value);
    }


    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getParameter('validationUrl');
    }

    /**
     * Set the validation URL for the request
     *
     * @param string $url
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setValidationUrl($url)
    {
        return $this->setParameter('validationUrl', $url);
    }

    /**
     * @return null|string
     */
    public function getMerchantIdentifier()
    {
        return $this->getParameter('merchantIdentifier');
    }

    /**
     * Set the merchant identifier for the request
     *
     * @param string $url
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setMerchantIdentifier($value)
    {
        return $this->setParameter('merchantIdentifier', $value);
    }

    /**
     * @return null|string
     */
    public function getDisplayName()
    {
        return $this->getParameter('displayName');
    }

    /**
     * Sets the display name for the request
     *
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setDisplayName($value)
    {
        return $this->setParameter('displayName', $value);
    }

    /**
     * @return null|string
     */
    public function getApplicationType()
    {
        return $this->getParameter('applicationType');
    }

    /**
     * Sets the application type or the "initiative" for the request
     *
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setApplicationType($value)
    {
        return $this->setParameter('applicationType', $value);
    }

    /**
     * @return null|string
     */
    public function getApplicationUrl()
    {
        return $this->getParameter('applicationUrl');
    }

    /**
     * Sets the application url or the "initiativeContext" for the request
     *
     * @param string $value
     * @return Omnipay\Common\Message\AbstractRequest
     */
    public function setApplicationUrl($value)
    {
        return $this->setParameter('applicationUrl', $value);
    }

    /**
     * Get HTTP Method.
     *
     * This is nearly always POST but can be over-ridden in sub classes.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * @return array
     */
    public function getData()
    {
        // We must have the validation URL set. If it isn't, don't make the call.
        $this->validate('validationUrl');
        $data = array();

        // Required request parameters for Apple Session object.
        $data['merchantIdentifier'] = $this->getMerchantIdentifier();
        $data['displayName'] = $this->getDisplayName();
        $data['initiative'] = $this->getApplicationType();
        $data['initiativeContext'] = $this->getApplicationUrl();

        return $data;
    }

    /**
     * @param       $data
     * @param array $headers
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    protected function createClientRequest($data, array $headers = null)
    {
        $pemCertPath = $this->getPemCertPath();
        $keyCertPath = $this->getKeyCertPath();
        $keyCertPassword = $this->getKeyCertPassword();

        // Setting options for the request.
        $config                            = $this->httpClient->getConfig();
        $curlOptions                       = $config->get('curl.options');
        $curlOptions[CURLOPT_SSLVERSION]   = 6;
        $curlOptions[CURLOPT_SSLCERT]      = $pemCertPath;
        $curlOptions[CURLOPT_SSLKEY]       = $keyCertPath;
        $curlOptions[CURLOPT_SSLKEYPASSWD] = $keyCertPassword;
        $curlOptions[CURLOPT_POST]         = 1;

        $config->set('curl.options', $curlOptions);
        $this->httpClient->setConfig($config);

        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            function ($event) {
                if ($event['response']->isClientError()) {
                    $event->stopPropagation();
                }
            }
        );

        // Creating the request to be sent.
        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getValidationUrl(),
            $headers,
            // ApplePayJs requires that the data be a json object.
            json_encode($data)
        );

        return $httpRequest;
    }

    /**
     * Overriding AbstractRequest::sendData() so that we can make a REST call instead of a SOAP call.
     * @param array $data
     * @return Response
     */
    public function sendData($data)
    {
        $headers = array("Content-Type" => "application/json",
        "Accept" => "application/json");

        // Create a request object to be sent.
        $httpRequest  = $this->createClientRequest($data, $headers);
        $httpResponse = $httpRequest->send();

        $statusCode = array(
            'statusCode' => $httpResponse->getStatusCode(),
            // A human readable version of the numeric status code.
            'reason' => $httpResponse->getReasonPhrase()
        );

        // Assemble the response..
        try {
            $message = $httpResponse->json();
            $response = array_merge(
                $statusCode,
                $message
            );
        // If you try to parse an empty response body, error will be thrown.
        } catch (\RunTimeException $e) {
            $response = array_merge(
                $statusCode,
                array()
            );
        }

        $this->response = new static::$RESPONSE_CLASS($this, $response);
        return $this->response;
    }
}
