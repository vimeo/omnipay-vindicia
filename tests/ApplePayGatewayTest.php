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
    public function testAuthorize()
    {
        $request = $this->gateway->authorize(
            array(
                'validationURL' => 'https://apple-pay-gateway-nc-pod5.apple.com/paymentservices/startSession',
            )
        );
    }
}
