<?php

namespace Omnipay\Vindicia\Message;

class ApplePayAuthorizeRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * @return array
     */
    public function getData()
    {
        $data = array();

        $data['merchantIdentifier'] = 'merchant.com.vimeo';
        $data['displayName'] = 'Vimeo';
        $data['initiative'] = 'web';
        $data['initiativeContext'] = "vimeo.com";
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
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        //TODO: Add Apple Pay certs here.

        return $headers;
    }

    /**
     * Overriding AbstractReuqest::sendData() so that we can make a REST call instead of a SOAP call.
     * @param array $data
     * @return Response
     */
    public function sendData($data)
    {
        $headers = array_merge(
            $this->getHeaders(),
            array(
                'Authorization' => 'Basic ' . base64_encode($this->getApiKey() . ':'),
                'json' => true
            )
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
    /**
     * @return string
     */
    public function getEndpoint()
    {
        return 'https://'.$this->getValidationURL()."/paymentSession";
    }
}
