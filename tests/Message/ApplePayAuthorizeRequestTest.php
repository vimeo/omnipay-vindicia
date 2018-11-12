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
        $this->faker = new DataFaker();
        $this->pemCertPath = $this->faker->password();
        $this->keyCertPath = $this->faker->password();
        $this->keyCertPassword = $this->faker->password();

        $this->validationUrl = $this->faker->url();
        $this->merchantIdentifier = $this->faker->transactionId();
        $this->displayName = $this->faker->username();
        $this->applicationType = $this->faker->applePayApplicationType();
        $this->applicationUrl = $this->faker->domainName();

        $this->request = new ApplePayAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'pemCertPath' => $this->pemCertPath,
                'keyCertPath' => $this->keyCertPath,
                'keyCertPassword' => $this->keyCertPassword,
                'validationUrl' => $this->validationUrl,
                'merchantIdentifier' => $this->merchantIdentifier,
                'displayName' => $this->displayName,
                'applicationType' => $this->applicationType,
                'applicationUrl' => $this->applicationUrl
            )
        );
    }

    public function testGetData()
    {
        $data = $this->request->getData();
        $this->assertSame($this->merchantIdentifier, $data['merchantIdentifier']);
        $this->assertSame($this->displayName, $data['displayName']);
        $this->assertSame($this->applicationType, $data['initiative']);
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
    public function testApplicationType()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setApplicationType($this->applicationType));
        $this->assertSame($this->applicationType, $request->getApplicationType());
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
        $this->assertSame('OK', $response->getReason());
        $this->assertSame(200, $response->getStatusCode());
    }

   /**
	* @return void
	*/
    public function testSendFailure()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Not Found', $response->getReason());
    }
}
