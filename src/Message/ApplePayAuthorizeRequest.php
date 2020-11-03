<?php

namespace Omnipay\Vindicia\Message;

use Exception;
use Omnipay\Common\Message\ResponseInterface;
use Guzzle\Common\Event;
use PaymentGatewayLogger\Event\Constants;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Retrieve an Apple Pay Payment Session object.
 *
 * Post a request to Apple Pay server's Payment Session endpoint using two-way TLS.
 * The response to this request is an Apple Pay session object – it expires after five minutes.
 * Pass the session object to the completion function completeMerchantValidation in the front end
 * to verify the merchant.
 *
 * Parameters set by the gateway and this class:
 * - pemCertPath: Path to the cert of the split Merchant Identity certification, associated with your
 * merchantID needed to make the call.
 * - keyCertPath: Path to the key cert of the split Merchant Identity certification, associated with
 * your merchantID needed to make the call.
 * - keyCertPassword: Key certification password for the key certification.
 *
 * Parameters set by this request:
 * - validationUrl: Validation URL received from session.onvalidatemerchant on the client.
 * - merchantIdentifier: Your merchant ID provided by Apple.
 * - displayName: A name for your store, suitable for display, appears in the touch bar.
 * - applicationUrl: Provide your fully qualified domain name associated with your Apple Pay
 *   Merchant Identity Certificate
 *
 * For further code examples see the *omnipay-example* repository on github.
 * @see GatewayInterface
 * For more information on Apple Pay JS API visit:
 * @link https://developer.apple.com/documentation/apple_pay_on_the_web
 *
 * Example:
 * In the front end, the user initiates an Apple Pay payment. Then the Apple Pay payment sheet is displayed
 * and partially loaded. The Apple Pay gateway makes the call to Apple's servers using this request to retrieve
 * an Apple Pay payment session from Apple's servers. Once the session is retrieved (via the response object)
 * you can pass it to the front end to fully load the payment sheet and accept user payment.
 *
 * <code>
 *    // Setup the gateway with your username and password for Vindicia.
 *    // Include the paths to your Apple Pay Merchant Identity cert, key cert and key cert password.
 *   $gateway = \Omnipay\Omnipay::create('Vindicia ApplePay');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setPemCertPath('./certs/path_to_cert.crt.pem');
 *   $gateway->setKeyCertPath('./certs/path_to_key_cert.key.pem');
 *   $gateway->setKeyCerPassword('y0ur_key_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *    // User clicks or taps an Apple Pay button. The payment sheet is partially loaded. So now Apple
 *    // can validate your merchant identity and you can receive a session object to fully load the
 *    // payment sheet to accept payment.
 *
 *    // To request the payment session, pass the required Apple Pay options to the authorize method in this class
 *    // and send the request. An AbstractResponse object will be returned.
 *   $authorizeResponse = $gateway->authorize(array(
 *       // sandbox validation Url
 *       'validationUrl' => 'https://apple-pay-gateway-cert.apple.com/paymentservices/startSession',
 *       'merchantIdentifier' => 'merchant.com.example',
 *       'displayName' => 'MyStore',
 *       'applicationUrl' => 'mystore.com' // initiativeContext: the domain name of your site.
 *       )
 *   ))->send();
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
 *    //Pass authorizeResponse back to the client to validate your merchant and continue with an Apple Pay payment.
 *    //If successful, the payment sheet should be fully loaded.
 *    $apple_pay_session = $authorizeResponse->getPaymentSessionObject();
 *
 *    // TODO: Add functionality for completeAuthorize() and capture().
 *    // An opaque Apple Pay Session is returned as a response (expires after 5 mins) and it can be sent to
 *    // the front end to fully load the payment sheet. This allows the user to optionally configure their
 *    // payment option and shipping methods (if needed) and submit their payment.
 * </code>
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
     * @return static
     */
    public function setUsername($value)
    {
        /**
         * @var static
         */
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
     * @return static
     */
    public function setPassword($value)
    {
        /**
         * @var static
         */
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
     * @return static
     */
    public function setPemCertPath($value)
    {
        /**
         * @var static
         */
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
        /**
         * @var static
         */
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
     * @param string $url
     * @return static
     */
    public function setKeyCertPassword($value)
    {
        /**
         * @var static
         */
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
     * @return static
     */
    public function setValidationUrl($url)
    {
        /**
         * @var static
         */
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
     * @param string $value
     * @return static
     */
    public function setMerchantIdentifier($value)
    {
        /**
         * @var static
         */
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
     * @return static
     */
    public function setDisplayName($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('displayName', $value);
    }

    /**
     * @return null|string
     */
    public function getApplicationUrl()
    {
        /**
         * @var null|string
         */
        return $this->getParameter('applicationUrl');
    }

    /**
     * Sets the application url or the "initiativeContext" for the request
     *
     * @param string $value
     * @return static
     */
    public function setApplicationUrl($value)
    {
        /**
         * @var static
         */
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
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getData()
    {
        // This ensures that the validation URL set. If it isn't, the request call won't be sent.
        $this->validate('validationUrl');
        $data = array();

        // Required request parameters for Apple Session object.
        $data['validationUrl'] = $this->getValidationUrl();
        $data['displayName'] = $this->getDisplayName();
        $data['merchantIdentifier'] = $this->getMerchantIdentifier();
        // Default parameter for Apple Pay on the Web.
        $data['initiative'] = 'web';
        $data['initiativeContext'] = $this->getApplicationUrl();

        return $data;
    }

    /**
     * Assembles the HTTP request to be sent.
     *
     * @param       $data
     * @param array $headers
     *
     * @return static
     * @psalm-suppress UndefinedMethod because Guzzle\Client::setDefaultOption() is actually defined.
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
        $this->httpClient->setDefaultOption('verify', \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath());

        // don't throw exceptions for 4xx errors
        $this->httpClient->getEventDispatcher()->addListener(
            'request.error',
            /** @return void */
            function (Event $event) {
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

        /**
         * @var static
         */
        return $httpRequest;
    }

    /**
     * Overriding AbstractRequest::sendData() so that we can make a REST call instead of a SOAP call.
     *
     * @param array $data
     * @return ResponseInterface
     * @psalm-suppress UndefinedMethod
     */
    public function sendData($data)
    {
        $headers = array(
            "Content-Type" => "application/json",
            "Accept" => "application/json"
        );

        /** @var EventDispatcherInterface|null $eventDispatcher */
        $eventDispatcher = $this->httpClient->getEventDispatcher();

        if ($eventDispatcher) {
            // Log the ApplePay request before it is sent.
            $eventDispatcher->dispatch(Constants::OMNIPAY_REQUEST_BEFORE_SEND, new RequestEvent($this));
        }

        $httpResponse = null;
        try {
            // Create a request object to be sent.
            $httpRequest  = $this->createClientRequest($data, $headers);
            $httpResponse = $httpRequest->send();
        } catch (Exception $exception) {
            if ($eventDispatcher) {
                // Log any errors with the request.
                $eventDispatcher->dispatch(
                    Constants::OMNIPAY_REQUEST_ERROR,
                    new ErrorEvent($exception, $this)
                );
            }

            throw $exception;
        }

        // Retrieve the status code and it's corresponding status message.
        $status = array(
            /**
             * Have to suppress this error since Guzzle is out of scope for Psalm.
             * Casting to a string so that it matches the return type of
             * Omnipay\Common\Message\ResponseInterface::getCode().
             *
             * @psalm-suppress UndefinedMethod
             */
            'code' => (string)$httpResponse->getStatusCode(),
            /**
             * A human readable version of the numeric status code.
             * Have to suppress this error since Guzzle is out of scope for Psalm.
             *
             * @psalm-suppress UndefinedMethod
             */
            'message' => $httpResponse->getReasonPhrase()
        );

        // Assemble the response..
        try {
            $message = $httpResponse->json();
            $response = array_merge(
                $status,
                array('body' => $message)
            );
        } catch (\RunTimeException $e) {
            // If you try to parse an empty response body, error will be thrown.
            $response = array_merge(
                $status,
                array('body' => '')
            );
        }

        /**
         * @var ApplePayAuthorizeResponse
         */
        $this->response = new static::$RESPONSE_CLASS($this, $response);

        if ($eventDispatcher) {
            // Log successful request responses.
            $eventDispatcher->dispatch(Constants::OMNIPAY_RESPONSE_SUCCESS, new ResponseEvent($this->response));
        }

        return $this->response;
    }
}
