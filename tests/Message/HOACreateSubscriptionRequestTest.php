<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\AttributeBag;

class HOACreateSubscriptionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
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
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->attributes = $this->faker->attributesAsArray();
        $this->HOAAttributes = $this->faker->attributesAsArray();
        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->minChargebackProbability = $this->faker->chargebackProbability();

        $this->request = new HOACreateSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
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
                'paymentMethodId' => $this->paymentMethodId,
                'paymentMethodReference' => $this->paymentMethodReference,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip,
                'minChargebackProbability' => $this->minChargebackProbability,
                'name' => $this->name,
                'email' => $this->email,
                'HOAAttributes' => $this->HOAAttributes
            )
        );

        $this->webSessionReference = $this->faker->webSessionReference();
    }

    /**
     * @return void
     */
    public function testMinChargebackProbability()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $minChargebackProbability = $this->faker->chargebackProbability();
        $this->assertSame($request, $request->setMinChargebackProbability($minChargebackProbability));
        $this->assertSame($minChargebackProbability, $request->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setProductId($this->productId));
        $this->assertSame($this->productId, $request->getProductId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $request->getProductReference());
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setName($this->name));
        $this->assertSame($this->name, $request->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setEmail($this->email));
        $this->assertSame($this->email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $this->assertSame($this->request, $this->request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $this->request->getAttributes());
    }

    /**
     * @return void
     */
    public function testHOAAttributes()
    {
        $this->assertSame($this->request, $this->request->setHOAAttributes($this->HOAAttributes));
        $this->assertEquals(new AttributeBag($this->HOAAttributes), $this->request->getHOAAttributes());
    }

    /**
     * @return void
     */
    public function testIp()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testReturnUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setReturnUrl($this->returnUrl));
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
    }

    /**
     * @return void
     */
    public function testErrorUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreateSubscriptionRequest');
        $request->initialize();

        $this->assertSame($request, $request->setErrorUrl($this->errorUrl));
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->returnUrl, $data['session']->returnURL);
        $this->assertSame($this->errorUrl, $data['session']->errorURL);
        $this->assertSame('AutoBill_update', $data['session']->method);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $numHOAAttributes = count($this->HOAAttributes);
        $this->assertSame($numHOAAttributes, count($data['session']->nameValues));
        for ($i = 0; $i < $numHOAAttributes; $i++) {
            $this->assertSame($this->HOAAttributes[$i]['name'], $data['session']->nameValues[$i]->name);
            $this->assertSame($this->HOAAttributes[$i]['value'], $data['session']->nameValues[$i]->value);
        }
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_billingStatementIdentifier', $this->statementDescriptor),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_currency', $this->currency),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_merchantAutoBillId', $this->subscriptionId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_VID', $this->subscriptionReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_sourceIp', $this->ip),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_startTimestamp', $this->startTime),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_statementFormat', 'DoNotSend'),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_BillingPlan_merchantBillingPlanId', $this->planId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_items_0_product_merchantProductId', $this->productId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_merchantPaymentMethodId', $this->paymentMethodId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_merchantAccountId', $this->customerId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_name', $this->name),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_emailAddress', $this->email),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_BillingPlan_VID', $this->planReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_items_0_product_VID', $this->productReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_VID', $this->paymentMethodReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_VID', $this->customerReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_AutoBill_currency', $this->currency),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('AutoBill_Update_minChargebackProbability', $this->minChargebackProbability),
            $data['session']->methodParamValues
        ));

        $this->assertSame('initialize', $data['action']);
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('HOACreateSubscriptionSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'CUSTOMER_ID' => $this->customerId,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'PLAN_ID' => $this->planId,
            'PRODUCT_ID' => $this->productId,
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'STATEMENT_DESCRIPTOR' => $this->statementDescriptor
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->webSessionReference, $response->getWebSessionReference());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/WebSession.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('HOACreateSubscriptionFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        // really Vindicia?
        $this->assertSame('Unable to initialize WebSession: Insert or update ("insert into web_session (ip_id, error_url_id, method, version, expire_time, merchant_id, entity_id) VALUES (\'123456789\', \'123456789\', \'AutoBill_Update\', \'18.0\', \'2016-08-15 08:04:32\', \'123456789\', \'123456789\')") failed:  ORA-01400: cannot insert NULL into ("VINDICIA"."WEB_SESSION"."RETURN_URL_ID") (DBD ERROR: OCIStmtExecute)', $response->getMessage());

        $this->assertNull($response->getWebSessionReference());
    }
}
