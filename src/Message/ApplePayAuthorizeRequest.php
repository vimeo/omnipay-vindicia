<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Message\ResponseInterface;

class ApplePayAuthorizeRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\ApplePayAuthorizeResponse';

    /**
     * Default display name for Apple Pay Session.
     *
     * @var string
     */
    const DEFAULT_DISPLAY_NAME = 'DISPLAY_NAME_HERE';

    /**
     * @param array<string, mixed> $parameters
     * @return ApplePayAuthorizeRequest
     */
    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('displayName', $parameters)) {
            $parameters['displayName'] = self::DEFAULT_DISPLAY_NAME;
        }
        parent::initialize($parameters);

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $this->validate('validationURL');

        $data = array();

        // These are from Apple's certificate – they should never change.
        $data["merchantIdentifier"] = "merchant.com";
        $data["initiative"] = "web";
        $data["initiativeContext"] = "abcd.com";

        if (isset($this->data['validationURL'])) {
            $data['validationURL'] = $this->getValidationUrl();
        }

        if (isset($this->data['epochTimestamp'])) {
            $data['epochTimestamp'] = $this->getTimeStamp();
        }

        if (isset($this->data['expiresAt'])) {
            $data['expiresAt'] = $this->getExpirationTimeStamp();
        }

        if (isset($this->data['merchantSessionIdentifier'])) {
            $data['merchantSessionIdentifier'] = $this->getMerchantSessionID();
        }

        if (isset($this->data['nonce'])) {
            $data['nonce'] = $this->getNonceToken();
        }

        if (isset($this->data['merchantSessionIdentifier'])) {
            $data['merchantSessionIdentifier'] = $this->getMerchantID();
        }

        if (isset($this->data['domainName'])) {
            $data['domainName'] = $this->getDomainName();
        }

        if (isset($this->data['displayName'])) {
            $data['displayName'] = $this->getDisplayName();
        }

        if (isset($this->data['signature'])) {
            $data['signature'] = $this->getSignatureID();
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getParameter('validationURL');
    }

    /**
     * Set the validation URL for the authorize request
     *
     * @param string $url
     * @return static
     */
    public function setValidationUrl($url)
    {
        return $this->setParameter('validationURL', $url);
    }

    /**
     * @return null|string
     */
    public function getTimeStamp()
    {
        return $this->getParameter('epochTimestamp');
    }

    /**
     * Sets the (epoch) timestamp for the request
     *
     * @param string $value
     * @return static
     */
    public function setTimeStamp($value)
    {
        return $this->setParameter('epochTimestamp', $value);
    }

    /**
     * @return null|string
     */
    public function getExpirationTimeStamp()
    {
        return $this->getParameter('expiresAt');
    }

    /**
     * Sets the expiration (epoch) timestamp for the request
     *
     * @param string $value
     * @return static
     */
    public function setExpirationTimeStamp($value)
    {
        return $this->setParameter('expiresAt', $value);
    }

    /**
     * @return null|string
     */
    public function getMerchantSessionID()
    {
        return $this->getParameter('merchantSessionIdentifier');
    }

    /**
     * Sets the merchant ID to be sent with the request
     *
     * @param string $value
     * @return static
     */
    public function setMerchantSessionID($value)
    {
        return $this->setParameter('merchantSessionIdentifier', $value);
    }

    /**
     * @return null|string
     */
    public function getNonceToken()
    {
        return $this->getParameter('nonce');
    }

    /**
     * Sets the nonce token from the request
     *
     * @param string $value
     * @return static
     */
    public function setNonceToken($value)
    {
        return $this->setParameter('nonce', $value);
    }

    /**
     * @return null|string
     */
    public function getMerchantID()
    {
        return $this->getParameter('merchantSessionIdentifier');
    }

    /**
     * Sets the merchant ID sent with the request
     *
     * @param string $value
     * @return static
     */
    public function setMerchantID($value)
    {
        return $this->setParameter('merchantSessionIdentifier', $value);
    }

    /**
     * @return null|string
     */
    public function getDomainName()
    {
        return $this->getParameter('domainName');
    }

    /**
     * Sets the domain name sent with the request
     *
     * @param string $value
     * @return static
     */
    public function setDomainName($value)
    {
        return $this->setParameter('domainName', $value);
    }

    /**
     * @return null|string
     */
    public function getDisplayName()
    {
        return $this->getParameter('displayName');
    }

    /**
     * Sets the display name sent with the request
     *
     * @param string $value
     * @return static
     */
    public function setDisplayName($value)
    {
        return $this->setParameter('displayName', $value);
    }

    /**
     * @return null|string
     */
    public function getSignatureID()
    {
        return $this->getParameter('signature');
    }

    /**
     * Sets the signature for the request
     *
     * @param string $value
     * @return static
     */
    public function setSignatureID($value)
    {
        return $this->setParameter('signature', $value);
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * Sets the username
     *
     * @param string $value
     * @return static
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
     * Sets the password
     *
     * @param string $value
     * @return static
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * Overriding AbstractReuqest::sendData() so that we can make a REST call instead of a SOAP call.
     * @param array $data
     * @return Response
     */
    public function sendData($data)
    {
        $httpRequest  = $this->createClientRequest($data);
        $httpResponse = $httpRequest->send();

        $statusCodes = array(
            'message' => $httpResponse->getReasonPhrase(),
            'statusCode' => $httpResponse->getStatusCode()
        );

        try {
            $message = $httpResponse->json();
            $response = array_merge(
                $statusCodes,
                $message
            );
        // If you try to parse an empty response body, error will be thrown.
        } catch (\RunTimeException $e) {
            $response = array_merge(
                $statusCodes,
                array()
            );
        }

        $this->response = new static::$RESPONSE_CLASS($this, $response);

        return $this->response;
    }

    /**
     * @param       $data
     * @param array $headers
     *
     * @return \Guzzle\Http\Message\RequestInterface
     */
    protected function createClientRequest($data, array $headers = null)
    {
        $config                          = $this->httpClient->getConfig();
        $curlOptions                     = $config->get('curl.options');
        $curlOptions[CURLOPT_SSLVERSION] = 6;
        $curlOptions[CURLOPT_SSLCERT] = "YOUR_CERTS_HERE";
        $curlOptions[CURLOPT_SSLKEY] = "YOUR_CERTS_HERE";
        $curlOptions[CURLOPT_SSLKEYPASSWD] = "PASSWORD";
        $curlOptions[CURLOPT_SSLCERTPASSWD]  = "PASSWORD";

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

        $httpRequest = $this->httpClient->createRequest(
            $this->getHttpMethod(),
            $this->getValidationUrl(),
            array('headers' => ['Content-Type' => 'application/json', 'Accept' => 'application/json']),
            $data
        );

        return $httpRequest;
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
}
