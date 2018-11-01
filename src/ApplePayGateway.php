<?php

namespace Omnipay\Vindicia;


class ApplePayGateway extends AbstractVindiciaGateway
{

    /**
     * Get the gateway parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'username' => '',
            'password' => '',
            'testMode' => false,
        );
    }

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
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @return AbstractVindiciaGateway
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
     * @return AbstractVindiciaGateway
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
