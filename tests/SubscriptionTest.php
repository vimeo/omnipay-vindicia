<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class SubscriptionTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->subscription = new Subscription();
        $this->id = $this->faker->subscriptionId();
        $this->reference = $this->faker->subscriptionReference();
    }

    public function testConstructWithParams()
    {
        $subscription = new Subscription(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $subscription->getId());
        $this->assertSame($this->reference, $subscription->getReference());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->subscription, $this->subscription->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->subscription->getId());
        $this->assertSame($this->reference, $this->subscription->getReference());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->subscription, $this->subscription->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->subscription->getParameters());
    }

    public function testId()
    {
        $this->assertSame($this->subscription, $this->subscription->setId($this->id));
        $this->assertSame($this->id, $this->subscription->getId());
    }

    public function testReference()
    {
        $this->assertSame($this->subscription, $this->subscription->setReference($this->reference));
        $this->assertSame($this->reference, $this->subscription->getReference());
    }

    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->subscription, $this->subscription->setCurrency($currency));
        $this->assertSame($currency, $this->subscription->getCurrency());
    }

    public function testCustomer()
    {
        $customer = new Customer();
        $this->assertSame($this->subscription, $this->subscription->setCustomer($customer));
        $this->assertSame($customer, $this->subscription->getCustomer());
    }

    public function testCustomerId()
    {
        $customerId = $this->faker->customerId();
        $this->assertSame($this->subscription, $this->subscription->setCustomerId($customerId));
        $this->assertSame($customerId, $this->subscription->getCustomerId());
    }

    public function testCustomerReference()
    {
        $customerReference = $this->faker->customerReference();
        $this->assertSame($this->subscription, $this->subscription->setCustomerReference($customerReference));
        $this->assertSame($customerReference, $this->subscription->getCustomerReference());
    }

    public function testProduct()
    {
        $product = new Product();
        $this->assertSame($this->subscription, $this->subscription->setProduct($product));
        $this->assertSame($product, $this->subscription->getProduct());
    }

    public function testProductId()
    {
        $productId = $this->faker->productId();
        $this->assertSame($this->subscription, $this->subscription->setProductId($productId));
        $this->assertSame($productId, $this->subscription->getProductId());
    }

    public function testProductReference()
    {
        $productReference = $this->faker->productReference();
        $this->assertSame($this->subscription, $this->subscription->setProductReference($productReference));
        $this->assertSame($productReference, $this->subscription->getProductReference());
    }

    public function testPlan()
    {
        $plan = new Plan();
        $this->assertSame($this->subscription, $this->subscription->setPlan($plan));
        $this->assertSame($plan, $this->subscription->getPlan());
    }

    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->subscription, $this->subscription->setPlanId($planId));
        $this->assertSame($planId, $this->subscription->getPlanId());
    }

    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->subscription, $this->subscription->setPlanReference($planReference));
        $this->assertSame($planReference, $this->subscription->getPlanReference());
    }

    public function testPaymentMethod()
    {
        $paymentMethod = new PaymentMethod();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethod($paymentMethod));
        $this->assertSame($paymentMethod, $this->subscription->getPaymentMethod());
    }

    public function testPaymentMethodId()
    {
        $paymentMethodId = $this->faker->paymentMethodId();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethodId($paymentMethodId));
        $this->assertSame($paymentMethodId, $this->subscription->getPaymentMethodId());
    }

    public function testPaymentMethodReference()
    {
        $paymentMethodReference = $this->faker->paymentMethodReference();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethodReference($paymentMethodReference));
        $this->assertSame($paymentMethodReference, $this->subscription->getPaymentMethodReference());
    }

    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->subscription, $this->subscription->setAttributes($attributes));
        $this->assertSame($attributes, $this->subscription->getAttributes());
    }
}
