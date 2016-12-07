<?php
/**
 * The responses for HOA completion are a bit different since
 * they include a response for the method that was actually
 * called by HOA. If the request itself fails, we return its
 * error message and code. Otherwise, we return the status
 * for the method called by HOA.
 * isRequestFailure and isMethodFailure can be used to
 * determine if it was a request failure or method failure.
 */
namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Vindicia\Attribute;
use ReflectionClass;

class CompleteHOAResponse extends Response
{
    /**
     * Since parsing the structure is a bit complex, we parse once in the
     * constructor and then store the results in instance variables.
     */
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var string|null
     */
    protected $failureType;
    /**
     * @var bool
     */
    protected $isSuccessful;

    // Cached objects:
    protected $formValues;
    protected $transaction;
    protected $subscription;
    protected $paymentMethod;

    /**
     * Constants to indicate whether it was the HOA request that failed
     * or the method HOA called.
     */
    const REQUEST_FAILURE = 'request_failure';
    const METHOD_FAILURE = 'method_failure';

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        // if available, we want the response from the method
        if (isset($this->data->session->apiReturn)) {
            $this->code = $this->data->session->apiReturn->returnCode;
            $this->message = $this->data->session->apiReturn->returnString;

            if ($this->wasAuthorize()) {
                $requestClass = '\Omnipay\Vindicia\Message\HOAAuthorizeRequest';
            } elseif ($this->wasPurchase()) {
                $requestClass = '\Omnipay\Vindicia\Message\HOAPurchaseRequest';
            } elseif ($this->wasCreatePaymentMethod()) {
                $requestClass = '\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest';
            } elseif ($this->wasCreateSubscription()) {
                $requestClass = '\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest';
            } else {
                // sometimes Vindicia doesn't return any method info on a failure
                $this->isSuccessful = false;
                $this->failureType = self::METHOD_FAILURE;
                return;
            }

            $requestReflection = new ReflectionClass($requestClass);
            $regularRequestClassProperty = $requestReflection->getProperty('REGULAR_REQUEST_CLASS');
            $regularRequestClassProperty->setAccessible(true);
            $regularRequestClass = $regularRequestClassProperty->getValue();
            $regularRequestClassReflection = new ReflectionClass($regularRequestClass);
            $regularRequestResponseClassProperty = $regularRequestClassReflection->getProperty('RESPONSE_CLASS');
            $regularRequestResponseClassProperty->setAccessible(true);
            $regularRequestResponseClass = $regularRequestResponseClassProperty->getValue();
            $regularRequestResponseSuccessCodes = $regularRequestResponseClass::getSuccessCodes();

            $this->isSuccessful = in_array(intval($this->getCode()), $regularRequestResponseSuccessCodes, true);
            if (!$this->isSuccessful) {
                $this->failureType = self::METHOD_FAILURE;
            }

            return;
        }

        // otherwise, we want the response from the request
        $this->code = parent::getCode();
        $this->message = parent::getMessage();
        $this->isSuccessful = parent::isSuccessful();
        if (!$this->isSuccessful) {
            $this->failureType = self::REQUEST_FAILURE;
        }
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
        if (!empty($this->message)) {
            return $this->message;
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
        if (!empty($this->code)) {
            return $this->code;
        }
        throw new InvalidResponseException('Response has no code.');
    }

    public function isSuccessful()
    {
        return $this->isSuccessful;
    }

    public function getTransaction()
    {
        if (isset($this->transaction)) {
            return $this->transaction;
        }

        if (isset($this->data->session->apiReturnValues->transactionAuth->transaction)) {
            $transaction = $this->data->session->apiReturnValues->transactionAuth->transaction;
        } elseif (isset($this->data->session->apiReturnValues->transactionAuthCapture->transaction)) {
            $transaction = $this->data->session->apiReturnValues->transactionAuthCapture->transaction;
        }

        if (isset($transaction)) {
            $this->transaction = $this->objectHelper->buildTransaction($transaction);
            return $this->transaction;
        }

        return null;
    }

