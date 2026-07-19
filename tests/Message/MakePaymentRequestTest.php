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
    public function setUp(): void
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->amount = $this->faker->monetaryAmount('USD');
        $this->invoiceReference = $this->faker->invoiceReference();
        $this->note = $this->faker->note();

        $this->request = new MakePaymentRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'paymentMethodId' => $this->paymentMethodId,
                'amount' => $this->amount,
                'invoiceReference' => $this->invoiceReference,
                'note' => $this->note
            )
        );

        $this->currency = $this->faker->currency();
        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->transactionId = $this->faker->transactionId();
        $this->timestamp = date('Y-m-d\T12:00:00-04:00');
        $this->summary = $this->faker->summary();
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
    public function testNote()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\MakePaymentRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setNote($this->note));
        $this->assertSame($this->note, $request->getNote());
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
        $this->assertSame($this->invoiceReference, $data['invoiceId']);
        $this->assertNull($data['currency']);
        $this->assertNull($data['overageDisposition']);
        $this->assertTrue($data['usePaymentMethodForFutureBilling']);
        $this->assertSame($this->note, $data['note']);
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

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('MakePaymentSuccess.xml', array(
            'TRANSACTION_ID' => $this->transactionId,
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'TIMESTAMP' => $this->timestamp,
            'SUMMARY' => $this->summary
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $transaction = $response->getTransaction();
        $this->assertInstanceOf('\Omnipay\Vindicia\Transaction', $transaction);
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->currency, $transaction->getCurrency());
        $this->assertSame($this->amount, $transaction->getAmount());

        $attributes = $transaction->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
            $this->assertTrue(is_string($attribute->getName()));
            $this->assertTrue(is_string($attribute->getValue()));
        }
        $this->assertEquals($this->timestamp, $transaction->getTimestamp());

        $this->assertSame($this->summary, $response->getSummary());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

        /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('MakePaymentFailure.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());

        $message = 'Failed to make payment: No Transaction Billing objects which can accept ' .
                   'payment were found for AutoBill Product Serial ' . $this->subscriptionId;
        $this->assertSame($message, $response->getMessage());
    }
}
