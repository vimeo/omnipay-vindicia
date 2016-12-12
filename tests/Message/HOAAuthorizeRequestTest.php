<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\AttributeBag;

class HOAAuthorizeRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->currency = $this->faker->currency();
        $this->taxClassification = $this->faker->taxClassification();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->minChargebackProbability = $this->faker->chargebackProbability();
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->attributes = $this->faker->attributesAsArray();
        $this->HOAAttributes = $this->faker->attributesAsArray();

        $this->request = new HOAAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'taxClassification' => $this->taxClassification,
                'statementDescriptor' => $this->statementDescriptor,
                'paymentMethodId' => $this->paymentMethodId,
                'customerId' => $this->customerId,
                'paymentMethodReference' => $this->paymentMethodReference,
                'customerReference' => $this->customerReference,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip,
                'minChargebackProbability' => $this->minChargebackProbability,
                'name' => $this->name,
                'email' => $this->email,
                'attributes' => $this->attributes,
                'HOAAttributes' => $this->HOAAttributes
            )
        );
        $this->webSessionReference = $this->faker->webSessionReference();
        $this->items = $this->faker->itemsAsArray($this->currency);
    }

    /**
     * @return void
     */
    public function testMinChargebackProbability()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $minChargebackProbability = $this->faker->chargebackProbability();
        $this->assertSame($request, $request->setMinChargebackProbability($minChargebackProbability));
        $this->assertSame($minChargebackProbability, $request->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setItems($this->items));
        $this->assertEquals(new VindiciaItemBag($this->items), $request->getItems());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setName($this->name));
        $this->assertSame($this->name, $request->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setEmail($this->email));
        $this->assertSame($this->email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
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
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    /**
     * @return void
     */
    public function testReturnUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
        $request->initialize();

        $this->assertSame($request, $request->setReturnUrl($this->returnUrl));
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
    }

    /**
     * @return void
     */
    public function testErrorUrl()
    {
        $request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\HOAAuthorizeRequest');
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
        $this->assertSame('Transaction_auth', $data['session']->method);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $this->assertTrue(in_array(
            new NameValue('vin_Transaction_currency', $this->currency),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Transaction_transactionItems_0_price', $this->amount),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Transaction_transactionItems_0_taxClassification', $this->taxClassification),
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
            new NameValue('vin_PaymentMethod_merchantPaymentMethodId', $this->paymentMethodId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_VID', $this->customerReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_VID', $this->paymentMethodReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_type', 'CreditCard'),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_active', true),
            $data['session']->privateFormValues
        ));

        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_minChargebackProbability', $this->minChargebackProbability),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_sendEmailNotification', false),
            $data['session']->methodParamValues
        ));
        $this->assertFalse(in_array(
            new NameValue('Transaction_Auth_campaignCode', null),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_dryrun', false),
            $data['session']->methodParamValues
        ));

        $numAttributes = count($this->attributes);
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_nameValues_' . $this->attributes[$i]['name'], $this->attributes[$i]['value']),
                $data['session']->privateFormValues
            ));
        }

        $this->assertSame('initialize', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataMultipleItems()
    {
        $this->request->setAmount(null)->setItems($this->items);

        $data = $this->request->getData();

        $this->assertSame($this->returnUrl, $data['session']->returnURL);
        $this->assertSame($this->errorUrl, $data['session']->errorURL);
        $this->assertSame('Transaction_auth', $data['session']->method);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $numHOAAttributes = count($this->HOAAttributes);
        $this->assertSame($numHOAAttributes, count($data['session']->nameValues));
        for ($i = 0; $i < $numHOAAttributes; $i++) {
            $this->assertSame($this->HOAAttributes[$i]['name'], $data['session']->nameValues[$i]->name);
            $this->assertSame($this->HOAAttributes[$i]['value'], $data['session']->nameValues[$i]->value);
        }
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $numItems = count($this->items);
        for ($i = 0; $i < $numItems; $i++) {
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_transactionItems_' . $i . '_price', $this->items[$i]['price']),
                $data['session']->privateFormValues
            ));
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_transactionItems_' . $i . '_quantity', $this->items[$i]['quantity']),
                $data['session']->privateFormValues
            ));
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_transactionItems_' . $i . '_sku', $this->items[$i]['sku']),
                $data['session']->privateFormValues
            ));
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_transactionItems_' . $i . '_nameValues_description', $this->items[$i]['description']),
                $data['session']->privateFormValues
            ));
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_transactionItems_' . $i . '_taxClassification', $this->taxClassification),
                $data['session']->privateFormValues
            ));
        }

        $this->assertTrue(in_array(
            new NameValue('vin_Transaction_currency', $this->currency),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_merchantAccountId', $this->customerId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_emailAddress', $this->email),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_name', $this->name),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_merchantPaymentMethodId', $this->paymentMethodId),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_Account_VID', $this->customerReference),
            $data['session']->privateFormValues
        ));
        $this->assertTrue(in_array(
            new NameValue('vin_PaymentMethod_VID', $this->paymentMethodReference),
            $data['session']->privateFormValues
        ));

        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_minChargebackProbability', $this->minChargebackProbability),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_sendEmailNotification', false),
            $data['session']->methodParamValues
        ));
        $this->assertFalse(in_array(
            new NameValue('Transaction_Auth_campaignCode', null),
            $data['session']->methodParamValues
        ));
        $this->assertTrue(in_array(
            new NameValue('Transaction_Auth_dryrun', false),
            $data['session']->methodParamValues
        ));

        $numAttributes = count($this->attributes);
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertTrue(in_array(
                new NameValue('vin_Transaction_nameValues_' . $this->attributes[$i]['name'], $this->attributes[$i]['value']),
                $data['session']->privateFormValues
            ));
        }

        $this->assertSame('initialize', $data['action']);
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('HOAAuthorizeSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'CUSTOMER_ID' => $this->customerId,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'MIN_CHARGEBACK_PROBABILITY' => $this->minChargebackProbability,
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->webSessionReference, $response->getWebSessionReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/WebSession.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('HOAAuthorizeFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        // really Vindicia?
        $this->assertSame('Unable to initialize WebSession: Insert or update ("insert into web_session (ip_id, error_url_id, method, version, expire_time, merchant_id, entity_id) VALUES (\'123456789\', \'123456789\', \'Transaction_Auth\', \'18.0\', \'2016-08-15 08:04:32\', \'123456789\', \'123456789\')") failed:  ORA-01400: cannot insert NULL into ("VINDICIA"."WEB_SESSION"."RETURN_URL_ID") (DBD ERROR: OCIStmtExecute)', $response->getMessage());

        $this->assertNull($response->getWebSessionReference());
    }
}
