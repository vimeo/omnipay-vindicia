<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidCreditCardException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Vindicia\TestableSoapClient;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\AttributeBag;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\PriceBag;
use Omnipay\Common\Exception\InvalidRequestException;
use PaymentGatewayLogger\Event\Constants;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;
use SoapFault;
use stdClass;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\Http\ClientInterface;
use InvalidArgumentException;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\NonStrippingCreditCard;

/**
 * Vindicia Abstract Request
 */
abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{
    /**
     * True if this is the update version of a request rather than the create
     * version. (They're essentially the same but some validation logic is
     * slightly different.
     *
     * @var bool
     */
    protected $isUpdate;

    /**
     * @var EventDispatcherInterface|null
     */
    protected $eventDispatcher;

    const API_VERSION = '18.0';
    const LIVE_ENDPOINT = 'https://soap.vindicia.com';
    const TEST_ENDPOINT = 'https://soap.staging.us-west.vindicia.com';

    const VINDICIA_EXPIRATION_DATE_FORMAT = 'Ym';

    /**
     * Object names used in the Vindicia API
     */
    /**
     * @var string
     */
    protected static $TRANSACTION_OBJECT = 'Transaction';
    /**
     * @var string
     */
    protected static $SUBSCRIPTION_OBJECT = 'AutoBill';
    /**
     * @var string
     */
    protected static $CUSTOMER_OBJECT = 'Account';
    /**
     * @var string
     */
    protected static $PAYMENT_METHOD_OBJECT = 'PaymentMethod';
    /**
     * @var string
     */
    protected static $REFUND_OBJECT = 'Refund';
    /**
     * @var string
     */
    protected static $CHARGEBACK_OBJECT = 'Chargeback';
    /**
     * @var string
     */
    protected static $PLAN_OBJECT = 'BillingPlan';
    /**
     * @var string
     */
    protected static $PRODUCT_OBJECT = 'Product';
    /**
     * @var string
     */
    protected static $WEB_SESSION_OBJECT = 'WebSession';

    /**
     * Default amount of time to wait for connection or response, in seconds
     *
     * @var int
     */
    const DEFAULT_TIMEOUT = 120;

    /**
     * Default tax classification for products.
     *
     * @var string
     */
    const DEFAULT_TAX_CLASSIFICATION = 'TaxExempt';

    /**
     * Payment method types
     *
     * @var string
     */
    const PAYMENT_METHOD_PAYPAL = 'PayPal';
    const PAYMENT_METHOD_APPLE_PAY = 'ApplePay';
    const PAYMENT_METHOD_CREDIT_CARD = 'CreditCard';
    const PAYMENT_METHOD_ECP = 'ECP';

    /**
     * If chargeback probability from risk scoring is greater than this,
     * the transaction will fail. By default, every transaction will
     * succeed.
     *
     * @var int
     */
    const DEFAULT_MIN_CHARGEBACK_PROBABILITY = 100;

    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\Response';

    /**
     * Create a new Request
     *
     * @param ClientInterface $httpClient  A Guzzle client to make API calls with
     * @param HttpRequest     $httpRequest A Symfony HTTP request object
     * @param bool            $isUpdate    True if this is an update request rather than a create (default false)
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest, $isUpdate = false)
    {
        parent::__construct($httpClient, $httpRequest);

        $this->isUpdate = $isUpdate;

        // Used to fire events before and after executing a request.
        $this->eventDispatcher = new EventDispatcher();
    }

    /**
     * Initialize request with parameters.
     * For new products, the default tax classification is Tax Exempt
     *
     * @param array<string, mixed> $parameters
     * @return static
     */
    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('timeout', $parameters)) {
            $parameters['timeout'] = self::DEFAULT_TIMEOUT;
        }
        if (!$this->isUpdate() && !array_key_exists('taxClassification', $parameters)) {
            $parameters['taxClassification'] = self::DEFAULT_TAX_CLASSIFICATION;
        }

        parent::initialize($parameters);

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * @param string $value
     * @return static
     */
    public function setUsername($value)
    {
        return $this->setParameter('username', $value);
    }

    /**
     * @return null|string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param string $value
     * @return static
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return null|string
     */
    public function getIp()
    {
        return $this->getParameter('ip');
    }

    /**
     * @return static
     */
    public function setIp($value)
    {
        return $this->setParameter('ip', $value);
    }

    /**
     * @return null|string
     */
    public function getCustomerId()
    {
        return $this->getParameter('customerId');
    }

    /**
     * @return static
     */
    public function setCustomerId($value)
    {
        return $this->setParameter('customerId', $value);
    }

    /**
     * @return null|string
     */
    public function getCustomerReference()
    {
        return $this->getParameter('customerReference');
    }

    /**
     * @return static
     */
    public function setCustomerReference($value)
    {
        return $this->setParameter('customerReference', $value);
    }

    /**
     * Gets the customer's name
     *
     * @return null|string
     */
    public function getName()
    {
        return $this->getParameter('name');
    }

    /**
     * Sets the customer's name
     *
     * @param string $value
     * @return static
     */
    public function setName($value)
    {
        return $this->setParameter('name', $value);
    }

    /**
     * Gets the customer's email
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->getParameter('email');
    }

    /**
     * Sets the customer's email
     *
     * @param string $value
     * @return static
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * @return null|string
     */
    public function getRefundId()
    {
        return $this->getParameter('refundId');
    }

    /**
     * @return static
     */
    public function setRefundId($value)
    {
        return $this->setParameter('refundId', $value);
    }

    /**
     * @return null|string
     */
    public function getPaymentMethodId()
    {
        return $this->getParameter('paymentMethodId');
    }

    /**
     * @return static
     */
    public function setPaymentMethodId($value)
    {
        return $this->setParameter('paymentMethodId', $value);
    }

    /**
     * @return null|string
     */
    public function getPaymentMethodReference()
    {
        return $this->getParameter('paymentMethodReference');
    }

    /**
     * @return static
     */
    public function setPaymentMethodReference($value)
    {
        return $this->setParameter('paymentMethodReference', $value);
    }

    /**
     * @return null|string
     */
    public function getPaymentMethodType()
    {
        return $this->getParameter('paymentMethodType');
    }

    /**
     * @return static
     */
    public function setPaymentMethodType($value) {
        return $this->setParameter('paymentMethodType', $value);
    }

    /**
     * @return null|string
     */
    public function getTaxClassification()
    {
        return $this->getParameter('taxClassification');
    }

    /**
     * @return null|string
     */
    public function getSubscriptionId()
    {
        return $this->getParameter('subscriptionId');
    }

    /**
     * @return static
     */
    public function setSubscriptionId($value)
    {
        return $this->setParameter('subscriptionId', $value);
    }

    /**
     * @return null|string
     */
    public function getSubscriptionReference()
    {
        return $this->getParameter('subscriptionReference');
    }

    /**
     * @return static
     */
    public function setSubscriptionReference($value)
    {
        return $this->setParameter('subscriptionReference', $value);
    }

    /**
     * @return null|string
     */
    public function getPlanId()
    {
        return $this->getParameter('planId');
    }

    /**
     * @return static
     */
    public function setPlanId($value)
    {
        return $this->setParameter('planId', $value);
    }

    /**
     * @return null|string
     */
    public function getPlanReference()
    {
        return $this->getParameter('planReference');
    }

    /**
     * @return static
     */
    public function setPlanReference($value)
    {
        return $this->setParameter('planReference', $value);
    }

    /**
     * @return null|string
     */
    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->getParameter('quantity');
    }

    /**
     * @return static
     */
    public function setQuantity($value)
    {
        return $this->setParameter('quantity', $value);
    }

    /**
     * @return static
     */
    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    /**
     * @return null|string
     */
    public function getProductReference()
    {
        return $this->getParameter('productReference');
    }

    /**
     * @return static
     */
    public function setProductReference($value)
    {
        return $this->setParameter('productReference', $value);
    }

    /**
     * Returns the invoice reference
     *
     * @return null|string
     */
    public function getInvoiceReference()
    {
        return $this->getParameter('invoiceReference');
    }

    /**
     * Sets the invoice reference
     *
     * @param string $value
     * @return static
     */
    public function setInvoiceReference($value)
    {
        return $this->setParameter('invoiceReference', $value);
    }

    /**
     * @return static
     */
    public function setTaxClassification($value)
    {
        return $this->setParameter('taxClassification', $value);
    }

    /**
     * Set the items in this order
     *
     * @param VindiciaItemBag|array $items
     * @return static
     */
    public function setItems($items)
    {
        if ($items && !$items instanceof VindiciaItemBag) {
            $items = new VindiciaItemBag($items);
        }

        return $this->setParameter('items', $items);
    }

    /**
     * A list of attributes
     *
     * @return AttributeBag|null
     */
    public function getAttributes()
    {
        return $this->getParameter('attributes');
    }

    /**
     * Set the attributes in this order
     *
     * @param AttributeBag|array $attributes
     * @return static
     */
    public function setAttributes($attributes)
    {
        if ($attributes && !$attributes instanceof AttributeBag) {
            $attributes = new AttributeBag($attributes);
        }

        return $this->setParameter('attributes', $attributes);
    }

    /**
     * Get amount of time to wait for connection or response, in seconds
     *
     * @return null|int
     */
    public function getTimeout()
    {
        return $this->getParameter('timeout');
    }

    /**
     * Set amount of time to wait for connection or response, in seconds
     *
     * @param int $value
     * @return static
     */
    public function setTimeout($value)
    {
        return $this->setParameter('timeout', $value);
    }

    /**
     * Get the billing day
     *
     * @return null|int
     */
    public function getBillingDay()
    {
        return $this->getParameter('billingDay');
    }

    /**
     * Set the billing day
     *
     * @param int $value
     * @return static
     */
    public function setBillingDay($value)
    {
        return $this->setParameter('billingDay', $value);
    }

    /**
     * Get the start time.
     *
     * @return null|string
     */
    public function getStartTime()
    {
        return $this->getParameter('startTime');
    }

    /**
     * Set the start time. Takes a date/timestamp string, such as
     * "2016-06-02T12:30:00-04:00" (June 2, 2016 @ 12:30 PM, GMT - 4 hours)
     * Use varies depending on request. May be used to specify the start time
     * of a subscription or a range of objects to fetch.
     *
     * @param string $value
     * @return static
     */
    public function setStartTime($value)
    {
        return $this->setParameter('startTime', $value);
    }

    /**
     * Get the end time.
     *
     * @return null|string
     */
    public function getEndTime()
    {
        return $this->getParameter('endTime');
    }

    /**
     * Set the end time. Takes a date/timestamp string, such as
     * "2016-06-02T12:30:00-04:00" (June 2, 2016 @ 12:30 PM, GMT - 4 hours)
     * Used to specify the end time of a range of objects to fetch.
     *
     * @param string $value
     * @return static
     */
    public function setEndTime($value)
    {
        return $this->setParameter('endTime', $value);
    }

    /**
     * Gets the description shown on the customers billing statement from the bank
     *
     * @return null|string
     */
    public function getStatementDescriptor()
    {
        return $this->getParameter('statementDescriptor');
    }

    /**
     * Sets the description shown on the customers billing statement from the bank
     * This field’s value and its format are constrained by your payment processor;
     * consult with Vindicia Client Services before setting the value.
     *
     * @param string $value
     * @return static
     */
    public function setStatementDescriptor($value)
    {
        return $this->setParameter('statementDescriptor', $value);
    }

    /**
     * Get the redirect url that the customer will be sent to after
     * HOA completes.
     *
     * @return null|string
     */
    public function getReturnUrl()
    {
        /**
         * @var null|string
         */
        return parent::getReturnUrl();
    }

    /**
     * Set the redirect url that the customer will be sent to after
     * HOA or a PayPal purchase completes.
     *
     * @param string $value
     * @return static
     */
    public function setReturnUrl($value)
    {
        /**
         * @var static
         */
        return parent::setReturnUrl($value);
    }

    /**
     * Get minimum chargeback probability.
     * If chargeback probability from risk scoring is greater than the
     * this value, the transaction will fail. If the value is 100,
     * all transactions will succeed.
     *
     * @return null|int
     */
    public function getMinChargebackProbability()
    {
        return $this->getParameter('minChargebackProbability');
    }

    /**
     * Set minimum chargeback probability.
     * If chargeback probability from risk scoring is greater than the
     * set value, the transaction will fail. If the value is 100,
     * all transactions will succeed.
     *
     * @param int $value
     * @return static
     */
    public function setMinChargebackProbability($value)
    {
        return $this->setParameter('minChargebackProbability', $value);
    }

    /**
     * Get the redirect url that will be used in the case of a cancel from PayPal's site.
     *
     * @return null|string
     */
    public function getCancelUrl()
    {
        /**
         * @var null|string
         */
        return parent::getCancelUrl();
    }

    /**
     * Set the redirect url that will be used in the case of a cancel from PayPal's site.
     *
     * @param string $value
     * @return static
     */
    public function setCancelUrl($value)
    {
        /**
         * @var static
         */
        return parent::setCancelUrl($value);
    }
    /**
     * Gets whether PayPal redirected the customer to your success page. If false, they
     * were redirected to the error page.
     *
     * @return null|bool
     */
    public function getSuccess()
    {
        return $this->getParameter('success');
    }

    /**
     * Sets whether PayPal redirected the customer to your success page. Set to false
     * if they were redirected to the error page.
     *
     * @param bool $value
     * @return static
     */
    public function setSuccess($value)
    {
        return $this->setParameter('success', $value);
    }

    /**
     * Gets the identifier generated by the gateway to represent the underlying
     * PayPal transaction.
     *
     * @return null|string
     */
    public function getPayPalTransactionReference()
    {
        return $this->getParameter('payPalTransactionReference');
    }

    /**
     * Sets the identifier generated by the gateway to represent the underlying
     * PayPal transaction.
     *
     * @param string $value
     * @return static
     */
    public function setPayPalTransactionReference($value)
    {
        return $this->setParameter('payPalTransactionReference', $value);
    }

    /**
     * A list of prices (currency and amount)
     *
     * @return null|PriceBag|null
     */
    public function getPrices()
    {
        return $this->getParameter('prices');
    }

    /**
     * Set the prices (currency and amount)
     * If you only need a price for one currency, you can also use setAmount and setCurrency.
     *
     * @param PriceBag|array $prices
     * @return static
     * @throws InvalidPriceBagException if multiple prices have the same currency
     */
    public function setPrices($prices)
    {
        if ($prices && !$prices instanceof PriceBag) {
            $prices = new PriceBag($prices);
        }

        return $this->setParameter('prices', $prices);
    }

    /**
     * Redefining to correct return type
     *
     * @return null|\Omnipay\Common\CreditCard
     */
    public function getCard()
    {
        /**
         * @var null|\Omnipay\Common\CreditCard
         */
        return parent::getCard();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|bool
     */
    public function getTestMode()
    {
        /**
         * @var null|bool
         */
        return parent::getTestMode();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|string
     */
    public function getAmount()
    {
        /**
         * @var null|string
         */
        return parent::getAmount();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|string
     */
    public function getCurrency()
    {
        /**
         * @var null|string
         */
        return parent::getCurrency();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|string
     */
    public function getDescription()
    {
        /**
         * @var null|string
         */
        return parent::getDescription();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|string
     */
    public function getTransactionId()
    {
        /**
         * @var null|string
         */
        return parent::getTransactionId();
    }

    /**
     * Redefining to correct return type
     *
     * @return null|string
     */
    public function getTransactionReference()
    {
        /**
         * @var null|string
         */
        return parent::getTransactionReference();
    }

    /**
     * Gets the ApplePayPaymentToken received from the parsed ApplePayPayment object.
     *
     * @return array|null
     */
    public function getApplePayToken()
    {
        return $this->getParameter('applePayToken');
    }

    /**
     * The object type on which the API call will be made
     *
     * @return string
     */
    abstract protected function getObject();

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    abstract protected function getFunction();

    /**
     * @return string
     */
    final protected function getEndpoint()
    {
        return ($this->getTestMode() === false ? self::LIVE_ENDPOINT : self::TEST_ENDPOINT)
               . '/'
               . self::API_VERSION
               . '/'
               . $this->getObject()
               . '.wsdl';
    }

    /**
     * @param array $data
     *
     * @return ResponseInterface
     * @throws SoapFault
     */
    public function sendData($data)
    {
        $originalWsdlCacheEnabled = ini_get('soap.wsdl_cache_enabled');
        $originalWsdlCacheTtl = ini_get('soap.wsdl_cache_ttl');
        $originalSocketTimeout = ini_get('default_socket_timeout');
        ini_set('soap.wsdl_cache_enabled', 1);
        ini_set('soap.wsdl_cache_ttl', 3600);
        $timeout = $this->getTimeout();
        if ($timeout !== null) {
            ini_set('default_socket_timeout', $timeout);
        }

        $data['srd'] = '';

        $auth = new stdClass();
        $auth->version = self::API_VERSION;
        $auth->login = $this->getUsername();
        $auth->password = $this->getPassword();
        $auth->evid = null;
        $auth->userAgent = null;
        $data['auth'] = $auth;

        $action = $data['action'];
        unset($data['action']);

        $params = array();
        $params['parameters'] = $data;

        if ($this->eventDispatcher) {
            // Log the request before it is sent.
            $this->eventDispatcher->dispatch(Constants::OMNIPAY_REQUEST_BEFORE_SEND, new RequestEvent($this));
        }

        try {
            $client = new TestableSoapClient(
                $this->getEndpoint(),
                array(
                    'style'              => SOAP_DOCUMENT,
                    'use'                => SOAP_LITERAL,
                    'connection_timeout' => $timeout,
                    'trace'              => true,
                    'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
                    'location'           => ($this->getTestMode() === false ? self::LIVE_ENDPOINT : self::TEST_ENDPOINT)
                                            . '/v'
                                            . self::API_VERSION
                                            . '/soap.pl'
                )
            );
            $response = $client->__soapCall($action, $params);
        } catch (SoapFault $exception) {
            if ($this->eventDispatcher) {
                // Log any errors.
                $this->eventDispatcher->dispatch(
                    Constants::OMNIPAY_REQUEST_ERROR,
                    new ErrorEvent($exception, $this)
                );
            }

            throw $exception;
        }
        // reset to how they were before
        ini_set('soap.wsdl_cache_enabled', $originalWsdlCacheEnabled);
        ini_set('soap.wsdl_cache_ttl', $originalWsdlCacheTtl);
        ini_set('default_socket_timeout', $originalSocketTimeout);

        $this->response = new static::$RESPONSE_CLASS($this, $response);
        if ($this->eventDispatcher) {
            // Log successful request responses.
            $this->eventDispatcher->dispatch(Constants::OMNIPAY_RESPONSE_SUCCESS, new ResponseEvent($this->response));
        }

        return $this->response;
    }

    /**
     * Overriding to provide a more precise return type
     * @return Response
     */
    public function send()
    {
        /**
         * @var Response
         */
        return parent::send();
    }

    /**
     * Returns true if this is an update call, false if it's a create call
     *
     * @return bool
     */
    protected function isUpdate()
    {
        return $this->isUpdate;
    }

    /**
     * Helper function to make a Vindicia transaction object.
     * Set $validateCard to false to skip card validation.
     *
     * @param string $paymentMethodType default null
     *
     * @return stdClass
     * @throws InvalidRequestException
     * @throws InvalidCreditCardException If unable to build the payment method specified by $paymentMethodType.
     */
    protected function buildTransaction($paymentMethodType = null)
    {
        $account = new stdClass();
        // doesn't work if account isn't created, id must be passed in :-(
        $account->merchantAccountId = $this->getCustomerId();
        $account->VID = $this->getCustomerReference();
        $account->name = $this->getName();
        $account->emailAddress = $this->getEmail();

        $amount = $this->getAmount();
        $transactionItems = array();
        $items = $this->getItems();

        if (!empty($items)) {
            $totalPriceOfItems = '0';
            foreach ($items as $i => $item) {
                $item->validate();

                $transactionItem = new stdClass();
                $transactionItem->name = $item->getName();
                $transactionItem->price = $item->getPrice();
                $transactionItem->quantity = $item->getQuantity();
                $transactionItem->indexNumber = $i + 1; // vindicia index numbers start at 1
                $transactionItem->itemType = 'Purchase';
                $transactionItem->taxClassification = $item->getTaxClassification() ?: $this->getTaxClassification();
                $transactionItem->sku = $item->getSku();
                $transactionItem->nameValues = array(
                    new NameValue('description', $item->getDescription())
                );

                $transactionItems[] = $transactionItem;

                // strval to avoid floating point error
                $totalPriceOfItems = strval($totalPriceOfItems + $transactionItem->price * $transactionItem->quantity);
            }

            if ($amount && floatval($amount) !== floatval($totalPriceOfItems)) {
                throw new InvalidRequestException('Sum of item prices not equal to set amount.');
            }
        } elseif ($amount) {
            $transactionItem = new stdClass();
            $transactionItem->name = 'Item'; // generic name since the name field is required
            $transactionItem->price = $amount;
            $transactionItem->quantity = 1;
            $transactionItem->indexNumber = 1;
            $transactionItem->itemType = 'Purchase';
            $transactionItem->taxClassification = $this->getTaxClassification();
            $transactionItem->sku = 0;

            $transactionItems[] = $transactionItem;
        }

        $card = $this->getCard();

        $transaction = new stdClass();
        $transaction->account = $account;
        $transaction->currency = $this->getCurrency();
        $transaction->merchantTransactionId = $this->getTransactionId();
        if ($paymentMethodType !== null) {
            $transaction->sourcePaymentMethod = $this->buildPaymentMethod($paymentMethodType);
        }
        $transaction->transactionItems = $transactionItems;
        $transaction->billingStatementIdentifier = $this->getStatementDescriptor();
        $transaction->sourceIp = $this->getIp();
        if ($card !== null) {
            $transaction->shippingAddress = $this->buildAddress($card);
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $transaction->nameValues = $this->buildNameValues($attributes);
        }

        return $transaction;
    }

    /**
     * Helper function to make a Vindicia payment method object.
     * Set $addAttributes to true if the request attributes should be added to the payment
     * method
     *
     * @param string|null $paymentMethodType (self::PAYMENT_METHOD_CREDIT_CARD, self::PAYMENT_METHOD_PAYPAL,
     *                                        self::PAYMENT_METHOD_APPLE_PAY, self::PAYMENT_METHOD_ECP,
     *                                        null autodetects or sets nothing if no specifying data provided)
     * @param bool $addAttributes default false
     *
     * @return stdClass
     * @throws InvalidArgumentException if $paymentMethodType is not supported
     * @throws InvalidCreditCardException
     * @psalm-suppress PossiblyInvalidArrayOffset for applePayToken object
     * @psalm-suppress PossiblyNullArrayAccess for applePayToken object
     */
    protected function buildPaymentMethod($paymentMethodType, $addAttributes = false)
    {
        $paymentMethod = new stdClass();
        $paymentMethod->merchantPaymentMethodId = $this->getPaymentMethodId();
        $paymentMethod->VID = $this->getPaymentMethodReference();
        $paymentMethod->active = true;
        $paymentMethod->currency = $this->getCurrency();

        $card = $this->getCard();

        if (in_array($paymentMethodType, [self::PAYMENT_METHOD_CREDIT_CARD, self::PAYMENT_METHOD_ECP], true)
            || ($paymentMethodType === null && $card)
        ) {
            if ($card) {
                // if we're adding a new credit card, the whole thing needs to be provided
                if (!$this->isUpdate()) {
                    $card->validate();
                }

                $creditCard = new stdClass();
                $creditCard->account = $card->getNumber();
                $creditCard->expirationDate = $card->getExpiryDate(self::VINDICIA_EXPIRATION_DATE_FORMAT);

                $paymentMethod->creditCard = $creditCard;
            }

            // never change the type on an update
            if (!$this->isUpdate()) {
                $paymentMethod->type = $paymentMethodType === self::PAYMENT_METHOD_CREDIT_CARD || $card
                    ? self::PAYMENT_METHOD_CREDIT_CARD
                    : self::PAYMENT_METHOD_ECP;
            }
        } elseif ($paymentMethodType === self::PAYMENT_METHOD_PAYPAL
                  || ($paymentMethodType === null && $this->getReturnUrl())
        ) {
            $paypal = new stdClass();
            $paypal->cancelUrl = $this->getCancelUrl();
            $paypal->returnUrl = $this->getReturnUrl();
            $paypal->requestReferenceId = true;

            $paymentMethod->paypal = $paypal;

            // never change the type on an update
            if (!$this->isUpdate()) {
                $paymentMethod->type = self::PAYMENT_METHOD_PAYPAL;
            }
        } elseif ($paymentMethodType === self::PAYMENT_METHOD_APPLE_PAY && $card) {
            // $card parameter is needed for successful authorization.
            $applePay = new stdClass();

            // 'applePayToken' is validated to ensure it's set before reaching here.
            $token = $this->getApplePayToken();
            $applePay->paymentInstrumentName = $token['paymentMethod']['displayName'];
            $applePay->paymentNetwork = $token['paymentMethod']['network'];
            $applePay->transactionIdentifier = $token['transactionIdentifier'];
            // Vindicia expects the paymentData to be a string
            $applePay->paymentData = json_encode($token['paymentData']);

            $paymentMethod->applePay = $applePay;

            // never change the type on an update
            if (!$this->isUpdate()) {
                $paymentMethod->type = self::PAYMENT_METHOD_APPLE_PAY;
            }
        } elseif ($paymentMethodType !== null) {
            throw new InvalidArgumentException('Unknown payment method type.');
        }

        $attributes = $this->getAttributes();
        if ($attributes && $addAttributes) {
            $paymentMethod->nameValues = $this->buildNameValues($attributes);
        }

        if ($card !== null) {
            $paymentMethod->accountHolderName = $card->getName();
            $paymentMethod->billingAddress = $this->buildAddress($card);

            if ($card->getCvv()) {
                if (!isset($paymentMethod->nameValues)) {
                    $paymentMethod->nameValues = array();
                }
                $paymentMethod->nameValues[] = new NameValue('CVN', $card->getCvv());
            }
        }

        return $paymentMethod;
    }

    /**
     * Helper function to make a Vindicia address
     *
     * @param CreditCard $card
     * @return stdClass
     */
    protected function buildAddress(CreditCard $card)
    {
        $address = new stdClass();
        $address->addr1 = $card->getAddress1();
        $address->addr2 = $card->getAddress2();
        $address->city = $card->getCity();
        $address->country = $card->getCountry();
        $address->district = $card->getState();
        $address->name = $card->getName();
        $address->postalCode = $card->getPostcode();

        return $address;
    }

    /**
     * Helper function to make an array of name values from a bag of attributes
     *
     * @param AttributeBag $attributes
     * @return array of NameValue
     */
    protected function buildNameValues(AttributeBag $attributes)
    {
        $nameValues = array();
        foreach ($attributes as $attribute) {
            $nameValues[] = new NameValue($attribute->getName(), $attribute->getValue());
        }

        return $nameValues;
    }

    /**
     * Builds the value for Vindicia's prices field
     *
     * @return array of stdClass
     * @throws InvalidRequestException
     */
    protected function makePricesForVindicia()
    {
        $prices = $this->getPrices();
        $amount = $this->getAmount();
        $currency = $this->getCurrency();
        if (!empty($prices) && (isset($amount) || isset($currency))) {
            throw new InvalidRequestException(
                'The amount and currency parameters cannot be set if the prices parameter is set.'
            );
        }

        $vindiciaPrices = array();
        if (!empty($prices)) {
            foreach ($prices as $price) {
                $vindiciaPrice = new stdClass();
                $vindiciaPrice->amount = $price->getAmount();
                $vindiciaPrice->currency = $price->getCurrency();
                $vindiciaPrices[] = $vindiciaPrice;
            }
        } else {
            if (isset($amount)) {
                $price = new stdClass();
                $price->amount = $amount;
                $price->currency = $currency;
                $vindiciaPrices[] = $price;
            }
        }

        return $vindiciaPrices;
    }

    /**
     * This method is only overriden to provide type hinting for static type checking
     * by PSALM.
     * This is necessary because Omnipay\Common\AbstractRequest::setParameter says it
     * returns AbstractRequest instead of static.
     *
     * @return static
     */
    protected function setParameter($key, $value)
    {
        /**
         * @var static
         */
        return parent::setParameter($key, $value);
    }

    /**
     * Get data for request
     *
     * @return array<string, mixed>
     */
    abstract public function getData();

    /**
     * Redefining to tell psalm it's variadic
     *
     * @return bool
     * @psalm-variadic
     */
    public function validate(...$args)
    {
        return call_user_func_array('parent::validate', func_get_args());
    }
}
