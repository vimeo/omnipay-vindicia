<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch a Vindicia plan object.
 *
 * Parameters:
 * - planId: Your identifier for the plan to be fetched. Either planId
 * or planReference is required.
 * - planReference: The gateway's identifier for the plan to be fetched. Either
 * planId or planReference is required.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a plan to govern the behavior of the subscription (eg billing frequency)
 *   $planResponse = $gateway->createPlan(array(
 *       'planId' => '123456789', // you choose this
 *       'interval' => 'week',
 *       'intervalCount' => 2,
 *       'prices' => array(
 *           'USD' => '9.99',
 *           'CAD' => '12.99'
 *       )
 *   ))->send();
 *
 *   if ($planResponse->isSuccessful()) {
 *       echo "Plan id: " . $planResponse->getPlanId() . PHP_EOL;
 *       echo "Plan reference: " . $planResponse->getPlanReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now we want to fetch the plan object
 *   $planResponse = $gateway->fetchPlan(array(
 *       'planId' => $planResponse->getPlanId() // could do by planReference also
 *   ))->send();
 *
 *   if ($planResponse->isSuccessful()) {
 *       var_dump($planResponse->getPlan());
 *   }
 * </code>
 */
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

    /**
     * @return string
     */
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
