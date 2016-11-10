<?php

namespace Omnipay\Vindicia\Message;

/**
 * Voids, or cancels, a previously authorized transaction. Will not work if the transaction
 * has already been captured, either by the capture function or purchase function.
 *
 * This should only be used after an authorize and should always reference either the
 * transactionId or transactionReference returned by the authorize call.
 *
 * This also cannot be used for PayPal transactions. If you have a PayPal transaction or
 * a card transaction that has already been captured, you should refund it instead.
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
 *   // At this point, no money has been transferred. Now we are going to void the transaction,
 *   // thereby canceling it and ensuring it will never be completed.
 *
 *   $voidResponse = $gateway->void(array(
 *       // You can identify the transaction by the transactionId or transactionReference
 *       // obtained from the authorize response
 *       'transactionId' => $authorizeResponse->getTransactionId(),
 *   ))->send();
 *
 *   if ($voidResponse->isSuccessful()) {
 *       // these are the same as they were on the authorize response, because it is the
 *       // same transaction
 *       echo "Transaction id: " . $voidResponse->getTransactionId() . PHP_EOL;
 *       echo "Transaction reference: " . $voidResponse->getTransactionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // If you tried to capture the transaction here, it would fail since it's been voided.
 *
 * </code>
 */
class VoidRequest extends CaptureRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'cancel';
    }
}
