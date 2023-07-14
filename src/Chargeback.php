<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Generic representation of a chargeback object returned by a gateway.
 * Hopefully this can be added to omnipay-common one day.
 */
class Chargeback
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new chargeback with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this chargeback with the specified parameters
     *
     * @param array $parameters An array of parameters to set on this object
     * @return static
     */
    public function initialize($parameters = array())
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * @return mixed
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    /**
     * @return Chargeback
     */
    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
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
     * Get the chargeback id (not supported by Vindicia)
     *
     * @return null|string
     * @deprecated see getChargebackId
     */
    public function getId()
    {
        return $this->getChargebackId();
    }

    /**
     * Set the chargeback id (not supported by Vindicia)
     *
     * @param string $value
     * @return static
     * @deprecated see setChargebackId
     */
    public function setId($value)
    {
        return $this->setChargebackId($value);
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
     * Get the transaction (not provided by vindicia)
     *
     * @return null|Transaction
     */
    public function getTransaction()
    {
        return $this->getParameter('transaction');
    }

    /**
     * Set the transaction
     *
     * @param Transaction $value
     * @return static
     */
    public function setTransaction($value)
    {
        return $this->setParameter('transaction', $value);
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
     * Get the transaction reference (not provided by vindicia)
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        return $this->getParameter('transactionReference');
    }

    /**
     * Set the transaction reference
     *
     * @param string $value
     * @return static
     */
    public function setTransactionReference($value)
    {
        return $this->setParameter('transactionReference', $value);
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
     * Set the case number
     *
     * @param string $value
     * @return static
     */
    public function setCaseNumber($value)
    {
        return $this->setParameter('caseNumber', $value);
    }

    public function getDivisionNumber(): ?string
    {
        return $this->getParameter('divisionNumber');
    }

    public function setDivisionNumber($value): self
    {
        return $this->setParameter('divisionNumber', $value);
    }

    public function getMerchantNumber(): ?string
    {
        return $this->getParameter('merchantNumber');
    }

    public function setMerchantNumber($value): self
    {
        return $this->setParameter('merchantNumber', $value);
    }

    public function getMerchantTransactionId(): ?string
    {
        return $this->getParameter('merchantTransactionId');
    }

    public function setMerchantTransactionId($value): self
    {
        return $this->setParameter('merchantTransactionId', $value);
    }

    public function getMerchantTransactionTimestamp(): ?string
    {
        return $this->getParameter('merchantTransactionTimestamp');
    }

    public function setMerchantTransactionTimestamp($value): self
    {
        return $this->setParameter('merchantTransactionTimestamp', $value);
    }

    public function getMerchantUserId(): ?string
    {
        return $this->getParameter('merchantUserId');
    }

    public function setMerchantUserId($value): self
    {
        return $this->setParameter('merchantUserId', $value);
    }

    public function getNote(): ?string
    {
        return $this->getParameter('note');
    }

    public function setNote($value): self
    {
        return $this->setParameter('note', $value);
    }

    public function getPresentmentAmount(): ?float
    {
        return $this->getParameter('presentmentAmount');
    }

    public function setPresentmentAmount($value): self
    {
        return $this->setParameter('presentmentAmount', $value);
    }

    public function getPresentmentCurrency(): ?string
    {
        return $this->getParameter('presentmentCurrency');
    }

    public function setPresentmentCurrency($value): self
    {
        return $this->setParameter('presentmentCurrency', $value);
    }

    public function getPostedTimestamp(): ?string
    {
        return $this->getParameter('postedTimestamp');
    }

    public function setPostedTimestamp($value): self
    {
        return $this->setParameter('postedTimestamp', $value);
    }

    public function getProcessorReceivedTimestamp(): ?string
    {
        return $this->getParameter('processorReceivedTimestamp');
    }

    public function setProcessorReceivedTimestamp($value): self
    {
        return $this->setParameter('processorReceivedTimestamp', $value);
    }

    public function getReferenceNumber(): ?string
    {
        return $this->getParameter('referenceNumber');
    }

    public function setReferenceNumber($value): self
    {
        return $this->setParameter('referenceNumber', $value);
    }

    public function getStatusChangedTimestamp(): ?string
    {
        return $this->getParameter('statusChangedTimestamp');
    }

    public function setStatusChangedTimestamp($value): self
    {
        return $this->setParameter('statusChangedTimestamp', $value);
    }

    public function getVID(): ?string
    {
        return $this->getParameter('VID');
    }

    public function setVID($value): self
    {
        return $this->setParameter('VID', $value);
    }
}
