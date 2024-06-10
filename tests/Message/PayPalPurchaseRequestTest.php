<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\NameValue;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\AttributeBag;

class PayPalPurchaseRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->attributes = $this->faker->attributesAsArray();
        $this->taxClassification = $this->faker->taxClassification();
        $this->returnUrl = $this->faker->url();
        $this->cancelUrl = $this->faker->url();
        $this->card = array(
            'country' => $this->faker->region(),
            'postcode' => $this->faker->postcode()
        );

        $this->request = new PayPalPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'statementDescriptor' => $this->statementDescriptor,
                'ip' => $this->ip,
                'attributes' => $this->attributes,
                'taxClassification' => $this->taxClassification,
                'returnUrl' => $this->returnUrl,
                'cancelUrl' => $this->cancelUrl
            )
        );

        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->items = $this->faker->itemsAsArray($this->currency);
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->payPalToken = $this->faker->payPalToken();
        $this->riskScore = $this->faker->riskScore();
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setStatementDescriptor($this->statementDescriptor));
        $this->assertSame($this->statementDescriptor, $request->getStatementDescriptor());
    }

    /**
     * @return void
     */
    public function testIp()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setIp($this->ip));
        $this->assertSame($this->ip, $request->getIp());
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setItems($this->items));
        $this->assertEquals(new VindiciaItemBag($this->items), $request->getItems());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testCard()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCard($this->card));
        $this->assertEquals(new CreditCard($this->card), $request->getCard());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $request->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->amount));
        $this->assertSame($this->amount, $request->getAmount());
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTaxClassification($this->taxClassification));
        $this->assertSame($this->taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testReturnUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setReturnUrl($this->returnUrl));
        $this->assertEquals($this->returnUrl, $request->getReturnUrl());
    }

    /**
     * @return void
     */
    public function testCancelUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCancelUrl($this->cancelUrl));
        $this->assertEquals($this->cancelUrl, $request->getCancelUrl());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\PayPalPurchaseRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $request->getAttributes());
    }

    /**
     * @return void
     */
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
        $this->assertSame($this->statementDescriptor, $data['transaction']->billingStatementIdentifier);
        $this->assertSame($this->ip, $data['transaction']->sourceIp);
        $this->assertSame($this->returnUrl, $data['transaction']->sourcePaymentMethod->paypal->returnUrl);
        $this->assertSame($this->cancelUrl, $data['transaction']->sourcePaymentMethod->paypal->cancelUrl);
        $this->assertSame('PayPal', $data['transaction']->sourcePaymentMethod->type);
        $this->assertSame($this->card['postcode'], $data['transaction']->sourcePaymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['transaction']->sourcePaymentMethod->billingAddress->country);

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
            $this->assertEquals(array(new NameValue('description', $this->items[$i]['description'])), $data['transaction']->transactionItems[$i]->nameValues);
            $this->assertSame($this->items[$i]['taxClassification'], $data['transaction']->transactionItems[$i]->taxClassification);
        }

        $this->assertSame($this->currency, $data['transaction']->currency);
        $this->assertSame($this->customerId, $data['transaction']->account->merchantAccountId);
        $this->assertSame($this->customerReference, $data['transaction']->account->VID);
        $this->assertSame($this->paymentMethodId, $data['transaction']->sourcePaymentMethod->merchantPaymentMethodId);
        $this->assertSame($this->statementDescriptor, $data['transaction']->billingStatementIdentifier);
        $this->assertSame($this->ip, $data['transaction']->sourceIp);
        $this->assertSame($this->returnUrl, $data['transaction']->sourcePaymentMethod->paypal->returnUrl);
        $this->assertSame($this->cancelUrl, $data['transaction']->sourcePaymentMethod->paypal->cancelUrl);
        $this->assertSame('PayPal', $data['transaction']->sourcePaymentMethod->type);
        $this->assertSame($this->card['postcode'], $data['transaction']->sourcePaymentMethod->billingAddress->postalCode);
        $this->assertSame($this->card['country'], $data['transaction']->sourcePaymentMethod->billingAddress->country);

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
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The returnUrl parameter is required
     * @return                   void
     */
    public function testReturnUrlRequired()
    {
        $this->request->setReturnUrl(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The cancelUrl parameter is required
     * @return                   void
     */
    public function testCancelUrlRequired()
    {
        $this->request->setCancelUrl(null);
        $this->request->getData();
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Sum of item prices not equal to set amount.
     * @return                   void
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

        $request = new PayPalPurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            array(
                'currency' => $this->currency,
                'paymentMethodId' => $this->paymentMethodId,
                'customerId' => $this->customerId,
                'items' => $items,
                'amount' => $amount,
                'returnUrl' => $this->returnUrl,
                'cancelUrl' => $this->cancelUrl
            )
        );

        $request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('PayPalPurchaseSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'RETURN_URL' => $this->returnUrl,
            'CANCEL_URL' => $this->cancelUrl,
            'PAYPAL_TOKEN' => $this->payPalToken,
            'RISK_SCORE' => $this->riskScore
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());
        $this->assertSame('https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=' . $this->payPalToken, $response->getRedirectUrl());
        $this->assertSame($this->riskScore, $response->getRiskScore());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('PayPalPurchaseFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Must specify line items in transaction to calculate sales tax for auth!', $response->getMessage());

        $this->assertNull($response->getTransactionId());
        $this->assertNull($response->getTransactionReference());
        $this->assertNull($response->getRedirectUrl());
    }
}
