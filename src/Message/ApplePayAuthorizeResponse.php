<?php

namespace Omnipay\Vindicia\Message;

class ApplePayAuthorizeResponse extends Response
{
    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isSuccessful()
    {
        $statusCode = $this->data['statusCode'];
        return ($statusCode >= 200 && $statusCode < 300) || $statusCode == 304;
    }

    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        return $this->data['message'];
    }

    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getCode()
    {
        return $this->data['statusCode'];
    }

    /**
     * Is the response a redirect?
     * Throws an exception if there's no code.
     * Successful PayPal purchase responses are always redirects.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isRedirect()
    {
        return $this->isSuccessful();
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        $transaction = $this->getTransaction();
        if (isset($transaction)) {
            return $transaction->getPayPalRedirectUrl();
        }
        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionTimeStamp()
    {
        if (isset($this->data['epochTimestamp'])) {
            return $this->data['epochTimestamp'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionExpirationTimeStamp()
    {
        if (isset($this->data['expiresAt'])) {
            return $this->data['expiresAt'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionMerchantSessionID()
    {
        if (isset($this->data['merchantSessionIdentifier'])) {
            return $this->data['merchantSessionIdentifier'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionNonceToken()
    {
        if (isset($this->data['nonce'])) {
            return $this->data['nonce'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionMerchantID()
    {
        if (isset($this->data['merchantIdentifier'])) {
            return $this->data['merchantIdentifier'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionDomainName()
    {
        if (isset($this->data['domainName'])) {
            return $this->data['domainName'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionDisplayName()
    {
        if (isset($this->data['displayName'])) {
            return $this->data['displayName'];
        }

        return null;
    }

    /**
     * @return string|null
     */
    public function getApplePaySessionSignature()
    {
        if (isset($this->data['signature'])) {
            return $this->data['signature'];
        }

        return null;
    }
}
