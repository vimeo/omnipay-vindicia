<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use stdClass;

/**
 * Refund a transaction.
 *
 * This is for use after a transaction has been captured or purchased. If the transaction
 * has only been authorized, you can void it instead.
 */
class ChargebackRequest extends AbstractRequest
{
    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$CHARGEBACK_OBJECT;
    }

    /**
     * Get the chargeback id (not supported by Vindicia)
     *
     * @return null|string
     */
    public function getChargebackId()
    {
        return $this->getParameter('chargebackId');
    }

    /**
     * Set the chargeback id (not supported by Vindicia)
     *
     * @param string $value
     * @return static
     */
    public function setChargebackId($value)
    {
        return $this->setParameter('chargebackId', $value);
    }

    /**
     * Get the chargeback reference
     *
     * @return null|string
     */
    public function getChargebackReference()
    {
        return $this->getParameter('chargebackReference');
    }

    /**
     * Set the chargeback reference
     *
     * @param string $value
     * @return static
     */
    public function setChargebackReference($value)
    {
        return $this->setParameter('chargebackReference', $value);
    }

    /**
     * Get the chargeback reference
     *
     * @return null|string
     * @deprecated see getChargebackReference
     */
    public function getReference()
    {
        return $this->getChargebackReference();
    }

    /**
     * Set the chargeback reference
     *
     * @param string $value
     * @return static
     * @deprecated see setChargebackReference
     */
    public function setReference($value)
    {
        return $this->setChargebackReference($value);
    }

    /**
     * Get the currency
     *
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->getParameter('currency');
    }

    /**
     * Set the currency
     *
     * @param string $value
     * @return static
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Get the monetary amount
     *
     * @return null|string
     */
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    /**
     * Set the monetary amount
     *
     * @param string $value
     * @return static
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    /**
     * Get the status
     *
     * @return null|string
     */
    public function getStatus()
    {
        return $this->getParameter('status');
    }

    /**
     * Set the status
     *
     * @param string $value
     * @return static
     */
    public function setStatus($value)
    {
        return $this->setParameter('status', $value);
    }

    /**
     * Get the time the status changed
     *
     * @return null|string
     */
    public function getStatusChangedTime()
    {
        return $this->getParameter('statusChangedTime');
    }

    /**
     * Set the time the status changed
     *
     * @param string $value
     * @return static
     */
    public function setStatusChangedTime($value)
    {
        return $this->setParameter('statusChangedTime', $value);
    }

    /**
     * Get the time the payment processor received the chargeback
     *
     * @return null|string
     */
    public function getProcessorReceivedTime()
    {
        return $this->getParameter('processorReceivedTime');
    }

    /**
     * Set the time the payment processor received the chargeback
     *
     * @param string $value
     * @return static
     */
    public function setProcessorReceivedTime($value)
    {
        return $this->setParameter('processorReceivedTime', $value);
    }

    /**
     * Get the transaction id
     *
     * @return null|string
     */
    public function getTransactionId()
    {
        return $this->getParameter('transactionId');
    }

    /**
     * Set the transaction id
     *
     * @param string $value
     * @return static
     */
    public function setTransactionId($value)
    {
        return $this->setParameter('transactionId', $value);
    }

    /**
     * Get the reason code
     *
     * The reason code reported by your bank for this Chargeback object.
     * It's a 2-to-4-digit alphanumeric code provided by the issuing bank involved in a chargeback,
     * For example, reason code 'F14' is No Cardmember Authorization from Amex
     *
     * @return null|string
     */
    public function getReasonCode()
    {
        return $this->getParameter('reasonCode');
    }

    /**
     * Set the reason code
     *
     * @param string $value
     * @return static
     */
    public function setReasonCode($value)
    {
        return $this->setParameter('reasonCode', $value);
    }

    /**
     * Get the case number
     *
     * Your bank’s case number for this Chargeback object, if any.
     *
     * @return null|string
     */
    public function getCaseNumber()
    {
        return $this->getParameter('caseNumber');
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'report';
    }

    /**
     * Set the case number
     *
     * @param string $value
     * @return static
     */
    public function setCaseNumber($value)
    {
        return $this->setParameter('caseNumber', $value);
    }

    public function getData()
    {
        $transactionId = $this->getTransactionId();
        if (empty($transactionId)) {
            throw new InvalidRequestException(
                'Either the transactionId or transactionReference parameter is required.'
            );
        }

        $chargeback = new stdClass();
        $chargeback->merchantTransactionId = $transactionId;
        $chargeback->amount = $this->getAmount();
        $chargeback->referenceNumber = $this->getReference();
        $chargeback->processorReceivedTimestamp = $this->getProcessorReceivedTime();

        $attributes = $this->getAttributes();
        if ($attributes) {
            $chargeback->nameValues = $this->buildNameValues($attributes);
        }

        $data = array();
        $data['chargeback'] = $chargeback;
        $data['action'] = $this->getFunction();

        return $data;
    }
}
