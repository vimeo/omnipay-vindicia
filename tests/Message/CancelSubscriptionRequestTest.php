<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class CancelSubscriptionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->cancelReason = $this->faker->subscriptionCancelReason();
        $this->coerce = $this->faker->coerce();

        $this->request = new CancelSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'subscriptionReference' => $this->subscriptionReference,
                'cancelReason' => $this->cancelReason,
                'coerce' => $this->coerce
            )
        );

        $this->planId = $this->faker->planId();
        $this->customerId = $this->faker->customerId();
        $this->productId = $this->faker->productId();
        $this->currency = $this->faker->currency();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->startTime = $this->faker->timestamp();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testCancelReason()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCancelReason($this->cancelReason));
        $this->assertSame($this->cancelReason, $request->getCancelReason());
    }

    /**
     * @return void
     */
    public function testCoerce()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCoercion($this->coerce));
        $this->assertSame($this->coerce, $request->getCoercion());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->subscriptionReference, $data['autobill']->VID);
        $this->assertSame('cancel', $data['action']);
        $this->assertFalse($data['disentitle']);
        $this->assertTrue($data['force']);
        $this->assertFalse($data['settle']);
        $this->assertFalse($data['sendCancellationNotice']);
        $this->assertSame($this->cancelReason, $data['cancelReasonCode']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the subscription id or reference is required.
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
        $this->setMockSoapResponse('CancelSubscriptionSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'CUSTOMER_ID' => $this->customerId,
            'PLAN_ID' => $this->planId,
            'PRODUCT_ID' => $this->productId,
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'STATEMENT_DESCRIPTOR' => $this->statementDescriptor,
            'IP_ADDRESS' => $this->ip,
            'CANCEL_REASON' => $this->cancelReason
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());
        $this->assertSame('Pending Cancel', $response->getSubscriptionStatus());
        $this->assertSame($this->cancelReason, $response->getSubscriptionCancelReason());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CancelSubscriptionFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load AutoBill:  .', $response->getMessage());

        $this->assertNull($response->getSubscriptionId());
        $this->assertNull($response->getSubscriptionReference());
    }
}
