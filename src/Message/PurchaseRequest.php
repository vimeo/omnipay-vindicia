<?php

namespace Omnipay\Vindicia\Message;

/**
 * Purchase something! Money will be transferred. Calling purchase is the equivalent of
 * calling authorize and then calling capture.
 *
 * Takes the same parameters as authorize, plus those listed below. See Message\AuthorizeRequest.
 *
 * Parameters:
 * - ignoreAvsPolicy: Determines whether to check AVS validation on payment method. Default value is false.
 * - ignoreCvnPolicy: Determines whether to check CVN validation on payment method. Default value is false.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // purchase!
 *   $purchaseResponse = $gateway->purchase(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '3.50', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '9.99', 'quantity' => 2)
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'customerId' => '123456', // will be created if it doesn't already exist
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card, which will
 *                                         // be attached to the customer's account
 *       'attributes' => array(
 *           'location' => 'FL'
 *       )
 *   ))->send();
 *
 *   if ($purchaseResponse->isSuccessful()) {
 *       // Note: Your transaction ID begins with a prefix you specified in your initial
 *       // Vindicia configuration. The ID is automatically assigned by Vindicia.
 *       echo "Transaction id: " . $purchaseResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $purchaseResponse->getTransactionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 * </code>
 */
class PurchaseRequest extends AuthorizeRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'authCapture';
    }

    /**
     * @param string $paymentMethodType
     * @return array
     */
    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $data = parent::getData($paymentMethodType);

        $ignore_avs = $this->getIgnoreAvsPolicy();
        $ignore_cvn = $this->getIgnoreCvnPolicy();
        $data['ignoreAvsPolicy'] = $ignore_avs ? $ignore_avs : false;
        $data['ignoreCvnPolicy'] = $ignore_cvn ? $ignore_cvn : false;

        return $data;
    }

    /**
     * @return null|bool
     */
    public function getIgnoreAvsPolicy()
    {
        return $this->getParameter('ignoreAvsPolicy');
    }

    /**
     * @param bool $ignore
     * @return static
     */
    public function setIgnoreAvsPolicy($ignore)
    {
        $this->setParameter('ignoreAvsPolicy', $ignore);
    }

    /**
     * @return null|bool
     */
    public function getIgnoreCvnPolicy()
    {
        return $this->getParameter('ignoreCvnPolicy');
    }

    /**
     * @param bool $ignore
     * @return static
     */
    public function setIgnoreCvnPolicy($ignore)
    {
        $this->setParameter('ignoreCvnPolicy', $ignore);
    }
}
