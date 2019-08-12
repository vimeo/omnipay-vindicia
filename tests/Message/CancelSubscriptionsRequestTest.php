<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use stdClass;

class CancelSubscriptionsRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->numSubscriptions = $this->faker->intBetween(1, 5);
        $this->subscriptionIds = array();
        for ($i = 0; $i < $this->numSubscriptions; $i++) {
            $this->subscriptionIds[] = $this->faker->subscriptionId();
        }
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->cancelReason = $this->faker->subscriptionCancelReason();

        $this->request = new CancelSubscriptionsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'subscriptionIds' => $this->subscriptionIds,
                'cancelReason' => $this->cancelReason
            )
        );

        $this->subscriptionReferences = array();
        for ($i = 0; $i < $this->numSubscriptions; $i++) {
            $this->subscriptionReferences[] = $this->faker->subscriptionReference();
        }
    }

    /**
     * @return void
     */
    public function testSubscriptionIds()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionIds($this->subscriptionIds));
        $this->assertSame($this->subscriptionIds, $request->getSubscriptionIds());
    }

    /**
     * @return void
     */
    public function testSubscriptionReferences()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReferences($this->subscriptionReferences));
        $this->assertSame($this->subscriptionReferences, $request->getSubscriptionReferences());
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testCancelReason()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCancelReason($this->cancelReason));
        $this->assertSame($this->cancelReason, $request->getCancelReason());
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->numSubscriptions, count($data['autobills']));
        foreach ($this->subscriptionIds as $subscriptionId) {
            $subscription = new stdClass();
            $subscription->merchantAutoBillId = $subscriptionId;
            $this->assertTrue(in_array($subscription, $data['autobills']));
        }

        $this->assertSame('stopAutoBilling', $data['action']);
        $this->assertFalse($data['disentitle']);
        $this->assertTrue($data['force']);
        $this->assertSame($this->cancelReason, $data['cancelReasonCode']);
    }

    /**
     * @return void
     */
    public function testGetDataBySubscriptionReference()
    {
        $this->request->setSubscriptionIds(null)->setSubscriptionReferences($this->subscriptionReferences);

        $data = $this->request->getData();
        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertSame($this->numSubscriptions, count($data['autobills']));
        foreach ($this->subscriptionReferences as $subscriptionReference) {
            $subscription = new stdClass();
            $subscription->VID = $subscriptionReference;
            $this->assertTrue(in_array($subscription, $data['autobills']));
        }

        $this->assertSame('stopAutoBilling', $data['action']);
        $this->assertFalse($data['disentitle']);
        $this->assertTrue($data['force']);
        $this->assertSame($this->cancelReason, $data['cancelReasonCode']);
    }

    /**
     * When no subscriptions are specified, all of the customer's subscriptions are canceled
     *
     * @return void
     */
    public function testGetDataNoSubscriptions()
    {
        $this->request->setSubscriptionIds(null);
        $data = $this->request->getData();

        $this->assertSame($this->customerId, $data['account']->merchantAccountId);
        $this->assertFalse(isset($data['subscriptions']));

        $this->assertSame('stopAutoBilling', $data['action']);
        $this->assertFalse($data['disentitle']);
        $this->assertTrue($data['force']);
        $this->assertSame($this->cancelReason, $data['cancelReasonCode']);
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     * @return                   void
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockSoapResponse('CancelSubscriptionsSuccess.xml', array(
            'CUSTOMER_ID' => $this->customerId,
            'CUSTOMER_REFERENCE' => $this->customerReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->customerId, $response->getCustomerId());
        $this->assertSame($this->customerReference, $response->getCustomerReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Account.wsdl', $this->getLastEndpoint());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockSoapResponse('CancelSubscriptionsFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        // why does Vindicia return OK here? beats me
        $this->assertSame('OK', $response->getMessage());

        $this->assertNull($response->getCustomerId());
    }
}
