<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class FetchRefundsRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @refund string
     */
    protected function getFunction()
    {
        return $this->getStartTime() ? 'fetchDeltaSince' : 'fetchByTransaction';
    }

    protected function getObject()
    {
        return self::$REFUND_OBJECT;
    }

    public function getData()
    {
        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();
        if (($transactionId || $transactionReference) && ($startTime || $endTime)) {
            throw new InvalidRequestException('Cannot fetch by both transaction and start/end time.');
        }
        if (!$transactionId && !$transactionReference && (!$startTime || !$endTime)) {
            throw new InvalidRequestException(
                'The transactionId parameter or the transactionReference parameter or the startTime and endTime '
                . 'parameters are required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($transactionId) {
            $transaction = new stdClass();
            $transaction->merchantTransactionId = $transactionId;
            $transaction->VID = $transactionReference;

            $data['transaction'] = $transaction;
        } else {
            $data['timestamp'] = $startTime;
            $data['endTimestamp'] = $endTime;
            $data['paymentMethod'] = null;
        }

        return $data;
    }
}
