<?php

namespace Omnipay\Vindicia\Message;

class CompleteCreatePayPalSubscriptionRequest extends AbstractRequest
{
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'finalizePayPalAuth';
    }

    public function getData()
    {
        $this->validate('payPalTransactionReference', 'success');

        return array(
            'action' => $this->getFunction(),
            'payPalTransactionId' => $this->getPayPalTransactionReference(),
            'success' => $this->getSuccess()
        );
    }
}
