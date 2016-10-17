<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\VoidRequest;
use Omnipay\VindiciaTest\SoapTestCase;

class VoidRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();

        $this->request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId,
                'transactionReference' => $this->transactionReference
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['transactions'][0]->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['transactions'][0]->VID);
        $this->assertSame('cancel', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the transactionId or transactionReference parameter is required.
     */
    public function testTransactionIdOrReferenceRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('VoidSuccess.xml', array(
            'TRANSACTION_ID' => $this->transactionId
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('Ok', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('VoidFailure.xml', array(
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
