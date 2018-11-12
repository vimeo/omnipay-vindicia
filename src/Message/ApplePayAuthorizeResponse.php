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
}
