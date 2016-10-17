<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchTransactionRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getTransactionId() ? 'fetchByMerchantTransactionId' : 'fetchByVid';
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

        $data = array(
            'action' => $this->getFunction()
        );

        if ($transactionId) {
            $data['merchantTransactionId'] = $this->getTransactionId();
        } else {
            $data['vid'] = $this->getTransactionReference();
        }

        return $data;
    }
}
