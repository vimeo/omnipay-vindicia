<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch multiple chargebacks, either for a transaction or for a time window.
 *
 * Parameters:
 * - transactionId: Your identifier for the transaction whose chargebacks should be fetched.
 * Either transactionId or transactionReference is required if you're fetching by transaction.
 * - transactionReference: The gateway's identifier for the transaction whose chargebacks should
 * be fetched. Either transactionId or transactionReference is required if you're fetching by
 * transaction.
 * - startTime: The beginning of the date range for which chargebacks should be fetched.
 * If fetching by date range, startTime and endTime are required and a transaction cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 * - endTime: The end of the date range for which chargebacks should be fetched.
 * If fetching by date range, startTime and endTime are required and a transaction cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
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
 *   // now say there's a chargeback on the purchase...
 *
 *   // Now we can fetch the chargeback objects by transaction
 *   $fetchResponse = $gateway->fetchChargebacks(array(
 *       'transactionId' => $purchaseResponse->getTransactionId() // could do by transactionReference also
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getChargebacks());
 *   }
 *
 *   // Alternatively, we could fetch the chargebacks by date range
 *   $fetchResponse = $gateway->fetchChargebacks(array(
 *       'startTime' => '2016-06-01T12:30:00-04:00',
 *       'endTime' => '2016-07-01T12:30:00-04:00'
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getChargebacks());
 *   }
 * </code>
 */
class FetchChargebacksRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        if ($this->getTransactionId()) {
            return 'fetchByMerchantTransactionId';
        } elseif ($this->getTransactionReference()) {
            return 'fetchByVid';
        } else {
            return 'fetchDeltaSince';
        }
    }

    protected function getObject()
    {
        return self::$CHARGEBACK_OBJECT;
    }

    public function getData()
    {
        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();
        if (($transactionId || $transactionReference) && ($startTime || $endTime)) {
            throw new InvalidRequestException('Cannot fetch by both transaction and start/end time.');
        }
        if (!$transactionId && !$transactionReference && (!$startTime || !$endTime)) {
            throw new InvalidRequestException(
                'The transactionId parameter or the transactionReference parameter or the startTime and endTime '
                . 'parameters are required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($transactionId) {
            $data['merchantTransactionId'] = $transactionId;
        } elseif ($transactionReference) {
            $data['vid'] = $transactionReference;
        } else {
            $data['timestamp'] = $startTime;
            $data['endTimestamp'] = $endTime;
        }

        return $data;
    }
}