    public function getPaymentMethod()
    {

        if (!isset($this->paymentMethod)) {
            if (isset($this->data->session->apiReturnValues->accountUpdatePaymentMethod->account->paymentMethods[0])) {
                $vindiciaPaymentMethod =
                    $this->data->session->apiReturnValues->accountUpdatePaymentMethod->account->paymentMethods[0];
            } elseif (isset($this->data->session->apiReturnValues->paymentMethodUpdate->paymentMethod)) {
                $vindiciaPaymentMethod = $this->data->session->apiReturnValues->paymentMethodUpdate->paymentMethod;
            } else {
                return null;
            }

            $this->paymentMethod = $this->objectHelper->buildPaymentMethod($vindiciaPaymentMethod);
        }

        return isset($this->paymentMethod) ? $this->paymentMethod : null;
    }

    public function getSubscription()
    {
        if (!isset($this->subscription)
            && isset($this->data->session->apiReturnValues->autobillUpdate->autobill)
        ) {
            $this->subscription = $this->objectHelper->buildSubscription(
                $this->data->session->apiReturnValues->autobillUpdate->autobill
            );
        }
        return isset($this->subscription) ? $this->subscription : null;
    }

    /**
     * If the response failed, returns self::REQUEST_FAILURE to indicate that
     * the HOA request failed or self::METHOD_FAILURE to indicate that the
     * method called by HOA failed. Returns null if the request was successful.
     *
     * @return string|null
     * @deprecated Use isRequestFailure and isMethodFailure instead
     */
    public function getFailureType()
    {
        return $this->failureType;
    }

    /**
     * Returns true if the HOA request failed (as opposed to the method being called by
     * HOA failing).
     *
     * @return bool
     */
    public function isRequestFailure()
    {
        return $this->failureType === self::REQUEST_FAILURE;
    }

    /**
     * Returns true if the HOA method failed (as opposed to the request itself failing).
     *
     * @return bool
     */
    public function isMethodFailure()
    {
        return $this->failureType === self::METHOD_FAILURE;
    }

    /**
     * Gets the values that were set on the form. Returns an array of Attributes.
     *
     * @return array<Attribute>|null
     */
    public function getFormValues()
    {
        if (!isset($this->formValues) && isset($this->data->session->postValues)) {
            $formValues = array();
            foreach ($this->data->session->postValues as $postValue) {
                $formValues[] = new Attribute(array(
                    'name' => $postValue->name,
                    'value' => $postValue->value
                ));
            }
            $this->formValues = $formValues;
        }
        return isset($this->formValues) ? $this->formValues : null;
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
        if (isset($this->data->session->apiReturnValues->transactionAuth->score)) {
            return intval($this->data->session->apiReturnValues->transactionAuth->score);
        }
        if (isset($this->data->session->apiReturnValues->transactionAuthCapture->score)) {
            return intval($this->data->session->apiReturnValues->transactionAuthCapture->score);
        }
        return null;
    }

    /**
     * Returns true if the HOA call completed an authorize request
     *
     * @return bool
     */
    public function wasAuthorize()
    {
        return $this->getMethod() === 'transactionAuth';
    }

    /**
     * Returns true if the HOA call completed a purchase request
     *
     * @return bool
     */
    public function wasPurchase()
    {
        return $this->getMethod() === 'transactionAuthCapture';
    }

    /**
     * Returns true if the HOA call completed a create or update payment method request
     *
     * @return bool
     */
    public function wasCreatePaymentMethod()
    {
        return in_array($this->getMethod(), array(
            'accountUpdatePaymentMethod',
            'paymentMethodUpdate' // @todo double check this
        ), true);
    }

    /**
     * Returns true if the HOA call completed a create or update subscription request
     *
     * @return bool
     */
    public function wasCreateSubscription()
    {
        return $this->getMethod() === 'autobillUpdate';
    }

    /**
     * Get the name of the request that was made in this call.
     *
     * @return string
     */
    protected function getMethod()
    {
        if (isset($this->data->session->apiReturnValues->method)) {
            return $this->data->session->apiReturnValues->method;
        }

        return null;
    }
}
