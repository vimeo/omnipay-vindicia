<?php

namespace Omnipay\Vindicia\Message;

/**
 * Authorize a transaction using an ApplePayPayment object from the Apple Pay payment sheet on frontend.
 * No money will be transferred during authorization. After authorizing a transaction, call capture to 
 * complete the transaction and transfer the money. If you want to do authorize and 
 * capture in one step, use the purchase function, if available.
 *
 * You may use other fields from the ApplePayPayment object to fill out billing info.
 * This request only requires the 'token' field from the ApplePayPayment object.
 * 
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia ApplePay');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // authorize the transaction
 *   $authorizeResponse = $gateway->completeAuthorize(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2)
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'card' => array(
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card, which will
 *        // be attached to the customer's account
 *       'attributes' => array(
 *           'location' => 'FL'
 *       )
 *       //ApplePayPayment token extracted from ApplePayPayment object.
 *       //You retrieve the ApplePayPayment object when a user authorizes a payment
 *       //using the ApplePsy payment sheet.
 *       'token' => array(
 *           //ApplePayPayment token info
 *       )
 *   ))->send();
 *
 *   if ($authorizeResponse->isSuccessful()) {
 *       // Note: Your transaction ID begins with a prefix you specified in your initial
 *       // Vindicia configuration. The ID is automatically assigned by Vindicia.
 *       echo "Transaction id: " . $authorizeResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $authorizeResponse->getTransactionReference() . PHP_EOL;
 *       echo "The transaction risk score is: " . $authorizeResponse->getRiskScore();
 *   } else {
 *       // error handling
 *   }
 */

class ApplePayCompleteAuthorizeRequest extends AuthorizeRequest
{

    /**
     * Sets the token received from the parsed ApplePayPayment object.
     *
     * @param string $value
     * @return static
     */
    public function setToken($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('token', $value);
    }

    /**
     * Gets the token received from the parsed ApplePayPayment object.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $this->validate('token');

        return parent::getData(self::PAYMENT_METHOD_APPLE_PAY);
    }

    /**
     * Overriding to provide a more precise return type
     * @return AbstractResponse
     */
    public function send()
    {
        /**
         * @var AbstractResponse
         */
        return parent::send();
    }
}
