<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CreatePlanRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'update';
    }

    protected function getObject()
    {
        return self::$PLAN_OBJECT;
    }

    /**
     * Get the units for the billing period (day, week, month, year)
     *
     * @return string
     */
    public function getInterval()
    {
        return $this->getParameter('interval');
    }

    /**
     * Set the units for the billing period (day, week, month, year)
     *
     * @param string $value
     * @return static
     */
    public function setInterval($value)
    {
        return $this->setParameter('interval', $value);
    }

    /**
     * Get the frequency of the billing period (using the units specified by the interval parameter)
     *
     * @return int
     */
    public function getIntervalCount()
    {
        return $this->getParameter('intervalCount');
    }

    /**
     * Set the billing interval (using the units specified by the interval parameter)
     * eg) if you set the interval to 'month' and the intervalCount to 2, the customer
     * will be billed every 2 months
     *
     * @param int $value
     * @return static
     */
    public function setIntervalCount($value)
    {
        return $this->setParameter('intervalCount', $value);
    }

    public function getData()
    {
        $this->validate('interval', 'intervalCount');

        $planId = $this->getPlanId();
        $planReference = $this->getPlanReference();
        if (!$this->isUpdate()) {
            $this->validate('planId');
        } elseif (!$planId && !$planReference) {
            throw new InvalidRequestException('Either the planId or planReference parameter is required.');
        }

        $plan = new stdClass();
        $plan->merchantBillingPlanId = $planId;
        $plan->VID = $planReference;
        $plan->taxClassification = $this->getTaxClassification();
        $plan->billingStatementIdentifier = $this->getStatementDescriptor();

        $period = new stdClass();
        $period->type = ucfirst(strtolower($this->getInterval()));
        $period->quantity = $this->getIntervalCount();
        $period->cycles = 0; // use this period indefinitely
        // could add a trial period by putting another period first in the array
        $plan->periods = array($period);

        $prices = $this->makePricesForVindicia();
        if (!empty($prices)) {
            $plan->prices = $prices;
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $plan->nameValues = $this->buildNameValues($attributes);
        }

        $data['billingPlan'] = $plan;
        $data['action'] = $this->getFunction();

        return $data;
    }
}
