<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchPaymentMethodRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->paymentMethodId = $this->faker->paymentMethodId();

        $this->request = new FetchPaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'paymentMethodId' => $this->paymentMethodId
            )
        );

        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->card = $this->faker->card();
    }

    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchPaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethodId']);
        $this->assertSame('fetchByMerchantPaymentMethodId', $data['action']);
    }

    public function testGetDataByReference()
    {
        $this->request->setPaymentMethodId(null)->setPaymentMethodReference($this->paymentMethodReference);

        $data = $this->request->getData();

        $this->assertSame($this->paymentMethodReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the paymentMethodId or paymentMethodReference parameter is required.
     */
    public function testPaymentMethodIdOrReferenceRequired()
    {
        $this->request->setPaymentMethodId(null);
        $this->request->setPaymentMethodReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchPaymentMethodSuccess.xml', array(
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'CARD_NUMBER' => $this->card['number'],
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear']
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $paymentMethod = $response->getPaymentMethod();
        $this->assertInstanceOf('\Omnipay\Vindicia\PaymentMethod', $paymentMethod);
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());
        $this->assertSame($this->paymentMethodId, $paymentMethod->getId());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getReference());
        $card = $paymentMethod->getCard();
        $this->assertInstanceOf('\Omnipay\Common\CreditCard', $card);
        $this->assertSame($this->card['expiryMonth'], strval($card->getExpiryMonth()));
        $this->assertSame($this->card['expiryYear'], strval($card->getExpiryYear()));
        $this->assertSame($this->card['number'], $card->getNumber());
        $attributes = $paymentMethod->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
        }

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/PaymentMethod.wsdl', $this->getLastEndpoint());
    }

    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchPaymentMethodByReferenceSuccess.xml', array(
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getPaymentMethod());
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/PaymentMethod.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchPaymentMethodFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to find requested PaymentMethod:  No match.', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }

    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchPaymentMethodByReferenceFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to find requested PaymentMethod:  No matches.', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }
}
