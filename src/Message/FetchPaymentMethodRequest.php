<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch a Vindicia paymentMethod object.
 *
 * Parameters:
 * - paymentMethodId: Your identifier for the paymentMethod to be fetched. Either paymentMethodId
 * or paymentMethodReference is required.
 * - paymentMethodReference: The gateway's identifier for the paymentMethod to be fetched. Either
 * paymentMethodId or paymentMethodReference is required.
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
 *   // now we want to fetch the payment method
 *   $fetchResponse = $gateway->fetchPaymentMethod(array(
 *       'paymentMethodId' => $paymentMethodResponse->getPaymentMethodId() // could also do by reference
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getPaymentMethod());
 *   }
 * </code>
 */
class FetchPaymentMethodRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getPaymentMethodId() ? 'fetchByMerchantPaymentMethodId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$PAYMENT_METHOD_OBJECT;
    }

    public function getData()
    {
        $paymentMethodId = $this->getPaymentMethodId();
        $paymentMethodReference = $this->getPaymentMethodReference();

        if (!$paymentMethodId && !$paymentMethodReference) {
            throw new InvalidRequestException(
                'Either the paymentMethodId or paymentMethodReference parameter is required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($paymentMethodId) {
            $data['paymentMethodId'] = $this->getPaymentMethodId();
        } else {
            $data['vid'] = $this->getPaymentMethodReference();
        }

        return $data;
    }
}
