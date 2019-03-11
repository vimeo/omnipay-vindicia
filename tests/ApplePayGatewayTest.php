<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Omnipay;

class ApplePayGatewayTest extends GatewayTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->gateway = new ApplePayGateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->faker = new DataFaker();

        $this->pemCertPath = $this->faker->path();
        $this->keyCertPath = $this->faker->path();
        $this->keyCertPassword = $this->faker->password();

        $this->validationUrl = $this->faker->url();
        $this->merchantIdentifier = $this->faker->transactionId();
        $this->displayName = $this->faker->username();
        $this->applicationUrl = $this->faker->url();
    }

    /**
     * @return void
     */
    public function testGetName()
    {
        $this->assertSame('Vindicia ApplePay', $this->gateway->getName());
    }
    
    /**
     * @return void
     */
    public function testCreation()
    {
        $gateway = Omnipay::create('Vindicia_ApplePay');
        $this->assertInstanceOf('Omnipay\Vindicia\ApplePayGateway', $gateway);
    }

    /**
     * @return void
     */
    public function testUsername()
    {
        $username = $this->faker->username();

        $this->assertSame($this->gateway, $this->gateway->setUsername($username));
        $this->assertSame($username, $this->gateway->getUsername());
    }

    /**
     * @return void
     */
    public function testPassword()
    {
        $password = $this->faker->password();

        $this->assertSame($this->gateway, $this->gateway->setPassword($password));
        $this->assertSame($password, $this->gateway->getPassword());
    }

    /**
     * @return void
     */
    public function testTestMode()
    {
        $testMode = $this->faker->bool();

        $this->assertSame($this->gateway, $this->gateway->setTestMode($testMode));
        $this->assertSame($testMode, $this->gateway->getTestMode());
    }

    /**
     * @return void
     */
    public function testPemCertPath()
    {
        $this->assertSame($this->gateway, $this->gateway->setPemCertPath($this->pemCertPath));
        $this->assertSame($this->pemCertPath, $this->gateway->getPemCertPath());
    }

    /**
     * @return void
     */
    public function testKeyCertPath()
    {
        $this->assertSame($this->gateway, $this->gateway->setKeyCertPath($this->keyCertPath));
        $this->assertSame($this->keyCertPath, $this->gateway->getKeyCertPath());
    }

    /**
     * @return void
     */
    public function testKeyCertPassword()
    {
        $this->assertSame($this->gateway, $this->gateway->setKeyCertPassword($this->keyCertPassword));
        $this->assertSame($this->keyCertPassword, $this->gateway->getKeyCertPassword());
    }

    /**
     * @return void
     */
    public function testAuthorize()
    {
        $request = $this->gateway->authorize(
            array(
                'validationURL' => $this->validationUrl,
                'merchantIdentifier' => $this->merchantIdentifier,
                'displayName' => $this->displayName,
                'applicationUrl' => $this->applicationUrl
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\ApplePayAuthorizeRequest', $request);
        $this->assertSame($this->validationUrl, $request->getValidationUrl());
        $this->assertSame($this->merchantIdentifier, $request->getMerchantIdentifier());
        $this->assertSame($this->displayName, $request->getDisplayName());
        $this->assertSame($this->applicationUrl, $request->getApplicationUrl());
    }

    /**
     * @return void
     */
    public function testUpdatePaymentMethod()
    {
        $card = $this->faker->card();
        $customerId = $this->faker->customerId();

        $request = $this->gateway->updatePaymentMethod(
            array(
                'card' => $card,
                'customerId' => $customerId
            )
        );

        // update uses create's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $request);
        $this->assertSameCard($card, $request->getCard());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    protected function assertSameCard($card, $requestCard)
    {
        $this->assertSame($card['number'], $requestCard->getNumber());
        $this->assertSame(intval($card['expiryMonth']), $requestCard->getExpiryMonth());
        $this->assertSame(intval($card['expiryYear']), $requestCard->getExpiryYear());
    }
}
