<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchTransactionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->transactionId = $this->faker->transactionId();

        $this->request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId
            )
        );

        $this->transactionReference = $this->faker->transactionReference();
        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->tax_amount = $this->faker->monetaryAmount($this->currency);
        if ($this->amount < $this->tax_amount) {
            $temp = $this->tax_amount;
            $this->tax_amount = $this->amount;
            $this->amount = $this->tax_amount;
        }
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->ipAddress = $this->faker->ipAddress();
        $this->sku = $this->faker->sku();
        $this->taxClassification = $this->faker->taxClassification();
        $this->authorizationCode = $this->faker->statusCode();
        $this->cvvCode = $this->faker->statusCode();
        $this->avsCode = $this->faker->statusCode();
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchTransactionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['merchantTransactionId']);
        $this->assertSame('fetchByMerchantTransactionId', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByReference()
    {
        $this->request->setTransactionId(null)->setTransactionReference($this->transactionReference);

        $data = $this->request->getData();

        $this->assertSame($this->transactionReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the transactionId or transactionReference parameter is required.
     * @return                   void
     */
    public function testTransactionIdOrReferenceRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchTransactionSuccess.xml', array(
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'TAX_AMOUNT' => $this->tax_amount,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'IP_ADDRESS' => $this->ipAddress,
            'TAX_CLASSIFICATION' => $this->taxClassification,
            'SKU' => $this->sku,
            'AUTHORIZATION_CODE' => $this->authorizationCode,
            'CVV_CODE' => $this->cvvCode,
            'AVS_CODE' => $this->avsCode
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());

        $transaction = $response->getTransaction();
        $this->assertInstanceOf('\Omnipay\Vindicia\Transaction', $transaction);
        $this->assertSame($this->transactionId, $response->getTransactionId());
        $this->assertSame($this->transactionReference, $response->getTransactionReference());
        $this->assertSame($this->transactionId, $transaction->getId());
        $this->assertSame($this->transactionReference, $transaction->getReference());
        $this->assertSame($this->currency, $transaction->getCurrency());
        $this->assertSame($this->amount, $transaction->getAmount());
        $customer = $transaction->getCustomer();
        $this->assertSame($this->customerId, $transaction->getCustomerId());
        $this->assertSame($this->customerId, $customer->getId());
        $this->assertSame($this->customerReference, $transaction->getCustomerReference());
        $this->assertSame($this->customerReference, $customer->getReference());
        $paymentMethod = $transaction->getPaymentMethod();
        $this->assertSame($this->paymentMethodId, $transaction->getPaymentMethodId());
        $this->assertSame($this->paymentMethodId, $paymentMethod->getId());
        $this->assertSame($this->paymentMethodReference, $transaction->getPaymentMethodReference());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getReference());
        $items = $transaction->getItems();
        $this->assertSame(2, count($items));
        foreach ($items as $i => $item) {
            $this->assertInstanceOf('\Omnipay\Vindicia\VindiciaItem', $item);
            $item->validate();
            if ($i === 0) {
                $this->assertSame($this->taxClassification, $item->getTaxClassification());
                $this->assertSame($this->amount, $item->getPrice());
                $this->assertSame($this->sku, $item->getSku());
            } else if ($i === 1) {
                $this->assertSame('Total Tax', $item->getSku());
                $this->assertSame($this->tax_amount, $item->getPrice());
            }
        }
        $this->assertSame($this->amount, $items[0]->getPrice());
        $this->assertSame($this->ipAddress, $transaction->getIp());
        $this->assertEquals($this->authorizationCode, $transaction->getAuthorizationCode());
        $this->assertEquals($this->cvvCode, $transaction->getCvvCode());
        $this->assertEquals($this->avsCode, $transaction->getAvsCode());
        $this->assertSame('Authorized', $transaction->getStatus());
        $statusLog = $transaction->getStatusLog();
        $this->assertSame(2, count($statusLog));
        foreach ($statusLog as $logEntry) {
            $this->assertInstanceOf('\Omnipay\Vindicia\TransactionStatus', $logEntry);
        }
        $this->assertSame('Authorized', $statusLog[0]->getStatus());
        $this->assertSame('New', $statusLog[1]->getStatus());
        $attributes = $transaction->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
            $this->assertTrue(is_string($attribute->getName()));
            $this->assertTrue(is_string($attribute->getValue()));
        }

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchTransactionByReferenceSuccess.xml', array(
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
        $this->assertSame($this->transactionId, $response->getTransaction()->getId());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Transaction.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchTransactionFailure.xml', array(
            'TRANSACTION_ID' => $this->transactionId,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to load transaction by merchantTransactionId "' . $this->transactionId . '":  ', $response->getMessage());

        $this->assertNull($response->getTransaction());
    }

    /**
     * @return void
     */
    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchTransactionByReferenceFailure.xml', array(
            'TRANSACTION_REFERENCE' => $this->transactionReference,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to load transaction by VID "' . $this->transactionReference . '":  ', $response->getMessage());

        $this->assertNull($response->getTransaction());
    }
}
