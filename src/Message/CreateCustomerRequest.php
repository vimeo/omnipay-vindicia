<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Vindicia\TaxExemptionBag;
use Omnipay\Common\Exception\InvalidRequestException;

class CreateCustomerRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'update';
    }

    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    public function getName()
    {
        return $this->getParameter('name');
    }

    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    public function getTaxExemptions()
    {
        return $this->getParameter('taxExemptions');
    }

    public function setTaxExemptions($value)
    {
        if ($value && !$value instanceof TaxExemptionBag) {
            $value = new TaxExemptionBag($value);
        }

        return $this->setParameter('taxExemptions', $value);
    }

    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$this->isUpdate()) {
            $this->validate('customerId');
        } elseif (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $card = $this->getCard();
        $paymentMethod = null;
        if ($card) {
            if ($this->isUpdate()) {
                throw new InvalidRequestException(
                    'Do not update a customer\'s card in an update customer request. Use update payment method instead.'
                );
            }
            $paymentMethod = $this->buildPaymentMethod(self::PAYMENT_METHOD_CREDIT_CARD);
        }

        $account = new stdClass();
        $account->name = $this->getName();
        $account->emailAddress = $this->getEmail();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;
        if ($paymentMethod !== null) {
            $account->paymentMethods = array($paymentMethod);
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $account->nameValues = $this->buildNameValues($attributes);
        }

        $taxExemptions = $this->getTaxExemptions();
        if (!empty($taxExemptions)) {
            $vindiciaTaxExemptions = array();

            foreach ($taxExemptions as $taxExemption) {
                $vindiciaTaxExemption = new stdClass();
                $vindiciaTaxExemption->active = $taxExemption->getActive();
                $vindiciaTaxExemption->exemptionId = $taxExemption->getExemptionId();
                $vindiciaTaxExemption->region = $taxExemption->getRegion();
                $vindiciaTaxExemptions[] = $vindiciaTaxExemption;
            }

            $account->taxExemptions = $vindiciaTaxExemptions;
        }

        return array(
            'account' => $account,
            'action' => $this->getFunction()
        );
    }
}
