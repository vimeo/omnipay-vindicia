<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchCustomerRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->customerId = $this->faker->customerId();

        $this->request = new FetchCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId
            )
        );

        $this->customerReference = $this->faker->customerReference();
        $this->email = $this->faker->email();
        $this->name = $this->faker->name();
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['merchantAccountId']);
        $this->assertSame('fetchByMerchantAccountId', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByReference()
    {
        $this->request->setCustomerId(null)->setCustomerReference($this->customerReference);
        $data = $this->request->getData();

        $this->assertSame($this->customerReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     * @return                   void
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchCustomerSuccess.xml', array(
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'NAME' => $this->name,
            'EMAIL' => $this->email
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $customer = $response->getCustomer();
        $this->assertInstanceOf('\Omnipay\Vindicia\Customer', $customer);
        $this->assertSame($this->customerId, $response->getCustomerId());
        $this->assertSame($this->customerReference, $response->getCustomerReference());
        $this->assertSame($this->customerId, $customer->getId());
        $this->assertSame($this->customerReference, $customer->getReference());
        $this->assertSame($this->name, $customer->getName());
        $this->assertSame($this->email, $customer->getEmail());
        $attributes = $customer->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
        }

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchCustomerByReferenceSuccess.xml', array(
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getCustomer());
        $this->assertSame($this->customerId, $response->getCustomerId());
        $this->assertSame($this->customerReference, $response->getCustomerReference());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchCustomerFailure.xml', array(
            'CUSTOMER_ID' => $this->customerId,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load account by merchantAccountId "' . $this->customerId . '":  No match.', $response->getMessage());

        $this->assertNull($response->getCustomer());
    }

    /**
     * @return void
     */
    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchCustomerByReferenceFailure.xml', array(
            'CUSTOMER_REFERENCE' => $this->customerReference,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load account by VID "' . $this->customerReference . '":  No match.', $response->getMessage());

        $this->assertNull($response->getCustomer());
    }
}
