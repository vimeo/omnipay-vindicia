<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CreateSubscriptionRequest extends AuthorizeRequest
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
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $subscriptionId = $this->getSubscriptionId();
        $subscriptionReference = $this->getSubscriptionReference();
        if (!$this->isUpdate()) {
            $this->validate('subscriptionId');
        } elseif (!$subscriptionId && !$subscriptionReference) {
            throw new InvalidRequestException(
                'Either the subscriptionId or subscriptionReference parameter is required.'
            );
        }

        $productId = $this->getProductId();
        $productReference = $this->getProductReference();
        if (!$productId && !$productReference) {
            throw new InvalidRequestException('Either the productId or productReference parameter is required.');
        }

        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $subscription = new stdClass();
        $subscription->billingStatementIdentifier = $this->getStatementDescriptor();
        $subscription->currency = $this->getCurrency();
        $subscription->merchantAutoBillId = $subscriptionId;
        $subscription->VID = $subscriptionReference;
        $subscription->sourceIp = $this->getIp();
        $subscription->startTimestamp = $this->getStartTime();
        $subscription->statementFormat = 'DoNotSend';

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;
        $subscription->account = $account;

        $plan = new stdClass();
        $plan->merchantBillingPlanId = $this->getPlanId();
        $plan->VID = $this->getPlanReference();
        $subscription->billingPlan = $plan;

        $product = new stdClass();
        $product->merchantProductId = $productId;
        $product->VID = $productReference;
        $item = new stdClass();
        $item->product = $product;
        $subscription->items = array($item);

        $attributes = $this->getAttributes();
        if ($attributes) {
            $subscription->nameValues = $this->buildNameValues($attributes);
        }

        $subscription->paymentMethod = $this->buildPaymentMethod($paymentMethodType);

        return array(
            'autobill' => $subscription,
            'action' => $this->getFunction(),
            'immediateAuthFailurePolicy' => 'doNotSaveAutoBill',
            'validateForFuturePayment' => true,
            'ignoreAvsPolicy' => false,
            'ignoreCvnPolicy' => false,
            'campaignCode' => null,
            'dryrun' => false,
            'cancelReasonCode' => null,
            'minChargebackProbability' => $this->getMinChargebackProbability()
        );
    }
}
