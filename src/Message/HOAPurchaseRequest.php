<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

class HOAPurchaseRequest extends HOAAuthorizeRequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\PurchaseRequest';

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue(
                'Transaction_AuthCapture_minChargebackProbability',
                $regularRequestData['minChargebackProbability']
            ),
            new NameValue(
                'Transaction_AuthCapture_sendEmailNotification',
                $regularRequestData['sendEmailNotification']
            ),
            new NameValue('Transaction_AuthCapture_campaignCode', $regularRequestData['campaignCode']),
            new NameValue('Transaction_AuthCapture_dryrun', $regularRequestData['dryrun']),
            new NameValue('Transaction_AuthCapture_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
            new NameValue('Transaction_AuthCapture_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy'])
        );
    }
}
