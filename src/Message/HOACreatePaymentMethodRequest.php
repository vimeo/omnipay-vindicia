<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\NameValue;
use ReflectionMethod;

/**
 * Initialize a HOA Web Session to collect new card details. This can also be used to
 * update an existing card if called via updatePaymentMethod.
 *
 * Uses the same parameters as a regular create payment method request, but you would
 * not provide the card details since those will come from the form.
 *
 * This function can also be used to update an existing payment method if called
 * via updatePaymentMethod.
 *
 * Additional parameters for the Web Session:
 * - returnUrl: The URL the customer should be sent to after form submission and a
 * successful authorization. Required.
 * - errorUrl: The URL the customer should be sent to after form submission if the
 * authorization fails. Defaults the the returnUrl.
 * - hoaAttributes: The attributes parameter will specify attributes on the transaction,
 * so if you wish to specify attributes on the Web Session, you can use hoaAttributes.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia_HOA');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a customer
 *   $customerResponse = $gateway->createCustomer(array(
 *       'name' => 'Test Customer',
 *       'email' => 'customer@example.com',
 *       'customerId' => '123456789'
 *   ))->send();
 *
 *   if ($customerResponse->isSuccessful()) {
 *       echo "Customer id: " . $customerResponse->getCustomerId() . PHP_EOL;
 *       echo "Customer reference: " . $customerResponse->getCustomerReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   $paymentMethodResponse = $gateway->createPaymentMethod(array(
 *       'customerId' => $customerResponse->getCustomerId(), // alternatively you could use customerReference
 *       'paymentMethodId' => 'cc-123456', // this ID will be assigned to the card
 *       'returnUrl' => 'http://www.example.com/success',
 *       'errorUrl' => 'http://www.example.com/failure'
 *   ))->send();
 *
 *   if ($paymentMethodResponse->isSuccessful()) {
 *       echo "Web session reference: " . $paymentMethodResponse->getWebSessionReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // ... Now the user is filling in the credit card form on your site. They click submit
 *   // and the form is submitted directly to Vindicia to create the payment method. Vindicia
 *   // returns to your returnUrl, so now we're going to complete the request:
 *
 *   $completeResponse = $gateway->complete(array(
 *       // use the webSessionReference from the authorize response to identify the session to complete
 *       'webSessionReference' => $paymentMethodResponse->getWebSessionReference()
 *   ))->send();
 *
 *   if ($completeResponse->isSuccessful()) {
 *       // You can check what request was just completed:
 *       echo "Did we just complete a create payment method web session? "
 *            . $completeResponse->wasCreatePaymentMethod() . PHP_EOL;
 *       // payment method object:
 *       var_dump($completeResponse->getPaymentMethod());
 *       // values that were passed in the form:
 *       var_dump($completeResponse->getFormValues());
 *   } else {
 *       if ($completeResponse->isRequestFailure()) {
 *           echo 'The HOA request itself failed!' . PHP_EOL;
 *       } else {
 *           // This case means that although the HOA request succeeded, the method it called,
 *           // such as authorize or purchase, had an error. Also identifiable by
 *           // $completeResponse->isMethodFailure()
 *           echo 'The HOA request succeeded, but the method it called failed!' . PHP_EOL;
 *       }
 *       echo 'Error message: ' . $completeResponse->getMessage() . PHP_EOL;
 *       // error handling
 *   }
 *
 * </code>
 */
class HOACreatePaymentMethodRequest extends AbstractHOARequest
{
    /**
     * @var string
     */
    protected static $REGULAR_REQUEST_CLASS = '\Omnipay\Vindicia\Message\CreatePaymentMethodRequest';

    /**
     * @var \Omnipay\Vindicia\Message\CreatePaymentMethodRequest
     */
    protected $regularRequest;

    /**
     * @param array<string, mixed> $parameters
     * @return HOACreatePaymentMethodRequest
     */
    public function initialize(array $parameters = array())
    {
        parent::initialize($parameters);

        // card parameter isn't required since that will come from the HOA form
        $this->regularRequest->setCardRequired(false);

        return $this;
    }

    protected function getObjectParamNames()
    {
        $names = array(
            self::$PAYMENT_METHOD_OBJECT => 'paymentMethod'
        );

        if ($this->hasCustomer()) {
            $names[self::$CUSTOMER_OBJECT] = 'account';
        }

        return $names;
    }

    /**
     * @return null|bool
     */
    public function getValidate()
    {
        return $this->regularRequest->getValidate();
    }

    /**
     * @return HOACreatePaymentMethodRequest
     */
    public function setValidate($value)
    {
        $this->regularRequest->setValidate($value);
        return $this;
    }

    /**
     * @return array<int, NameValue>
     */
    protected function getMethodParamValues()
    {
        $regularRequestData = $this->regularRequest->getData();

        if ($this->hasCustomer()) {
            return array(
                new NameValue(
                    'Account_UpdatePaymentMethod_replaceOnAllAutoBills',
                    $regularRequestData['replaceOnAllAutoBills']
                ),
                new NameValue('Account_UpdatePaymentMethod_updateBehavior', $regularRequestData['updateBehavior']),
                new NameValue('Account_UpdatePaymentMethod_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
                new NameValue('Account_UpdatePaymentMethod_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy'])
            );
        } else {
            return array(
                new NameValue(
                    'PaymentMethod_Update_replaceOnAllAutoBills',
                    $regularRequestData['replaceOnAllAutoBills']
                ),
                new NameValue(
                    'PaymentMethod_Update_replaceOnAllChildAutoBills',
                    $regularRequestData['replaceOnAllChildAutoBills']
                ),
                new NameValue('PaymentMethod_Update_validate', $regularRequestData['validate']),
                new NameValue(
                    'PaymentMethod_Update_minChargebackProbability',
                    $regularRequestData['minChargebackProbability']
                ),
                new NameValue('PaymentMethod_Update_sourceIp', $regularRequestData['sourceIp']),
                new NameValue('PaymentMethod_Update_ignoreAvsPolicy', $regularRequestData['ignoreAvsPolicy']),
                new NameValue('PaymentMethod_Update_ignoreCvnPolicy', $regularRequestData['ignoreCvnPolicy'])
            );
        }
    }

    /**
     * @return bool
     */
    protected function hasCustomer()
    {
        // make it so we can access the regular requests's method since we're
        // faking double inheritance
        $hasCustomer = new ReflectionMethod(static::$REGULAR_REQUEST_CLASS, 'hasCustomer');
        $hasCustomer->setAccessible(true);
        return $hasCustomer->invoke($this->regularRequest, null);
    }
}
