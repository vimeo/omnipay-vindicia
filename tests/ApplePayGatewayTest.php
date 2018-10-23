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

        $this->validationURL = $this->faker->url();
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
    public function testAuthorize()
    {
        $request = $this->gateway->authorize(
            array(
                'validationURL' => $this->validationURL,
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\ApplePayAuthorizeRequest', $request);
        $this->assertSame($this->validationURL, $request->getValidationUrl());
    }
}
