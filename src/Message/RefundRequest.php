<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Vindicia\VindiciaRefundItemBag;

/**
 * Refund a transaction.
 *
 * This is for use after a transaction has been captured or purchased. If the transaction
 * has only been authorized, you can void it instead.
 *
 * Parameters:
 * - refundId: Your identifier to represent the refund. Required.
 * - transactionId: Your identifier to represent the transaction that should be refunded.
 * Either the transactionId or transactionReference is required.
 * - transactionReference: The gateway's identifier to represent the transaction that should
 * be refunded. Either the transactionId or transactionReference is required.
 * - amount: The amount to refund. If neither amount or items is provided, the remaining
 * balance will be refunded. If both are provided, the sum of the items must equal the amount.
 * The amount can be up to the remaining non-refunded balance of the transaction (ie, the total
 * amount of the transaction if it has not been refunded before). After a non-itemized refund
 * has been issued on a transaction, an itemized refund cannot be issued for the same
 * transaction.
 * - items: Line-items for the transaction. If neither amount or items is provided, the remaining
 * balance will be refunded. If both are provided, the sum of the items must equal the amount.
 * The amount can be up to the remaining non-refunded balance of the transaction (ie, the total
 * amount of the transaction if it has not been refunded before). After a non-itemized refund
 * has been issued on a transaction, an itemized refund cannot be issued for the same
 * transaction. Each item must contain a sku, amount, and transactionItemIndexNumber.
 * transactionItemIndex number is the position of the item in the original transaction, indexed
 * starting at 1. A taxOnly parameter can be set to true if only the tax should be refunded.
 * Refunding by items may not work if you are using Vindicia's old tax engine.
 * - reason: A reason to attach to the refund. Optional.
 * - attributes: Custom values you wish to have stored with the refund. They have
 * no affect on anything.
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
 *   // now we want to refund the purchase for some reason
 *   $refundResponse = $gateway->refund(array(
 *       'refundId' => '654321', // you choose this
 *       // identify the transaction above to refund. could also identify by transactionReference.
 *       'transactionId' => $purchaseResponse->getTransactionId(),
 *       'items' => array(
 *           array('transactionItemIndexNumber' => '1', 'sku' => '1', 'amount' => '3.50'),
 *           array('transactionItemIndexNumber' => '2', 'sku' => '2', 'amount' => '19.98')
 *       ),
 *       'amount' => '23.48', // not necessary since items are provided
 *       'reason' => 'The reason for the refund'
 *   ))->send();
 *
 *   if ($refundResponse->isSuccessful()) {
 *       echo "Refund id: " . $refundResponse->getRefundId() . PHP_EOL;
 *       echo "Refund reference: " . $refundResponse->getRefundReference() . PHP_EOL;
 *       echo "Refund amount: " . $refundResponse->getRefund()->getAmount() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 * </code>
 */
class RefundRequest extends AbstractRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\RefundResponse';

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'perform';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$REFUND_OBJECT;
    }

    /**
     * Get the reason associated with this refund.
     *
     * @return null|string
     */
    public function getReason()
    {
        return $this->getParameter('reason');
    }

    /**
     * Set a reason to be associated with this refund.
     *
     * @param string $value
     * @return static
     */
    public function setReason($value)
    {
        return $this->setParameter('reason', $value);
    }

    /**
     * Get the reason associated with this refund.
     *
     * @return null|string
     * @deprecated in favor of getReason
     */
    public function getNote()
    {
        return $this->getReason();
    }

    /**
     * Set a reason to be associated with this refund.
     *
     * @param string $value
     * @return static
     * @deprecated in favor of setReason
     */
    public function setNote($value)
    {
        return $this->setReason($value);
    }

    /**
     * Set the items
     *
     * @todo this isn't completely tested because itemized refunds aren't supported
     * by our tax vendor.
     *
     * @param VindiciaRefundItemBag|array $items
     * @return static
     */
    public function setItems($items)
    {
        if ($items && !$items instanceof VindiciaRefundItemBag) {
            $items = new VindiciaRefundItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    public function getData()
    {
        $this->validate('refundId');

        $transactionId = $this->getTransactionId();
        $transactionReference = $this->getTransactionReference();
        if (empty($transactionId) && empty($transactionReference)) {
            throw new InvalidRequestException(
                'Either the transactionId or transactionReference parameter is required.'
            );
        }

        $items = $this->getItems();
        $amount = $this->getAmount();

        $refund = new stdClass();
        $refund->merchantRefundId = $this->getRefundId();
        $refund->amount = $this->getAmount();
        $refund->currency = $this->getCurrency();
        $refund->note = $this->getReason();

        $transaction = new stdClass();
        $transaction->merchantTransactionId = $transactionId;
        $transaction->VID = $transactionReference;
        $refund->transaction = $transaction;

        if (!empty($items)) {
            $refundItems = array();
            $totalRefundAmountFromItems = '0.0';
            foreach ($items as $item) {
                $item->validate();

                $refundItem = new stdClass();
                $refundItem->amount = $item->getAmount();
                $refundItem->taxOnly = $item->getTaxOnly();
                $refundItem->sku = $item->getSku();
                $refundItem->transactionItemIndexNumber = $item->getTransactionItemIndexNumber();

                $refundItems[] = $refundItem;

                // strval to avoid floating point error
                $totalRefundAmountFromItems = strval($totalRefundAmountFromItems + $refundItem->amount);
            }

            if ($amount && floatval($amount) !== floatval($totalRefundAmountFromItems)) {
                throw new InvalidRequestException('Sum of refund item amounts not equal to set amount.');
            }

            $refund->refundItems = $refundItems;
            $refund->refundDistributionStrategy = 'SpecifiedItems';

        } elseif (!empty($amount)) {
            $refund->amount = $amount;
            $refund->refundDistributionStrategy = 'None';
        } else {
            // if neither amount nor items is set, refund the remaining balance
            $refund->refundDistributionStrategy = 'RemainingBalance';
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $refund->nameValues = $this->buildNameValues($attributes);
        }

        $data = array();
        $data['refunds'] = array($refund);
        $data['action'] = $this->getFunction();

        return $data;
    }

    /**
     * Overriding to provide a more precise return type
     * @return RefundResponse
     */
    public function send()
    {
        /**
         * @var RefundResponse
         */
        return parent::send();
    }
}
