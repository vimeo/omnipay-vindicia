<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionsRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->startTime = $this->faker->timestamp();
        $this->endTime = $this->faker->timestamp();
        // make sure endTime is after startTime
        if ($this->endTime < $this->startTime) {
            $temp = $this->endTime;
            $this->endTime = $this->startTime;
            $this->startTime = $temp;
        }

        $this->request = new FetchSubscriptionsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference
            )
        );
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertSame('fetchByAccount', $data['action']);
    }

    public function testGetDataByTime()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $data = $this->request->getData();

        $this->assertSame($this->startTime, $data['timestamp']);
        $this->assertSame($this->endTime, $data['endTimestamp']);
        $this->assertSame('fetchDeltaSince', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The customerId parameter or the customerReference parameter or the startTime and endTime parameters are required.
     */
    public function testCustomerIdOrTimesRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Cannot fetch by both customer and start/end time.
     */
    public function testCustomerIdOrTimesNotBoth()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionsSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $subscriptions = $response->getSubscriptions();
        $this->assertTrue(is_array($subscriptions));
        $this->assertSame(2, count($subscriptions));
        $this->assertNotNull($subscriptions[0]->merchantAutoBillId);
        $this->assertNotNull($subscriptions[1]->merchantAutoBillId);

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionsByTimeSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $subscriptions = $response->getSubscriptions();
        $this->assertTrue(is_array($subscriptions));
        $this->assertSame(2, count($subscriptions));
        $this->assertNotNull($subscriptions[0]->merchantAutoBillId);
        $this->assertNotNull($subscriptions[1]->merchantAutoBillId);

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionsFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load account:  No match.', $response->getMessage());

        $this->assertNull($response->getSubscriptions());
    }

    public function testSendByTimeFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionsByTimeFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('500', $response->getCode());
        $this->assertSame('Process failed (Internal)', $response->getMessage());

        $this->assertNull($response->getSubscriptions());
    }
}
