<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\FetchPaymentMethodsRequest;
use Omnipay\VindiciaTest\SoapTestCase;

class FetchPaymentMethodsRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();

        $this->request = new FetchPaymentMethodsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference
            )
        );
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPaymentMethodsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPaymentMethodsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertSame('fetchByAccount', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The customerId parameter or the customerReference parameter is required.
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchPaymentMethodsSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertTrue(is_array($response->getPaymentMethods()));
        $this->assertSame(2, count($response->getPaymentMethods()));
        $this->assertTrue(isset($response->getPaymentMethods()[0]->merchantPaymentMethodId));
        $this->assertTrue(isset($response->getPaymentMethods()[1]->merchantPaymentMethodId));

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/PaymentMethod.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchPaymentMethodsFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Account not found.', $response->getMessage());

        $this->assertNull($response->getPaymentMethods());
    }
}
