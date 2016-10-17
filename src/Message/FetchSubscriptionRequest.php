<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchSubscriptionRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getSubscriptionId() ? 'fetchByMerchantAutoBillId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData()
    {
        $subscriptionId = $this->getSubscriptionId();
        $subscriptionReference = $this->getSubscriptionReference();

        if (!$subscriptionId && !$subscriptionReference) {
            throw new InvalidRequestException(
                'Either the subscriptionId or subscriptionReference parameter is required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($subscriptionId) {
            $data['merchantAutoBillId'] = $this->getSubscriptionId();
        } else {
            $data['vid'] = $this->getSubscriptionReference();
        }

        return $data;
    }
}
