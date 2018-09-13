<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class MakePaymentRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount('USD');
        $this->invoiceId = $this->faker->invoiceId();

        $this->request = new MakePaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'paymentMethodId' => $this->paymentMethodId,
                'amount' => $this->amount,
                'invoiceId' => $this->invoiceId
            )
        );

        // amount, currency, merchantTransactionId, attributes
        $this->subscriptionReference = $this->faker->subscriptionReference();
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\MakePaymentRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\MakePaymentRequest')->makePartial();
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
        $this->assertSame('makePayment', $data['action']);
        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->amount, $data['amount']);
        $this->assertSame($this->invoiceId, $data['invoiceId']);
        $this->assertNull($data['currency']);
        $this->assertNull($data['overageDisposition']);
        $this->assertSame('Payment initiated by makePayment', $data['note']);
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
     * @expectedExceptionMessage Either the paymentMethodId or paymentMethodReference parameter is required.
     * @return                   void
     */
    public function testPaymentMethodIdOrReferenceRequired()
    {
        $this->request->setPaymentMethodId(null);
        $this->request->setPaymentMethodReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount parameter is required.
     * @return                   void
     */
    public function testAmountRequired()
    {
        $this->request->setAmount(null);
        $this->request->getData();
    }
}
