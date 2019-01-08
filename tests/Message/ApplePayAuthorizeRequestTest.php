<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ApplePayAuthorizeRequestTest extends TestCase
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
        $this->validationUrl = $this->faker->url();
        $this->merchantIdentifier = $this->faker->transactionId();
        $this->displayName = $this->faker->username();
        $this->applicationUrl = $this->faker->url();

        $this->request = new ApplePayAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'pemCertPath' => $this->pemCertPath,
                'keyCertPath' => $this->keyCertPath,
                'keyCertPassword' => $this->keyCertPassword,
                'validationUrl' => $this->validationUrl,
                'merchantIdentifier' => $this->merchantIdentifier,
                'displayName' => $this->displayName,
                'applicationUrl' => $this->applicationUrl
            )
        );
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame($this->merchantIdentifier, $data['merchantIdentifier']);
        $this->assertSame($this->displayName, $data['displayName']);
        $this->assertSame('web', $data['initiative']);
        $this->assertSame($this->applicationUrl, $data['initiativeContext']);
    }

    /**
     * @return void
     */
    public function testValidationUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setValidationUrl($this->validationUrl));
        $this->assertEquals($this->validationUrl, $request->getValidationUrl());
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The validationUrl parameter is required
     * @return                   void
     */
    public function testValidationUrlRequired()
    {
        $this->request->setValidationUrl(null);
        $this->request->getData();
    }

    /**
     * @return void
     */
    public function testMerchantIdentifier()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setMerchantIdentifier($this->merchantIdentifier));
        $this->assertSame($this->merchantIdentifier, $request->getMerchantIdentifier());
    }

    /**
     * @return void
     */
    public function testDisplayName()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setDisplayName($this->displayName));
        $this->assertSame($this->displayName, $request->getDisplayName());
    }

    /**
     * @return void
     */
    public function testApplicationUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setApplicationUrl($this->applicationUrl));
        $this->assertSame($this->applicationUrl, $request->getApplicationUrl());
    }

    /**
     * @return void
     */
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestSuccess.txt');
        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame('200', $response->getCode());
    }

    /**
     * @return void
     */
    public function testSendFailure()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Not Found', $response->getMessage());
    }

    /**
     * @return void
     */
    public function testGetPaymentSessionObjectFailure()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');
        $response = $this->request->send();

        $this->assertEmpty(
            $response->getPaymentSessionObject()
        );
    }

    /**
     * @return void
     */
    public function testGetPaymentSessionObjectSuccess()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestSuccess.txt');
        $response = $this->request->send();
        
        $this->assertJsonStringEqualsJsonString(
            json_encode(
                array(
                    'epochTimestamp' => 1234567890,
                    'expiresAt' => 123456789000,
                    'merchantSessionIdentifier' => 'SSH1234567890',
                    'nonce' => 'd12345',
                    'merchantIdentifier' => 'SSHDC1234567890IJK',
                    'domainName' => 'abcd.com',
                    'displayName' => 'Abcd',
                    'signature' => '1234567890'
                )
            ),
            $response->getPaymentSessionObject()
        );
    }
}
