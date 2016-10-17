<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchCustomerRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getCustomerId() ? 'fetchByMerchantAccountId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();

        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($customerId) {
            $data['merchantAccountId'] = $this->getCustomerId();
        } else {
            $data['vid'] = $this->getCustomerReference();
        }

        return $data;
    }
}
