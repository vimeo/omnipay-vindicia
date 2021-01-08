<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchRefundsRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
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

        $this->refundId = $this->faker->refundId();
        $this->refundReference = $this->faker->refundReference();
        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->timestamp = date('Y-m-d\T12:00:00-04:00');
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchRefundsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['transaction']->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['transaction']->VID);
        $this->assertSame('fetchByTransaction', $data['action']);
    }

    /**
     * @return void
     */
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
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The transactionId parameter or the transactionReference parameter or the startTime and endTime parameters are required.
     * @return                   void
     */
    public function testTransactionIdOrReferenceOrTimesRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Cannot fetch by both transaction and start/end time.
     * @return                   void
     */
    public function testTransactionIdOrTimesNotBoth()
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
        $this->setMockSoapResponse('FetchRefundsSuccess.xml', array(
            'REFUND_ID' => $this->refundId,
            'REFUND_REFERENCE' => $this->refundReference,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $refunds = $response->getRefunds();
        $this->assertTrue(is_array($refunds));
        $this->assertSame(1, count($refunds));

        $refund = $refunds[0];
        $this->assertInstanceOf('\Omnipay\Vindicia\Refund', $refund);
        $this->assertSame($this->refundId, $refund->getId());
        $this->assertSame($this->refundReference, $refund->getReference());
        $this->assertSame($this->currency, $refund->getCurrency());
        $this->assertSame($this->amount, $refund->getAmount());
        $transaction = $refund->getTransaction();
        $this->assertSame($this->transactionId, $refund->getTransactionId());
        $this->assertSame($this->transactionId, $transaction->getId());
        $this->assertSame($this->transactionReference, $refund->getTransactionReference());
        $this->assertSame($this->transactionReference, $transaction->getReference());
        $attributes = $refund->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
        }

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Refund.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchRefundsByTimeSuccess.xml', array(
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $refunds = $response->getRefunds();
        $this->assertTrue(is_array($refunds));
        $this->assertSame(1, count($refunds));
        $this->assertNotNull($refunds[0]->getId());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Refund.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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
