<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\CreatePlanRequest;
use Omnipay\Vindicia\Price;
use Omnipay\VindiciaTest\SoapTestCase;
use Omnipay\Vindicia\AttributeBag;

class CreatePlanRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
        $this->interval = $this->faker->billingInterval();
        $this->intervalCount = $this->faker->billingIntervalCount();
        $this->prices = $this->faker->prices(true);
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->taxClassification = $this->faker->taxClassification();
        $this->attributes = $this->faker->attributes(true);

        $this->request = new CreatePlanRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'planId' => $this->planId,
                'planReference' => $this->planReference,
                'interval' => $this->interval,
                'intervalCount' => $this->intervalCount,
                'prices' => $this->prices,
                'statementDescriptor' => $this->statementDescriptor,
                'taxClassification' => $this->taxClassification,
                'attributes' => $this->attributes
            )
        );

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
    }

    public function testPlanId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanId($this->planId));
        $this->assertSame($this->planId, $request->getPlanId());
    }

    public function testPlanReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPlanReference($this->planReference));
        $this->assertSame($this->planReference, $request->getPlanReference());
    }

    public function testInterval()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setInterval($this->interval));
        $this->assertSame($this->interval, $request->getInterval());
    }

    public function testIntervalCount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setIntervalCount($this->intervalCount));
        $this->assertSame($this->intervalCount, $request->getIntervalCount());
    }

    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    public function testPricesAsBag()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        // $prices is a PriceBag
        $prices = $this->faker->prices();
        $this->assertSame($request, $request->setPrices($prices));
        $this->assertSame($prices, $request->getPrices());
    }

    public function testPricesAsArray()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        // $prices is an array
        $prices = $this->faker->prices(true);
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
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CreatePlanRequest')->makePartial();
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

        $this->assertSame($this->planId, $data['billingPlan']->merchantBillingPlanId);
        $this->assertSame($this->planReference, $data['billingPlan']->VID);
        $this->assertSame($this->taxClassification, $data['billingPlan']->taxClassification);
        $this->assertSame($this->statementDescriptor, $data['billingPlan']->billingStatementIdentifier);
        $this->assertSame(ucfirst($this->interval), $data['billingPlan']->periods[0]->type);
        $this->assertSame($this->intervalCount, $data['billingPlan']->periods[0]->quantity);
        $this->assertSame(0, $data['billingPlan']->periods[0]->cycles);

        $numPrices = count($this->prices);
        $this->assertSame($numPrices, count($data['billingPlan']->prices));
        for ($i = 0; $i < $numPrices; $i++) {
            $this->assertSame($this->prices[$i]['currency'], $data['billingPlan']->prices[$i]->currency);
            $this->assertSame($this->prices[$i]['amount'], $data['billingPlan']->prices[$i]->amount);
        }

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['billingPlan']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['billingPlan']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['billingPlan']->nameValues[$i]->value);
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

        $this->assertSame($this->planId, $data['billingPlan']->merchantBillingPlanId);
        $this->assertSame($this->planReference, $data['billingPlan']->VID);
        $this->assertSame($this->taxClassification, $data['billingPlan']->taxClassification);
        $this->assertSame($this->statementDescriptor, $data['billingPlan']->billingStatementIdentifier);
        $this->assertSame(ucfirst($this->interval), $data['billingPlan']->periods[0]->type);
        $this->assertSame($this->intervalCount, $data['billingPlan']->periods[0]->quantity);
        $this->assertSame(0, $data['billingPlan']->periods[0]->cycles);

        $this->assertSame(1, count($data['billingPlan']->prices));
        $this->assertSame($this->amount, $data['billingPlan']->prices[0]->amount);
        $this->assertSame($this->currency, $data['billingPlan']->prices[0]->currency);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['billingPlan']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['billingPlan']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['billingPlan']->nameValues[$i]->value);
        }

        $this->assertSame('update', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The planId parameter is required
     */
    public function testPlanIdRequired()
    {
        $this->request->setPlanId(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The interval parameter is required
     */
    public function testIntervalRequired()
    {
        $this->request->setInterval(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The intervalCount parameter is required
     */
    public function testIntervalCountRequired()
    {
        $this->request->setIntervalCount(null);
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
        $this->setMockSoapResponse('CreatePlanSuccess.xml', array(
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference,
            'TAX_CLASSIFICATION' => $this->taxClassification,
            'INTERVAL' => ucfirst($this->interval),
            'INTERVAL_COUNT' => $this->intervalCount,
            'STATEMENT_DESCRIPTOR' => $this->statementDescriptor
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->planId, $response->getPlanId());
        $this->assertSame($this->planReference, $response->getPlanReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/BillingPlan.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('CreatePlanFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to update BillingPlan: Unable to load BillingPlanPeriod: Error: For this billing plan, the billing period type Hour is not valid', $response->getMessage());

        $this->assertNull($response->getPlanId());
        $this->assertNull($response->getPlanReference());
    }
}
