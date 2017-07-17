<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\NonStrippingCreditCard;
use stdClass;

class ObjectHelper
{
    /**
     * @return \Omnipay\Vindicia\Transaction
     */
    public function buildTransaction(stdClass $object)
    {
        $customer = isset($object->account) ? $this->buildCustomer($object->account) : null;
        $paymentMethod = isset($object->sourcePaymentMethod)
                       ? $this->buildPaymentMethod($object->sourcePaymentMethod)
                       : null;

        $items = null;
        if (isset($object->transactionItems)) {
            $items = array();
            foreach ($object->transactionItems as $item) {
                $price = null;
                if (isset($item->price) && isset($item->total)) {
                    // Vindicia only stores the total tax amount in the total field, not the price field
                    $price = $item->price == 0 && $item->total != 0 ? $item->total : $item->price;
                }
                $items[] = new VindiciaItem(array(
                    'price' => $price,
                    'sku' => isset($item->sku) ? $item->sku : null,
                    'quantity' => isset($item->quantity) ? $item->quantity : null,
                    'name' => isset($item->name) ? $item->name : null,
                    'taxClassification' => isset($item->taxClassification) ? $item->taxClassification : null
                ));
            }
        }

        $statusLog = null;
        if (isset($object->statusLog)) {
            $statusLog = array();
            foreach ($object->statusLog as $logEntry) {
                $statusLog[] = new TransactionStatus(array(
                    'status' => isset($logEntry->status) ? $logEntry->status : null,
                    'time' => isset($logEntry->timestamp) ? $logEntry->timestamp : null,
                    'authorizationCode' => isset($logEntry->creditCardStatus->authCode)
                                         ? $logEntry->creditCardStatus->authCode
                                         : null
                ));
            }
        }

        return new Transaction(array(
            'transactionId' => isset($object->merchantTransactionId) ? $object->merchantTransactionId : null,
            'transactionReference' => isset($object->VID) ? $object->VID : null,
            'currency' => isset($object->currency) ? $object->currency : null,
            'amount' => isset($object->amount) ? $object->amount : null,
            'customer' => $customer,
            'customerId' => isset($customer) ? $customer->getCustomerId() : null,
            'customerReference' => isset($customer) ? $customer->getCustomerReference() : null,
            'paymentMethod' => isset($paymentMethod) ? $paymentMethod : null,
            'paymentMethodId' => isset($paymentMethod) ? $paymentMethod->getPaymentMethodId() : null,
            'paymentMethodReference' => isset($paymentMethod) ? $paymentMethod->getPaymentMethodReference() : null,
            'items' => $items,
            'ip' => isset($object->sourceIp) ? $object->sourceIp : null,
            'authorizationCode' => isset($object->statusLog[0]->creditCardStatus->authCode)
                                 ? $object->statusLog[0]->creditCardStatus->authCode
                                 : null,
            'avsCode' => isset($object->statusLog[0]->creditCardStatus->avsCode)
                       ? $object->statusLog[0]->creditCardStatus->avsCode
                       : null,
            'cvvCode' => isset($object->statusLog[0]->creditCardStatus->cvnCode)
                       ? $object->statusLog[0]->creditCardStatus->cvnCode
                       : null,
            'status' => isset($object->statusLog[0]->status) ? $object->statusLog[0]->status : null,
            'statusLog' => $statusLog,
            'payPalEmail' => isset($object->statusLog[0]->payPalStatus->payPalEmail)
                           ? $object->statusLog[0]->payPalStatus->payPalEmail
                           : null,
            'payPalRedirectUrl' => isset($object->statusLog[0]->payPalStatus->redirectUrl)
                                 ? $object->statusLog[0]->payPalStatus->redirectUrl
                                 : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Chargeback
     */
    public function buildChargeback(stdClass $object)
    {
        return new Chargeback(array(
            'chargebackId' => null,
            'chargebackReference' => isset($object->VID) ? $object->VID : null,
            'transactionId' => isset($object->merchantTransactionId) ? $object->merchantTransactionId : null,
            'transactionReference' => null,
            'transaction' => null,
            'currency' => isset($object->currency) ? $object->currency : null,
            'amount' => isset($object->amount) ? $object->amount : null,
            'status' => isset($object->status) ? $object->status : null,
            'statusChangedTime' => isset($object->statusChangedTimestamp) ? $object->statusChangedTimestamp : null,
            'processorReceivedTime' => isset($object->processorReceivedTimestamp)
                                     ? $object->processorReceivedTimestamp
                                     : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Customer
     */
    public function buildCustomer(stdClass $object)
    {
        $taxExemptions = null;
        if (isset($object->taxExemptions)) {
            $taxExemptions = array();
            foreach ($object->taxExemptions as $exemption) {
                $taxExemptions[] = new TaxExemption(array(
                    'exemptionId' => isset($exemption->exemptionId) ? $exemption->exemptionId : null,
                    'region' => isset($exemption->region) ? $exemption->region : null,
                    'active' => isset($exemption->active) ? $exemption->active : null
                ));
            }
        }

        return new Customer(array(
            'customerId' => isset($object->merchantAccountId) ? $object->merchantAccountId : null,
            'customerReference' => isset($object->VID) ? $object->VID : null,
            'name' => isset($object->name) ? $object->name : null,
            'email' => isset($object->emailAddress) ? $object->emailAddress : null,
            'taxExemptions' => isset($taxExemptions) ? $taxExemptions : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\PaymentMethod
     */
    public function buildPaymentMethod(stdClass $object)
    {
        $cvv = null;
        $nameValues = null;
        if (isset($object->nameValues)) {
            $nameValues = $object->nameValues;
            foreach ($nameValues as $i => $nameValue) {
                if ($nameValue->name === 'CVN') {
                    $cvv = $nameValue->value;

                    // remove CVV so it's not in the attributes
                    unset($nameValues[$i]);
                    break;
                }
            }
        }

        return new PaymentMethod(array(
            'paymentMethodId' => isset($object->merchantPaymentMethodId) ? $object->merchantPaymentMethodId : null,
            'paymentMethodReference' => isset($object->VID) ? $object->VID : null,
            // NonStrippingCreditCard won't remove the X's that Vindicia masks with
            'card' => new NonStrippingCreditCard(array(
                'name' => isset($object->accountHolderName) ? $object->accountHolderName : null,
                'address1' => isset($object->billingAddress->addr1) ? $object->billingAddress->addr1 : null,
                'address2' => isset($object->billingAddress->addr2) ? $object->billingAddress->addr2 : null,
                'city' => isset($object->billingAddress->city) ? $object->billingAddress->city : null,
                'postcode' => isset($object->billingAddress->postalCode) ? $object->billingAddress->postalCode : null,
                'state' => isset($object->billingAddress->district) ? $object->billingAddress->district : null,
                'country' => isset($object->billingAddress->country) ? $object->billingAddress->country : null,
                'number' => isset($object->creditCard->account) ? $object->creditCard->account : null,
                'expiryMonth' => isset($object->creditCard->expirationDate)
                               ? substr($object->creditCard->expirationDate, 4)
                               : null,
                'expiryYear' => isset($object->creditCard->expirationDate)
                              ? substr($object->creditCard->expirationDate, 0, 4)
                              : null,
                'cvv' => $cvv
            )),
            'attributes' => isset($nameValues) ? $this->buildAttributes($nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Plan
     */
    public function buildPlan(stdClass $object)
    {
        return new Plan(array(
            'planId' => isset($object->merchantBillingPlanId) ? $object->merchantBillingPlanId : null,
            'planReference' => isset($object->VID) ? $object->VID : null,
            'interval' => isset($object->periods[0]->type) ? strtolower($object->periods[0]->type) : null,
            'intervalCount' => isset($object->periods[0]->quantity) ? strtolower($object->periods[0]->quantity) : null,
            'taxClassification' => isset($object->taxClassification) ? $object->taxClassification : null,
            'prices' => isset($object->periods[0]->prices) ? $this->buildPrices($object->periods[0]->prices) : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Product
     */
    public function buildProduct(stdClass $object)
    {
        $plan = isset($object->defaultBillingPlan) ? $this->buildPlan($object->defaultBillingPlan) : null;

        return new Product(array(
            'productId' => isset($object->merchantProductId) ? $object->merchantProductId : null,
            'productReference' => isset($object->VID) ? $object->VID : null,
            'plan' => isset($plan) ? $plan : null,
            'planId' => isset($plan) ? $plan->getPlanId() : null,
            'planReference' => isset($plan) ? $plan->getPlanReference() : null,
            'taxClassification' => isset($object->taxClassification) ? $object->taxClassification : null,
            'prices' => isset($object->prices) ? $this->buildPrices($object->prices) : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Refund
     */
    public function buildRefund(stdClass $object)
    {
        $transaction = isset($object->transaction) ? $this->buildTransaction($object->transaction) : null;

        $items = null;
        if (isset($object->refundItems)) {
            $items = array();
            foreach ($object->refundItems as $item) {
                $items[] = new VindiciaRefundItem(array(
                    'amount' => isset($item->amount) ? $item->amount: null,
                    'sku' => isset($item->sku) ? $item->sku : null,
                    'transactionItemIndexNumber' => isset($item->transactionItemIndexNumber)
                                                  ? $item->transactionItemIndexNumber
                                                  : null,
                    'taxOnly' => isset($item->taxOnly) ? $item->taxOnly : null,
                ));
            }
        }

        return new Refund(array(
            'refundId' => isset($object->merchantRefundId) ? $object->merchantRefundId : null,
            'refundReference' => isset($object->VID) ? $object->VID : null,
            'currency' => isset($object->currency) ? $object->currency : null,
            'amount' => isset($object->amount) ? $object->amount : null,
            'reason' => isset($object->note) ? $object->note : null,
            'time' => isset($object->timestamp) ? $object->timestamp : null,
            'transaction' => isset($transaction) ? $transaction : null,
            'transactionId' => isset($transaction) ? $transaction->getTransactionId() : null,
            'transactionReference' => isset($transaction) ? $transaction->getTransactionReference() : null,
            'items' => isset($items) ? $items : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return \Omnipay\Vindicia\Subscription
     */
    public function buildSubscription(stdClass $object)
    {
        $customer = isset($object->account) ? $this->buildCustomer($object->account) : null;
        $product = isset($object->items[0]->product) ? $this->buildProduct($object->items[0]->product) : null;
        $plan = isset($object->billingPlan) ? $this->buildPlan($object->billingPlan) : null;
        $paymentMethod = isset($object->paymentMethod) ? $this->buildPaymentMethod($object->paymentMethod) : null;

        $items = null;
        if (isset($object->items)) {
            $items = array();
            foreach ($object->items as $item) {
                $items[] = new VindiciaItem(array(
                    'itemReference' => isset($item->VID) ? $item->VID : null,
                    'sku' => isset($item->product->merchantProductId) ? $item->product->merchantProductId : null,
                    'index' => isset($item->index) ? $item->index : null,
                    'price' => isset($item->price) ? $item->price : null,
                    'quantity' => isset($item->quantity) ? $item->quantity : null,
                    'name' => isset($item->name) ? $item->name : null,
                    'taxClassification' => isset($item->taxClassification) ? $item->taxClassification : null,
                    'currency' => isset($item->currency) ? $item->currency : null
                ));
            }
        }

        return new Subscription(array(
            'subscriptionId' => isset($object->merchantAutoBillId) ? $object->merchantAutoBillId : null,
            'subscriptionReference' => isset($object->VID) ? $object->VID : null,
            'currency' => isset($object->currency) ? $object->currency : null,
            'customer' => isset($customer) ? $customer : null,
            'customerId' => isset($customer) ? $customer->getCustomerId() : null,
            'customerReference' => isset($customer) ? $customer->getCustomerReference() : null,
            'items' => $items,
            'product' => isset($product) ? $product : null,
            'productId' => isset($product) ? $product->getProductId() : null,
            'productReference' => isset($product) ? $product->getProductReference() : null,
            'plan' => isset($plan) ? $plan : null,
            'planId' => isset($plan) ? $plan->getPlanId() : null,
            'planReference' => isset($plan) ? $plan->getPlanReference() : null,
            'paymentMethod' => isset($paymentMethod) ? $paymentMethod : null,
            'paymentMethodId' => isset($paymentMethod) ? $paymentMethod->getPaymentMethodId() : null,
            'paymentMethodReference' => isset($paymentMethod) ? $paymentMethod->getPaymentMethodReference() : null,
            'ip' => isset($object->sourceIp) ? $object->sourceIp : null,
            'status' => isset($object->status) ? $object->status : null,
            'billingState' => isset($object->billingState) ? $object->billingState : null,
            'startTime' => isset($object->startTimestamp) ? $object->startTimestamp : null,
            'endTime' => isset($object->endTimestamp) ? $object->endTimestamp : null,
            'attributes' => isset($object->nameValues) ? $this->buildAttributes($object->nameValues) : null
        ));
    }

    /**
     * @return array<int, \Omnipay\Vindicia\Attribute>
     */
    protected function buildAttributes(array $nameValues)
    {
        $returnArray = array();
        foreach ($nameValues as $nameValue) {
            $returnArray[] = new Attribute(array(
                'name' => $nameValue->name,
                'value' => $nameValue->value
            ));
        }
        return $returnArray;
    }

    /**
     * @return array<int, \Omnipay\Vindicia\Price>
     */
    protected function buildPrices(array $prices)
    {
        $returnArray = array();
        foreach ($prices as $price) {
            $returnArray[] = new Price(array(
                'amount' => $price->amount,
                'currency' => $price->currency
            ));
        }
        return $returnArray;
    }
}
