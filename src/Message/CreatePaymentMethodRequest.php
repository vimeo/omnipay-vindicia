<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Helper;
use Omnipay\Common\Exception\InvalidRequestException;

class CreatePaymentMethodRequest extends AbstractRequest
{
    /**
     * Whether the card is required to make this request (false for HOA requests)
     *
     * @var bool
     */
    protected $cardRequired;

    /**
     * Constants used to tell Vindicia whether to validate the card before
     * adding it or just to update it.
     */
    const VALIDATE_CARD = 'Validate';
    const SKIP_CARD_VALIDATION = 'Update';

    public function initialize(array $parameters = array())
    {
        $this->cardRequired = true;

        if (!array_key_exists('validate', $parameters)) {
            $parameters['validate'] = false;
        }
        parent::initialize($parameters);

        return $this;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'updatePaymentMethod';
    }

    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    /**
     * If result is true, Vindicia will validate the card before adding it
     * (generally by a 99 cent authorization)
     *
     * @return int
     */
    public function getValidate()
    {
        return $this->getParameter('validate');
    }

    /**
     * If set to true, Vindicia will validate the card before adding it
     * (generally by a 99 cent authorization)
     *
     * @param bool $value
     * @return static
     */
    public function setValidate($value)
    {
        return $this->setParameter('validate', $value);
    }

    /**
     * Gets whether the request is invalid if the card parameter is not set.
     *
     * @return bool
     */
    public function getCardRequired()
    {
        return $this->cardRequired;
    }

    /**
     * Sets whether the request is invalid if the card parameter is not set.
     * Card is not needed for HOA requests since it comes from the form. This
     * should not be used elsewhere.
     *
     * @param bool $value
     * @return static
     */
    public function setCardRequired($value)
    {
        $this->cardRequired = $value;
        return $this;
    }

    public function getData($paymentMethodType = self::PAYMENT_METHOD_CREDIT_CARD)
    {
        $paymentMethodId = $this->getPaymentMethodId();
        $paymentMethodReference = $this->getPaymentMethodReference();
        if (!$this->isUpdate()) {
            $this->validate('paymentMethodId');
        } elseif (!$paymentMethodId && !$paymentMethodReference) {
            throw new InvalidRequestException(
                'Either the paymentMethodId or paymentMethodReference parameter is required.'
            );
        }

        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        if ($this->getCardRequired()) {
            $this->validate('card');
        }

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;

        $data = array();
        $data['account'] = $account;
        $data['paymentMethod'] = $this->buildPaymentMethod($paymentMethodType);
        $data['action'] = $this->getFunction();
        $data['replaceOnAllAutoBills'] = true;
        $data['updateBehavior'] = $this->getValidate() ? self::VALIDATE_CARD : self::SKIP_CARD_VALIDATION;
        $data['ignoreAvsPolicy'] = false;
        $data['ignoreCvnPolicy'] = false;

        return $data;
    }
}
