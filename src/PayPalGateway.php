<?php

namespace Omnipay\Vindicia;

/**
 * Vindicia PayPal Gateway
 *
 * Vindicia also supports purchases via PayPal Express. This gateway provides
 * that functionality. Note that PayPal Express is an offsite gateway.
 *
 * Example setup:
 * <code>
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_PayPal');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 * </code>
 */
class PayPalGateway extends AbstractVindiciaGateway
{
    /**
     * Get the gateway name.
     *
     * @return string
     */
    public function getName()
    {
        return 'Vindicia PayPal';
    }

    /**
     * Begin a PayPal purchase. A redirect URL will be returned to send the user to PayPal's
     * website.
     *
     * Uses all the same parameters as a regular purchase request. A card does not need to be
     * specified, but you may choose to in order to provide address or customer name information.
     * Providing a card number is meaningless and it will be ignored.
     *
     * Requires two additional parameters:
     * - returnUrl: The URL to which the user should be redirected after they complete the
     * purchase on PayPal's site.
     * - cancelUrl: The URL to which the user should be redirected if they cancel their purchase
     * on PayPal's site.
     *
     * Example:
     * <code>
     *   $response = $gateway->purchase(array(
     *       'amount' => '9.99',
     *       'currency' => 'USD',
     *       'customerId' => '123456789',
     *       'card' => array(
     *           'postcode' => '12345'
     *       ),
     *       'paymentMethodId' => 'pp-123456', // this ID will be assigned to the PayPal account
     *       'returnUrl' => 'http://www.example.com/order_summary,
     *       'cancelUrl' => 'http://www.example.com/cart
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Transaction id: " . $response->getTransactionId() . PHP_EOL;
     *       echo "Transaction reference: " . $response->getTransactionReference() . PHP_EOL;
     *       echo "Redirecting to: " . $response->getRedirectUrl();
     *       $response->redirect();
     *   }
     * </code>
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\PayPalPurchaseRequest', $parameters);
    }

    /**
     * Complete a PayPal purchase.
     *
     * After the user completes the purchase on PayPal's site, they will be redirected to
     * the returnUrl you specified in the purchase request. On this page, you should call
     * this function to finalize the purchase.
     *
     * Parameters:
     * - success: Denotes whether the user successfully completed the purchase on PayPal.
     * If they user is redirected to your returnUrl, you can set this to true. You could
     * set it to false and call this function on your cancelUrl to cancel the transaction,
     * or you can just leave it in the AuthorizePending state and it will never be completed.
     * - payPalTransactionReference: When PayPal redirects to the returnUrl or cancelUrl,
     * a vindicia_vid parameter will be added to the URL. It's value must be set here.
     *
     * Example:
     * <code>
     *   $response = $gateway->completePurchase(array(
     *       'success' => true,
     *       'payPalTransactionReference' => '1234567890abcdef1234567890abcdef'
     *   ))->send();
     *
     *   if ($response->isSuccessful()) {
     *       echo "Transaction id: " . $response->getTransactionId() . PHP_EOL;
     *       echo "Transaction reference: " . $response->getTransactionReference() . PHP_EOL;
     *   }
     * </code>
     */
    public function completePurchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CompletePayPalPurchaseRequest', $parameters);
    }

    /**
     * Create a subscription through PayPal. A redirect URL will be returned to send
     * the user to PayPal's website.
     *
     * Uses the same parameters as a regular create subscription request and the additional
     * parameters used on the PayPal purchase request.
     */
    public function createSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest', $parameters);
    }

    /**
     * Update a subscription through PayPal. A redirect URL will be returned to send
     * the user to PayPal's website.
     *
     * Uses the same parameters as a regular update subscription request and the additional
     * parameters used on the PayPal purchase request.
     *
     * You should NOT change the currency of an existing subscription.
     */
    public function updateSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest', $parameters, true);
    }

    /**
     * Complete a subscription creation through PayPal.
     *
     * Usage and parameters are the same as completePurchase.
     */
    public function completeCreateSubscription(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CompleteCreatePayPalSubscriptionRequest', $parameters);
    }

    /**
     * Complete a subscription update through PayPal.
     *
     * Usage and parameters are the same as completePurchase.
     */
    public function completeUpdateSubscription(array $parameters = array())
    {
        return $this->createRequest(
            '\Omnipay\Vindicia\Message\CompleteCreatePayPalSubscriptionRequest',
            $parameters,
            true
        );
    }

    /**
     * Update a payment method.
     *
     * Parameters and usage are the same as for createPaymentMethod, except that the
     * payment method can be referenced by the paymentMethodId or the paymentMethodReference
     * (the gateway's identifier for this payment method).
     *
     * Although you cannot create a PayPal payment method with createPaymentMethod, you
     * could update it to change stuff like the customer's name.
     */
    public function updatePaymentMethod(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $parameters, true);
    }

    // see AbstractVindiciaGateway for more functions and documentation
    // NOTE: PayPal transactions cannot be voided/canceld and should be refunded instead.
}
