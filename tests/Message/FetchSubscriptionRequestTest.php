<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class FetchSubscriptionRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->subscriptionId = $this->faker->subscriptionId();

        $this->request = new FetchSubscriptionRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'subscriptionId' => $this->subscriptionId
            )
        );

        $this->subscriptionReference = $this->faker->subscriptionReference();
        $this->currency = $this->faker->currency();
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->productId = $this->faker->productId();
        $this->sku = $this->productId;
        $this->productReference = $this->faker->productReference();
        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
        $this->ipAddress = $this->faker->ipAddress();
        $this->startTime = $this->faker->timestamp();
        $this->endTime = $this->faker->timestamp();
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionId($this->subscriptionId));
        $this->assertSame($this->subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReference($this->subscriptionReference));
        $this->assertSame($this->subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->subscriptionId, $data['merchantAutoBillId']);
        $this->assertSame('fetchByMerchantAutoBillId', $data['action']);
    }

    /**
     * @return void
     */
    public function testGetDataByReference()
    {
        $this->request->setSubscriptionId(null)->setSubscriptionReference($this->subscriptionReference);

        $data = $this->request->getData();

        $this->assertSame($this->subscriptionReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the subscriptionId or subscriptionReference parameter is required.
     * @return                   void
     */
    public function testSubscriptionIdOrReferenceRequired()
    {
        $this->request->setSubscriptionId(null);
        $this->request->setSubscriptionReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionSuccess.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference,
            'CURRENCY' => $this->currency,
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference,
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'PAYMENT_METHOD_REFERENCE' => $this->paymentMethodReference,
            'PRODUCT_ID' => $this->productId,
            'SKU' => $this->sku,
            'PRODUCT_REFERENCE' => $this->productReference,
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference,
            'IP_ADDRESS' => $this->ipAddress,
            'START_TIMESTAMP' => $this->startTime,
            'END_TIMESTAMP' => $this->endTime
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $subscription = $response->getSubscription();
        $this->assertInstanceOf('\Omnipay\Vindicia\Subscription', $subscription);
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());
        $this->assertSame($this->subscriptionId, $subscription->getId());
        $this->assertSame($this->subscriptionReference, $subscription->getReference());
        $this->assertSame($this->currency, $subscription->getCurrency());
        $this->assertSame($this->startTime, $subscription->getStartTime());
        $this->assertSame($this->endTime, $subscription->getEndTime());
        $customer = $subscription->getCustomer();
        $this->assertSame($this->customerId, $subscription->getCustomerId());
        $this->assertSame($this->customerId, $customer->getId());
        $this->assertSame($this->customerReference, $subscription->getCustomerReference());
        $this->assertSame($this->customerReference, $customer->getReference());
        $paymentMethod = $subscription->getPaymentMethod();
        $this->assertSame($this->paymentMethodId, $subscription->getPaymentMethodId());
        $this->assertSame($this->paymentMethodId, $paymentMethod->getId());
        $this->assertSame($this->paymentMethodReference, $subscription->getPaymentMethodReference());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getReference());
        $product = $subscription->getProduct();
        $this->assertSame($this->productId, $subscription->getProductId());
        $this->assertSame($this->productId, $product->getId());
        $this->assertSame($this->productReference, $subscription->getProductReference());
        $this->assertSame($this->productReference, $product->getReference());
        $plan = $subscription->getPlan();
        $this->assertSame($this->planId, $subscription->getPlanId());
        $this->assertSame($this->planId, $plan->getId());
        $this->assertSame($this->planReference, $subscription->getPlanReference());
        $this->assertSame($this->planReference, $plan->getReference());
        $items = $subscription->getItems();
        $this->assertSame(1, count($items));
        foreach ($items as $i => $item) {
            $this->assertInstanceOf('\Omnipay\Vindicia\VindiciaItem', $item);
            $this->assertSame($this->sku, $item->getSku());
        }
        $this->assertSame($this->ipAddress, $subscription->getIp());
        $attributes = $subscription->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
        }

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchSubscriptionByReferenceSuccess.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId,
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getSubscription());
        $this->assertSame($this->subscriptionId, $response->getSubscriptionId());
        $this->assertSame($this->subscriptionReference, $response->getSubscriptionReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/AutoBill.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionFailure.xml', array(
            'SUBSCRIPTION_ID' => $this->subscriptionId
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to find autobill by serial number ' . $this->subscriptionId . ': ', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }

    /**
     * @return void
     */
    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchSubscriptionByReferenceFailure.xml', array(
            'SUBSCRIPTION_REFERENCE' => $this->subscriptionReference
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load autobill by VID "' . $this->subscriptionReference . '":  .', $response->getMessage());

        $this->assertNull($response->getPaymentMethod());
    }
}
