<?php

namespace Omnipay\Vindicia;

use Exception;
use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\TestFramework\TestSubscriber;
use PaymentGatewayLogger\Event\Constants;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;
use PaymentGatewayLogger\Test\Framework\TestLogger;
use SoapFault;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventEmitterTest extends SoapTestCase
{
    /** @var ClientInterface */
    protected $customHttpClient;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var TestSubscriber */
    protected $testSubscriber;

    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');

        $this->faker = new DataFaker();

        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->riskScore = $this->faker->riskScore();

        $this->customHttpClient = new Client('', array('redirect.disable' => true));
        $this->eventDispatcher = $this->customHttpClient->getEventDispatcher();

        $this->testSubscriber = new TestSubscriber($this->faker->name(), new TestLogger());
        $this->eventDispatcher->addSubscriber($this->testSubscriber);
    }

    /**
     * Ensures that 'Request' and 'Response' events are emitted when issuing a request.
     *
     * @return void
     */
    public function testAuthorizeRequestSuccessfulResponseEmitted()
    {
        $this->setMockSoapResponse('AuthorizeSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'AMOUNT' => $this->amount,
            'CUSTOMER_ID' => $this->customerId,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'CVV' => $this->card['cvv'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'RISK_SCORE' => $this->riskScore
        ));

        $class = $this;
        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_REQUEST_BEFORE_SEND,
                /** @return void */
                function (RequestEvent $event) use ($class) {
                    $request = $event['request'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\AuthorizeRequest', $request);
                }
            );

        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_RESPONSE_SUCCESS,
                /** @return void */
                function (ResponseEvent $event) use ($class) {
                    $response = $event['response'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\Response', $response);
                }
            );

        $gateway = new Gateway($this->customHttpClient, $this->getHttpRequest());
        $gateway->setTestMode(true);
        $request = $gateway->authorize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'customerId' => $this->customerId
            )
        );
        $response = $request->send();
        $this->assertTrue($response->isSuccessful());

        $eventsDispatched = $this->testSubscriber->eventsDispatched;
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_BEFORE_SEND]);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_RESPONSE_SUCCESS]);
        $this->assertArrayNotHasKey(Constants::OMNIPAY_REQUEST_ERROR, $eventsDispatched);
    }

    /**
     * Ensures that 'Request' and 'Error' events are emitted when issuing an improper request.
     *
     * @psalm-suppress UndefinedMethod
     * @return void
     */
    public function testAuthorizeRequestErrorEventEmitted()
    {
        $class = $this;
        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_REQUEST_BEFORE_SEND,
                /** @return void */
                function (RequestEvent $event) use ($class) {
                    $request = $event['request'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\AuthorizeRequest', $request);
                }
            );

        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_REQUEST_ERROR,
                /** @return void */
                function (ErrorEvent $event) use ($class) {
                    $error = $event['error'];
                    $class->assertInstanceOf('\SoapFault', $error);
                }
            );

        // By mocking the 'getObject' method to return an invalid object value, we will cause the SOAP client to throw
        // a SoapFault exception.
        $mockRequest = $this->getMock(
            '\Omnipay\Vindicia\Message\AuthorizeRequest',
            array('getObject'),
            array($this->customHttpClient, $this->getHttpRequest())
        );

        $mockRequest->method('getObject')->willReturn('invalid_object');
        $mockRequest->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'customerId' => $this->customerId
            )
        );

        $eventsDispatched  = array();
        try {
            $mockRequest->send();
        } catch (SoapFault $exception) {
            // We want to resume program execution to check $eventsDispatched.
            $eventsDispatched = $this->testSubscriber->eventsDispatched;
        }

        // A SoapFault exception will always be expected. Therefore $eventsDispatched should never be empty.
        $this->assertNotEmpty($eventsDispatched);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_BEFORE_SEND]);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_ERROR]);
        $this->assertArrayNotHasKey(Constants::OMNIPAY_RESPONSE_SUCCESS, $eventsDispatched);
    }

    /**
     * @return void
     */
    public function testAppleAuthorizeRequestSuccessfulResponseEmitted()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestSuccess.txt');

        $class = $this;
        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_REQUEST_BEFORE_SEND,
                /** @return void */
                function (RequestEvent $event) use ($class) {
                    $request = $event['request'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest', $request);
                }
            );

        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_RESPONSE_SUCCESS,
                /** @return void */
                function (ResponseEvent $event) use ($class) {
                    $response = $event['response'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\Response', $response);
                }
            );

        $gateway = new ApplePayGateway($this->customHttpClient, $this->getHttpRequest());
        $gateway->setTestMode(true);

        $request = $gateway->authorize(
            array(
                'validationURL' => $this->faker->url(),
                'merchantIdentifier' => $this->faker->transactionId(),
                'displayName' => $this->faker->username(),
                'applicationUrl' => $this->faker->url()
            )
        );
        $request->send();

        $eventsDispatched = $this->testSubscriber->eventsDispatched;
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_BEFORE_SEND]);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_RESPONSE_SUCCESS]);
        $this->assertArrayNotHasKey(Constants::OMNIPAY_REQUEST_ERROR, $eventsDispatched);
    }

    /**
     * @psalm-suppress UndefinedMethod
     * @return void
     */
    public function testAppleAuthorizeRequestErrorEmitted()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');

        $class = $this;
        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_REQUEST_BEFORE_SEND,
                /** @return void */
                function (RequestEvent $event) use ($class) {
                    $request = $event['request'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest', $request);
                }
            );

        $this->eventDispatcher
            ->addListener(
                Constants::OMNIPAY_RESPONSE_SUCCESS,
                /** @return void */
                function (ResponseEvent $event) use ($class) {
                    $response = $event['response'];
                    $class->assertInstanceOf('\Omnipay\Vindicia\Message\Response', $response);
                }
            );

        // By mocking the 'getObject' method to return an invalid object value, we will cause the SOAP client to throw
        // a SoapFault exception.
        $mockRequest = $this->getMock(
            '\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest',
            array('getValidationUrl'),
            array($this->customHttpClient, $this->getHttpRequest())
        );

        $mockRequest->method('getValidationUrl')->willReturn('invalid_url');
        $mockRequest->initialize(
            array(
                'pemCertPath' => $this->faker->path(),
                'keyCertPath' => $this->faker->path(),
                'keyCertPassword' => $this->faker->password(),
                'validationUrl' => $this->faker->url(),
                'merchantIdentifier' => $this->faker->transactionId(),
                'displayName' => $this->faker->username(),
                'applicationUrl' => $this->faker->url()
            )
        );

        $eventsDispatched = array();
        try {
            $mockRequest->send();
        } catch (Exception $exception) {
            $eventsDispatched = $this->testSubscriber->eventsDispatched;
        }

        // An exception will always be expected. Therefore $eventsDispatched should never be empty.
        $this->assertNotEmpty($eventsDispatched);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_BEFORE_SEND]);
        $this->assertEquals(1, $eventsDispatched[Constants::OMNIPAY_REQUEST_ERROR]);
        $this->assertArrayNotHasKey(Constants::OMNIPAY_RESPONSE_SUCCESS, $eventsDispatched);
    }
}
