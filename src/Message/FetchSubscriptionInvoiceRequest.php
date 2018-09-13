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
 * - invoiceId: the invoice number which uniquely identifies the fetched invoice within the subscription
 * - invoiceState: the invoice state which limits the returned objects to the specified state:
 * Open, Due, Paid, Overdue, or WrittenOff.
 *
 * Example:
 * <code>
 *   // first create gateway, plan, product and subscription as described in FetchSubsciptionRequest
 *   // then use subscriptionId returned from $subscriptionResponse->getSubscriptionId()
 *   // and fetch an array of invoice numbers filtered by subscription id and invoice state
 *
 *   // get $subscriptionResponse from createSubscription call
 *
 *   $fetchInvoiceNumbersResponse = $gateway->fetchSubscriptionInvoice(array(
 *       'subscriptionId' => $subscriptionResponse->getSubscriptionId(), // could also do by reference
 *       'invoiceState' => 'Overdue'
 *   ))->send();
 *
 *   if ($fetchInvoiceNumbersResponse->isSuccessful()) {
 *       var_dump($fetchInvoiceNumbersResponse->getInvoiceNumbers());
 *   }
 *
 *   // then take the first invoice number as invoice id to fetch the invoice object
 *   // normally fetch invoice number only returns an array with one invoice identifier
 *
 *.  $invoice_numbers = $fetchInvoiceNumbersResponse->getInvoiceNumbers(); // array of strings
 *   if (!empty($invoice_numbers)) {
 *       $invoice_id = $invoice_numbers[0];
 *       $fetchInvoiceResponse = $gateway->fetchSubscriptionInvoice(array(
 *           'subscriptionId' => $subscriptionResponse->getSubscriptionId(), // could also do by reference
 *           'invoiceId' => $invoice_id
 *        ))->send();
 *
 *        if ($fetchInvoiceResponse->isSuccessful()) {
 *            var_dump($fetchInvoiceResponse->getInvoice());
 *        }
 *.  }
 * </code>
 */
class FetchSubscriptionInvoiceRequest extends AbstractRequest
{
    /**
     * Returns the invoice state, one of 'Open', 'Due', 'Paid', 'Overdue', or 'WrittenOff'
     *
     * @return null|string
     */
    public function getInvoiceState()
    {
        return $this->getParameter('invoiceState');
    }

    /**
     * Sets the invoice state
     *
     * @param string $value
     * @return static
     */
    public function setInvoiceState($value)
    {
        return $this->setParameter('invoiceState', $value);
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getInvoiceId() ? 'fetchInvoice' : 'fetchInvoiceNumbers';
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

        $invoiceId = $this->getInvoiceId();

        $data = array(
            'action' => $this->getFunction()
        );

        if ($invoiceId === null) {
            // fetchInvoiceNumbers call, should go before fetchInvoice call
            $data['autobill'] = $subscription;
            $data['invoicestate'] = $this->getInvoiceState(); // not camel case as described in the doc
        } else {
            // fetchInvoice call
            $data['autobill'] = $subscription;
            $data['invoiceId'] = $this->getInvoiceId();
            $data['asPDF'] = false;
            $data['statementTemplateId'] = null;
            $data['dunningIndex'] = 0;
            $data['language'] = 'en-US';
        }

        return $data;
    }
}
