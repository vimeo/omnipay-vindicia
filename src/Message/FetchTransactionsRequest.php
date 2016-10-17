<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class FetchTransactionsRequest extends AbstractRequest
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
        }

        return $data;
    }
}
