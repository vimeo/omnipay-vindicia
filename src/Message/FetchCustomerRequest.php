<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch a Vindicia customer object.
 *
 * Parameters:
 * - customerId: Your identifier for the customer to be fetched. Either customerId
 * or customerReference is required.
 * - customerReference: The gateway's identifier for the customer to be fetched. Either
 * customerId or customerReference is required.
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
 *   // now we want to fetch the customer
 *   $response = $gateway->fetchCustomer(array(
 *       'customerId' => $customerResponse->getCustomerId() // could also do it by customerReference
 *   ))->send();
 *
 *   if ($response->isSuccessful()) {
 *       var_dump($response->getCustomer());
 *   }
 *
 * </code>
 */
class FetchCustomerRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getCustomerId() ? 'fetchByMerchantAccountId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$CUSTOMER_OBJECT;
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();

        if (!$customerId && !$customerReference) {
            throw new InvalidRequestException('Either the customerId or customerReference parameter is required.');
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($customerId) {
            $data['merchantAccountId'] = $this->getCustomerId();
        } else {
            $data['vid'] = $this->getCustomerReference();
        }

        return $data;
    }
}
