<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchTransactionsRequestTest extends SoapTestCase
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

        $this->request = new FetchTransactionsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference
            )
        );
        $this->timestamp = date('Y-m-d\T12:00:00-04:00', time());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEndTime($this->endTime));
        $this->assertSame($this->endTime, $request->getEndTime());
    }

    /**
     * @return void
     */
    public function testPageSize()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPageSize($this->pageSize));
        $this->assertSame($this->pageSize, $request->getPageSize());
    }

    /**
     * @return void
     */
    public function testPage()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionsRequest')->makePartial();
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
        $this->assertSame('fetchByAccount', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByTime()
    {
        $this->request->setStartTime($this->startTime);
        $this->request->setEndTime($this->endTime);
        $this->request->setPage($this->page);
        $this->request->setPageSize($this->pageSize);
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
        $this->setMockSoapResponse('FetchTransactionsSuccess.xml', array(
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $transactions = $response->getTransactions();
        $this->assertTrue(is_array($transactions));
        $this->assertSame(2, count($transactions));
        $this->assertInstanceOf('\Omnipay\Vindicia\Transaction', $transactions[0]);
        $this->assertInstanceOf('\Omnipay\Vindicia\Transaction', $transactions[1]);
        $this->assertNotNull($transactions[0]->getId());
        $this->assertNotNull($transactions[1]->getId());
        $this->assertEquals($this->timestamp, $transactions[0]->getTimestamp());
        $this->assertEquals($this->timestamp, $transactions[1]->getTimestamp());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByTimeSuccess()
    {
        $this->setMockSoapResponse('FetchTransactionsByTimeSuccess.xml', array(
            'TIMESTAMP' => $this->timestamp
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $transactions = $response->getTransactions();
        $this->assertTrue(is_array($transactions));
        $this->assertSame(2, count($transactions));
        $this->assertNotNull($transactions[0]->getId());
        $this->assertNotNull($transactions[1]->getId());
        $this->assertEquals($this->timestamp, $transactions[0]->getTimestamp());
        $this->assertEquals($this->timestamp, $transactions[1]->getTimestamp());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchTransactionsFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load account to search by:  No matches.', $response->getMessage());

        $this->assertNull($response->getTransactions());
    }

    /**
     * @return void
     */
    public function testSendByTimeFailure()
    {
        $this->setMockSoapResponse('FetchTransactionsByTimeFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('500', $response->getCode());
        $this->assertSame('Process failed (Internal)', $response->getMessage());

        $this->assertNull($response->getTransactions());
    }
}
