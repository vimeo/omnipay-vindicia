<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create a new plan or update an existing one (if called via updatePlan).
 * A plan defines the behavior of a subscription, such as how
 * often the customer is billed.
 *
 * It is rare that you will need many different plans since you can attach the same
 * plan to multiple different subscriptions. You may find it easier to create the
 * plans through Vindicia's web portal, which will also give you more control over
 * their features.
 *
 * Parameters:
 * - planId: Your identifier to represent this plan. Required.
 * - interval: The units of the billing period (day, week, month, year). Required.
 * - intervalCount: The frequency to bill, using the units specified by interval. (eg,
 * if interval is week and intervalCount is 2, the customer will be billed every two
 * weeks. Required.
 * - prices: Prices in multiple currencies associated with the billing plan. These
 * are a fallback in case the product does not specify prices. Optional.
 * - taxClassification: The tax classification of the plan. Values may vary depending
 * on your tax engine, consult with Vindicia to learn what values are available to you.
 * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
 * - statementDescriptor: The description shown on the customers billing statement from the bank
 * This field’s value and its format are constrained by your payment processor; consult with
 * Vindicia Client Services before setting the value.
 * - attributes: Custom values you wish to have stored with the plan. They have
 * no affect on anything.
 *
 * See CreateSubscriptionRequest for a code example.
 */
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

        return array(
            'billingPlan' => $plan,
            'action' => $this->getFunction()
        );
    }
}
