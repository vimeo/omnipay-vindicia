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
 * - pageSize: The number of results to return. Will attempt to return up to 10000 if this
 * parameter is not specified. This request will likely time out if there are that many records
 * to return.
 * - page: The page number to return. Starts at 0. For example, if pageSize is 10 and page is
 * 0, returns the first 10 results. If page is 1, returns the second 10 results. Defaults to 0.
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
    const DEFAULT_PAGE_SIZE = 10000;

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    /**
     * Get the number of records to return per call
     *
     * @return null|int
     */
    public function getPageSize()
    {
        return $this->getParameter('pageSize');
    }

    /**
     * Set the number of records to return per call
     *
     * @param int $value
     * @return static
     */
    public function setPageSize($value)
    {
        return $this->setParameter('pageSize', $value);
    }

    /**
     * Get the page to return. Starts at 0.
     * For example, if pageSize is 10 and page is 0, returns the first 10
     * results. If page is 1, returns the second 10 results.
     *
     * @return null|int
     */
    public function getPage()
    {
        return $this->getParameter('page');
    }

    /**
     * Set the page to return. Starts at 0.
     * For example, if pageSize is 10 and page is 0, returns the first 10
     * results. If page is 1, returns the second 10 results.
     *
     * @param int $value
     * @return static
     */
    public function setPage($value)
    {
        return $this->setParameter('page', $value);
    }

    public function getData()
    {
        $data = parent::getData();
        // foir some reason, page and pageSize are not optional for autobills
        $data['page'] = $this->getPage() ?: 0;
        $data['pageSize'] = $this->getPageSize() ?: self::DEFAULT_PAGE_SIZE;
        return $data;
    }
}
