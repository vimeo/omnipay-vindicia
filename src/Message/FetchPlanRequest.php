<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchPlanRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getPlanId() ? 'fetchByMerchantBillingPlanId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$PLAN_OBJECT;
    }

    public function getData()
    {
        $planId = $this->getPlanId();
        $planReference = $this->getPlanReference();

        if (!$planId && !$planReference) {
            throw new InvalidRequestException('Either the planId or planReference parameter is required.');
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($planId) {
            $data['merchantBillingPlanId'] = $this->getPlanId();
        } else {
            $data['vid'] = $this->getPlanReference();
        }

        return $data;
    }
}
