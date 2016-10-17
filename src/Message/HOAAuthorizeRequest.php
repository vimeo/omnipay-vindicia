<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

class HOAAuthorizeRequest extends AbstractHOARequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\AuthorizeRequest';

    protected function getObjectParamNames()
    {
        return array(self::$TRANSACTION_OBJECT => 'transaction');
    }

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue('Transaction_Auth_minChargebackProbability', $regularRequestData['minChargebackProbability']),
            new NameValue('Transaction_Auth_sendEmailNotification', $regularRequestData['sendEmailNotification']),
            new NameValue('Transaction_Auth_campaignCode', $regularRequestData['campaignCode']),
            new NameValue('Transaction_Auth_dryrun', $regularRequestData['dryrun'])
        );
    }
}
