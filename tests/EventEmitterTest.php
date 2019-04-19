<?php

namespace Omnipay\Vindicia;

use Guzzle\Common\HasDispatcherInterface;
use Guzzle\Http\Client;
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Vindicia\Message\AuthorizeRequest;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\TestFramework\TestSubscriber;
use PaymentGatewayLogger\Event\Constants;
use PaymentGatewayLogger\Event\ErrorEvent;
use PaymentGatewayLogger\Event\RequestEvent;
use PaymentGatewayLogger\Event\ResponseEvent;

class EventEmitterTest extends SoapTestCase
{
    /** @var HasDispatcherInterface */
    protected $customHttpClient;

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
        $this->customerReference = $this->faker->customerReference();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->riskScore = $this->faker->riskScore();
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->attributes = $this->faker->attributesAsArray();
        $this->taxClassification = $this->faker->taxClassification();
        $this->minChargebackProbability = $this->faker->chargebackProbability();

        $this->customHttpClient = new Client('', array('redirect.disable' => true));
        $this->customHttpClient->getEventDispatcher()->addSubscriber(new TestSubscriber());

        $this->request = new AuthorizeRequest($this->customHttpClient, $this->getHttpRequest());
        $this->request->initialize(
            array(
                'amount' => $this->amount,
                'currency' => $this->currency,
                'card' => $this->card,
                'paymentMethodId' => $this->paymentMethodId,
                'paymentMethodReference' => $this->paymentMethodReference,
                'customerId' => $this->customerId,
                'customerReference' => $this->customerReference,
                'name' => $this->name,
                'email' => $this->email,
                'statementDescriptor' => $this->statementDescriptor,
                'ip' => $this->ip,
                'attributes' => $this->attributes,
                'taxClassification' => $this->taxClassification,
                'minChargebackProbability' => $this->minChargebackProbability
            )
        );
    }

    /**
     * #ret
     */
    public function testSuccessfulResponseEmitted()
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
        $this->customHttpClient
            ->getEventDispatcher()
            ->addListener(Constants::OMNIPAY_REQUEST_BEFORE_SEND, function(RequestEvent $event) use ($class) {
                $request = $event['request'];
                $class->assertInstanceOf('\Omnipay\Vindicia\Message\AuthorizeRequest', $request);
            }
        );

        $this->customHttpClient
            ->getEventDispatcher()
            ->addListener(Constants::OMNIPAY_RESPONSE_SUCCESS, function(ResponseEvent $event) use ($class) {
                $response = $event['response'];
                $class->assertInstanceOf('\Omnipay\Vindicia\Message\Response', $response);
        });

        $response = $this->request->send();
        $this->assertTrue($response->isSuccessful());
    }

    /**
     */
    public function testErrorEventEmitted()
    {
        $this->setMockSoapResponse('AuthorizeFailure.xml');

        $class = $this;
        $this->customHttpClient
            ->getEventDispatcher()
            ->addListener(Constants::OMNIPAY_REQUEST_BEFORE_SEND, function(RequestEvent $event) use ($class) {
                $request = $event['request'];
                $class->assertInstanceOf('\Omnipay\Vindicia\Message\AuthorizeRequest', $request);
            }
        );

        $this->customHttpClient
            ->getEventDispatcher()
            ->addListener(Constants::OMNIPAY_REQUEST_ERROR, function(ErrorEvent $event) use ($class) {
                $error = $event['error'];
                $class->assertInstanceOf('\Exception', $error);
            }
        );

        $response = $this->request->send();
        $this->assertFalse($response->isSuccessful());
    }
}
