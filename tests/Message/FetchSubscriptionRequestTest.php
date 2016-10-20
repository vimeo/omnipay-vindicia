<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();

        $this->request = new FetchSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId
            )
        );

        $this->subscriptionReference = $this->faker->subscriptionReference();
    }

    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['merchantAutoBillId']);
        $this->assertSame('fetchByMerchantAutoBillId', $data['action']);
    }

    public function testGetDataByReference()
    {
        $this->request->setSubscriptionId(null)->setSubscriptionReference($this->subscriptionReference);

        $data = $this->request->getData();

        $this->assertSame($this->subscriptionReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the subscriptionId or subscriptionReference parameter is required.
     */
    public function testSubscriptionIdOrReferenceRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->setSubscriptionReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionSuccess.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getSubscription());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionByReferenceSuccess.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getSubscription());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionFailure.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to find autobill by serial number ' . $this->subscriptionId . ': ', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }

    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionByReferenceFailure.xml', array(
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load autobill by VID "' . $this->subscriptionReference . '":  .', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }
}
