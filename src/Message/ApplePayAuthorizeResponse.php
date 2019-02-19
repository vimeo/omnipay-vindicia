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
        return ($this->getCode() == '200');
    }

    /**
     * The entire Apple Pay payment session object to be used to validate the merchant.
     * This should be passed to the front end.
     *
     * @return string
     */
    public function getPaymentSessionObject()
    {
        /**
         * @var string
         */
        return (empty($this->data['body'])) ? '' : json_encode($this->data['body']);
    }

    /**
     * The response message.
     *
     * @return string
     */
    public function getMessage()
    {
        /**
         * @var string
         */
        return $this->data['message'];
    }

    /**
     * The response code.
     *
     * @return string
     */
    public function getCode()
    {
        /**
         * @var string
         */
        return $this->data['code'];
    }
}
