<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class AuthorizeRequest extends AbstractRequest
{
    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('minChargebackProbability', $parameters)) {
            $parameters['minChargebackProbability'] = self::DEFAULT_MIN_CHARGEBACK_PROBABILITY;
        }
        parent::initialize($parameters);

        return $this;
    }

    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'auth';
    }

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        if (empty($this->getAmount()) && empty($this->getItems())) {
            throw new InvalidRequestException('Either the amount or items parameter is required.');
        }

        // if it's not an update, the customer must be specified
        if (!$this->isUpdate() && empty($this->getCustomerId()) && empty($this->getCustomerReference())) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $transaction = $this->buildTransaction($paymentMethodType);

        $data = array();
        $data['transaction'] = $transaction;
        $data['action'] = $this->getFunction();
        // default, all transactions will succeed
        $data['minChargebackProbability'] = $this->getMinChargebackProbability();
        $data['sendEmailNotification'] = false;
        $data['campaignCode'] = null;
        $data['dryrun'] = false;

        return $data;
    }
}
