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
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->request = Mocker::mock('\Omnipay\Vindicia\Message\AbstractRequest')->makePartial()->shouldAllowMockingProtectedMethods();
        $this->request->initialize();
        $this->faker = new DataFaker();

        $this->username = $this->faker->username();
        $this->password = $this->faker->password();
    }

    /**
     * @return void
     */
    public function testUsername()
    {
        $this->assertSame($this->request, $this->request->setUsername($this->username));
        $this->assertSame($this->username, $this->request->getUsername());
    }

    /**
     * @return void
     */
    public function testPassword()
    {
        $this->assertSame($this->request, $this->request->setPassword($this->password));
        $this->assertSame($this->password, $this->request->getPassword());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $customerId = $this->faker->customerId();
        $this->assertSame($this->request, $this->request->setCustomerId($customerId));
        $this->assertSame($customerId, $this->request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $customerReference = $this->faker->customerReference();
        $this->assertSame($this->request, $this->request->setCustomerReference($customerReference));
        $this->assertSame($customerReference, $this->request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testName()
    {
        $name = $this->faker->name();
        $this->assertSame($this->request, $this->request->setName($name));
        $this->assertSame($name, $this->request->getName());
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $email = $this->faker->email();
        $this->assertSame($this->request, $this->request->setEmail($email));
        $this->assertSame($email, $this->request->getEmail());
    }

    /**
     * @return void
     */
    public function testRefundId()
    {
        $refundId = $this->faker->refundId();
        $this->assertSame($this->request, $this->request->setRefundId($refundId));
        $this->assertSame($refundId, $this->request->getRefundId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $paymentMethodId = $this->faker->paymentMethodId();
        $this->assertSame($this->request, $this->request->setPaymentMethodId($paymentMethodId));
        $this->assertSame($paymentMethodId, $this->request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $paymentMethodReference = $this->faker->paymentMethodReference();
        $this->assertSame($this->request, $this->request->setPaymentMethodReference($paymentMethodReference));
        $this->assertSame($paymentMethodReference, $this->request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $transactionId = $this->faker->transactionId();
        $this->assertSame($this->request, $this->request->setTransactionId($transactionId));
        $this->assertSame($transactionId, $this->request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $transactionReference = $this->faker->transactionReference();
        $this->assertSame($this->request, $this->request->setTransactionReference($transactionReference));
        $this->assertSame($transactionReference, $this->request->getTransactionReference());
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $productId = $this->faker->productId();
        $this->assertSame($this->request, $this->request->setProductId($productId));
        $this->assertSame($productId, $this->request->getProductId());
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $productReference = $this->faker->productReference();
        $this->assertSame($this->request, $this->request->setProductReference($productReference));
        $this->assertSame($productReference, $this->request->getProductReference());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->request, $this->request->setPlanId($planId));
        $this->assertSame($planId, $this->request->getPlanId());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->request, $this->request->setPlanReference($planReference));
        $this->assertSame($planReference, $this->request->getPlanReference());
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $this->assertSame($this->request, $this->request->setSubscriptionId($subscriptionId));
        $this->assertSame($subscriptionId, $this->request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $subscriptionReference = $this->faker->subscriptionReference();
        $this->assertSame($this->request, $this->request->setSubscriptionReference($subscriptionReference));
        $this->assertSame($subscriptionReference, $this->request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testInvoiceReference()
    {
        $invoiceReference = $this->faker->invoiceReference();
        $this->assertSame($this->request, $this->request->setInvoiceReference($invoiceReference));
        $this->assertSame($invoiceReference, $this->request->getInvoiceReference());
    }

    /**
     * @return void
     */
    public function testTimeout()
    {
        $timeout = $this->faker->timeout();
        $this->assertSame($this->request, $this->request->setTimeout($timeout));
        $this->assertSame($timeout, $this->request->getTimeout());
    }

    /**
     * @return void
     */
    public function testIp()
    {
        $ip = $this->faker->ipAddress();
        $this->assertSame($this->request, $this->request->setIp($ip));
        $this->assertSame($ip, $this->request->getIp());
    }

     /**
     * @return void
     */
    public function testBillingDay()
    {
        $billingDay = $this->faker->intBetween(1, 31);
        $this->assertSame($this->request, $this->request->setBillingDay($billingDay));
        $this->assertSame($billingDay, $this->request->getBillingDay());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $startTime = $this->faker->timestamp();
        $this->assertSame($this->request, $this->request->setStartTime($startTime));
        $this->assertSame($startTime, $this->request->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $endTime = $this->faker->timestamp();
        $this->assertSame($this->request, $this->request->setEndTime($endTime));
        $this->assertSame($endTime, $this->request->getEndTime());
    }

    /**
     * @return void
     */
    public function testItemsAsBag()
    {
        // $items is a VindiciaItemBag
        $items = $this->faker->items($this->faker->currency());
        $this->assertSame($this->request, $this->request->setItems($items));
        $this->assertSame($items, $this->request->getItems());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testAttributesAsBag()
    {
        // $attributes is an AttributeBag
        $attributes = $this->faker->attributes();
        $this->assertSame($this->request, $this->request->setAttributes($attributes));
        $this->assertSame($attributes, $this->request->getAttributes());
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testCard()
    {
        $card = new CreditCard();
        $this->assertSame($this->request, $this->request->setCard($card));
        $this->assertSame($card, $this->request->getCard());
    }

    /**
     * @return void
     */
    public function testSetCardWithArray()
    {
        $card = $this->faker->card();
        $this->assertSame($this->request, $this->request->setCard($card));

        $returnedCard = $this->request->getCard();
        $this->assertInstanceOf('\Omnipay\Common\CreditCard', $returnedCard);
        $this->assertSame($card['number'], $returnedCard->getNumber());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertSame($this->request, $this->request->setTaxClassification($taxClassification));
        $this->assertSame($taxClassification, $this->request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $statementDescriptor = $this->faker->statementDescriptor();
        $this->assertSame($this->request, $this->request->setStatementDescriptor($statementDescriptor));
        $this->assertSame($statementDescriptor, $this->request->getStatementDescriptor());
    }

    /**
     * @return void
     */
    public function testReturnUrl()
    {
        $returnUrl = $this->faker->url();
        $this->assertSame($this->request, $this->request->setReturnUrl($returnUrl));
        $this->assertSame($returnUrl, $this->request->getReturnUrl());
    }

    /**
     * @return void
     */
    public function testCancelUrl()
    {
        $cancelUrl = $this->faker->url();
        $this->assertSame($this->request, $this->request->setCancelUrl($cancelUrl));
        $this->assertSame($cancelUrl, $this->request->getCancelUrl());
    }

    /**
     * @return void
     */
    public function testMinChargebackProbability()
    {
        $minChargebackProbability = $this->faker->chargebackProbability();
        $this->assertSame($this->request, $this->request->setMinChargebackProbability($minChargebackProbability));
        $this->assertSame($minChargebackProbability, $this->request->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    public function testSuccess()
    {
        $success = $this->faker->bool();
        $this->assertSame($this->request, $this->request->setSuccess($success));
        $this->assertSame($success, $this->request->getSuccess());
    }

    /**
     * @return void
     */
    public function testPayPalTransactionReference()
    {
        $payPalTransactionReference = $this->faker->payPalTransactionReference();
        $this->assertSame($this->request, $this->request->setPayPalTransactionReference($payPalTransactionReference));
        $this->assertSame($payPalTransactionReference, $this->request->getPayPalTransactionReference());
    }

    /**
     * @return void
     */
    public function testPrices()
    {
        $prices = $this->faker->prices();
        $this->assertSame($this->request, $this->request->setPrices($prices));
        $this->assertSame($prices, $this->request->getPrices());
    }

    /**
     * @return void
     */
    public function testDefaultParameterOnCreate()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\AbstractRequest')->makePartial()->shouldAllowMockingProtectedMethods();
        $request->initialize();
        $this->assertSame(AbstractRequest::DEFAULT_TAX_CLASSIFICATION, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testQuantity()
    {
        $quantity = mt_rand(1, mt_getrandmax());
        $this->assertSame($this->request, $this->request->setQuantity($quantity));
        $this->assertSame($quantity, $this->request->getQuantity());
    }

    /**
     * @return void
     */
    public function testDefaultParameterOnUpdate()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\AbstractRequest')->makePartial()->shouldAllowMockingProtectedMethods();
        $request->shouldReceive('isUpdate')->andReturn(true);
        $request->initialize();
        $this->assertNull($request->getTaxClassification());
    }

    /**
     * @return void
     */
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

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/' . $object . '.wsdl', $this->getLastEndpoint());
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
