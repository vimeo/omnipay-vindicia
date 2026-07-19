<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionInvoiceReferencesRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->invoiceState = $this->faker->invoiceState();

        $this->request = new FetchSubscriptionInvoiceReferencesRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'invoiceState' => $this->invoiceState
            )
        );

        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->invoiceReference = $this->faker->invoiceReference();
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceReferencesRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceReferencesRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testInvoiceState()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionInvoiceReferencesRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setInvoiceState($this->invoiceState));
        $this->assertSame($this->invoiceState, $request->getInvoiceState());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame('fetchInvoiceNumbers', $data['action']);
        $this->assertSame($this->invoiceState, $data['invoicestate']);
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
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceReferencesSuccess.xml', array(
            'INVOICE_REFERENCE' => $this->invoiceReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $invoice_references_array = $response->getInvoiceReferences();
        $this->assertNotNull($invoice_references_array);
        $this->assertEquals(2, count($invoice_references_array));
        $this->assertSame($this->invoiceReference, $invoice_references_array[0]);

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionInvoiceReferencesFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load AutoBill: ', $response->getMessage());
    }
}
