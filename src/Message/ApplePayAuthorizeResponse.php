<?php

namespace Omnipay\Vindicia\Message;

class ApplePayAuthorizeResponse extends Response
{
    /**
     * Was the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        /**
         * @var boolean
         */
        return ($this->data['statusCode'] == 200);
    }

    /**
     * The entire Apple Pay payment session object to be used to validate the merchant.
     * This should be passed to the front end.
     *
     * @return static
     */
    public function getPaymentSessionObject()
    {
        /**
         * @var static
         */
        return json_encode($this->data);
    }

    /**
     * The human-readable status code.
     *
     * @return static
     */
    public function getReason()
    {
        /**
         * @var static
         */
        return $this->data['reason'];
    }

    /**
     * The numerical status code.
     *
     * @return static
     */
    public function getStatusCode()
    {
        /**
         * @var static
         */
        return $this->data['statusCode'];
    }
}
