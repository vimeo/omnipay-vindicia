<?php

namespace Omnipay\Vindicia\Message;

class PayPalPurchaseRequest extends PurchaseRequest
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
