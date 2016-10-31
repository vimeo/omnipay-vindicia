<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\AttributeBag;

class PurchaseRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->attributes = $this->faker->attributesAsArray();
        $this->taxClassification = $this->faker->taxClassification();
        $this->minChargebackProbability = $this->faker->chargebackProbability();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'paymentMethodReference' => $this->paymentMethodReference,
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'statementDescriptor' => $this->statementDescriptor,
                'ip' => $this->ip,
                'attributes' => $this->attributes,
                'taxClassification' => $this->taxClassification,
                'minChargebackProbability' => $this->minChargebackProbability,
            )
        );

        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->items = $this->faker->itemsAsArray($this->currency);
        $this->soapId = $this->faker->soapId();
    }

    public function testMinChargebackProbability()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setMinChargebackProbability($this->minChargebackProbability));
        $this->assertSame($this->minChargebackProbability, $request->getMinChargebackProbability());
    }

    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    public function testIp()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testItems()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setItems($this->items));
        $this->assertEquals(new VindiciaItemBag($this->items), $request->getItems());
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new CreditCard($this->card), $request->getCard());
    }

    public function testAttributes()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $request->getAttributes());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['transaction']->transactionItems[0]->price);
        $this->assertSame($this->taxClassification, $data['transaction']->transactionItems[0]->taxClassification);
        $this->assertSame(1, $data['transaction']->transactionItems[0]->quantity);
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->customerReference, $data['transaction']->account->VID);
        $this->assertSame($this->paymentMethodId, $data['transaction']->sourcePaymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['transaction']->sourcePaymentMethod->VID);
        $this->assertSame($this->card['number'], $data['transaction']->sourcePaymentMethod->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['transaction']->sourcePaymentMethod->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['transaction']->sourcePaymentMethod->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['transaction']->sourcePaymentMethod->nameValues));
        $this->assertSame($this->card['postcode'], $data['transaction']->sourcePaymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['transaction']->sourcePaymentMethod->billingAddress->country);
        $this->assertSame($this->statementDescriptor, $data['transaction']->billingStatementIdentifier);
        $this->assertSame($this->ip, $data['transaction']->sourceIp);
        $this->assertSame('CreditCard', $data['transaction']->sourcePaymentMethod->type);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['transaction']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['transaction']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['transaction']->nameValues[$i]->value);
        }

        $this->assertSame('authCapture', $data['action']);
        $this->assertSame(false, $data['sendEmailNotification']);
        $this->assertNull($data['campaignCode']);
        $this->assertSame(false, $data['dryrun']);
        $this->assertSame($this->minChargebackProbability, $data['minChargebackProbability']);
    }

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
            $this->assertEquals(array(new NameValue('description', $this->items[$i]['description'])), $data['transaction']->transactionItems[$i]->nameValues);
            $this->assertSame($this->taxClassification, $data['transaction']->transactionItems[$i]->taxClassification);
        }

        $this->assertSame($this->taxClassification, $data['transaction']->transactionItems[0]->taxClassification);
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->customerReference, $data['transaction']->account->VID);
        $this->assertSame($this->paymentMethodId, $data['transaction']->sourcePaymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->paymentMethodReference, $data['transaction']->sourcePaymentMethod->VID);
        $this->assertSame($this->card['number'], $data['transaction']->sourcePaymentMethod->creditCard->account);
        $this->assertSame($this->card['expiryYear'], substr($data['transaction']->sourcePaymentMethod->creditCard->expirationDate, 0, 4));
        $this->assertSame(intval($this->card['expiryMonth']), intval(substr($data['transaction']->sourcePaymentMethod->creditCard->expirationDate, 4)));
        $this->assertTrue(in_array(new NameValue('CVN', $this->card['cvv']), $data['transaction']->sourcePaymentMethod->nameValues));
        $this->assertSame($this->card['postcode'], $data['transaction']->sourcePaymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['transaction']->sourcePaymentMethod->billingAddress->country);
        $this->assertSame($this->statementDescriptor, $data['transaction']->billingStatementIdentifier);
        $this->assertSame($this->ip, $data['transaction']->sourceIp);
        $this->assertSame('CreditCard', $data['transaction']->sourcePaymentMethod->type);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['transaction']->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['transaction']->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['transaction']->nameValues[$i]->value);
        }

        $this->assertSame('authCapture', $data['action']);
        $this->assertSame(false, $data['sendEmailNotification']);
        $this->assertNull($data['campaignCode']);
        $this->assertSame(false, $data['dryrun']);
        $this->assertSame($this->minChargebackProbability, $data['minChargebackProbability']);
    }

    /**
     * If you're using a saved card, you only have to specify the card id, not all the details
     */
    public function testGetDataPaymentMethodIdOnly()
    {
        $this->request->setCard(null);

        $data = $this->request->getData();

        $this->assertSame($this->amount, $data['transaction']->transactionItems[0]->price);
        $this->assertSame(1, $data['transaction']->transactionItems[0]->quantity);
        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->paymentMethodId, $data['transaction']->sourcePaymentMethod->merchantPaymentMethodId);
        $this->assertSame('authCapture', $data['action']);
        $this->assertSame($this->minChargebackProbability, $data['minChargebackProbability']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the amount or items parameter is required.
     */
    public function testAmountRequired()
    {
        $this->request->setAmount(null);
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

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Sum of item prices not equal to set amount.
     */
    public function testAmountMustEqualSumOfItems()
    {
        $items = new VindiciaItemBag($this->items);

        $sumOfItems = '0.0';
        foreach ($items as $item) {
            $sumOfItems = strval($sumOfItems + $item->getPrice() * $item->getQuantity());
        }

        do {
            $amount = $this->faker->monetaryAmount($this->currency);
        } while ($amount == $sumOfItems);

        $request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            array(
                'currency' => $this->currency,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'customerId' => $this->customerId,
                'items' => $items,
                'amount' => $amount
            )
        );

        $request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('PurchaseSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'CVV' => $this->card['cvv'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * If you're using a saved card, you only have to specify the card id, not all the details
     */
    public function testSendSuccessPaymentMethodIdOnly()
    {
        $this->setMockSoapResponse('PurchaseSuccess.xml', array(
            'SOAP_ID' => $this->soapId,
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());
        $this->assertSame($this->soapId, $response->getSoapId());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('PurchaseFailure.xml', array(
            'SOAP_ID' => $this->soapId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Data validation error Failed to create Payment-Type-Specific Payment Record: Credit Card conversion failed: Credit Card failed Luhn check. ', $response->getMessage());

        // no id or reference since Vindicia creates them both
        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getTransactionReference());
        $this->assertSame($this->soapId, $response->getSoapId());
    }
}
