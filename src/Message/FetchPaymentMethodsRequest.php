<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch all payment methods for a customer.
 *
 * Parameters:
 * - customerId: Your identifier for the customer whose payment methods should be fetched.
 * Either customerId or customerReference is required.
 * - customerReference: The gateway's identifier for the customer whose paymentMethods should
 * be fetched. Either customerId or customerReference is required.
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
 *   // now we want to fetch all the customer's payment methods
 *   $response = $gateway->fetchPaymentMethods(array(
 *       'customerId' => $customerResponse->getCustomerId() // could do by customerReference also
 *   ))->send();
 *
 *   if ($response->isSuccessful()) {
 *       var_dump($response->getPaymentMethods());
 *   }
 * </code>
 */
class FetchPaymentMethodsRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'fetchByAccount';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$PAYMENT_METHOD_OBJECT;
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException(
                'The customerId parameter or the customerReference parameter is required.'
            );
        }

        $account = new stdClass();
        $account->merchantAccountId = $customerId;
        $account->VID = $customerReference;

        return array(
            'action' => $this->getFunction(),
            'account' => $account,
            'includeChildren' => false
        );
    }
}
