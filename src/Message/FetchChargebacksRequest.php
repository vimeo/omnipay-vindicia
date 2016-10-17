<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchChargebacksRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        if ($this->getTransactionId()) {
            return 'fetchByMerchantTransactionId';
        } elseif ($this->getTransactionReference()) {
            return 'fetchByVid';
        } else {
            return 'fetchDeltaSince';
        }
    }

    protected function getObject()
    {
        return self::$CHARGEBACK_OBJECT;
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
            $data['merchantTransactionId'] = $transactionId;
        } elseif ($transactionReference) {
            $data['vid'] = $transactionReference;
        } else {
            $data['timestamp'] = $startTime;
            $data['endTimestamp'] = $endTime;
        }

        return $data;
    }
}
