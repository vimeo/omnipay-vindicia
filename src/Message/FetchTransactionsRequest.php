<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch multiple transactions, either for a customer or for a time window.
 *
 * Parameters:
 * - customerId: Your identifier for the customer whose transactions should be fetched.
 * Either customerId or customerReference is required if you're fetching by customer.
 * - customerReference: The gateway's identifier for the customer whose transactions should
 * be fetched. Either customerId or customerReference is required if you're fetching by customer.
 * - startTime: The beginning of the date range for which transactions should be fetched.
 * If fetching by date range, startTime and endTime are required and a customer cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 * - endTime: The end of the date range for which transactions should be fetched.
 * If fetching by date range, startTime and endTime are required and a customer cannot be
 * specified. Example: 2016-06-02T12:30:00-04:00 means June 2, 2016 @ 12:30 PM, GMT - 4 hours
 * - pageSize: The number of results to return. Will attempt to return up to 10000 if this
 * parameter is not specified. This request will likely time out if there are that many records
 * to return.
 * - page: The page number to return. Starts at 0. For example, if pageSize is 10 and page is
 * 0, returns the first 10 results. If page is 1, returns the second 10 results. Defaults to 0.
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
 *   // ... customer purchases some things ...
 *
 *   // fetch by customer
 *   $fetchResponse = $gateway->fetchTransactions(array(
 *       'customerId' => $customerResponse->getCustomerId() // could also use customerReference
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getTransactions());
 *   }
 *
 *   // alternatively, you could fetch by a time range and optionally page and page size
 *   $fetchResponse = $gateway->fetchTransactions(array(
 *       'startTime' => '2016-06-01T12:30:00-04:00',
 *       'endTime' => '2016-07-01T12:30:00-04:00',
 *       'page' => 5,
 *       'pageSize' => 500
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getTransactions());
 *   }
 *
 * </code>
 */
class FetchTransactionsRequest extends AbstractPageableRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getStartTime() ? 'fetchDeltaSince' : 'fetchByAccount';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    public function getData()
    {
        $customerId = $this->getCustomerId();
        $customerReference = $this->getCustomerReference();
        $startTime = $this->getStartTime();
        $endTime = $this->getEndTime();
        if (($customerId || $customerReference) && ($startTime || $endTime)) {
            throw new InvalidRequestException('Cannot fetch by both customer and start/end time.');
        }
        if (!$customerId && !$customerReference && (!$startTime || !$endTime)) {
            throw new InvalidRequestException(
                'The customerId parameter or the customerReference parameter or the startTime and endTime '
                . 'parameters are required.'
            );
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($customerId || $customerReference) {
            $account = new stdClass();
            $account->merchantAccountId = $customerId;
            $account->VID = $customerReference;

            $data['account'] = $account;
            $data['includeChildren'] = false;
        } else {
            $data['timestamp'] = $startTime;
            $data['endTimestamp'] = $endTime;
            $data['page'] = $this->getPage() ?: 0;
            $data['pageSize'] = $this->getPageSize() ?: self::DEFAULT_PAGE_SIZE;
        }

        return $data;
    }
}
