<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\VindiciaItemBag;

class CalculateSalesTaxRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
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
        $this->postcode = $this->faker->postcode();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();

        $this->card = array(
            'country' => $this->country,
            'postcode' => $this->postcode
        );
        $this->taxClassification = $this->faker->taxClassification();

        $this->request = new CalculateSalesTaxRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'taxClassification' => $this->taxClassification,
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference
            )
        );

        $this->items = $this->faker->itemsAsArray($this->currency);
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new CreditCard($this->card), $request->getCard());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertEquals($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertEquals($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CalculateSalesTaxRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setItems($this->items));
        $this->assertEquals(new VindiciaItemBag($this->items), $request->getItems());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['transaction']->transactionItems[0]->price);
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->taxClassification, $data['transaction']->transactionItems[0]->taxClassification);
        $this->assertSame($this->card['country'], $data['transaction']->shippingAddress->country);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->customerReference, $data['transaction']->account->VID);
        $this->assertFalse(isset($data['transaction']->sourcePaymentMethod));

        $this->assertSame('calculateSalesTax', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataMultipleItems()
    {
        $this->request->setAmount(null)->setItems($this->items);

        $data = $this->request->getData();

        $numItems = count($this->items);
        $this->assertSame($numItems, count($data['transaction']->transactionItems));
        for ($i = 0; $i < $numItems; $i++) {
            $this->assertSame($this->items[$i]['price'], $data['transaction']->transactionItems[$i]->price);
            $this->assertSame($this->items[$i]['quantity'], $data['transaction']->transactionItems[$i]->quantity);
            $this->assertSame($this->items[$i]['sku'], $data['transaction']->transactionItems[$i]->sku);
            $this->assertSame($this->items[$i]['taxClassification'], $data['transaction']->transactionItems[$i]->taxClassification);
        }
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->card['country'], $data['transaction']->shippingAddress->country);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->customerReference, $data['transaction']->account->VID);
        $this->assertFalse(isset($data['transaction']->sourcePaymentMethod));

        $this->assertSame('calculateSalesTax', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the amount or items parameter is required.
     * @return                   void
     */
    public function testAmountRequired()
    {
        $this->request->setAmount(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
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

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
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
