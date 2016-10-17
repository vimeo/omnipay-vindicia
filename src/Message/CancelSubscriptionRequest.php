<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CancelSubscriptionRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @vreturn string
     */
    protected function getFunction()
    {
        return 'cancel';
    }

    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData()
    {
        $subscription = new stdClass();
        $subscription->merchantAutoBillId = $this->getSubscriptionId();
        $subscription->VID = $this->getSubscriptionReference();

        if (!isset($subscription->merchantAutoBillId) && !isset($subscription->VID)) {
            throw new InvalidRequestException('Either the subscription id or reference is required.');
        }

        return array(
            'autobill' => $subscription,
            'action' => $this->getFunction(),
            'disentitle' => false,
            'force' => true,
            'settle' => false,
            'sendCancellationNotice' => false,
            'cancelReasonCode' => null
        );
    }
}
