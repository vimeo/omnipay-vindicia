<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\VindiciaItem;
use Omnipay\Vindicia\Attribute;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\TestableSoapClient;
use stdClass;

class AbstractRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->request = Mocker::mock('\Omnipay\Vindicia\Message\AbstractRequest')->makePartial()->shouldAllowMockingProtectedMethods();
        $this->request->initialize();
        $this->faker = new DataFaker();

        $this->username = $this->faker->username();
        $this->password = $this->faker->password();
    }

    public function testUsername()
    {
        $this->assertSame($this->request, $this->request->setUsername($this->username));
        $this->assertSame($this->username, $this->request->getUsername());
    }

    public function testPassword()
    {
        $this->assertSame($this->request, $this->request->setPassword($this->password));
        $this->assertSame($this->password, $this->request->getPassword());
    }

    public function testCustomerId()
    {
        $customerId = $this->faker->customerId();
        $this->assertSame($this->request, $this->request->setCustomerId($customerId));
        $this->assertSame($customerId, $this->request->getCustomerId());
    }

    public function testCustomerReference()
    {
        $customerReference = $this->faker->customerReference();
        $this->assertSame($this->request, $this->request->setCustomerReference($customerReference));
        $this->assertSame($customerReference, $this->request->getCustomerReference());
    }

    public function testName()
    {
        $name = $this->faker->name();
        $this->assertSame($this->request, $this->request->setName($name));
        $this->assertSame($name, $this->request->getName());
    }

    public function testEmail()
    {
        $email = $this->faker->email();
        $this->assertSame($this->request, $this->request->setEmail($email));
        $this->assertSame($email, $this->request->getEmail());
    }

    public function testRefundId()
    {
        $refundId = $this->faker->refundId();
        $this->assertSame($this->request, $this->request->setRefundId($refundId));
        $this->assertSame($refundId, $this->request->getRefundId());
    }

    public function testPaymentMethodId()
    {
        $paymentMethodId = $this->faker->paymentMethodId();
        $this->assertSame($this->request, $this->request->setPaymentMethodId($paymentMethodId));
        $this->assertSame($paymentMethodId, $this->request->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $paymentMethodReference = $this->faker->paymentMethodReference();
        $this->assertSame($this->request, $this->request->setPaymentMethodReference($paymentMethodReference));
        $this->assertSame($paymentMethodReference, $this->request->getPaymentMethodReference());
    }

    public function testTransactionId()
    {
        $transactionId = $this->faker->transactionId();
        $this->assertSame($this->request, $this->request->setTransactionId($transactionId));
        $this->assertSame($transactionId, $this->request->getTransactionId());
    }

    public function testTransactionReference()
    {
        $transactionReference = $this->faker->transactionReference();
        $this->assertSame($this->request, $this->request->setTransactionReference($transactionReference));
        $this->assertSame($transactionReference, $this->request->getTransactionReference());
    }

    public function testProductId()
    {
        $productId = $this->faker->productId();
        $this->assertSame($this->request, $this->request->setProductId($productId));
        $this->assertSame($productId, $this->request->getProductId());
    }

    public function testProductReference()
    {
        $productReference = $this->faker->productReference();
        $this->assertSame($this->request, $this->request->setProductReference($productReference));
        $this->assertSame($productReference, $this->request->getProductReference());
    }

    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->request, $this->request->setPlanId($planId));
        $this->assertSame($planId, $this->request->getPlanId());
    }

    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->request, $this->request->setPlanReference($planReference));
        $this->assertSame($planReference, $this->request->getPlanReference());
    }

    public function testSubscriptionId()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $this->assertSame($this->request, $this->request->setSubscriptionId($subscriptionId));
        $this->assertSame($subscriptionId, $this->request->getSubscriptionId());
    }

    public function testSubscriptionReference()
    {
        $subscriptionReference = $this->faker->subscriptionReference();
        $this->assertSame($this->request, $this->request->setSubscriptionReference($subscriptionReference));
        $this->assertSame($subscriptionReference, $this->request->getSubscriptionReference());
    }

    public function testTimeout()
    {
        $timeout = $this->faker->timeout();
        $this->assertSame($this->request, $this->request->setTimeout($timeout));
        $this->assertSame($timeout, $this->request->getTimeout());
    }

    public function testIp()
    {
        $ip = $this->faker->ipAddress();
        $this->assertSame($this->request, $this->request->setIp($ip));
        $this->assertSame($ip, $this->request->getIp());
    }

    public function testStartTime()
    {
        $startTime = $this->faker->timestamp();
        $this->assertSame($this->request, $this->request->setStartTime($startTime));
        $this->assertSame($startTime, $this->request->getStartTime());
    }

    public function testEndTime()
    {
        $endTime = $this->faker->timestamp();
        $this->assertSame($this->request, $this->request->setEndTime($endTime));
        $this->assertSame($endTime, $this->request->getEndTime());
    }

    public function testItemsAsBag()
    {
        // $items is a VindiciaItemBag
        $items = $this->faker->items($this->faker->currency());
        $this->assertSame($this->request, $this->request->setItems($items));
        $this->assertSame($items, $this->request->getItems());
    }

    public function testItemsAsArray()
    {
        // $items is an array
        $items = $this->faker->itemsAsArray($this->faker->currency());
        $this->assertSame($this->request, $this->request->setItems($items));

        $returnedItems = $this->request->getItems();
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaItemBag', $returnedItems);

        $numItems = count($items);
        $this->assertSame($numItems, $returnedItems->count());

        foreach ($returnedItems as $i => $returnedItem) {
            $this->assertEquals(new VindiciaItem($items[$i]), $returnedItem);
        }
    }

    public function testAttributesAsBag()
    {
        // $attributes is an AttributeBag
        $attributes = $this->faker->attributes();
        $this->assertSame($this->request, $this->request->setAttributes($attributes));
        $this->assertSame($attributes, $this->request->getAttributes());
    }

    public function testAttributesAsArray()
    {
        // $attributes is an array
        $attributes = $this->faker->attributesAsArray();
        $this->assertSame($this->request, $this->request->setAttributes($attributes));

        $returnedAttributes = $this->request->getAttributes();
        $this->assertInstanceOf('Omnipay\Vindicia\AttributeBag', $returnedAttributes);

        $numAttributes = count($attributes);
        $this->assertSame($numAttributes, $returnedAttributes->count());

        foreach ($returnedAttributes as $i => $returnedAttribute) {
            $this->assertEquals(new Attribute($attributes[$i]), $returnedAttribute);
        }
    }

    public function testCard()
    {
        $card = new CreditCard();
        $this->assertSame($this->request, $this->request->setCard($card));
        $this->assertSame($card, $this->request->getCard());
    }

    public function testSetCardWithArray()
    {
        $card = $this->faker->card();
        $this->assertSame($this->request, $this->request->setCard($card));

        $returnedCard = $this->request->getCard();
        $this->assertInstanceOf('\Omnipay\Common\CreditCard', $returnedCard);
        $this->assertSame($card['number'], $returnedCard->getNumber());
    }

    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($this->request, $this->request->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $this->request->getTaxClassification());
    }

    public function testStatementDescriptor()
    {
        $statementDescriptor = $this->faker->statementDescriptor();
        $this->assertSame($this->request, $this->request->setStatementDescriptor($statementDescriptor));
        $this->assertSame($statementDescriptor, $this->request->getStatementDescriptor());
    }

    public function testReturnUrl()
    {
        $returnUrl = $this->faker->url();
        $this->assertSame($this->request, $this->request->setReturnUrl($returnUrl));
        $this->assertSame($returnUrl, $this->request->getReturnUrl());
    }

    public function testCancelUrl()
    {
        $cancelUrl = $this->faker->url();
        $this->assertSame($this->request, $this->request->setCancelUrl($cancelUrl));
        $this->assertSame($cancelUrl, $this->request->getCancelUrl());
    }

    public function testMinChargebackProbability()
    {
        $minChargebackProbability = $this->faker->chargebackProbability();
        $this->assertSame($this->request, $this->request->setMinChargebackProbability($minChargebackProbability));
        $this->assertSame($minChargebackProbability, $this->request->getMinChargebackProbability());
    }

    public function testSuccess()
    {
        $success = $this->faker->bool();
        $this->assertSame($this->request, $this->request->setSuccess($success));
        $this->assertSame($success, $this->request->getSuccess());
    }

    public function testPayPalTransactionReference()
    {
        $payPalTransactionReference = $this->faker->payPalTransactionReference();
        $this->assertSame($this->request, $this->request->setPayPalTransactionReference($payPalTransactionReference));
        $this->assertSame($payPalTransactionReference, $this->request->getPayPalTransactionReference());
    }

    public function testPrices()
    {
        $prices = $this->faker->prices();
        $this->assertSame($this->request, $this->request->setPrices($prices));
        $this->assertSame($prices, $this->request->getPrices());
    }

    public function testSendData()
    {
        $object = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER . DataFaker::ALPHABET_UPPER, $this->faker->intBetween(5, 10));
        $action = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(3, 10));
        $param = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(3, 10));
        $value = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER . DataFaker::DIGITS, $this->faker->intBetween(1, 6));

        TestableSoapClient::setNextResponse(new stdClass());

        $this->request->shouldReceive('getObject')->andReturn($object);
        $this->request->shouldReceive('getTestMode')->andReturn(true);
        $this->request->shouldReceive('getUsername')->andReturn($this->username);
        $this->request->shouldReceive('getPassword')->andReturn($this->password);

        $this->request->sendData(
            array(
                'action' => $action,
                $param => $value
            )
        );

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/' . $object . '.wsdl', $this->getLastEndpoint());
        $this->assertSame(
            array(
                'style'              => SOAP_DOCUMENT,
                'use'                => SOAP_LITERAL,
                'connection_timeout' => AbstractRequest::DEFAULT_TIMEOUT,
                'trace'              => true,
                'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
                'location'           => AbstractRequest::TEST_ENDPOINT . '/v' . AbstractRequest::API_VERSION . '/soap.pl'
            ),
            TestableSoapClient::getLastOptions()
        );
        $this->assertSame($action, TestableSoapClient::getLastFunctionName());

        $auth = new stdClass();
        $auth->version = AbstractRequest::API_VERSION;
        $auth->login = $this->username;
        $auth->password = $this->password;
        $auth->evid = null;
        $auth->userAgent = null;

        $this->assertEquals(
            array(
                'parameters' => array(
                    $param => $value,
                    'srd' => '',
                    'auth' => $auth
                )
            ),
            TestableSoapClient::getLastArguments()
        );
    }
}
