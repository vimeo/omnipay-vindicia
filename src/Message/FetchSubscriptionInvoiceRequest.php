<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch a Vindicia subscription invoice object.
 *
 * Parameters:
 * - subscriptionId: Your identifier for the subscription to be fetched. Either subscriptionId
 * or subscriptionReference is required.
 * - subscriptionReference: The gateway's identifier for the subscription to be fetched. Either
 * subscriptionId or subscriptionReference is required.
 * - invoiceReference: the invoice number which uniquely identifies the fetched invoice within
 * the subscription. This number should be obtained from FetchSubscriptionInvoiceReferences request.
 *
 * Example:
 * <code>
 *   // first create gateway, plan, product and subscription as described in FetchSubsciptionRequest
 *   // then use subscriptionId returned from $subscriptionResponse->getSubscriptionId()
 *   // and fetch an array of invoice references filtered by subscription id and invoice state
 *
 *   // get $subscriptionResponse from createSubscription call
 *
 *   $fetchInvoiceReferencesResponse = $gateway->fetchSubscriptionInvoiceReferences(array(
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId(), // could also do by reference
 *       'invoiceState' => 'Overdue'
 *   ))->send();
 *
 *   if ($fetchInvoiceReferencesResponse->isSuccessful()) {
 *       var_dump($fetchInvoiceReferencesResponse->getInvoiceReferences());
 *   }
 *
 *   // then take the first invoice reference as invoice identifier to fetch the invoice object
 *   // normally fetch invoice references only returns an array with one invoice identifier
 *
 *.  $invoice_references = $fetchInvoiceReferencesResponse->getInvoiceReferences(); // array of strings
 *   if (!empty($invoice_references)) {
 *       $invoice_reference = $invoice_references[0];
 *       $fetchInvoiceResponse = $gateway->fetchSubscriptionInvoice(array(
 *           'subscriptionId' => $subscriptionResponse->getSubscriptionId(), // could also do by reference
 *           'invoiceReference' => $invoice_reference
 *       ))->send();
 *
 *       if ($fetchInvoiceResponse->isSuccessful()) {
 *           var_dump($fetchInvoiceResponse->getInvoice());
 *       }
 *.  }
 * </code>
 */
class FetchSubscriptionInvoiceRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'fetchInvoice';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$SUBSCRIPTION_OBJECT;
    }

    public function getData()
    {
        $subscriptionId = $this->getSubscriptionId();
        $subscriptionReference = $this->getSubscriptionReference();

        if (!$subscriptionId && !$subscriptionReference) {
            throw new InvalidRequestException(
                'Either the subscriptionId or subscriptionReference parameter is required.'
            );
        }

        $subscription = new stdClass();
        $subscription->merchantAutoBillId = $subscriptionId;
        $subscription->VID = $subscriptionReference;

        $invoiceReference = $this->getInvoiceReference();
        if (!$invoiceReference) {
            throw new InvalidRequestException('The invoiceReference parameter is required.');
        }

        $data = array(
            'action' => $this->getFunction()
        );

        $data['autobill'] = $subscription;
        $data['invoiceId'] = $invoiceReference;
        $data['asPDF'] = false;    // using HTML text format as default
        $data['statementTemplateId'] = null;    // using the default template defined by the AutoBill
        $data['dunningIndex'] = 0;    // by default we fetch the invoice that was the first issued to the user
        $data['language'] = 'en-US';

        return $data;
    }
}
