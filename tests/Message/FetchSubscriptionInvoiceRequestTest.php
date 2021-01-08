<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionInvoiceRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->invoiceReference = $this->faker->invoiceReference();
        $this->amount = $this->faker->monetaryAmount('USD');

        $this->request = new FetchSubscriptionInvoiceRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'invoiceReference' => $this->invoiceReference,
                'amount' => $this->amount
            )
        );

        $this->subscriptionReference = $this->faker->subscriptionReference();
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->invoiceReference, $data['invoiceId']);
        $this->assertSame('fetchInvoice', $data['action']);
        $this->assertFalse($data['asPDF']);
        $this->assertNull($data['statementTemplateId']);
        $this->assertEquals(0, $data['dunningIndex']);
        $this->assertSame('en-US', $data['language']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the subscriptionId or subscriptionReference parameter is required.
     * @return                   void
     */
    public function testSubscriptionIdOrReferenceRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->setSubscriptionReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The invoiceReference parameter is required.
     * @return                   void
     */
    public function testInvoiceReferenceRequired()
    {
        $this->request->setInvoiceReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceSuccess.xml', array(
            'INVOICE_REFERENCE' => $this->invoiceReference,
            'AMOUNT' => $this->amount
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $invoice_lines = explode("\n", $response->getInvoice());
        $amount = null;
        foreach ($invoice_lines as $line) {
            if (stripos($line, 'Total Amount Due:') !== false) {
                preg_match_all('!\d+(?:\.\d+)?!', $line, $matches);
                $floats = array_map('floatval', $matches[0]);
                $amount = $floats[0];
            }
        }
        $this->assertEquals($this->amount, $amount);

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
    }
}
