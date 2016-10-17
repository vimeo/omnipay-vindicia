<?php

namespace Omnipay\Vindicia\Message;

class CreatePayPalSubscriptionRequest extends CreateSubscriptionRequest
{
    public function getData()
    {
        $this->validate('returnUrl', 'cancelUrl');

        return parent::getData(self::PAYMENT_METHOD_PAYPAL);
    }

    /**
     * Use a special response object for PayPal subscription requests.
     *
     * @param object $response
     * @return CreatePayPalSubscriptionResponse
     */
    protected function buildResponse($response)
    {
        return new CreatePayPalSubscriptionResponse($this, $response);
    }
}
