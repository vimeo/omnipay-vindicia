<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Vindicia\PayPalGateway;
use Omnipay\Omnipay;

class PayPalGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new PayPalGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->faker = new DataFaker();
    }

    public function testGetName()
    {
        $this->assertSame('Vindicia PayPal', $this->gateway->getName());
    }

    public function testCreation()
    {
        $gateway = Omnipay::create('Vindicia_PayPal');
        $this->assertInstanceOf('Omnipay\Vindicia\PayPalGateway', $gateway);
    }

    public function testUsername()
    {
        $username = $this->faker->username();

        $this->assertSame($this->gateway, $this->gateway->setUsername($username));
        $this->assertSame($username, $this->gateway->getUsername());
    }

    public function testPassword()
    {
        $password = $this->faker->password();

        $this->assertSame($this->gateway, $this->gateway->setPassword($password));
        $this->assertSame($password, $this->gateway->getPassword());
    }

    public function testTestMode()
    {
        $testMode = $this->faker->bool();

        $this->assertSame($this->gateway, $this->gateway->setTestMode($testMode));
        $this->assertSame($testMode, $this->gateway->getTestMode());
    }

    public function testPurchase()
    {
        $currency = $this->faker->currency();
        $amount = $this->faker->monetaryAmount($currency);
        $customerId = $this->faker->customerId();
        $returnUrl = $this->faker->url();
        $cancelUrl = $this->faker->url();

        $request = $this->gateway->purchase(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'customerId' => $customerId,
                'returnUrl' => $returnUrl,
                'cancelUrl' => $cancelUrl
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\PayPalPurchaseRequest', $request);
        $this->assertSame($amount, $request->getAmount());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($returnUrl, $request->getReturnUrl());
        $this->assertSame($cancelUrl, $request->getCancelUrl());
    }

    public function testCompletePurchase()
    {
        $success = $this->faker->bool();
        $payPalTransactionReference = $this->faker->payPalTransactionReference();

        $request = $this->gateway->completePurchase(
            array(
                'success' => $success,
                'payPalTransactionReference' => $payPalTransactionReference
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CompletePayPalPurchaseRequest', $request);
        $this->assertSame($success, $request->getSuccess());
        $this->assertSame($payPalTransactionReference, $request->getPayPalTransactionReference());
    }

    public function testCreateSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $planId = $this->faker->planId();
        $customerId = $this->faker->customerId();
        $productId = $this->faker->productId();
        $currency = $this->faker->currency();
        $statementDescriptor = $this->faker->statementDescriptor();
        $ip = $this->faker->ipAddress();
        $startTime = $this->faker->timestamp();
        $paymentMethodId = $this->faker->paymentMethodId();
        $returnUrl = $this->faker->url();
        $cancelUrl = $this->faker->url();

        $request = $this->gateway->createSubscription(
            array(
                'planId' => $planId,
                'subscriptionId' => $subscriptionId,
                'productId' => $productId,
                'customerId' => $customerId,
                'statementDescriptor' => $statementDescriptor,
                'currency' => $currency,
                'ip' => $ip,
                'startTime' => $startTime,
                'paymentMethodId' => $paymentMethodId,
                'returnUrl' => $returnUrl,
                'cancelUrl' => $cancelUrl
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePayPalSubscriptionRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($ip, $request->getIp());
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
        $this->assertSame($returnUrl, $request->getReturnUrl());
        $this->assertSame($cancelUrl, $request->getCancelUrl());
    }
}
