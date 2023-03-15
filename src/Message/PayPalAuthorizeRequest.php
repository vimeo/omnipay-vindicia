<?php

namespace Omnipay\Vindicia\Message;

class PayPalAuthorizeRequest extends AuthorizeRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\PayPalPurchaseResponse';

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $this->validate('returnUrl', 'cancelUrl');

        return parent::getData(self::PAYMENT_METHOD_PAYPAL);
    }

    /**
     * Overriding to provide a more precise return type
     * @return PayPalPurchaseResponse
     */
    public function send()
    {
        /**
         * @var PayPalPurchaseResponse
         */
        return parent::send();
    }
}
