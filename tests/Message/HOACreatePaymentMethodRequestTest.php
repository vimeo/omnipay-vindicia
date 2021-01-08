<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\AttributeBag;

class HOACreatePaymentMethodRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->email = $this->faker->email();
        $this->name = $this->faker->name();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->validate = $this->faker->bool();
        $this->HOAAttributes = $this->faker->attributesAsArray();

        $this->request = new HOACreatePaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'paymentMethodId' => $this->paymentMethodId,
                'customerReference' => $this->customerReference,
                'paymentMethodReference' => $this->paymentMethodReference,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip,
                'validate' => $this->validate,
                'HOAAttributes' => $this->HOAAttributes
            )
        );

        $this->webSessionReference = $this->faker->webSessionReference();
    }

    /**
     * @return void
     */
    public function testValidate()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setValidate($this->validate));
        $this->assertSame($this->validate, $request->getValidate());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
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
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    /**
     * @return void
     */
    public function testReturnUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
        $request->initialize();

        $this->assertSame($request, $request->setReturnUrl($this->returnUrl));
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
    }

    /**
     * @return void
     */
    public function testErrorUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest');
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
        $this->assertSame('Account_updatePaymentMethod', $data['session']->method);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $numHOAAttributes = count($this->HOAAttributes);
        $this->assertSame($numHOAAttributes, count($data['session']->nameValues));
        for ($i = 0; $i < $numHOAAttributes; $i++) {
            $this->assertSame($this->HOAAttributes[$i]['name'], $data['session']->nameValues[$i]->name);
            $this->assertSame($this->HOAAttributes[$i]['value'], $data['session']->nameValues[$i]->value);
        }
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_merchantPaymentMethodId', $this->paymentMethodId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_merchantAccountId', $this->customerId),
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
            new NameValue('Account_UpdatePaymentMethod_updateBehavior', $this->validate ? CreatePaymentMethodRequest::VALIDATE_CARD : CreatePaymentMethodRequest::SKIP_CARD_VALIDATION),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Account_UpdatePaymentMethod_replaceOnAllAutoBills', true),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Account_UpdatePaymentMethod_ignoreAvsPolicy', false),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Account_UpdatePaymentMethod_ignoreCvnPolicy', false),
            $data['session']->methodParamValues
        ));

        $this->assertSame('initialize', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataNoCustomer()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);
        $data = $this->request->getData();

        $this->assertSame($this->returnUrl, $data['session']->returnURL);
        $this->assertSame($this->errorUrl, $data['session']->errorURL);
        $this->assertSame('PaymentMethod_update', $data['session']->method);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $numHOAAttributes = count($this->HOAAttributes);
        $this->assertSame($numHOAAttributes, count($data['session']->nameValues));
        for ($i = 0; $i < $numHOAAttributes; $i++) {
            $this->assertSame($this->HOAAttributes[$i]['name'], $data['session']->nameValues[$i]->name);
            $this->assertSame($this->HOAAttributes[$i]['value'], $data['session']->nameValues[$i]->value);
        }
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_merchantPaymentMethodId', $this->paymentMethodId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_VID', $this->paymentMethodReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('PaymentMethod_Update_validate', $this->validate),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('PaymentMethod_Update_replaceOnAllAutoBills', true),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('PaymentMethod_Update_replaceOnAllChildAutoBills', true),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('PaymentMethod_Update_ignoreAvsPolicy', false),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('PaymentMethod_Update_ignoreCvnPolicy', false),
            $data['session']->methodParamValues
        ));

        $this->assertSame('initialize', $data['action']);
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('HOACreatePaymentMethodSuccess.xml', array(
            'CUSTOMER_ID' => $this->customerId,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip
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
        $this->setMockSoapResponse('HOACreatePaymentMethodFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        // really Vindicia?
        $this->assertSame('Unable to initialize WebSession: Insert or update ("insert into web_session (ip_id, error_url_id, method, version, expire_time, merchant_id, entity_id) VALUES (\'123456789\', \'123456789\', \'Account_UpdatePaymentMethod\', \'18.0\', \'2016-08-15 08:04:32\', \'123456789\', \'123456789\')") failed:  ORA-01400: cannot insert NULL into ("VINDICIA"."WEB_SESSION"."RETURN_URL_ID") (DBD ERROR: OCIStmtExecute)', $response->getMessage());

        $this->assertNull($response->getWebSessionReference());
    }
}
