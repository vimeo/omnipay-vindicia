<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest;
use Omnipay\VindiciaTest\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\VindiciaCreditCard;
use Omnipay\Vindicia\AttributeBag;

class CreatePayPalSubscriptionRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->planId = $this->faker->planId();
        $this->customerId = $this->faker->customerId();
        $this->productId = $this->faker->productId();
        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->planReference = $this->faker->planReference();
        $this->customerReference = $this->faker->customerReference();
        $this->productReference = $this->faker->productReference();
        $this->currency = $this->faker->currency();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->startTime = $this->faker->timestamp();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->attributes = $this->faker->attributes(true);
        $this->returnUrl = $this->faker->url();
        $this->cancelUrl = $this->faker->url();
        $this->card = [
            'country' => $this->faker->region(),
            'postcode' => $this->faker->postcode()
        ];

        $this->request = new CreatePayPalSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'planId' => $this->planId,
                'customerId' => $this->customerId,
                'productId' => $this->productId,
                'subscriptionReference' => $this->subscriptionReference,
                'planReference' => $this->planReference,
                'customerReference' => $this->customerReference,
                'productReference' => $this->productReference,
                'currency' => $this->currency,
                'statementDescriptor' => $this->statementDescriptor,
                'ip' => $this->ip,
                'startTime' => $this->startTime,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'attributes' => $this->attributes,
                'returnUrl' => $this->returnUrl,
                'cancelUrl' => $this->cancelUrl
            )
        );

        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->payPalToken = $this->faker->payPalToken();
        $this->amount = $this->faker->monetaryAmount($this->currency);
    }

    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    public function testPlanId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testProductId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductId($this->productId));
        $this->assertSame($this->productId, $request->getProductId());
    }

    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    public function testPlanReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    public function testProductReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $request->getProductReference());
    }

    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    public function testIp()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    public function testReturnUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setReturnUrl($this->returnUrl));
        $this->assertEquals($this->returnUrl, $request->getReturnUrl());
    }

    public function testCancelUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCancelUrl($this->cancelUrl));
        $this->assertEquals($this->cancelUrl, $request->getCancelUrl());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->planId, $data['autobill']->billingPlan->merchantBillingPlanId);
        $this->assertSame($this->subscriptionReference, $data['autobill']->VID);
        $this->assertSame($this->planReference, $data['autobill']->billingPlan->VID);
        $this->assertSame(1, count($data['autobill']->items));
        $this->assertSame($this->productId, $data['autobill']->items[0]->product->merchantProductId);
        $this->assertSame($this->customerId, $data['autobill']->account->merchantAccountId);
        $this->assertSame($this->productReference, $data['autobill']->items[0]->product->VID);
        $this->assertSame($this->customerReference, $data['autobill']->account->VID);
        $this->assertSame($this->currency, $data['autobill']->currency);
        $this->assertSame($this->ip, $data['autobill']->sourceIp);
        $this->assertSame($this->startTime, $data['autobill']->startTimestamp);
        $this->assertSame('DoNotSend', $data['autobill']->statementFormat);
        $this->assertSame($this->statementDescriptor, $data['autobill']->billingStatementIdentifier);
        $this->assertSame($this->paymentMethodId, $data['autobill']->paymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->card['postcode'], $data['autobill']->paymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['autobill']->paymentMethod->billingAddress->country);
        $this->assertSame('PayPal', $data['autobill']->paymentMethod->type);
        $this->assertSame($this->returnUrl, $data['autobill']->paymentMethod->paypal->returnUrl);
        $this->assertSame($this->cancelUrl, $data['autobill']->paymentMethod->paypal->cancelUrl);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['autobill']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['autobill']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['autobill']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
        $this->assertSame('doNotSaveAutoBill', $data['immediateAuthFailurePolicy']);
        $this->assertSame(true, $data['validateForFuturePayment']);
        $this->assertSame(false, $data['ignoreAvsPolicy']);
        $this->assertSame(false, $data['ignoreCvnPolicy']);
        $this->assertSame(null, $data['campaignCode']);
        $this->assertSame(false, $data['dryrun']);
        $this->assertSame(null, $data['cancelReasonCode']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The returnUrl parameter is required
     */
    public function testReturnUrlRequired()
    {
        $this->request->setReturnUrl(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The cancelUrl parameter is required
     */
    public function testCancelUrlRequired()
    {
        $this->request->setCancelUrl(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the productId or productReference parameter is required.
     */
    public function testProductIdOrReferenceRequired()
    {
        $this->request->setProductId(null);
        $this->request->setProductReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The subscriptionId parameter is required
     */
    public function testSubscriptionIdRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CreatePayPalSubscriptionSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference,
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference,
            'PRODUCT_ID' => $this->productId,
            'PRODUCT_REFERENCE' => $this->productReference,
            'RETURN_URL' => $this->returnUrl,
            'CANCEL_URL' => $this->cancelUrl,
            'PAYPAL_TOKEN' => $this->payPalToken
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());
        $this->assertSame('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=' . $this->payPalToken, $response->getRedirectUrl());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreatePayPalSubscriptionFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to update BillingPlan: Unable to create BillingPlan without a Billing Period ', $response->getMessage());

        $this->assertNull($response->getSubscriptionId());
        $this->assertNull($response->getSubscriptionReference());
        $this->assertNull($response->getRedirectUrl());
    }
}
