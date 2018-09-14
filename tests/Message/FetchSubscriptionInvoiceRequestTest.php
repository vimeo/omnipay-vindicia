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
        $this->invoiceState = $this->faker->invoiceState();
        $this->invoiceId = $this->faker->invoiceId();
        $this->amount = $this->faker->monetaryAmount('USD');

        $this->request = new FetchSubscriptionInvoiceRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'invoiceState' => $this->invoiceState,
                'invoiceId' => $this->invoiceId,
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
    public function testInvoiceState()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setInvoiceState($this->invoiceState));
        $this->assertSame($this->invoiceState, $request->getInvoiceState());
    }

    /**
     * @return void
     */
    public function testGetDataInvoiceNumbers()
    {
        $this->request->setInvoiceId(null);

        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame('fetchInvoiceNumbers', $data['action']);
        $this->assertSame($this->invoiceState, $data['invoicestate']);
    }

    /**
     * @return void
     */
    public function testGetDataInvoice()
    {
        $this->request->setInvoiceId($this->invoiceId);

        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->invoiceId, $data['invoiceId']);
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
     * @return void
     */
    public function testFetchSubscriptionInvoiceNumbersSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceNumbersSuccess.xml', array(
            'INVOICE_ID' => $this->invoiceId
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $invoice_numbers_array = $response->getInvoiceNumbers();
        $this->assertNotNull($invoice_numbers_array);
        $this->assertEquals(2, count($invoice_numbers_array));
        $this->assertSame($this->invoiceId, $invoice_numbers_array[0]);

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testFetchSubscriptionInvoiceSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceSuccess.xml', array(
            'INVOICE_ID' => $this->invoiceId,
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

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testFetchSubscriptionInvoiceNumbersSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceNumbersFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load AutoBill: ', $response->getMessage());
    }

    /**
     * @return void
     */
    public function testFetchSubscriptionInvoiceSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
    }
}
