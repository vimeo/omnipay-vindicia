<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Capture a previously authorized transaction.
 *
 * This should only be used after an authorize and should always reference either the
 * transactionId or transactionReference returned by the authorize call. It can and should
 * be used after an authorize from the Vindicia_HOA gateway as well as the regular Vindicia
 * gateway. Vindicia_PayPal does not support authorize, as such this function is not used
 * there.
 *
 * Parameters:
 * - transactionId: Your identifier to represent this transaction. Either the transactionId
 * or transactionReference is required to specify what transaction should be captured.
 * - transactionReference: The gateway's identifier to represent this transaction. Either the
 * transactionId or transactionReference is required to specify what transaction should be
 * captured.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // authorize the transaction
 *   $authorizeResponse = $gateway->authorize(array(
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
 *   if ($authorizeResponse->isSuccessful()) {
 *       // Note: Your transaction ID begins with a prefix you specified in your initial
 *       // Vindicia configuration. The ID is automatically assigned by Vindicia.
 *       echo "Transaction id: " . $authorizeResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $authorizeResponse->getTransactionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // At this point, no money has been transferred. Now we will capture the transaction to
 *   // complete it and transfer the money.
 *
 *   $captureResponse = $gateway->capture(array(
 *       // You can identify the transaction by the transactionId or transactionReference
 *       // obtained from the authorize response
 *       'transactionId' => $authorizeResponse->getTransactionId(),
 *   ))->send();
 *
 *   if ($captureResponse->isSuccessful()) {
 *       // these are the same as they were on the authorize response, because it is the
 *       // same transaction
 *       echo "Transaction id: " . $captureResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $captureResponse->getTransactionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 * </code>
 */
class CaptureRequest extends AbstractRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\CaptureResponse';

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'capture';
    }

    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    public function getData()
    {
        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        if (!$transactionId && !$transactionReference) {
            throw new InvalidRequestException(
                'Either the transactionId or transactionReference parameter is required.'
            );
        }

        $transaction = new stdClass();
        $transaction->merchantTransactionId = $transactionId;
        $transaction->VID = $transactionReference;

        $data = array();
        $data['transactions'] = array($transaction);
        $data['action'] = $this->getFunction();

        return $data;
    }

    /**
     * Overriding to provide a more precise return type
     * @return CaptureResponse
     */
    public function send()
    {
        /**
         * @var CaptureResponse
         */
        return parent::send();
    }
}
