<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class CompleteCreatePayPalSubscriptionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();

        $this->success = $this->faker->bool();
        $this->payPalTransactionReference = $this->faker->payPalTransactionReference();

        $this->request = new CompleteCreatePayPalSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'success' => $this->success,
                'payPalTransactionReference' => $this->payPalTransactionReference
            )
        );

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->returnUrl = $this->faker->url();
        $this->cancelUrl = $this->faker->url();
        $this->subscriptionId = $this->faker->subscriptionId();
        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->productId = $this->faker->productId();
        $this->productReference = $this->faker->productReference();
        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
        $this->payPalToken = $this->faker->payPalToken();
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CompleteCreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSuccess($this->success));
        $this->assertSame($this->success, $request->getSuccess());
    }

    /**
     * @return void
     */
    public function testPayPalTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CompleteCreatePayPalSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSuccess($this->success));
        $this->assertSame($this->success, $request->getSuccess());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame('finalizePayPalAuth', $data['action']);
        $this->assertSame($this->payPalTransactionReference, $data['payPalTransactionId']);
        $this->assertSame($this->success, $data['success']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The success parameter is required
     * @return                   void
     */
    public function testSuccessRequired()
    {
        $this->request->setSuccess(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The payPalTransactionReference parameter is required
     * @return                   void
     */
    public function testPayPalTransactionReferenceRequired()
    {
        $this->request->setPayPalTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CompleteCreatePayPalSubscriptionSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference,
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference,
            'PRODUCT_ID' => $this->productId,
            'PRODUCT_REFERENCE' => $this->productReference,
            'RETURN_URL' => $this->returnUrl,
            'CANCEL_URL' => $this->cancelUrl,
            'PAYPAL_TOKEN' => $this->payPalToken
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CompleteCreatePayPalSubscriptionFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('PayPal tx needs to be in AuthPending dispsosition Disposition AuthorizationPending 1234567890(3)', $response->getMessage());

        $this->assertNull($response->getSubscriptionId());
        $this->assertNull($response->getSubscriptionReference());
    }
}
