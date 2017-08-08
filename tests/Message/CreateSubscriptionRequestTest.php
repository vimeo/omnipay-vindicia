<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\Price;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;

class CreateSubscriptionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();
        $this->planId = $this->faker->planId();
        $this->customerId = $this->faker->customerId();
        $this->productId = $this->faker->productId();
        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->planReference = $this->faker->planReference();
        $this->customerReference = $this->faker->customerReference();
        $this->productReference = $this->faker->productReference();
        $this->currency = $this->faker->currency();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->startTime = $this->faker->timestamp();
        $this->billingDay = $this->faker->intBetween(1, 31);
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->minChargebackProbability = $this->faker->chargebackProbability();
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->subscriptionStatus = $this->faker->subscriptionStatus();
        $this->subscriptionBillingState = $this->faker->subscriptionBillingState();
        $this->shouldAuthorize = $this->faker->bool();
        $this->attributes = $this->faker->attributesAsArray();

        $this->request = new CreateSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId,
                'planId' => $this->planId,
                'customerId' => $this->customerId,
                'productId' => $this->productId,
                'subscriptionReference' => $this->subscriptionReference,
                'planReference' => $this->planReference,
                'customerReference' => $this->customerReference,
                'productReference' => $this->productReference,
                'currency' => $this->currency,
                'statementDescriptor' => $this->statementDescriptor,
                'ip' => $this->ip,
                'startTime' => $this->startTime,
                'billingDay' => $this->billingDay,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'paymentMethodReference' => $this->paymentMethodReference,
                'minChargebackProbability' => $this->minChargebackProbability,
                'name' => $this->name,
                'email' => $this->email,
                'shouldAuthorize' => $this->shouldAuthorize,
                'attributes' => $this->attributes
            )
        );

        $this->riskScore = $this->faker->riskScore();
    }

    /**
     * @return void
     */
    public function testMinChargebackProbability()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setMinChargebackProbability($this->minChargebackProbability));
        $this->assertSame($this->minChargebackProbability, $request->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductId($this->productId));
        $this->assertSame($this->productId, $request->getProductId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $request->getProductReference());
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    /**
     * @return void
     */
    public function testIp()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setName($this->name));
        $this->assertSame($this->name, $request->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setEmail($this->email));
        $this->assertSame($this->email, $request->getEmail());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStartTime($this->startTime));
        $this->assertSame($this->startTime, $request->getStartTime());
    }

    /**
     * @return void
     */
    public function testShouldAuthorize()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setShouldAuthorize($this->shouldAuthorize));
        $this->assertSame($this->shouldAuthorize, $request->getShouldAuthorize());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->planId, $data['autobill']->billingPlan->merchantBillingPlanId);
        $this->assertSame($this->subscriptionReference, $data['autobill']->VID);
        $this->assertSame($this->planReference, $data['autobill']->billingPlan->VID);
        $this->assertSame(1, count($data['autobill']->items));
        $this->assertSame($this->productId, $data['autobill']->items[0]->product->merchantProductId);
        $this->assertSame($this->productReference, $data['autobill']->items[0]->product->VID);
        $this->assertSame($this->customerId, $data['autobill']->account->merchantAccountId);
        $this->assertSame($this->name, $data['autobill']->account->name);
        $this->assertSame($this->email, $data['autobill']->account->emailAddress);
        $this->assertSame($this->customerReference, $data['autobill']->account->VID);
        $this->assertSame($this->currency, $data['autobill']->currency);
        $this->assertSame($this->ip, $data['autobill']->sourceIp);
        $this->assertSame($this->startTime, $data['autobill']->startTimestamp);
        $this->assertSame($this->billingDay, $data['autobill']->billingDay);
        $this->assertSame('DoNotSend', $data['autobill']->statementFormat);
        $this->assertSame($this->statementDescriptor, $data['autobill']->billingStatementIdentifier);
        $this->assertSame($this->paymentMethodId, $data['autobill']->paymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['autobill']->paymentMethod->VID);
        $this->assertSame($this->card['number'], $data['autobill']->paymentMethod->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['autobill']->paymentMethod->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['autobill']->paymentMethod->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['autobill']->paymentMethod->nameValues));
        $this->assertSame($this->card['postcode'], $data['autobill']->paymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['autobill']->paymentMethod->billingAddress->country);
        $this->assertSame('CreditCard', $data['autobill']->paymentMethod->type);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['autobill']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['autobill']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['autobill']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
        $this->assertSame('putAutoBillInRetryCycleIfPaymentMethodIsValid', $data['immediateAuthFailurePolicy']);
        $this->assertSame($this->shouldAuthorize, $data['validateForFuturePayment']);
        $this->assertSame(false, $data['ignoreAvsPolicy']);
        $this->assertSame(false, $data['ignoreCvnPolicy']);
        $this->assertSame(null, $data['campaignCode']);
        $this->assertSame(false, $data['dryrun']);
        $this->assertSame(null, $data['cancelReasonCode']);
        $this->assertSame($this->minChargebackProbability, $data['minChargebackProbability']);
    }

    /**
     * @return void
     */
    public function testGetDataNoCard()
    {
        $this->request->setCard(null);
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['autobill']->merchantAutoBillId);
        $this->assertSame($this->planId, $data['autobill']->billingPlan->merchantBillingPlanId);
        $this->assertSame($this->subscriptionReference, $data['autobill']->VID);
        $this->assertSame($this->planReference, $data['autobill']->billingPlan->VID);
        $this->assertSame(1, count($data['autobill']->items));
        $this->assertSame($this->productId, $data['autobill']->items[0]->product->merchantProductId);
        $this->assertSame($this->productReference, $data['autobill']->items[0]->product->VID);
        $this->assertSame($this->customerId, $data['autobill']->account->merchantAccountId);
        $this->assertSame($this->name, $data['autobill']->account->name);
        $this->assertSame($this->email, $data['autobill']->account->emailAddress);
        $this->assertSame($this->customerReference, $data['autobill']->account->VID);
        $this->assertSame($this->currency, $data['autobill']->currency);
        $this->assertSame($this->ip, $data['autobill']->sourceIp);
        $this->assertSame($this->startTime, $data['autobill']->startTimestamp);
        $this->assertSame($this->billingDay, $data['autobill']->billingDay);
        $this->assertSame('DoNotSend', $data['autobill']->statementFormat);
        $this->assertSame($this->statementDescriptor, $data['autobill']->billingStatementIdentifier);
        $this->assertSame($this->paymentMethodId, $data['autobill']->paymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['autobill']->paymentMethod->VID);
        $this->assertFalse(isset($data['autobill']->paymentMethod->creditCard));
        $this->assertFalse(isset($data['autobill']->paymentMethod->type));

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['autobill']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['autobill']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['autobill']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
        $this->assertSame('putAutoBillInRetryCycleIfPaymentMethodIsValid', $data['immediateAuthFailurePolicy']);
        $this->assertSame($this->shouldAuthorize, $data['validateForFuturePayment']);
        $this->assertSame(false, $data['ignoreAvsPolicy']);
        $this->assertSame(false, $data['ignoreCvnPolicy']);
        $this->assertSame(null, $data['campaignCode']);
        $this->assertSame(false, $data['dryrun']);
        $this->assertSame(null, $data['cancelReasonCode']);
        $this->assertSame($this->minChargebackProbability, $data['minChargebackProbability']);
    }

    /**
     * @return void
     */
    public function testGetDataDefaultShouldAuthorize()
    {
        $this->request->setShouldAuthorize(null);
        $data = $this->request->getData();
        $this->assertFalse($data['validateForFuturePayment']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the productId or productReference parameter is required.
     * @return                   void
     */
    public function testProductIdOrReferenceRequired()
    {
        $this->request->setProductId(null);
        $this->request->setProductReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The subscriptionId parameter is required
     * @return                   void
     */
    public function testSubscriptionIdRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     * @return                   void
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CreateSubscriptionSuccess.xml', array(
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
            'RISK_SCORE' => $this->riskScore,
            'BILLING_DAY' => $this->billingDay,
            'STATUS' => $this->subscriptionStatus,
            'BILLING_STATE' => $this->subscriptionBillingState
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());
        $this->assertSame($this->subscriptionStatus, $response->getSubscriptionStatus());
        $this->assertSame($this->subscriptionBillingState, $response->getSubscriptionBillingState());
        $this->assertSame($this->riskScore, $response->getRiskScore());
        $this->assertSame($this->billingDay, $response->getBillingDay());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreateSubscriptionFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Invalid Arguments - Item "Vindicia::Soap::AutoBill.startTimestamp" of type xsd:dateTime has invalid value "2016-12-01P12:00:00-04:00"!', $response->getMessage());

        $this->assertNull($response->getSubscriptionId());
        $this->assertNull($response->getSubscriptionReference());
    }
}
