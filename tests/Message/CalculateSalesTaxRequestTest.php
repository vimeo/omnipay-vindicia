<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\CalculateSalesTaxRequest;
use Omnipay\VindiciaTest\SoapTestCase;
use Omnipay\Vindicia\VindiciaCreditCard;

class CalculateSalesTaxRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->tax_amount = $this->faker->monetaryAmount($this->currency);
        // make tax_amount less
        if ($this->tax_amount > $this->amount) {
            $temp = $this->amount;
            $this->amount = $this->tax_amount;
            $this->tax_amount = $temp;
        }
        $this->country = $this->faker->region();

        $this->card = array(
            'country' => $this->country
        );
        $this->taxClassification = $this->faker->taxClassification();

        $this->request = new CalculateSalesTaxRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'taxClassification' => $this->taxClassification
            )
        );
    }

    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new VindiciaCreditCard($this->card), $request->getCard());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['transaction']->transactionItems[0]->price);
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->taxClassification, $data['transaction']->transactionItems[0]->taxClassification);
        $this->assertSame($this->card['country'], $data['transaction']->sourcePaymentMethod->billingAddress->country);

        $this->assertSame('calculateSalesTax', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The amount parameter is required
     */
    public function testAmountRequired()
    {
        $this->request->setAmount(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CalculateSalesTaxSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'COUNTRY' => $this->country,
            'TAX_CLASSIFICATION' => $this->taxClassification,
            'TAX_AMOUNT' => $this->tax_amount
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->tax_amount, $response->getSalesTax());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('CalculateSalesTaxFailure.xml', array(
            'TAX_CLASSIFICATION' => $this->taxClassification
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Error validating transaction:  The value you provided in the TaxClassification field (' . $this->taxClassification . ') is not an accepted value (sku: 0): Not a Vindicia tax classification', $response->getMessage());

        // no id or reference since Vindicia creates them both
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getTransactionReference());
    }
}
