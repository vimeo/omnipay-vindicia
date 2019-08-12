<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Vindicia\ObjectHelper;
use Omnipay\Common\Message\RequestInterface;

class Response extends AbstractResponse
{
    /**
     * @var array<int>
     */
    protected static $SUCCESS_CODES = array(
        200,
        202 // Authorize requests can return a 202 if the tax service goes down.
            // Vindicia will, by default, proceed but use inclusive taxes.
    );

    /**
     * @var ObjectHelper
     */
    protected $objectHelper;

    // Cached objects:
    /**
     * @var \Omnipay\Vindicia\Transaction
     */
    protected $transaction;
    /**
     * @var \Omnipay\Vindicia\Subscription
     */
    protected $subscription;
    /**
     * @var \Omnipay\Vindicia\Customer
     */
    protected $customer;
    /**
     * @var \Omnipay\Vindicia\Plan
     */
    protected $plan;
    /**
     * @var \Omnipay\Vindicia\Product
     */
    protected $product;
    /**
     * @var \Omnipay\Vindicia\PaymentMethod
     */
    protected $paymentMethod;
    /**
     * @var array<\Omnipay\Vindicia\Refund>
     */
    protected $refunds;
    /**
     * @var array<\Omnipay\Vindicia\Transaction>
     */
    protected $transactions;
    /**
     * @var array<\Omnipay\Vindicia\Subscription>
     */
    protected $subscriptions;
    /**
     * @var array<\Omnipay\Vindicia\Chargeback>
     */
    protected $chargebacks;
    /**
     * @var array<\Omnipay\Vindicia\PaymentMethod>
     */
    protected $paymentMethods;
    /**
     * @var int
     */
    protected $billingDay;

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
        $this->objectHelper = new ObjectHelper();
    }

    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isSuccessful()
    {
        return in_array(intval($this->getCode()), static::getSuccessCodes(), true);
    }

    /**
     * Get the response message from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        if (isset($this->data->return)) {
            return $this->data->return->returnString;
        }
        throw new InvalidResponseException('Response has no message.');
    }

    /**
     * Get the response code from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getCode()
    {
        if (isset($this->data->return)) {
            return $this->data->return->returnCode;
        }
        throw new InvalidResponseException('Response has no code.');
    }

    /**
     * @return bool
     */
    public function isCvvValidationFailure()
    {
        return !$this->isSuccessful()
            && in_array($this->getCode(), array('408', '409'))
            && strpos($this->getMessage(), 'CVN');
    }

    /**
     * @return null|\Omnipay\Vindicia\Transaction
     */
    public function getTransaction()
    {
        if (!isset($this->transaction) && isset($this->data->transaction)) {
            $this->transaction = $this->objectHelper->buildTransaction($this->data->transaction);
        }
        return isset($this->transaction) ? $this->transaction : null;
    }

    /**
     * Get the reference provided by the gateway to represent this transaction
     *
     * @return string|null
     */
    public function getTransactionReference()
    {
        $transaction = $this->getTransaction();
        if ($transaction) {
            return $transaction->getTransactionReference();
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this transaction
     * NOTE: When you create a new transaction, Vindicia automatically
     * generates this value with the prefix you specified during initial
     * configuration.
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        $transaction = $this->getTransaction();
        if ($transaction) {
            return $transaction->getTransactionId();
        }
        return null;
    }

    /**
     * @return null|\Omnipay\Vindicia\Customer
     */
    public function getCustomer()
    {
        if (!isset($this->customer) && isset($this->data->account)) {
            $this->customer = $this->objectHelper->buildCustomer($this->data->account);
        }
        return isset($this->customer) ? $this->customer : null;
    }

    /**
     * Get the reference provided by the gateway to represent this customer.
     *
     * @return string|null
     */
    public function getCustomerReference()
    {
        $customer = $this->getCustomer();
        if ($customer) {
            return $customer->getCustomerReference();
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this customer.
     * This ID must be provided when creating a purchase/authorize request.
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        $customer = $this->getCustomer();
        if ($customer) {
            return $customer->getCustomerId();
        }
        return null;
    }

    /**
     * @return null|\Omnipay\Vindicia\Plan
     */
    public function getPlan()
    {
        if (!isset($this->plan) && isset($this->data->billingPlan)) {
            $this->plan = $this->objectHelper->buildPlan($this->data->billingPlan);
        }
        return isset($this->plan) ? $this->plan : null;
    }

    /**
     * Get the reference provided by the gateway to represent this plan.
     *
     * @return string|null
     */
    public function getPlanReference()
    {
        $plan = $this->getPlan();
        if ($plan) {
            return $plan->getPlanReference();
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this plan.
     *
     * @return string|null
     */
    public function getPlanId()
    {
        $plan = $this->getPlan();
        if ($plan) {
            return $plan->getPlanId();
        }
        return null;
    }

    /**
     * @return null|\Omnipay\Vindicia\Product
     */
    public function getProduct()
    {
        if (!isset($this->product) && isset($this->data->product)) {
            $this->product = $this->objectHelper->buildProduct($this->data->product);
        }
        return isset($this->product) ? $this->product : null;
    }

    /**
     * Get the reference provided by the gateway to represent this product.
     *
     * @return string|null
     */
    public function getProductReference()
    {
        $product = $this->getProduct();
        if ($product) {
            return $product->getProductReference();
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this product.
     *
     * @return string|null
     */
    public function getProductId()
    {
        $product = $this->getProduct();
        if ($product) {
            return $product->getProductId();
        }
        return null;
    }

    /**
     * @return null|\Omnipay\Vindicia\Subscription
     */
    public function getSubscription()
    {
        if (!isset($this->subscription) && isset($this->data->autobill)) {
            $this->subscription = $this->objectHelper->buildSubscription($this->data->autobill);
        }
        return isset($this->subscription) ? $this->subscription : null;
    }

    /**
     * Get the reference provided by the gateway to represent this subscription.
     *
     * @return string|null
     */
    public function getSubscriptionReference()
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            return $subscription->getSubscriptionReference();
        }
        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this subscription.
     *
     * @return string|null
     */
    public function getSubscriptionId()
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            return $subscription->getSubscriptionId();
        }
        return null;
    }

    /**
     * Get the status of the subscription.
     *
     * @return string|null
     */
    public function getSubscriptionStatus()
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            return $subscription->getStatus();
        }
        return null;
    }

    /**
     * Get the billing state of the subscription.
     *
     * @return string|null
     */
    public function getSubscriptionBillingState()
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            return $subscription->getBillingState();
        }
        return null;
    }

    /**
     * Get the billing day of the subscription.
     *
     * @return int|null
     */
    public function getBillingDay()
    {
        if (isset($this->data->autobill)) {
            return intval($this->data->autobill->billingDay);
        }
        return null;
    }

    /**
     * @return null|\Omnipay\Vindicia\PaymentMethod
     */
    public function getPaymentMethod()
    {
        if (isset($this->paymentMethod)) {
            return $this->paymentMethod;
        }

        if (isset($this->data->paymentMethod)) {
            $this->paymentMethod = $this->objectHelper->buildPaymentMethod($this->data->paymentMethod);
            return $this->paymentMethod;
        }

        // sometimes it's in the account object
        if (!isset($this->data->account) || !isset($this->data->account->paymentMethods)) {
            return null;
        }

        // Vindicia returns all of the payment methods for this account, so we have to find
        // the one that we added in the request. Theoretically we could just return the id
        // that was added in the request, but this way we ensure it is actually returned
        // in the response
        foreach ($this->data->account->paymentMethods as $paymentMethod) {
            if ($paymentMethod->merchantPaymentMethodId === $this->getRequest()->getPaymentMethodId()) {
                $this->paymentMethod = $this->objectHelper->buildPaymentMethod($paymentMethod);
                return $this->paymentMethod;
            }
        }

        return null;
    }

    /**
     * Get the id you (the merchant) provided to represent this payment method.
     * This ID must be provided when creating a create payment method request.
     *
     * @return string|null
     */
    public function getPaymentMethodId()
    {
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod) {
            return $paymentMethod->getPaymentMethodId();
        }

        return null;
    }

    /**
     * Get the reference provided by the gateway to represent this payment method.
     *
     * @return string|null
     */
    public function getPaymentMethodReference()
    {
        $paymentMethod = $this->getPaymentMethod();
        if ($paymentMethod) {
            return $paymentMethod->getPaymentMethodReference();
        }

        return null;
    }

    /**
     * @return null|array<\Omnipay\Vindicia\Refund>
     */
    public function getRefunds()
    {
        if (!isset($this->refunds) && isset($this->data->refunds)) {
            $refunds = array();
            foreach ($this->data->refunds as $refund) {
                $refunds[] = $this->objectHelper->buildRefund($refund);
            }
            $this->refunds = $refunds;
        }
        return isset($this->refunds) ? $this->refunds : null;
    }

    /**
     * @return null|array<\Omnipay\Vindicia\Transaction>
     */
    public function getTransactions()
    {
        if (!isset($this->transactions) && isset($this->data->transactions)) {
            $transactions = array();
            foreach ($this->data->transactions as $transaction) {
                $transactions[] = $this->objectHelper->buildTransaction($transaction);
            }
            $this->transactions = $transactions;
        }
        return isset($this->transactions) ? $this->transactions : null;
    }

    /**
     * @return null|array<\Omnipay\Vindicia\Subscription>
     */
    public function getSubscriptions()
    {
        if (!isset($this->subscriptions) && isset($this->data->autobills)) {
            $subscriptions = array();
            foreach ($this->data->autobills as $subscription) {
                $subscriptions[] = $this->objectHelper->buildSubscription($subscription);
            }
            $this->subscriptions = $subscriptions;
        }
        return isset($this->subscriptions) ? $this->subscriptions : null;
    }

    /**
     * @return null|array<\Omnipay\Vindicia\PaymentMethod>
     */
    public function getPaymentMethods()
    {
        if (!isset($this->paymentMethods) && isset($this->data->paymentMethods)) {
            $paymentMethods = array();
            foreach ($this->data->paymentMethods as $paymentMethod) {
                $paymentMethods[] = $this->objectHelper->buildPaymentMethod($paymentMethod);
            }
            $this->paymentMethods = $paymentMethods;
        }
        return isset($this->paymentMethods) ? $this->paymentMethods : null;
    }

    /**
     * @return null|array<\Omnipay\Vindicia\Chargeback>
     */
    public function getChargebacks()
    {
        if (!isset($this->chargebacks) && isset($this->data->chargebacks)) {
            $chargebacks = array();
            foreach ($this->data->chargebacks as $chargeback) {
                $chargebacks[] = $this->objectHelper->buildChargeback($chargeback);
            }
            $this->chargebacks = $chargebacks;
        }
        return isset($this->chargebacks) ? $this->chargebacks : null;
    }

    /**
     * Get the reference provided by the gateway to represent this HOA Web Session.
     * NOTE: Web sessions do not have IDs, only references.
     *
     * @return string|null
     */
    public function getWebSessionReference()
    {
        if (isset($this->data->session)) {
            return $this->data->session->VID;
        }
        return null;
    }

    /**
     * Returns the total sales tax. For use after a CalculateSalesTax request
     *
     * @return null|float
     */
    public function getSalesTax()
    {
        if (isset($this->data->totalTax)) {
            return $this->data->totalTax;
        }
        return null;
    }

    /**
     * Return the soap ID from the soap response.
     *
     * @return string|null
     */
    public function getSoapId()
    {
        if (isset($this->data->return)) {
            return $this->data->return->soapId;
        }

        return null;
    }

    /**
     * Gets the risk score for the transaction, that is, the estimated probability that
     * this transaction will result in a chargeback. This number ranges from 0 (best) to
     * 100 (worst). It can also be -1, meaning that Vindicia has no opinion. (-1 indicates
     * a transaction with no originating IP addresses, an incomplete addresses, or both.
     * -2 indicates an error; retry later.)
     *
     * @return int|null
     */
    public function getRiskScore()
    {
        if (isset($this->data->score)) {
            return intval($this->data->score);
        }
        return null;
    }

    /**
     * Returns the invoice references. For use of FetchSubscriptionInvoiceReferences request
     *
     * @return null|array<string>
     */
    public function getInvoiceReferences()
    {
        if (isset($this->data->invoicenum)) {
            // PHP SOAP parsing may mess up the field if only one invoice num is in the response
            // so we force it to return an array of string
            if (is_string($this->data->invoicenum)) {
                return array($this->data->invoicenum);
            }
            return $this->data->invoicenum;
        }
        return null;
    }

    /**
     * Returns the invoice in HTML text format. For use of FetchSubscriptionInvoice request
     *
     * @return null|string
     */
    public function getInvoice()
    {
        if (isset($this->data->invoice)) {
            return $this->data->invoice;
        }
        return null;
    }

    /**
     * Returns summary of an invoice payment, either 'Success', 'Failure', or 'Pending'. For use of MakePayment request
     *
     * @return null|string
     */
    public function getSummary()
    {
        if (isset($this->data->summary)) {
            return $this->data->summary;
        }
        return null;
    }

    /**
     * Get the reason the subscription was canceled
     *
     * One of the reason codes documented here by Vindicia will be returned:
     * https://www.vindicia.com/documents/2500ProgGuideHTML5/Default.htm#ProgGuide/Canceling_AutoBills\
     * _with.htm%3FTocPath%3DCashBox2500ProgGuide%7C5%2520Working%2520with%2520AutoBills%7C5.3%2520\
     * Canceling%2520AutoBills%7C_____1
     *
     * The value can be set by Vindicia or by the merchant.
     *
     * @return string|null
     */
    public function getSubscriptionCancelReason()
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            return $subscription->getCancelReason();
        }
        return null;
    }

    /**
     * Override to set return type correctly
     *
     * @return AbstractRequest
     */
    public function getRequest()
    {
        /**
         * @var AbstractRequest
         */
        return parent::getRequest();
    }

    /**
     * Get the codes that indicate success
     *
     * @return array<int>
     */
    public static function getSuccessCodes()
    {
        return static::$SUCCESS_CODES;
    }
}
