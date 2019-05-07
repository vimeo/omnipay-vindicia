<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ApplePayCompleteAuthorizeRequestTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        // Gateway parameters.
        $this->faker = new DataFaker();
        $this->pemCertPath = $this->faker->path();
        $this->keyCertPath = $this->faker->path();
        $this->keyCertPassword = $this->faker->password();

        // Request parameters.
        $this->token = $this->faker->applePayPaymentToken();

        $this->request = new ApplePayCompleteAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'token' => $this->token
            )
        );
    }

    /**
     * @return void
     */
    public function testToken()
    {
        $data = $this->request->getToken();
        $this->assertSame($this->token, $data);
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $this->request->setToken($this->token);
        $data = $this->request->getData();

        $this->assertSame($this->token, $data['token']);
    }
}
