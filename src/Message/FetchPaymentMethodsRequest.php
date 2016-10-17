<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class FetchPaymentMethodsRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'fetchByAccount';
    }

    protected function getObject()
    {
        return self::$PAYMENT_METHOD_OBJECT;
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException(
                'The customerId parameter or the customerReference parameter is required.'
            );
        }

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;

        return array(
            'action' => $this->getFunction(),
            'account' => $account,
            'includeChildren' => false
        );

        return $data;
    }
}
