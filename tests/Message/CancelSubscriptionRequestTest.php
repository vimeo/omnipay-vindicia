<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\CancelSubscriptionRequest;
use Omnipay\VindiciaTest\SoapTestCase;

class CancelSubscriptionRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->subscriptionReference = $this->faker->subscriptionReference();

        $this->request = new CancelSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'subscriptionReference' => $this->subscriptionReference
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

    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

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
        $this->assertNull($data['cancelReasonCode']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the subscription id or reference is required.
     */
    public function testSubscriptionIdOrReferenceRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->setSubscriptionReference(null);
        $this->request->getData();
    }

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
            'IP_ADDRESS' => $this->ip
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());
        $this->assertSame('Pending Cancel', $response->getSubscriptionStatus());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

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
