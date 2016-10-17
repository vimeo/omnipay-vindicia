<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\CancelSubscriptionsRequest;
use Omnipay\VindiciaTest\SoapTestCase;
use stdClass;

class CancelSubscriptionsRequestTest extends SoapTestCase
{
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

        $this->request = new CancelSubscriptionsRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'subscriptionIds' => $this->subscriptionIds
            )
        );

        $this->subscriptionReferences = array();
        for ($i = 0; $i < $this->numSubscriptions; $i++) {
            $this->subscriptionReferences[] = $this->faker->subscriptionReference();
        }
    }

    public function testSubscriptionIds()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionIds($this->subscriptionIds));
        $this->assertSame($this->subscriptionIds, $request->getSubscriptionIds());
    }

    public function testSubscriptionReferences()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSubscriptionReferences($this->subscriptionReferences));
        $this->assertSame($this->subscriptionReferences, $request->getSubscriptionReferences());
    }

    public function testCustomerId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerId($this->customerId));
        $this->assertSame($this->customerId, $request->getCustomerId());
    }

    public function testCustomerReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\CancelSubscriptionsRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCustomerReference($this->customerReference));
        $this->assertSame($this->customerReference, $request->getCustomerReference());
    }

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
        $this->assertNull($data['cancelReasonCode']);
    }

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
        $this->assertNull($data['cancelReasonCode']);
    }

    /**
     * When no subscriptions are specified, all of the customer's subscriptions are canceled
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
        $this->assertNull($data['cancelReasonCode']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the customerId or customerReference parameter is required.
     */
    public function testCustomerIdOrReferenceRequired()
    {
        $this->request->setCustomerId(null);
        $this->request->setCustomerReference(null);
        $this->request->getData();
    }

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
