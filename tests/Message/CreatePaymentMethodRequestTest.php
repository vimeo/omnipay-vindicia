<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\AttributeBag;

class CreatePaymentMethodRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
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
        $this->attributes = $this->faker->attributesAsArray();

        $this->request = new CreatePaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'card' => $this->card,
                'customerId' => $this->customerId,
                'paymentMethodId' => $this->paymentMethodId,
                'customerReference' => $this->customerReference,
                'paymentMethodReference' => $this->paymentMethodReference,
                'attributes' => $this->attributes
            )
        );
    }

    /**
     * @return void
     */
    public function testValidate()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $value = $this->faker->bool();
        $this->assertSame($request, $request->setValidate($value));
        $this->assertSame($value, $request->getValidate());
    }

    /**
     * @return void
     */
    public function testSkipAvsValidation()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $value = $this->faker->bool();
        $this->assertSame($request, $request->setSkipAvsValidation($value));
        $this->assertSame($value, $request->getSkipAvsValidation());
    }

    /**
     * @return void
     */
    public function testSkipCvvValidation()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $value = $this->faker->bool();
        $this->assertSame($request, $request->setSkipCvvValidation($value));
        $this->assertSame($value, $request->getSkipCvvValidation());
    }

    /**
     * @return void
     */
    public function testUpdateSubscriptions()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $value = $this->faker->bool();
        $this->assertSame($request, $request->setUpdateSubscriptions($value));
        $this->assertSame($value, $request->getUpdateSubscriptions());
    }

    /**
     * @return void
     */
    public function testActive()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $value = $this->faker->bool();
        $this->assertSame($request, $request->setActive($value));
        $this->assertSame($value, $request->getActive());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePaymentMethodRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new CreditCard($this->card), $request->getCard());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['paymentMethod']->nameValues));
        $this->assertSame($this->card['postcode'], $data['paymentMethod']->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['paymentMethod']->billingAddress->country);
        $this->assertSame('CreditCard', $data['paymentMethod']->type);
        $this->assertTrue($data['paymentMethod']->active);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes + 1, count($data['paymentMethod']->nameValues)); // +1 accounts for CVV
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['paymentMethod']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['paymentMethod']->nameValues[$i]->value);
        }

        $this->assertSame(CreatePaymentMethodRequest::SKIP_CARD_VALIDATION, $data['updateBehavior']);
        $this->assertFalse($data['ignoreAvsPolicy']);
        $this->assertFalse($data['ignoreCvnPolicy']);
        $this->assertTrue($data['replaceOnAllAutoBills']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataWithValidation()
    {
        $skipAvsValidation = $this->faker->bool();
        $skipCvvValidation = $this->faker->bool();
        $data = $this->request->setValidate(true)
                              ->setSkipAvsValidation($skipAvsValidation)
                              ->setSkipCvvValidation($skipCvvValidation)
                              ->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertSame(CreatePaymentMethodRequest::VALIDATE_CARD, $data['updateBehavior']);
        $this->assertSame($skipAvsValidation, $data['ignoreAvsPolicy']);
        $this->assertSame($skipCvvValidation, $data['ignoreCvnPolicy']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataDoNotUpdateSubscriptions()
    {
        $data = $this->request->setUpdateSubscriptions(false)->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);
        $this->assertFalse($data['replaceOnAllAutoBills']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataPaymentMethodNotActive()
    {
        $data = $this->request->setActive(false)->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertFalse($data['paymentMethod']->active);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataNoCustomer()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);
        $data = $this->request->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['paymentMethod']->nameValues));
        $this->assertSame($this->card['postcode'], $data['paymentMethod']->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['paymentMethod']->billingAddress->country);
        $this->assertSame('CreditCard', $data['paymentMethod']->type);
        $this->assertFalse(isset($data['account']));

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes + 1, count($data['paymentMethod']->nameValues)); // +1 accounts for CVV
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['paymentMethod']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['paymentMethod']->nameValues[$i]->value);
        }

        $this->assertFalse($data['validate']);
        $this->assertFalse($data['ignoreAvsPolicy']);
        $this->assertFalse($data['ignoreCvnPolicy']);
        $this->assertTrue($data['replaceOnAllAutoBills']);
        $this->assertTrue($data['replaceOnAllChildAutoBills']);
        $this->assertSame('update', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataNoCustomerWithValidation()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);
        $skipAvsValidation = $this->faker->bool();
        $skipCvvValidation = $this->faker->bool();
        $data = $this->request->setValidate(true)
                              ->setSkipAvsValidation($skipAvsValidation)
                              ->setSkipCvvValidation($skipCvvValidation)
                              ->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertFalse(isset($data['account']));
        $this->assertTrue($data['validate']);
        $this->assertSame($skipAvsValidation, $data['ignoreAvsPolicy']);
        $this->assertSame($skipCvvValidation, $data['ignoreCvnPolicy']);
        $this->assertSame('update', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataNoCustomerDoNotUpdateSubscriptions()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);
        $data = $this->request->setUpdateSubscriptions(false)->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->card['number'], $data['paymentMethod']->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['paymentMethod']->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['paymentMethod']->creditCard->expirationDate, 4)));
        $this->assertFalse(isset($data['account']));
        $this->assertFalse($data['replaceOnAllAutoBills']);
        $this->assertFalse($data['replaceOnAllChildAutoBills']);
        $this->assertSame('update', $data['action']);
    }

    /**
     * Test getData for an update. The card parameter is not required for updates.
     *
     * @return void
     */
    public function testGetDataUpdateNoCard()
    {
        $request = new CreatePaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest(), true);
        $request->initialize(
            array(
                'customerId' => $this->customerId,
                'paymentMethodId' => $this->paymentMethodId,
                'customerReference' => $this->customerReference,
                'paymentMethodReference' => $this->paymentMethodReference,
                'attributes' => $this->attributes
            )
        );

        $data = $request->getData();

        $this->assertSame($this->paymentMethodId, $data['paymentMethod']->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['paymentMethod']->VID);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['paymentMethod']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['paymentMethod']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['paymentMethod']->nameValues[$i]->value);
        }

        $this->assertSame(CreatePaymentMethodRequest::SKIP_CARD_VALIDATION, $data['updateBehavior']);
        $this->assertFalse($data['ignoreAvsPolicy']);
        $this->assertFalse($data['ignoreCvnPolicy']);
        $this->assertTrue($data['replaceOnAllAutoBills']);
        $this->assertSame('updatePaymentMethod', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The paymentMethodId parameter is required
     * @return                   void
     */
    public function testPaymentMethodIdRequired()
    {
        $this->request->setPaymentMethodId(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The card parameter is required
     * @return                   void
     */
    public function testCardRequired()
    {
        $this->request->setCard(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidCreditCardException
     * @return            void
     */
    public function testCardValidation()
    {
        $this->request->setCard($this->faker->invalidCard());
        $this->request->getData();
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testSendNoCustomerSuccess()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);

        $this->setMockSoapResponse('CreatePaymentMethodNoCustomerSuccess.xml', array(
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
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/PaymentMethod.wsdl', $this->getLastEndpoint());
    }

    /**
     * Check that a different success code still works
     *
     * @return void
     */
    public function testSendNoCustomer228Success()
    {
        $this->request->setCustomerId(null)->setCustomerReference(null);

        $this->setMockSoapResponse('CreatePaymentMethodNoCustomer228Success.xml', array(
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
        $this->assertSame('Payment method saved but missing associated account - unable to replace on autobills ', $response->getMessage());
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/PaymentMethod.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
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
        $this->assertFalse($response->isCvvValidationFailure());
    }

    /**
     * @return void
     */
    public function testSendCvvFailure()
    {
        $this->setMockSoapResponse('CreatePaymentMethodCvvFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('408', $response->getCode());
        $this->assertSame('Failed CVN policy evaluation', $response->getMessage());

        $this->assertNull($response->getCustomerId());
        $this->assertTrue($response->isCvvValidationFailure());
    }
}
