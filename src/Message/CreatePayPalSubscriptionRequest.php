<?php

namespace Omnipay\Vindicia\Message;

class CreatePayPalSubscriptionRequest extends CreateSubscriptionRequest
{
    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
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
