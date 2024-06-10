<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ApplePayCompleteAuthorizeRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        // Gateway parameters.
        $this->faker = new DataFaker();
        $this->pemCertPath = $this->faker->path();
        $this->keyCertPath = $this->faker->path();
        $this->keyCertPassword = $this->faker->password();

        // Request parameters.
        // Apple Pay Payment token.
        $this->token = $this->faker->applePayToken();
        // Params needed to initialize AuthorizeRequest.
        $this->currency = $this->faker->currency();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->customerId = $this->faker->customerId();
        $this->customerReference = $this->faker->customerReference();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
        $this->statementDescriptor = $this->faker->statementDescriptor();
        $this->ip = $this->faker->ipAddress();
        $this->attributes = $this->faker->attributesAsArray();
        $this->taxClassification = $this->faker->taxClassification();
        $this->minChargebackProbability = $this->faker->chargebackProbability();
        $this->name = $this->faker->name();
        $this->email = $this->faker->email();

        $this->request = new ApplePayCompleteAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'applePayToken' => $this->token,
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
     * @return void
     */
    public function testApplePayToken()
    {
        $data = $this->request->getApplePayToken();
        $this->assertSame($this->token, $data);
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $this->request->setApplePayToken($this->token);
        $data = $this->request->getData();

        $this->assertSame($this->token['paymentMethod']['displayName'], $data['transaction']->sourcePaymentMethod->applePay->paymentInstrumentName);
        $this->assertSame($this->token['paymentMethod']['network'], $data['transaction']->sourcePaymentMethod->applePay->paymentNetwork);
        $this->assertSame($this->token['transactionIdentifier'], $data['transaction']->sourcePaymentMethod->applePay->transactionIdentifier);
        $this->assertSame(json_encode($this->token['paymentData']), $data['transaction']->sourcePaymentMethod->applePay->paymentData);
    }
}
