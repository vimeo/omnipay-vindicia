<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->transaction = new Transaction();
        $this->id = $this->faker->transactionId();
        $this->reference = $this->faker->transactionReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $transaction = new Transaction(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $transaction->getId());
        $this->assertSame($this->reference, $transaction->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->transaction, $this->transaction->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->transaction->getId());
        $this->assertSame($this->reference, $this->transaction->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->transaction, $this->transaction->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->transaction->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->transaction, $this->transaction->setId($this->id));
        $this->assertSame($this->id, $this->transaction->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->transaction, $this->transaction->setReference($this->reference));
        $this->assertSame($this->reference, $this->transaction->getReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->transaction, $this->transaction->setCurrency($currency));
        $this->assertSame($currency, $this->transaction->getCurrency());
    }

    /**
     * @return void
     */
    public function testAmount()
    {
        $amount = $this->faker->monetaryAmount($this->faker->currency());
        $this->assertSame($this->transaction, $this->transaction->setAmount($amount));
        $this->assertSame($amount, $this->transaction->getAmount());
    }

    /**
     * @return void
     */
    public function testCustomer()
    {
        $customer = new Customer();
        $this->assertSame($this->transaction, $this->transaction->setCustomer($customer));
        $this->assertSame($customer, $this->transaction->getCustomer());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $customerId = $this->faker->customerId();
        $this->assertSame($this->transaction, $this->transaction->setCustomerId($customerId));
        $this->assertSame($customerId, $this->transaction->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $customerReference = $this->faker->customerReference();
        $this->assertSame($this->transaction, $this->transaction->setCustomerReference($customerReference));
        $this->assertSame($customerReference, $this->transaction->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testPaymentMethod()
    {
        $paymentMethod = new PaymentMethod();
        $this->assertSame($this->transaction, $this->transaction->setPaymentMethod($paymentMethod));
        $this->assertSame($paymentMethod, $this->transaction->getPaymentMethod());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $paymentMethodId = $this->faker->paymentMethodId();
        $this->assertSame($this->transaction, $this->transaction->setPaymentMethodId($paymentMethodId));
        $this->assertSame($paymentMethodId, $this->transaction->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $paymentMethodReference = $this->faker->paymentMethodReference();
        $this->assertSame($this->transaction, $this->transaction->setPaymentMethodReference($paymentMethodReference));
        $this->assertSame($paymentMethodReference, $this->transaction->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $items = array($this->faker->item($this->faker->currency()));
        $this->assertSame($this->transaction, $this->transaction->setItems($items));
        $this->assertSame($items, $this->transaction->getItems());
    }

    /**
     * @return void
     */
    public function testIp()
    {
        $ip = $this->faker->ipAddress();
        $this->assertSame($this->transaction, $this->transaction->setIp($ip));
        $this->assertSame($ip, $this->transaction->getIp());
    }

    /**
     * @return void
     */
    public function testAuthorizationCode()
    {
        $authorizationCode = $this->faker->statusCode();
        $this->assertSame($this->transaction, $this->transaction->setAuthorizationCode($authorizationCode));
        $this->assertSame($authorizationCode, $this->transaction->getAuthorizationCode());
    }

    /**
     * @return void
     */
    public function testCvvCode()
    {
        $cvvCode = $this->faker->statusCode();
        $this->assertSame($this->transaction, $this->transaction->setCvvCode($cvvCode));
        $this->assertSame($cvvCode, $this->transaction->getCvvCode());
    }

    /**
     * @return void
     */
    public function testAvsCode()
    {
        $avsCode = $this->faker->statusCode();
        $this->assertSame($this->transaction, $this->transaction->setAvsCode($avsCode));
        $this->assertSame($avsCode, $this->transaction->getAvsCode());
    }

    /**
     * @return void
     */
    public function testPayPalEmail()
    {
        $payPalEmail = $this->faker->email();
        $this->assertSame($this->transaction, $this->transaction->setPayPalEmail($payPalEmail));
        $this->assertSame($payPalEmail, $this->transaction->getPayPalEmail());
    }

    /**
     * @return void
     */
    public function testPayPalRedirectUrl()
    {
        $payPalRedirectUrl = $this->faker->url();
        $this->assertSame($this->transaction, $this->transaction->setPayPalRedirectUrl($payPalRedirectUrl));
        $this->assertSame($payPalRedirectUrl, $this->transaction->getPayPalRedirectUrl());
    }

    /**
     * @return void
     */
    public function testStatus()
    {
        $status = $this->faker->status();
        $this->assertSame($this->transaction, $this->transaction->setStatus($status));
        $this->assertSame($status, $this->transaction->getStatus());
    }

    /**
     * @return void
     */
    public function testStatusLog()
    {
        $status = new TransactionStatus(array(
            'status' => $this->faker->status()
        ));
        $this->assertSame($this->transaction, $this->transaction->setStatusLog(array($status)));
        $this->assertSame(array($status), $this->transaction->getStatusLog());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->transaction, $this->transaction->setAttributes($attributes));
        $this->assertSame($attributes, $this->transaction->getAttributes());
    }
}
