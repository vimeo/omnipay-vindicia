<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Vindicia\VindiciaRefundItemBag;

class RefundRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'perform';
    }

    protected function getObject()
    {
        return self::$REFUND_OBJECT;
    }

    /**
     * Get the note associated with this refund.
     *
     * @return string
     */
    public function getNote()
    {
        return $this->getParameter('note');
    }

    /**
     * Set a note to be associated with this refund.
     *
     * @param string $value
     * @return static
     */
    public function setNote($value)
    {
        return $this->setParameter('note', $value);
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
        $refund->amount = $this->getAmount();
        $refund->currency = $this->getCurrency();
        $refund->note = $this->getNote();

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
     * Use a special response object for Refund requests.
     *
     * @param object $response
     * @return Response
     */
    protected function buildResponse($response)
    {
        return new RefundResponse($this, $response);
    }
}
