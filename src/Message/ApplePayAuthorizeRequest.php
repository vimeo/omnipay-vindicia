<?php

namespace Omnipay\Vindicia\Message;

class ApplePayAuthorizeRequest extends \Omnipay\Common\Message\AbstractRequest
{
    public function getData()
    {
        $this->validate('validationURL');

        $data = array();

        $data['validationURL'] = $this->getValidationUrl();
        return $data;
    }

    public function getValidationUrl()
    {
        return $this->getParameter('validationURL');
    }

    /**
     * Overriding AbstractReuqest::sendData() so that we can make a REST call instead of a SOAP call.
     */
    public function sendData($data)
    {
        $headers = array_merge(
            $this->getHeaders(),
            array('Authorization' => 'Basic ' . base64_encode($this->getApiKey() . ':'))
        );
        $httpRequest  = $this->createClientRequest($data, $headers);
        $httpResponse = $httpRequest->send();
        $this->response = new Response($this, $httpResponse->json());
        if ($httpResponse->hasHeader('Request-Id')) {
            $this->response->setRequestId((string) $httpResponse->getHeader('Request-Id'));
        }
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
        // Stripe only accepts TLS >= v1.2, so make sure Curl is told
        $config                          = $this->httpClient->getConfig();
        $curlOptions                     = $config->get('curl.options');
        $curlOptions[CURLOPT_SSLVERSION] = 6;
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
            $this->getEndpoint(),
            $headers,
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

    abstract public function getEndpoint();

    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        if ($this->getConnectedStripeAccountHeader()) {
            $headers['Stripe-Account'] = $this->getConnectedStripeAccountHeader();
        }
        if ($this->getIdempotencyKeyHeader()) {
            $headers['Idempotency-Key'] = $this->getIdempotencyKeyHeader();
        }
        return $headers;
    }

    /**
     * Get the gateway API Key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getParameter('apiKey');
    }
}
