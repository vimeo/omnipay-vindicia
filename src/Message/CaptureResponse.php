<?php
/**
 * The responses for captures are a bit different since the
 * Vindicia API lets you capture multiple transactions in one
 * request.
 */
namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidResponseException;

class CaptureResponse extends Response
{
    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     * Throws an exception if the different success codes in the API response
     * don't match up.
     *
     * @return boolean
     * @throws Omnipay\Common\Exception\InvalidResponseException
     */
    public function isSuccessful()
    {
        $success = parent::isSuccessful();

        // Check all the other response codes that come back and make sure they match up
        if ($success
            && (!isset($this->data->return)
                || intval($this->data->return->returnCode) !== self::SUCCESS_CODE
                || intval($this->data->qtySuccess) !== 1
                || intval($this->data->qtyFail) !== 0
            )
        ) {
            throw new InvalidResponseException('The response codes in the response don\'t match each other');
        }

        return $success;
    }

    /**
     * Get the response code from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getCode()
    {
        if (isset($this->data->results)) {
            return $this->data->results[0]->returnCode;
        }
        throw new InvalidResponseException('Response has no code.');
    }

    /**
     * Get the response message from the payment gateway.
     * Throws an exception if it's not present.
     * NOTE: Since the API allows you to capture multiple transactions
     * at once, Vindicia only gives a response for the overall request,
     * not the individual transaction captures. So a capture can fail
     * and the response message may be 'Ok'. Really not very helpful.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        return parent::getMessage();
    }

    /**
     * Get the reference provided by the gateway to represent this transaction
     * NOTE: Vindicia does not return the reference in this API call. Use
     * getTransactionId instead.
     *
     * @return null
     */
    public function getTransactionReference()
    {
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this transaction
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        if (isset($this->data->results)) {
            return $this->data->results[0]->merchantTransactionId;
        }
        return null;
    }
}
