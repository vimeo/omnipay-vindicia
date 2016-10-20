<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class CompleteHOARequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->webSessionReference = $this->faker->webSessionReference();

        $this->request = new CompleteHOARequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'webSessionReference' => $this->webSessionReference
            )
        );

        $this->currency = $this->faker->currency();
        $this->customerId = $this->faker->customerId();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->minChargebackProbability = $this->faker->chargebackProbability();
    }

    public function testWebSessionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CompleteHOARequest')->makePartial();
        $request->initialize();

        $webSessionReference = $this->faker->chargebackProbability();
        $this->assertSame($request, $request->setWebSessionReference($webSessionReference));
        $this->assertSame($webSessionReference, $request->getWebSessionReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->webSessionReference, $data['session']->VID);
        $this->assertSame('finalize', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The webSessionReference parameter is required
     */
    public function testWebSessionReferenceRequired()
    {
        $this->request->setWebSessionReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->markTestSkipped('@todo need to figure out what the success responses look like by doing an actual HOA form.');
    }

    /**
     * Test when the HOA request fails
     */
    public function testSendRequestFailure()
    {
        $this->setMockSoapResponse('CompleteHOARequestFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Must specify a WebSession to finalize!', $response->getMessage());
        $this->assertSame(CompleteHOAResponse::REQUEST_FAILURE, $response->getFailureType());
    }

    /**
     * Test when the method called by HOA fails
     */
    public function testSendMethodFailure()
    {
        $this->setMockSoapResponse('CompleteHOAMethodFailure.xml', array(
            'CURRENCY' => $this->currency,
            'CUSTOMER_ID' => $this->customerId,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'MIN_CHARGEBACK_PROBABILITY' => $this->minChargebackProbability,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('402', $response->getCode());
        $this->assertSame('Missing required parameter: vin_PaymentMethod', $response->getMessage());
        $this->assertSame(CompleteHOAResponse::METHOD_FAILURE, $response->getFailureType());
    }
}
