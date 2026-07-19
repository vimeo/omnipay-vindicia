<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class SubscriptionTest extends TestCase
{
    /** @var Subscription */
    protected $subscription;

    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->faker = new DataFaker();
        $this->subscription = new Subscription();
        $this->subscriptionId = $this->faker->subscriptionId();
        $this->subscriptionReference = $this->faker->subscriptionReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $subscription = new Subscription(array(
            'subscriptionId' => $this->subscriptionId,
            'subscriptionReference' => $this->subscriptionReference
        ));
        $this->assertSame($this->subscriptionId, $subscription->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $subscription->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->subscription, $this->subscription->initialize(array(
            'subscriptionId' => $this->subscriptionId,
            'subscriptionReference' => $this->subscriptionReference
        )));
        $this->assertSame($this->subscriptionId, $this->subscription->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $this->subscription->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->subscription, $this->subscription->setSubscriptionId($this->subscriptionId)->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame(array('subscriptionId' => $this->subscriptionId, 'subscriptionReference' => $this->subscriptionReference), $this->subscription->getParameters());
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $this->assertSame($this->subscription, $this->subscription->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $this->subscription->getSubscriptionId());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testId()
    {
        $this->assertSame($this->subscription, $this->subscription->setId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $this->subscription->getId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $this->assertSame($this->subscription, $this->subscription->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $this->subscription->getSubscriptionReference());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testReference()
    {
        $this->assertSame($this->subscription, $this->subscription->setReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $this->subscription->getReference());
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $currency = $this->faker->currency();
        $this->assertSame($this->subscription, $this->subscription->setCurrency($currency));
        $this->assertSame($currency, $this->subscription->getCurrency());
    }

    /**
     * @return void
     */
    public function testCustomer()
    {
        $customer = new Customer();
        $this->assertSame($this->subscription, $this->subscription->setCustomer($customer));
        $this->assertSame($customer, $this->subscription->getCustomer());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $customerId = $this->faker->customerId();
        $this->assertSame($this->subscription, $this->subscription->setCustomerId($customerId));
        $this->assertSame($customerId, $this->subscription->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $customerReference = $this->faker->customerReference();
        $this->assertSame($this->subscription, $this->subscription->setCustomerReference($customerReference));
        $this->assertSame($customerReference, $this->subscription->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testProduct()
    {
        $product = new Product();
        $this->assertSame($this->subscription, $this->subscription->setProduct($product));
        $this->assertSame($product, $this->subscription->getProduct());
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $productId = $this->faker->productId();
        $this->assertSame($this->subscription, $this->subscription->setProductId($productId));
        $this->assertSame($productId, $this->subscription->getProductId());
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $productReference = $this->faker->productReference();
        $this->assertSame($this->subscription, $this->subscription->setProductReference($productReference));
        $this->assertSame($productReference, $this->subscription->getProductReference());
    }

    /**
     * @return void
     */
    public function testPlan()
    {
        $plan = new Plan();
        $this->assertSame($this->subscription, $this->subscription->setPlan($plan));
        $this->assertSame($plan, $this->subscription->getPlan());
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $planId = $this->faker->planId();
        $this->assertSame($this->subscription, $this->subscription->setPlanId($planId));
        $this->assertSame($planId, $this->subscription->getPlanId());
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $planReference = $this->faker->planReference();
        $this->assertSame($this->subscription, $this->subscription->setPlanReference($planReference));
        $this->assertSame($planReference, $this->subscription->getPlanReference());
    }

    /**
     * @return void
     */
    public function testPaymentMethod()
    {
        $paymentMethod = new PaymentMethod();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethod($paymentMethod));
        $this->assertSame($paymentMethod, $this->subscription->getPaymentMethod());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $paymentMethodId = $this->faker->paymentMethodId();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethodId($paymentMethodId));
        $this->assertSame($paymentMethodId, $this->subscription->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $paymentMethodReference = $this->faker->paymentMethodReference();
        $this->assertSame($this->subscription, $this->subscription->setPaymentMethodReference($paymentMethodReference));
        $this->assertSame($paymentMethodReference, $this->subscription->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testStartTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->subscription, $this->subscription->setStartTime($time));
        $this->assertSame($time, $this->subscription->getStartTime());
    }

    /**
     * @return void
     */
    public function testEndTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->subscription, $this->subscription->setEndTime($time));
        $this->assertSame($time, $this->subscription->getEndTime());
    }

    /**
     * @return void
     */
    public function testStatus()
    {
        $status = $this->faker->subscriptionStatus();
        $this->assertSame($this->subscription, $this->subscription->setStatus($status));
        $this->assertSame($status, $this->subscription->getStatus());
    }

    /**
     * @return void
     */
    public function testCancelReason()
    {
        $cancelReason = $this->faker->subscriptionCancelReason();
        $this->assertSame($this->subscription, $this->subscription->setCancelReason($cancelReason));
        $this->assertSame($cancelReason, $this->subscription->getCancelReason());
    }

    /**
     * @return void
     */
    public function testBillingDay()
    {
        $billingDay = $this->faker->intBetween(1, 31);
        $this->assertSame($this->subscription, $this->subscription->setBillingDay($billingDay));
        $this->assertSame($billingDay, $this->subscription->getBillingDay());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->subscription, $this->subscription->setAttributes($attributes));
        $this->assertSame($attributes, $this->subscription->getAttributes());
    }
}
