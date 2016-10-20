<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchRefundsRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->startTime = $this->faker->timestamp();
        $this->endTime = $this->faker->timestamp();
        // make sure endTime is after startTime
        if ($this->endTime < $this->startTime) {
            $temp = $this->endTime;
            $this->endTime = $this->startTime;
            $this->startTime = $temp;
        }

        $this->request = new FetchRefundsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId,
                'transactionReference' => $this->transactionReference
            )
        );
    }

    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['transaction']->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['transaction']->VID);
        $this->assertSame('fetchByTransaction', $data['action']);
    }

    public function testGetDataByTime()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $data = $this->request->getData();

        $this->assertSame($this->startTime, $data['timestamp']);
        $this->assertSame($this->endTime, $data['endTimestamp']);
        $this->assertSame('fetchDeltaSince', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The transactionId parameter or the transactionReference parameter or the startTime and endTime parameters are required.
     */
    public function testTransactionIdOrReferenceOrTimesRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Cannot fetch by both transaction and start/end time.
     */
    public function testTransactionIdOrTimesNotBoth()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchRefundsSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $refunds = $response->getRefunds();
        $this->assertTrue(is_array($refunds));
        $this->assertSame(1, count($refunds));
        $this->assertNotNull($refunds[0]->merchantRefundId);

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Refund.wsdl', $this->getLastEndpoint());
    }

    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchRefundsByTimeSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $refunds = $response->getRefunds();
        $this->assertTrue(is_array($refunds));
        $this->assertSame(1, count($refunds));
        $this->assertNotNull($refunds[0]->merchantRefundId);

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Refund.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchRefundsFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to load transaction to search by: No matches.', $response->getMessage());
    }

    public function testSendByTimeFailure()
    {
        $this->setMockSoapResponse('FetchRefundsByTimeFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('500', $response->getCode());
        $this->assertSame('Process failed (Internal)', $response->getMessage());
    }
}
