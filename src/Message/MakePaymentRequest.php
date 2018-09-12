<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Make payment against an outstanding invoice amount.
 *
 * Parameters:
 * - subscriptionId: Your identifier for the subscription to be fetched. Either subscriptionId
 * or subscriptionReference is required.
 * - subscriptionReference: The gateway's identifier for the subscription to be fetched. Either
 * subscriptionId or subscriptionReference is required.
 * - paymentMethodId: The identifier for the payment method used to make the payment. Either
 * paymentMethodId or paymentMethodReference is required.
 * - paymentMethodReference: The gateway's identifier (VID) for the payment method used to
 * make the payment. Either paymentMethodId or paymentMethodReference is required.
 * - amount: The amount of the outstanding invoice balance. That should be obtained from reading
 * the invoice text to get the total amount due.
 *
 * Example:
 * <code>
 *   // first create gateway, plan, product and subscription as described in FetchSubsciptionRequest
 *   // then use subscriptionId returned from $subscriptionResponse->getSubscriptionId(),
 *   // the payment method id from payment method record and the amount due
 *
 *   // get $subscriptionResponse from createSubscription call
 *
 *   $makePaymentResponse = $gateway->makePayment(array(
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId(), // could also do by reference
 *       'paymentMethodId' => 'cc-123456' // could also do by reference (VID),
 *       'amount' => 59.95
 *   ))->send();
 *
 *   if ($makePaymentResponse->isSuccessful()) {
 *       echo 'Payment summary " . $makePaymentResponse->getSummary() . PHP_EOL;';
 *       vardump($makePaymentResponse->getTransaction());
 *   }
 *
 * </code>
 */
class MakePaymentRequest extends AbstractRequest
{
    /**
     * Returns the overdue amount
     *
     * @return null|string
     */
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    /**
     * Sets the overdue amount
     *
     * @param string $value
     * @return static
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'makePayment';
    }

    /**
     * @return string
     */
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

        $paymentMethodId = $this->getPaymentMethodId();
        $paymentMethodReference = $this->getPaymentMethodReference();
        if (!$paymentMethodId && !$paymentMethodReference) {
            throw new InvalidRequestException(
                'Either the $paymentMethodId or $paymentMethodReference parameter is required.'
            );
        }

        $amount = $this->getAmount();
        if (!$amount) {
            throw new InvalidRequestException('$amount parameter is required.');
        }

        $subscription = new stdClass();
        $subscription->merchantAutoBillId = $subscriptionId;
        $subscription->VID = $subscriptionReference;

        $data = array(
            'action' => $this->getFunction()
        );

        $data['autobill'] = $subscription;
        $data['paymentMethod'] = $this->buildPaymentMethod();
        $data['amount'] = $amount;
        $data['currency'] = null;
        $data['invoiceId'] = null;
        $data['overageDisposition'] = null;
        $data['note'] = 'Payment initiated by makePayment';

        return $data;
    }
}
