<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use ReflectionClass;
use ReflectionMethod;
use Omnipay\Vindicia\NameValue;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Vindicia\AttributeBag;

abstract class AbstractHOARequest extends AbstractRequest
{
    /**
     * @var string
     */
    protected static $REGULAR_REQUEST_CLASS = '\Omnipay\Vindicia\Message\AbstractRequest';

    /**
     * The corresponding regular (non-HOA) request. This is used to fake
     * double inheritance since PHP doesn't support it.
     *
     * @var \Omnipay\Vindicia\Message\AbstractRequest
     */
    protected $regularRequest;

    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest, $isUpdate = false)
    {
        // construct a corresponding instance of the regular (non HOA) request
        $reflectionClass = new ReflectionClass(static::$REGULAR_REQUEST_CLASS);
        /**
         * Really, it's static::$REGULAR_REQUEST_CLASS. But Psalm doesn't support that.
         * @var AbstractRequest
         */
        $this->regularRequest = $reflectionClass->newInstanceArgs(array($httpClient, $httpRequest, $isUpdate));

        parent::__construct($httpClient, $httpRequest, $isUpdate);
    }

    /**
     * @param array<string, mixed> $parameters
     * @return AbstractHOARequest
     */
    public function initialize(array $parameters = array())
    {
        $this->regularRequest->initialize($parameters);

        // some parameters are for the HOA request and some are for the regular
        // request. the unneeded ones are automatically ignored.
        parent::initialize($parameters);

        return $this;
    }

    public function setParameter($key, $value)
    {
        parent::setParameter($key, $value);

        $this->regularRequest->setParameter($key, $value);

        return $this;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'initialize';
    }

    /**
     * Returns the names of the parameters that hold Vindicia objects
     * that are passed in the request. Return format is
     * `object_name => parameter_name`
     *
     * @return array<string, string>
     */
    abstract protected function getObjectParamNames();

    public function getParameters()
    {
        return array_merge($this->parameters->all(), $this->regularRequest->getParameters());
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$WEB_SESSION_OBJECT;
    }

    /**
     * Get the redirect url that will be used in the case of an error.
     *
     * @return null|string
     */
    public function getErrorUrl()
    {
        return $this->getParameter('errorUrl');
    }

    /**
     * Set the redirect url that will be used in the case of an error.
     * Defaults to returnUrl if not provided.
     *
     * @param string $value
     * @return static
     */
    public function setErrorUrl($value)
    {
        return $this->setParameter('errorUrl', $value);
    }

    /**
     * Get the attributes for the HOA WebSession object itself. (getAttributes returns
     * the attributes for the regular request object, such as the Customer or Transaction)
     *
     * @return null|AttributeBag
     */
    public function getHOAAttributes()
    {
        return $this->getParameter('HOAAttributes');
    }

    /**
     * Set the attributes for the HOA WebSession object itself. (setAttributes returns
     * the attributes for the regular request object, such as the Customer or Transaction)
     *
     * @param array|AttributeBag $attributes
     * @return static
     */
    public function setHOAAttributes($attributes)
    {
        if ($attributes && !$attributes instanceof AttributeBag) {
            $attributes = new AttributeBag($attributes);
        }

        return $this->setParameter('HOAAttributes', $attributes);
    }

    public function getData()
    {
        $this->validate('returnUrl', 'ip');

        // make it so we can access the regular requests's getFunction method since we're
        // faking double inheritance
        $getFunction = new ReflectionMethod(static::$REGULAR_REQUEST_CLASS, 'getFunction');
        $getFunction->setAccessible(true);

        $session = new stdClass();
        $session->method = $this->regularRequest->getObject() . '_' . $getFunction->invoke($this->regularRequest, null);
        $session->ipAddress = $this->getIp();
        $session->returnURL = $this->getReturnUrl();
        $session->errorURL = $this->getErrorUrl();
        $session->privateFormValues = $this->getPrivateFormValues();
        $session->methodParamValues = $this->getMethodParamValues();
        $session->version = self::API_VERSION;

        $attributes = $this->getHOAAttributes();
        if ($attributes) {
            $session->nameValues = $this->buildNameValues($attributes);
        }

        $data = array();
        $data['session'] = $session;
        $data['action'] = $this->getFunction();

        return $data;
    }

    /**
     * @return array
     */
    protected function getPrivateFormValues()
    {
        $values = array();
        $objectParamNames = $this->getObjectParamNames();

        $payment_method_type = $this->getParameter('paymentMethodType') ?? self::PAYMENT_METHOD_CREDIT_CARD;;
        foreach ($objectParamNames as $object_name => $param_name) {
            $data = $this->regularRequest->getData($payment_method_type);
            $object = $data[$param_name];
            $values = array_merge($values, $this->buildPrivateFormValues('vin_' . $object_name, $object));
        }

        return $values;
    }

    /**
     * @return array
     */
    protected function buildPrivateFormValues($keySoFar, $member)
    {
        $lastKeyPart = array_slice(explode('_', $keySoFar), -1);
        if ($lastKeyPart[0] === 'nameValues') {
            // nameValues have special formatting, so loop through them and take care of them
            $nameValues = array();
            foreach ($member as $nameValue) {
                $nameValues[] = new NameValue(
                    $keySoFar . '_' . $nameValue->name,
                    $nameValue->value
                );
            }
            return $nameValues;
        } elseif (is_object($member) || is_array($member)) {
            $values = array();
            foreach ($member as $key => $value) {

                switch (strval($key)) {
                    // some objects get added separately instead of in the nexted structure
                    case 'sourcePaymentMethod':
                    case 'paymentMethod':
                        $keyExpanded = 'vin_PaymentMethod';
                        break;

                    case 'account':
                        $keyExpanded = 'vin_Account';
                        break;

                    case 'billingPlan':
                        $keyExpanded = 'vin_BillingPlan';
                        break;

                    default:
                        // all other fields get added on to the end of the string
                        $keyExpanded = $keySoFar . '_' . strval($key);
                }

                $values = array_merge(
                    $this->buildPrivateFormValues($keyExpanded, $value),
                    $values
                );
            }
            return $values;
        } elseif (isset($member)) {
            return array(new NameValue($keySoFar, $member));
        } else {
            return array();
        }
    }

    /**
     * @return array<int, NameValue>
     */
    abstract protected function getMethodParamValues();
}
