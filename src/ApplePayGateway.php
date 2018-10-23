<?php

namespace Omnipay\Vindicia;


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
