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

        $this->request = new FetchSubscriptionInvoiceRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'invoiceState' => $this->invoiceState,
                'invoiceId' => $this->invoiceId
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
}
