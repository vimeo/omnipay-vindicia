<?php
/**
 * The responses for refunds are a bit different since the
 * Vindicia API lets you refund multiple transactions in one
 * request.
 */
namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidResponseException;

class RefundResponse extends Response
{
    /**
     * Get the response message from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        if (!$this->isSuccessful() && isset($this->data->refunds[0]->note)) {
            // for some reason Vindicia sticks failure messages into the note field
            // instead of making a new field...
            return $this->data->refunds[0]->note;
        }
        return parent::getMessage();
    }

    /**
     * Get the reference provided by the gateway to represent this refund
     *
     * @return string|null
     */
    public function getRefundReference()
    {
        if (isset($this->data->refunds[0]->VID)) {
            return $this->data->refunds[0]->VID;
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this refund
     * This is auto-set by Vindicia with a prefix assigned in your initial
     * configuration.
     *
     * @return string|null
     */
    public function getRefundId()
    {
        if (isset($this->data->refunds[0]->merchantRefundId)) {
            return $this->data->refunds[0]->merchantRefundId;
        }
        return null;
    }

    /**
     * Get the refund object
     *
     * @return object|null
     */
    public function getRefund()
    {
        if (isset($this->data->refunds[0])) {
            return $this->data->refunds[0];
        }
        return null;
    }
}
