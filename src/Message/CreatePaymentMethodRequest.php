<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Helper;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create a new payment method and attach it to a customer. Or, update an existing
 * payment method.
 *
 * Note: You can also create a payment method in the same request as creating a customer.
 * See Message\CreateCustomerRequest.
 *
 * Parameters:
 * - customerId: Your identifier for the customer to whom this payment method will belong.
 * Either customerId or customerReference is required.
 * - customerReference: The gateway's identifier for the customer to whom this payment method
 * will belong. Either customerId or customerReference is required.
 * - card: The card details you're adding. Required. Attributes can also be specified on the
 * card.
 * - paymentMethodId: Your identifier for the payment method. Required.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a customer
 *   $customerResponse = $gateway->createCustomer(array(
 *       'name' => 'Test Customer',
 *       'email' => 'customer@example.com',
 *       'customerId' => '123456789'
 *   ))->send();
 *
 *   if ($customerResponse->isSuccessful()) {
 *       echo "Customer id: " . $customerResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $customerResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // add a payment method for that customer
 *   $paymentMethodResponse = $gateway->createPaymentMethod(array(
 *       'customerId' => $customerResponse->getCustomerId(), // alternatively you could use customerReference
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345',
 *           'attributes' => array(
 *               'color' => 'blue'
 *           )
 *       ),
 *       'paymentMethodId' => 'cc-123456' // you choose this
 *   ))->send();
 *
 *   if ($paymentMethodResponse->isSuccessful()) {
 *       // This is the payment method ID you set above
 *       echo "Payment method id: " . $paymentMethodResponse->getPaymentMethodId() . PHP_EOL;
 *       echo "Payment method reference: " . $paymentMethodResponse->getPaymentMethodReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now say we want to update the expiration date of the payment method
 *   $updateResponse = $gateway->updatePaymentMethod(array(
 *       'card' => array(
 *           'expiryMonth' => '02',
 *           'expiryYear' => '2025',
 *       ),
 *       'paymentMethodId' => $paymentMethodResponse->getPaymentMethodId() // reference payment method created above
 *   ))->send();
 *
 *   if ($updateResponse->isSuccessful()) {
 *       // This is the same payment method ID you set above
 *       echo "Payment method id: " . $updateResponse->getPaymentMethodId() . PHP_EOL;
 *       echo "Payment method reference: " . $updateResponse->getPaymentMethodReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 * </code>
 */
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

    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
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
