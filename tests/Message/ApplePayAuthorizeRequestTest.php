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

        $this->request = new ApplePayAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'validationURL' => $this->faker->url()
            )
        );

        $this->validationURL = $this->faker->url();

        $this->epochTimestamp = $this->faker->timestamp();
        $this->expiresAt = $this->faker->timestamp();
        $this->merchantSessionIdentifier = $this->faker->applePayMerchantSessionID();
        $this->nonce = $this->faker->applePayNonceToken();
        $this->merchantIdentifier = $this->faker->transactionId();
        $this->domainName = $this->faker->domainName();
        $this->displayName = $this->faker->username();
        $this->signature = $this->faker->applePaySignature();
    }

    /**
     * @return void
     */
    public function testValidationUrl()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setValidationURL($this->validationURL));
        $this->assertEquals($this->validationURL, $request->getValidationURL());
    }

    /**
     * @return void
     */
    public function testEpochTimestamp()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTimeStamp($this->epochTimestamp));
        $this->assertSame($this->epochTimestamp, $request->getTimeStamp($this->epochTimestamp));
    }

    /**
     * @return void
     */
    public function testExpiresAt()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setExpirationTimeStamp($this->expiresAt));
        $this->assertSame($this->expiresAt, $request->getExpirationTimeStamp());
    }

    /**
     * @return void
     */
    public function testMerchantSessionID()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setMerchantSessionID($this->merchantSessionIdentifier));
        $this->assertSame($this->merchantSessionIdentifier, $request->getMerchantSessionID());
    }

    /**
     * @return void
     */
    public function testNonceToken()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setNonceToken($this->nonce));
        $this->assertSame($this->nonce, $request->getNonceToken());
    }


    /**
     * @return void
     */
    public function testMerchantID()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setMerchantID($this->merchantIdentifier));
        $this->assertSame($this->merchantIdentifier, $request->getMerchantID());
    }

    /**
     * @return void
     */
    public function testDomainName()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setDomainName($this->domainName));
        $this->assertSame($this->domainName, $request->getDomainName());
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
    public function testSignatureID()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\ApplePayAuthorizeRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setSignatureID($this->signature));
        $this->assertSame($this->signature, $request->getSignatureID());
    }

    /**
     * @expectedException        \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The validationURL parameter is required
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
    public function testSendSuccess()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestSuccess.txt');
        $response = $this->request->send();

        $this->assertSame(1234567890, $response->getApplePaySessionTimeStamp());
        $this->assertSame(123456789000, $response->getApplePaySessionExpirationTimeStamp());
        $this->assertSame('SSHDC1234567890IJK', $response->getApplePaySessionMerchantID());
        $this->assertSame('d12345', $response->getApplePaySessionNonceToken());
        $this->assertSame('SSH1234567890', $response->getApplePaySessionMerchantSessionID());
        $this->assertSame('abcd.com', $response->getApplePaySessionDomainName());
        $this->assertSame('Abcd', $response->getApplePaySessionDisplayName());
        $this->assertSame('1234567890', $response->getApplePaySessionSignature());

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame(200, $response->getCode());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(400, $response->getCode());
        $this->assertSame('Not Found', $response->getMessage());
    }
}
