<?php

namespace Omnipay\Vindicia\Message;

use stdClass;

class FetchSubscriptionsRequest extends FetchTransactionsRequest
{
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
