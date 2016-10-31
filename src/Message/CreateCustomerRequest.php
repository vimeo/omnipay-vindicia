<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Vindicia\TaxExemptionBag;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create a new customer. Customers must be created before purchases can be made.
 * You can add a card for the customer at the same time that you create them.
 *
 * This request is also used to update an existing customer if called via
 * updateCustomer instead of createCustomer.
 *
 * Note that since you have to create a customer before purchasing, you may want
 * to do this immediately when the user creates an account on your site so you don't
 * have to send two requests at purchase time.
 *
 * Parameters:
 * - customerId: Your identifier to represent the customer. Required.
 * - name: The customer's name. Optional.
 * - email: The customer's email address. Optional.
 * - card: A new card to be added for the customer. If card is specified, paymentMethodId
 * should be as well.
 * - paymentMethodId: Your identifier for the payment method being added. This can reference
 * an existing saved payment method or can be used to assign an ID to the card passed in the
 * card parameter.
 * - paymentMethodReference: The gateway's identifier for the payment method. This would only
 * be used to attach an existing payment method to this new customer, and in this case can be
 * used in place of paymentMethodId if you wish.
 * - attributes: Custom values you wish to have stored with the customer. They have
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
 *   $createResponse = $gateway->createCustomer(array(
 *       'name' => 'Test Customer',
 *       'email' => 'customer@example.com',
 *       'customerId' => '123456789', // you choose this
 *       'card' => array(
 *           'number' => '5555555555554444',
 *           'expiryMonth' => '01',
 *           'expiryYear' => '2020',
 *           'cvv' => '123',
 *           'postcode' => '12345'
 *       ),
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'attributes' => array(
 *           'hasMustache' => false
 *       )
 *   ))->send();
 *
 *   if ($createResponse->isSuccessful()) {
 *       echo "Customer id: " . $createResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $createResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now the customer can buy stuff!
 *
 *   // now say we want to update the customer's email
 *   $updateResponse = $gateway->updateCustomer(array(
 *       'email' => 'customer@example.com',
 *       // identify the customer. you could also do this by customerReference
 *       'customerId' => $createResponse->getCustomerId()
 *   ))->send();
 *
 *   if ($updateResponse->isSuccessful()) {
 *       // same as from the create response
 *       echo "Customer id: " . $updateResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $updateResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 * </code>
 */
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

    /**
     * Get the customer's tax exemptions
     *
     * @return TaxExemptionBag
     */
    public function getTaxExemptions()
    {
        return $this->getParameter('taxExemptions');
    }

    /**
     * Set the customer's tax exemptions
     *
     * @param TaxExemptionBag|array $value
     * @return static
     */
    public function setTaxExemptions($value)
    {
        if ($value && !$value instanceof TaxExemptionBag) {
            $value = new TaxExemptionBag($value);
        }

        return $this->setParameter('taxExemptions', $value);
    }

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
