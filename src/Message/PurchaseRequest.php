<?php

namespace Omnipay\Vindicia\Message;

class PurchaseRequest extends AuthorizeRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'authCapture';
    }

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $data = parent::getData($paymentMethodType);

        $data['ignoreAvsPolicy'] = false;
        $data['ignoreCvnPolicy'] = false;

        return $data;
    }
}
