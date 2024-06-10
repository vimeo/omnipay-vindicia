<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class CaptureRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();
        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();

        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId,
                'transactionReference' => $this->transactionReference
            )
        );
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['transactions'][0]->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['transactions'][0]->VID);
        $this->assertSame('capture', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the transactionId or transactionReference parameter is required.
     * @return                   void
     */
    public function testTransactionIdOrReferenceRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CaptureSuccess.xml', array(
            'TRANSACTION_ID' => $this->transactionId
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CaptureFailure.xml', array(
            'TRANSACTION_ID' => $this->transactionId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        // Not a good error message, but it's the one Vindicia returns. See CaptureResponse::getMessage
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());
    }
}
