<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\VindiciaCreditCard;
use Omnipay\Vindicia\AttributeBag;

class CreatePaymentMethodRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->email = $this->faker->email();
        $this->name = $this->faker->name();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->attributes = $this->faker->attributesAsArray();
        $this->card['attributes'] = $this->attributes;

        $this->request = new CreatePaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'card' => $this->card,
                'customerId' => $this->customerId,
                'paymentMethodId' => $this->paymentMethodId,
                'customerReference' => $this->customerReference,
                'paymentMethodReference' => $this->paymentMethodReference
            )
        );
    }

    public function testValidate()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setValidate(true));
        $this->assertTrue($request->getValidate());
        $this->assertSame($request, $request->setValidate(false));
        $this->assertFalse($request->getValidate());
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new VindiciaCreditCard($this->card), $request->getCard());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['paymentMethod']->nameValues));
        $this->assertSame($this->card['postcode'], $data['paymentMethod']->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['paymentMethod']->billingAddress->country);
        $this->assertSame('CreditCard', $data['paymentMethod']->type);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes + 1, count($data['paymentMethod']->nameValues)); // +1 accounts for CVV
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['paymentMethod']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['paymentMethod']->nameValues[$i]->value);
        }

        $this->assertSame(CreatePaymentMethodRequest::SKIP_CARD_VALIDATION, $data['updateBehavior']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    public function testGetDataWithValidation()
    {
        $data = $this->request->setValidate(true)->getData();

        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame(CreatePaymentMethodRequest::VALIDATE_CARD, $data['updateBehavior']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The paymentMethodId parameter is required
     */
    public function testPaymentMethodIdRequired()
    {
        $this->request->setPaymentMethodId(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The card parameter is required
     */
    public function testCardRequired()
    {
        $this->request->setCard(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidCreditCardException
     */
    public function testCardValidation()
    {
        $this->request->setCard($this->faker->invalidCard());
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CreatePaymentMethodSuccess.xml', array(
            'EMAIL' => $this->email,
            'NAME' => $this->name,
            'CUSTOMER_ID' => $this->customerId,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->customerId, $response->getCustomerId());
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreatePaymentMethodFailure.xml', array(
            'CUSTOMER_ID' => $this->customerId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('No match found for merchantAccountId "' . $this->customerId . '"', $response->getMessage());

        $this->assertNull($response->getCustomerId());
    }
}
