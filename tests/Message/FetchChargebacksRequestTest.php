<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\FetchChargebacksRequest;
use Omnipay\VindiciaTest\SoapTestCase;

class FetchChargebacksRequestTest extends SoapTestCase
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

        $this->request = new FetchChargebacksRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId
            )
        );
    }

    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchChargebacksRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['merchantTransactionId']);
        $this->assertSame('fetchByMerchantTransactionId', $data['action']);
    }

    public function testGetDataByReference()
    {
        $this->request->setTransactionId(null)->setTransactionReference($this->transactionReference);

        $data = $this->request->getData();

        $this->assertSame($this->transactionReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

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
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The transactionId parameter or the transactionReference parameter or the startTime and endTime parameters are required.
     */
    public function testTransactionIdOrReferenceOrTimesRequired()
    {
        $this->request->setTransactionId(null);
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
        $this->setMockSoapResponse('FetchChargebacksSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertTrue(is_array($response->getChargebacks()));
        $this->assertSame(2, count($response->getChargebacks()));
        $this->assertTrue(isset($response->getChargebacks()[0]->merchantTransactionId));
        $this->assertTrue(isset($response->getChargebacks()[1]->merchantTransactionId));

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Chargeback.wsdl', $this->getLastEndpoint());
    }

    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchChargebacksByTimeSuccess.xml');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertTrue(is_array($response->getChargebacks()));
        $this->assertSame(2, count($response->getChargebacks()));
        $this->assertTrue(isset($response->getChargebacks()[0]->merchantTransactionId));
        $this->assertTrue(isset($response->getChargebacks()[1]->merchantTransactionId));

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Chargeback.wsdl', $this->getLastEndpoint());
    }

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
