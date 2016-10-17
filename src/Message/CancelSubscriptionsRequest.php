<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CancelSubscriptionsRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @vreturn string
     */
    protected function getFunction()
    {
        return 'stopAutoBilling';
    }

    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    /**
     * Get the subscription ids to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @return array<string>|null
     */
    public function getSubscriptionIds()
    {
        return $this->getParameter('subscriptionIds');
    }

    /**
     * Set the subscription ids to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @param array<string>|null $value
     * @return static
     */
    public function setSubscriptionIds($value)
    {
        return $this->setParameter('subscriptionIds', $value);
    }

    /**
     * Get the subscription references to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @return array<string>|null
     */
    public function getSubscriptionReferences()
    {
        return $this->getParameter('subscriptionReferences');
    }

    /**
     * Set the subscription references to be canceled. Null indicates that all
     * of the customer's subscriptions will be canceled.
     *
     * @param array<string>|null $value
     * @return static
     */
    public function setSubscriptionReferences($value)
    {
        return $this->setParameter('subscriptionReferences', $value);
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;

        $subscriptions = null;
        $subscriptionIds = $this->getSubscriptionIds();
        $subscriptionReferences = $this->getSubscriptionReferences();
        if (!empty($subscriptionIds)) {
            $subscriptions = array();
            foreach ($subscriptionIds as $subscriptionId) {
                $subscription = new stdClass();
                $subscription->merchantAutoBillId = $subscriptionId;
                $subscriptions[] = $subscription;
            }
        } elseif (!empty($subscriptionReferences)) {
            $subscriptions = array();
            foreach ($subscriptionReferences as $subscriptionReference) {
                $subscription = new stdClass();
                $subscription->VID = $subscriptionReference;
                $subscriptions[] = $subscription;
            }
        }

        return array(
            'account' => $account,
            'autobills' => $subscriptions,
            'action' => $this->getFunction(),
            'force' => true,
            'disentitle' => false,
            'cancelReasonCode' => null
        );
    }
}
