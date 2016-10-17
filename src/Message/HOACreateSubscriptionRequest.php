<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;

class HOACreateSubscriptionRequest extends AbstractHOARequest
{
    public static $REGULAR_REQUEST_CLASS = 'Omnipay\Vindicia\Message\CreateSubscriptionRequest';

    protected function getObjectParamNames()
    {
        return array(self::$SUBSCRIPTION_OBJECT => 'autobill');
    }

    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        return array(
            new NameValue(
                'AutoBill_Update_immediateAuthFailurePolicy',
                $regularRequestData['immediateAuthFailurePolicy']
            ),
            new NameValue('AutoBill_Update_validateForFuturePayment', $regularRequestData['validateForFuturePayment']),
            new NameValue('AutoBill_Update_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
            new NameValue('AutoBill_Update_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy']),
            new NameValue('AutoBill_Update_campaignCode', $regularRequestData['campaignCode']),
            new NameValue('AutoBill_Update_dryrun', $regularRequestData['dryrun']),
            new NameValue('AutoBill_Update_cancelReasonCode', $regularRequestData['cancelReasonCode']),
            new NameValue('AutoBill_Update_minChargebackProbability', $regularRequestData['minChargebackProbability'])
        );
    }
}
