<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionsRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();

        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->startTime = $this->faker->timestamp();
        $this->endTime = $this->faker->timestamp();
        $this->page = $this->faker->intBetween(0, 10);
        $this->pageSize = $this->faker->intBetween(10, 10000);
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
                'customerReference' => $this->customerReference,
                'page' => $this->page,
                'pageSize' => $this->pageSize
            )
        );
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    /**
     * @return void
     */
    public function testPageSize()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPageSize($this->pageSize));
        $this->assertSame($this->pageSize, $request->getPageSize());
    }

    /**
     * @return void
     */
    public function testPage()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPage($this->page));
        $this->assertSame($this->page, $request->getPage());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertSame($this->page, $data['page']);
        $this->assertSame($this->pageSize, $data['pageSize']);
        $this->assertSame('fetchByAccount', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataDefaultPageAndSize()
    {
        $this->request->setPage(null);
        $this->request->setPageSize(null);
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertSame(0, $data['page']);
        $this->assertSame(10000, $data['pageSize']);
        $this->assertSame('fetchByAccount', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByTime()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $data = $this->request->getData();

        $this->assertSame($this->startTime, $data['timestamp']);
        $this->assertSame($this->endTime, $data['endTimestamp']);
        $this->assertSame($this->page, $data['page']);
        $this->assertSame($this->pageSize, $data['pageSize']);
        $this->assertSame('fetchDeltaSince', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The customerId parameter or the customerReference parameter or the startTime and endTime parameters are required.
     * @return                   void
     */
    public function testCustomerIdOrTimesRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Cannot fetch by both customer and start/end time.
     * @return                   void
     */
    public function testCustomerIdOrTimesNotBoth()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->getData();
    }

    /**
     * @return void
     */
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
        $this->assertInstanceOf('\Omnipay\Vindicia\Subscription', $subscriptions[0]);
        $this->assertInstanceOf('\Omnipay\Vindicia\Subscription', $subscriptions[1]);
        $this->assertNotNull($subscriptions[0]->getId());
        $this->assertNotNull($subscriptions[1]->getId());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
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
        $this->assertNotNull($subscriptions[0]->getId());
        $this->assertNotNull($subscriptions[1]->getId());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
