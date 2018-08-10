<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchChargebacksRequestTest extends SoapTestCase
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

        $this->request = new FetchChargebacksRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId
            )
        );

        $this->chargebackReference = $this->faker->chargebackReference();
        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);

        $this->timestamp = date('Y-m-d\T12:00:00-04:00');

        $this->reasonCode = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 10));
        $this->caseNumber = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 10));
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
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

        $this->assertSame($this->transactionId, $data['merchantTransactionId']);
        $this->assertSame('fetchByMerchantTransactionId', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByReference()
    {
        $this->request->setTransactionId(null)->setTransactionReference($this->transactionReference);

        $data = $this->request->getData();

        $this->assertSame($this->transactionReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByTime()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->setTransactionId(null);
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
        $this->setMockSoapResponse('FetchChargebacksSuccess.xml', array(
            'CHARGEBACK_REFERENCE' => $this->chargebackReference,
            'TRANSACTION_ID' => $this->transactionId,
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'REASON_CODE' => $this->reasonCode,
            'CASE_NUMBER' => $this->caseNumber,
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertTrue(is_array($response->getChargebacks()));
        $this->assertSame(2, count($response->getChargebacks()));
        $chargebacks = $response->getChargebacks();
        $this->assertInstanceOf('\Omnipay\Vindicia\Chargeback', $chargebacks[0]);
        $this->assertInstanceOf('\Omnipay\Vindicia\Chargeback', $chargebacks[1]);
        $chargeback = $chargebacks[0];
        $this->assertSame($this->chargebackReference, $chargeback->getReference());
        $this->assertSame($this->transactionId, $chargeback->getTransactionId());
        $this->assertSame($this->currency, $chargeback->getCurrency());
        $this->assertSame($this->amount, $chargeback->getAmount());
        $this->assertSame($this->caseNumber, $chargeback->getCaseNumber());
        $this->assertSame($this->reasonCode, $chargeback->getReasonCode());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Chargeback.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchChargebacksByTimeSuccess.xml', array(
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertTrue(is_array($response->getChargebacks()));
        $this->assertSame(2, count($response->getChargebacks()));
        $chargebacks = $response->getChargebacks();
        $this->assertNotNull($chargebacks[0]->getTransactionId());
        $this->assertNotNull($chargebacks[1]->getTransactionId());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Chargeback.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchChargebacksFailure.xml', array(
            'TRANSACTION_ID' => $this->transactionId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load chargebacks by merchantTransactionId "' . $this->transactionId . ' ":  No match.', $response->getMessage());

        $this->assertNull($response->getChargebacks());
    }

    /**
     * @return void
     */
    public function testSendByTimeFailure()
    {
        $this->setMockSoapResponse('FetchChargebacksByTimeFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('500', $response->getCode());
        $this->assertSame('Process failed (Internal)', $response->getMessage());

        $this->assertNull($response->getChargebacks());
    }
}
