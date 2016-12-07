<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\Attribute;

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
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->minChargebackProbability = $this->faker->chargebackProbability();
        $this->riskScore = $this->faker->riskScore();
        $this->card = $this->faker->card();
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

    public function testSendAuthorizeSuccess()
    {
        $this->setMockSoapResponse('CompleteHOAAuthorizeSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'POSTCODE' => $this->card['postcode'],
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'MIN_CHARGEBACK_PROBABILITY' => $this->minChargebackProbability,
            'RISK_SCORE' => $this->riskScore,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference
        ));

        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNull($response->getFailureType());
        $this->assertSame($this->webSessionReference, $response->getWebSessionReference());
        $transaction = $response->getTransaction();
        $this->assertInstanceOf('\Omnipay\Vindicia\Transaction', $transaction);
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionId, $transaction->getId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());
        $this->assertSame($this->transactionReference, $transaction->getReference());
        $this->assertTrue($response->wasAuthorize());
        $this->assertFalse($response->wasPurchase());
        $this->assertFalse($response->wasCreatePaymentMethod());
        $this->assertFalse($response->wasCreateSubscription());
        $this->assertSame($this->riskScore, $response->getRiskScore());
        $formValues = $response->getFormValues();
        $this->assertTrue(is_array($formValues));
        $this->assertGreaterThan(0, count($formValues));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_WebSession_VID', 'value' => $this->webSessionReference)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_merchantPaymentMethodId', 'value' => $this->paymentMethodId)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_billingAddress_postalCode', 'value' => $this->card['postcode'])),
            $formValues
        ));
    }

    public function testSendPurchaseSuccess()
    {
        $this->markTestSkipped('@todo');
    }

    public function testSendCreatePaymentMethodSuccess()
    {
        $this->setMockSoapResponse('CompleteHOACreatePaymentMethodSuccess.xml', array(
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'POSTCODE' => $this->card['postcode'],
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference
        ));

        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNull($response->getFailureType());
        $this->assertSame($this->webSessionReference, $response->getWebSessionReference());
        $paymentMethod = $response->getPaymentMethod();
        $this->assertInstanceOf('\Omnipay\Vindicia\PaymentMethod', $paymentMethod);
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodId, $paymentMethod->getId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getReference());
        $this->assertFalse($response->wasAuthorize());
        $this->assertFalse($response->wasPurchase());
        $this->assertTrue($response->wasCreatePaymentMethod());
        $this->assertFalse($response->wasCreateSubscription());
        $formValues = $response->getFormValues();
        $this->assertTrue(is_array($formValues));
        $this->assertGreaterThan(0, count($formValues));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_WebSession_VID', 'value' => $this->webSessionReference)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_merchantPaymentMethodId', 'value' => $this->paymentMethodId)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_billingAddress_postalCode', 'value' => $this->card['postcode'])),
            $formValues
        ));
    }

    public function testSendCreatePaymentMethodNoCustomerSuccess()
    {
        $this->setMockSoapResponse('CompleteHOACreatePaymentMethodNoCustomerSuccess.xml', array(
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'POSTCODE' => $this->card['postcode'],
            'WEB_SESSION_REFERENCE' => $this->webSessionReference,
            'RETURN_URL' => $this->returnUrl,
            'ERROR_URL' => $this->errorUrl,
            'IP_ADDRESS' => $this->ip,
            'WEB_SESSION_REFERENCE' => $this->webSessionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('Payment method saved but missing associated account - unable to replace on autobills ', $response->getMessage());
        $this->assertNull($response->getFailureType());
        $this->assertSame($this->webSessionReference, $response->getWebSessionReference());
        $paymentMethod = $response->getPaymentMethod();
        $this->assertInstanceOf('\Omnipay\Vindicia\PaymentMethod', $paymentMethod);
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodId, $paymentMethod->getId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getReference());
        $this->assertFalse($response->wasAuthorize());
        $this->assertFalse($response->wasPurchase());
        $this->assertTrue($response->wasCreatePaymentMethod());
        $this->assertFalse($response->wasCreateSubscription());
        $formValues = $response->getFormValues();
        $this->assertTrue(is_array($formValues));
        $this->assertGreaterThan(0, count($formValues));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_WebSession_VID', 'value' => $this->webSessionReference)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_merchantPaymentMethodId', 'value' => $this->paymentMethodId)),
            $formValues
        ));
        $this->assertTrue(in_array(
            new Attribute(array('name' => 'vin_PaymentMethod_billingAddress_postalCode', 'value' => $this->card['postcode'])),
            $formValues
        ));
    }

    public function testSendCreateSubscriptionSuccess()
    {
        $this->markTestSkipped('@todo');
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
        $this->assertTrue($response->isRequestFailure());
    }

    /**
     * Test when the method called by HOA fails
     */
    public function testSendMethodFailure()
    {
        $this->setMockSoapResponse('CompleteHOAAuthorizeMethodFailure.xml', array(
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
        $this->assertTrue($response->isMethodFailure());
    }

    /**
     * Test when the method called by HOA fails and the request fails
     * This doesn't really mean anything different as far as I can tell, just another way
     * Vindicia returns method failures.
     */
    public function testSendRequestAndMethodFailure()
    {
        $this->setMockSoapResponse('CompleteHOARequestAndMethodFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Invalid payment method type:  ', $response->getMessage());
        $this->assertSame(CompleteHOAResponse::METHOD_FAILURE, $response->getFailureType());
        $this->assertTrue($response->isMethodFailure());
    }

    /**
     * Test when completing a create payment method request and there's a CVV validation
     * failure.
     */
    public function testSendCvvFailure()
    {
        $this->setMockSoapResponse('CompleteHOACvvFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('408', $response->getCode());
        $this->assertSame('Failed CVN policy evaluation', $response->getMessage());
        $this->assertSame(CompleteHOAResponse::METHOD_FAILURE, $response->getFailureType());
        $this->assertTrue($response->isMethodFailure());
        $this->assertTrue($response->isCvvValidationFailure());
    }
}
