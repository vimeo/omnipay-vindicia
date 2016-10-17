<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchPaymentMethodRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getPaymentMethodId() ? 'fetchByMerchantPaymentMethodId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$PAYMENT_METHOD_OBJECT;
    }

    public function getData()
    {
        $paymentMethodId = $this->getPaymentMethodId();
        $paymentMethodReference = $this->getPaymentMethodReference();

        if (!$paymentMethodId && !$paymentMethodReference) {
            throw new InvalidRequestException(
                'Either the paymentMethodId or paymentMethodReference parameter is required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($paymentMethodId) {
            $data['paymentMethodId'] = $this->getPaymentMethodId();
        } else {
            $data['vid'] = $this->getPaymentMethodReference();
        }

        return $data;
    }
}
