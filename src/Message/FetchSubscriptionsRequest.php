<?php

namespace Omnipay\Vindicia\Message;

use stdClass;

/**
 * Fetch multiple subscriptions, either for a customer or for a time window.
 *
 * Parameters:
 * - customerId: Your identifier for the customer whose subscriptions should be fetched.
 * Either customerId or customerReference is required if you're fetching by customer.
 * - customerReference: The gateway's identifier for the customer whose subscriptions should
 * be fetched. Either customerId or customerReference is required if you're fetching by customer.
 * - startTime: The beginning of the date range for which subscriptions should be fetched.
 * If fetching by date range, startTime and endTime are required and a customer cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 * - endTime: The end of the date range for which subscriptions should be fetched.
 * If fetching by date range, startTime and endTime are required and a customer cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 *
 * Example by customer:
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
 *   // ... customer purchases some things ...
 *
 *   // fetch by customer
 *   $fetchResponse = $gateway->fetchSubscriptions(array(
 *       'customerId' => $customerResponse->getCustomerId() // could also use customerReference
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getSubscriptions());
 *   }
 *
 *   // alternatively, you could fetch by a time range
 *   $fetchResponse = $gateway->fetchSubscriptions(array(
 *       'startTime' => '2016-06-01T12:30:00-04:00',
 *       'endTime' => '2016-07-01T12:30:00-04:00'
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getSubscriptions());
 *   }
 *
 * </code>
 */
class FetchSubscriptionsRequest extends FetchTransactionsRequest
{
    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData()
    {
        $data = parent::getData();
        // foir some reason, page and pageSize are not optional for autobills
        $data['page'] = 0;
        $data['pageSize'] = 10000;
        return $data;
    }
}
