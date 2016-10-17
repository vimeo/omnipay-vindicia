<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CaptureRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'capture';
    }

    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    public function getData()
    {
        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        if (!$transactionId && !$transactionReference) {
            throw new InvalidRequestException(
                'Either the transactionId or transactionReference parameter is required.'
            );
        }

        $transaction = new stdClass();
        $transaction->merchantTransactionId = $transactionId;
        $transaction->VID = $transactionReference;

        $data = array();
        $data['transactions'] = array($transaction);
        $data['action'] = $this->getFunction();

        return $data;
    }

    /**
     * Use a special response object for Capture requests.
     *
     * @param object $response
     * @return CaptureResponse
     */
    protected function buildResponse($response)
    {
        return new CaptureResponse($this, $response);
    }
}
