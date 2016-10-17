<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

/**
 * Some of Vindicia's documentation claims this method isn't supported by HOA,
 * but it is
 */
class HOACreatePaymentMethodRequest extends AbstractHOARequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\CreatePaymentMethodRequest';

    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        // card parameter isn't required since that will come from the HOA form
        $this->regularRequest->setCardRequired(false);

        return $this;
    }

    protected function getObjectParamNames()
    {
        return array(
            self::$PAYMENT_METHOD_OBJECT => 'paymentMethod',
            self::$CUSTOMER_OBJECT => 'account'
        );
    }

    public function getValidate()
    {
        return $this->regularRequest->getValidate();
    }

    public function setValidate($value)
    {
        $this->regularRequest->setValidate($value);
        return $this;
    }

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue(
                'Account_UpdatePaymentMethod_replaceOnAllAutoBills',
                $regularRequestData['replaceOnAllAutoBills']
            ),
            new NameValue('Account_UpdatePaymentMethod_updateBehavior', $regularRequestData['updateBehavior']),
            new NameValue('Account_UpdatePaymentMethod_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
            new NameValue('Account_UpdatePaymentMethod_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy'])
        );
    }
}
