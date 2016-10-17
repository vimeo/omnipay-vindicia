<?php

namespace Omnipay\Vindicia\Message;

class PayPalPurchaseRequest extends PurchaseRequest
{
    public function getData()
    {
        $this->validate('returnUrl', 'cancelUrl');

        return parent::getData(self::PAYMENT_METHOD_PAYPAL);
    }

    /**
     * Use a special response object for PayPal purchase requests.
     *
     * @param object $response
     * @return PayPalPurchaseResponse
     */
    protected function buildResponse($response)
    {
        return new PayPalPurchaseResponse($this, $response);
    }
}
