<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\Price;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\AttributeBag;

class CreateProductRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->productId = $this->faker->productId();
        $this->productReference = $this->faker->productReference();
        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
        $this->duplicateBehavior = $this->faker->duplicateBehavior();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->taxClassification = $this->faker->taxClassification();
        $this->prices = $this->faker->pricesAsArray();
        $this->attributes = $this->faker->attributesAsArray();

        $this->request = new CreateProductRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'productId' => $this->productId,
                'productReference' => $this->productReference,
                'planId' => $this->planId,
                'planReference' => $this->planReference,
                'duplicateBehavior' => $this->duplicateBehavior,
                'statementDescriptor' => $this->statementDescriptor,
                'taxClassification' => $this->taxClassification,
                'prices' => $this->prices,
                'attributes' => $this->attributes
            )
        );

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
    }

    public function testProductId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductId($this->productId));
        $this->assertSame($this->productId, $request->getProductId());
    }

    public function testProductReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $request->getProductReference());
    }

    public function testPlanId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    public function testPlanReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    public function testDuplicateBehavior()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setDuplicateBehavior($this->duplicateBehavior));
        $this->assertSame($this->duplicateBehavior, $request->getDuplicateBehavior());
    }

    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    public function testPricesAsBag()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        // $prices is a PriceBag
        $prices = $this->faker->prices();
        $this->assertSame($request, $request->setPrices($prices));
        $this->assertSame($prices, $request->getPrices());
    }

    public function testPricesAsArray()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        // $prices is an array
        $prices = $this->faker->pricesAsArray();
        $this->assertSame($request, $request->setPrices($prices));

        $returnedPrices = $request->getPrices();
        $this->assertInstanceOf('Omnipay\Vindicia\PriceBag', $returnedPrices);

        $numPrices = count($prices);
        $this->assertSame($numPrices, $returnedPrices->count());

        foreach ($returnedPrices as $i => $returnedPrice) {
            $this->assertEquals(new Price($prices[$i]), $returnedPrice);
        }
    }

    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreateProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    public function testAttributes()
    {
        $this->assertSame($this->request, $this->request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $this->request->getAttributes());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->statementDescriptor, $data['product']->billingStatementIdentifier);
        $this->assertSame($this->productId, $data['product']->merchantProductId);
        $this->assertSame($this->productReference, $data['product']->VID);
        $this->assertSame($this->taxClassification, $data['product']->taxClassification);
        $this->assertSame($this->planId, $data['product']->defaultBillingPlan->merchantBillingPlanId);
        $this->assertSame($this->planReference, $data['product']->defaultBillingPlan->VID);
        $this->assertSame($this->duplicateBehavior, $data['duplicateBehavior']);

        $numPrices = count($this->prices);
        $this->assertSame($numPrices, count($data['product']->prices));
        for ($i = 0; $i < $numPrices; $i++) {
            $this->assertSame($this->prices[$i]['currency'], $data['product']->prices[$i]->currency);
            $this->assertSame($this->prices[$i]['amount'], $data['product']->prices[$i]->amount);
        }

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['product']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['product']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['product']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
    }

    /**
     * Test getData when amount and currency are set instead of prices
     */
    public function testGetDataAmountAndCurrency()
    {
        $this->request->setPrices(null);
        $this->request->setCurrency($this->currency);
        $this->request->setAmount($this->amount);

        $data = $this->request->getData();

        $this->assertSame($this->statementDescriptor, $data['product']->billingStatementIdentifier);
        $this->assertSame($this->productId, $data['product']->merchantProductId);
        $this->assertSame($this->taxClassification, $data['product']->taxClassification);
        $this->assertSame($this->planId, $data['product']->defaultBillingPlan->merchantBillingPlanId);
        $this->assertSame($this->duplicateBehavior, $data['duplicateBehavior']);

        $this->assertSame(1, count($data['product']->prices));
        $this->assertSame($this->currency, $data['product']->prices[0]->currency);
        $this->assertSame($this->amount, $data['product']->prices[0]->amount);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['product']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['product']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['product']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The productId parameter is required
     */
    public function testProductIdRequired()
    {
        $this->request->setProductId(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount and currency parameters cannot be set if the prices parameter is set.
     */
    public function testCannotSetPricesAndAmount()
    {
        $this->request->setCurrency($this->currency);
        $this->request->setAmount($this->amount);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount and currency parameters cannot be set if the prices parameter is set.
     */
    public function testCannotSetPricesAndCurrency()
    {
        $this->request->setCurrency($this->currency);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CreateProductSuccess.xml', array(
            'PRODUCT_ID' => $this->productId,
            'PRODUCT_REFERENCE' => $this->productReference,
            'TAX_CLASSIFICATION' => $this->taxClassification,
            'STATEMENT_DESCRIPTOR' => $this->statementDescriptor,
            'PLAN_ID' => $this->planId,
            'AMOUNT' => $this->amount,
            'CURRENCY' => $this->currency
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->productId, $response->getProductId());
        $this->assertSame($this->productReference, $response->getProductReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Product.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreateProductFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to update Product: Unable to create BillingPlan without a Billing Period ', $response->getMessage());

        $this->assertNull($response->getProductId());
        $this->assertNull($response->getProductReference());
    }
}
