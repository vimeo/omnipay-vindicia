<?php

namespace Omnipay\Vindicia\Message;

class ApplePayAuthorizeResponse extends Response
{
    /**
     * Is the response successful?
     *
     * @return boolean|null
     */
    public function isSuccessful()
    {
        $statusCode = $this->getStatusCode();
        return ($statusCode == 200);
    }

    /**
     * The entire Apple Pay payment session object to be used to validate the merchant.
     *
     * @return string|null
     */
    public function getApplePayPaymentSessionObject()
    {
        return json_encode($this->data);
    }

    /**
     * The human-readable status code.
     *
     * @return string|null
     */
    public function getReason()
    {
        return $this->data['reason'];
    }

    /**
     * The numerical status code.
     *
     * @return string|null
     */
    public function getStatusCode()
    {
        return $this->data['statusCode'];
    }

    /**
     * The time when Apple responded with the Apple Pay payment session.
     *
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
     * The time when the Apple Pay payment session expires.
     *
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
     * The merchant session ID for the Apple Pay payment session.
     *
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
     * The nonce token for the Apple Pay payment session.
     *
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
     * The merchant ID for the Apple Pay payment session.
     *
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
     * The domain name for the Apple Pay payment session.
     *
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
     * The display name for the Apple Pay payment session.
     * This is what's displayed on the mac touch bar.
     *
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
     * The unique session signature ID for the Apple Pay payment session.
     *
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
