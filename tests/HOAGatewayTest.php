<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Vindicia\HOAGateway;
use Omnipay\Omnipay;

class HOAGatewayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new HOAGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->faker = new DataFaker();

        $this->returnUrl = $this->faker->url();
        $this->errorUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
    }

    public function testGetName()
    {
        $this->assertSame('Vindicia HOA', $this->gateway->getName());
    }

    public function testCreation()
    {
        $gateway = Omnipay::create('Vindicia_HOA');
        $this->assertInstanceOf('Omnipay\Vindicia\HOAGateway', $gateway);
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

    public function testAuthorize()
    {
        $currency = $this->faker->currency();
        $customerId = $this->faker->customerId();
        $paymentMethodId = $this->faker->paymentMethodId();
        $minChargebackProbability = $this->faker->chargebackProbability();

        $request = $this->gateway->authorize(
            array(
                'currency' => $currency,
                'paymentMethodId' => $paymentMethodId,
                'customerId' => $customerId,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip,
                'minChargebackProbability' => $minChargebackProbability
            )
        );

        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
        $this->assertSame($minChargebackProbability, $request->getMinChargebackProbability());
    }

    public function testPurchase()
    {
        $currency = $this->faker->currency();
        $customerId = $this->faker->customerId();
        $paymentMethodId = $this->faker->paymentMethodId();
        $minChargebackProbability = $this->faker->chargebackProbability();

        $request = $this->gateway->purchase(
            array(
                'currency' => $currency,
                'paymentMethodId' => $paymentMethodId,
                'customerId' => $customerId,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip,
                'minChargebackProbability' => $minChargebackProbability
            )
        );

        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
        $this->assertSame($minChargebackProbability, $request->getMinChargebackProbability());
    }

    public function testCreatePaymentMethod()
    {
        $customerId = $this->faker->customerId();

        $request = $this->gateway->createPaymentMethod(
            array(
                'customerId' => $customerId,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testUpdatePaymentMethod()
    {
        $customerId = $this->faker->customerId();

        $request = $this->gateway->updatePaymentMethod(
            array(
                'customerId' => $customerId,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\HOACreatePaymentMethodRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testCreateSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $planId = $this->faker->planId();
        $customerId = $this->faker->customerId();
        $productId = $this->faker->productId();
        $currency = $this->faker->currency();
        $statementDescriptor = $this->faker->statementDescriptor();
        $startTime = $this->faker->timestamp();

        $request = $this->gateway->createSubscription(
            array(
                'planId' => $planId,
                'subscriptionId' => $subscriptionId,
                'productId' => $productId,
                'customerId' => $customerId,
                'statementDescriptor' => $statementDescriptor,
                'currency' => $currency,
                'startTime' => $startTime,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testUpdateSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $planId = $this->faker->planId();
        $customerId = $this->faker->customerId();
        $productId = $this->faker->productId();
        $currency = $this->faker->currency();
        $statementDescriptor = $this->faker->statementDescriptor();
        $startTime = $this->faker->timestamp();

        $request = $this->gateway->updateSubscription(
            array(
                'planId' => $planId,
                'subscriptionId' => $subscriptionId,
                'productId' => $productId,
                'customerId' => $customerId,
                'statementDescriptor' => $statementDescriptor,
                'currency' => $currency,
                'startTime' => $startTime,
                'returnUrl' => $this->returnUrl,
                'errorUrl' => $this->errorUrl,
                'ip' => $this->ip
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\HOACreateSubscriptionRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($this->returnUrl, $request->getReturnUrl());
        $this->assertSame($this->errorUrl, $request->getErrorUrl());
        $this->assertSame($this->ip, $request->getIp());
    }

    public function testComplete()
    {
        $webSessionReference = $this->faker->webSessionReference();

        $request = $this->gateway->complete(
            array(
                'webSessionReference' => $webSessionReference
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CompleteHOARequest', $request);
        $this->assertSame($webSessionReference, $request->getWebSessionReference());
    }
}
