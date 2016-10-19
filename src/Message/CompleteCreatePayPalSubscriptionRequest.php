<?php

namespace Omnipay\Vindicia\Message;

/**
 * Complete a subscription creation through PayPal.
 *
 * After the user completes the purchase on PayPal's site, they will be redirected to
 * the returnUrl you specified in the create subscription request. On this page, you
 * should call this function to finalize the subscription purchase.
 *
 * This request is used for both completeCreateSubscription and completeUpdateSubscription.
 *
 * Parameters:
 * - success: Denotes whether the user successfully completed the purchase on PayPal.
 * If they user is redirected to your returnUrl, you can set this to true. You could
 * set it to false and call this function on your cancelUrl to cancel the transaction,
 * or you can just leave it in the AuthorizePending state and it will never be completed.
 * - payPalTransactionReference: When PayPal redirects to the returnUrl or cancelUrl,
 * a vindicia_vid parameter will be added to the URL. It's value must be set here.
 *
 * See CreatePayPalSubscriptioinRequest for a code example.
 */
class CompleteCreatePayPalSubscriptionRequest extends AbstractRequest
{
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'finalizePayPalAuth';
    }

    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
    public function getData()
    {
        $this->validate('payPalTransactionReference', 'success');

        return array(
            'action' => $this->getFunction(),
            'payPalTransactionId' => $this->getPayPalTransactionReference(),
            'success' => $this->getSuccess()
        );
    }
}
