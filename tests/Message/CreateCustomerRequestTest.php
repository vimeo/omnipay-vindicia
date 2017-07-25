<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\TaxExemption;
use Omnipay\Vindicia\TaxExemptions;
use Omnipay\Vindicia\AttributeBag;

class CreateCustomerRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->faker = new DataFaker();

        $this->name = $this->faker->name();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->email = $this->faker->email();
        $this->attributes = $this->faker->attributesAsArray();

        $this->request = new CreateCustomerRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'name' => $this->name,
                'email' => $this->email,
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'attributes' => $this->attributes
            )
        );

        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
    }

    /**
     * @return void
     */
    public function testName()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setName($this->name));
        $this->assertSame($this->name, $request->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEmail($this->email));
        $this->assertSame($this->email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testTaxExemptionsAsBag()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        // $exemptions is a TaxExemptionBag
        $exemptions = $this->faker->taxExemptions();
        $this->assertSame($request, $request->setTaxExemptions($exemptions));
        $this->assertSame($exemptions, $request->getTaxExemptions());
    }

    /**
     * @return void
     */
    public function testTaxExemptionsAsArray()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateCustomerRequest')->makePartial();
        $request->initialize();

        // $exemptions is an array
        $exemptions = $this->faker->taxExemptionsAsArray();
        $this->assertSame($request, $request->setTaxExemptions($exemptions));

        $returnedExemptions = $request->getTaxExemptions();
        $this->assertInstanceOf('Omnipay\Vindicia\TaxExemptionBag', $returnedExemptions);

        $numExemptions = count($exemptions);
        $this->assertSame($numExemptions, $returnedExemptions->count());

        foreach ($returnedExemptions as $i => $returnedExemption) {
            $this->assertEquals(new TaxExemption($exemptions[$i]), $returnedExemption);
        }
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $this->assertSame($this->request, $this->request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $this->request->getAttributes());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->name, $data['account']->name);
        $this->assertSame($this->email, $data['account']->emailAddress);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['account']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['account']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['account']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataWithCard()
    {
        $this->request->setCard($this->card)->setPaymentMethodId($this->paymentMethodId);

        $data = $this->request->getData();

        $this->assertSame($this->name, $data['account']->name);
        $this->assertSame($this->email, $data['account']->emailAddress);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['account']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['account']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['account']->nameValues[$i]->value);
        }

        $this->assertSame($this->paymentMethodId, $data['account']->paymentMethods[0]->merchantPaymentMethodId);
        $this->assertSame($this->card['number'], $data['account']->paymentMethods[0]->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['account']->paymentMethods[0]->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['account']->paymentMethods[0]->creditCard->expirationDate, 4)));

        $this->assertSame('update', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataWithExistingCard()
    {
        $this->request->setCard(null)->setPaymentMethodId($this->paymentMethodId);

        $data = $this->request->getData();

        $this->assertSame($this->name, $data['account']->name);
        $this->assertSame($this->email, $data['account']->emailAddress);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->customerReference, $data['account']->VID);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['account']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['account']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['account']->nameValues[$i]->value);
        }

        $this->assertSame($this->paymentMethodId, $data['account']->paymentMethods[0]->merchantPaymentMethodId);

        $this->assertSame('update', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataWithTaxExemptions()
    {
        $exemptions = $this->faker->taxExemptionsAsArray();
        $this->request->setTaxExemptions($exemptions);

        $data = $this->request->getData();

        $this->assertSame($this->name, $data['account']->name);
        $this->assertSame($this->email, $data['account']->emailAddress);
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['account']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['account']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['account']->nameValues[$i]->value);
        }

        $numExemptions = count($exemptions);
        $this->assertSame($numExemptions, count($data['account']->taxExemptions));
        for ($i = 0; $i < $numExemptions; $i++) {
            $this->assertSame($exemptions[$i]['exemptionId'], $data['account']->taxExemptions[$i]->exemptionId);
            $this->assertSame($exemptions[$i]['region'], $data['account']->taxExemptions[$i]->region);
            $this->assertSame($exemptions[$i]['active'], $data['account']->taxExemptions[$i]->active);
        }

        $this->assertSame('update', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The customerId parameter is required
     * @return                   void
     */
    public function testCustomerIdRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CreateCustomerSuccess.xml', array(
            'EMAIL' => $this->email,
            'NAME' => $this->name,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->customerId, $response->getCustomerId());
        $this->assertSame($this->customerReference, $response->getCustomerReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendWithCardSuccess()
    {
        $this->request->setCard($this->card)->setPaymentMethodId($this->paymentMethodId);

        $this->setMockSoapResponse('CreateCustomerWithPaymentMethodSuccess.xml', array(
            'EMAIL' => $this->email,
            'NAME' => $this->name,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
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
        $this->assertSame($this->customerReference, $response->getCustomerReference());
        $this->assertSame($this->paymentMethodId, $response->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $response->getPaymentMethodReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreateCustomerFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Account object is invalid: No merchantAccountId (unique identifier) specified for Account.', $response->getMessage());

        $this->assertNull($response->getCustomerId());
        $this->assertNull($response->getCustomerReference());
    }
}
