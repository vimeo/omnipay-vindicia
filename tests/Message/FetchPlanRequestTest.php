<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchPlanRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->planId = $this->faker->planId();

        $this->request = new FetchPlanRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'planId' => $this->planId
            )
        );

        $this->planReference = $this->faker->planReference();
    }

    public function testPlanId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    public function testPlanReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->planId, $data['merchantBillingPlanId']);
        $this->assertSame('fetchByMerchantBillingPlanId', $data['action']);
    }

    public function testGetDataByReference()
    {
        $this->request->setPlanId(null)->setPlanReference($this->planReference);
        $data = $this->request->getData();

        $this->assertSame($this->planReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the planId or planReference parameter is required.
     */
    public function testPlanIdOrReferenceRequired()
    {
        $this->request->setPlanId(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchPlanSuccess.xml', array(
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getPlan());
        $this->assertSame($this->planId, $response->getPlanId());
        $this->assertSame($this->planReference, $response->getPlanReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/BillingPlan.wsdl', $this->getLastEndpoint());
    }

    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchPlanByReferenceSuccess.xml', array(
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getPlan());
        $this->assertSame($this->planId, $response->getPlanId());
        $this->assertSame($this->planReference, $response->getPlanReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/BillingPlan.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchPlanFailure.xml', array(
            'PLAN_ID' => $this->planId,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('No BillingPlans found for Merchant Billing Plan id  = ' . $this->planId, $response->getMessage());

        $this->assertNull($response->getPlan());
    }

    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchPlanByReferenceFailure.xml', array(
            'PLAN_REFERENCE' => $this->planReference,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('No BillingPlans found for Vid = ' . $this->planReference, $response->getMessage());

        $this->assertNull($response->getPlan());
    }
}
