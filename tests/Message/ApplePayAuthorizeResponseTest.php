<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class ApplePayAuthorizeResponseTest extends TestCase
{
   /**
	* @return void
	*/
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->pemCertPath = $this->faker->path();
        $this->keyCertPath = $this->faker->path();
        $this->keyCertPassword = $this->faker->password();

        $this->validationUrl = $this->faker->url();
        $this->merchantIdentifier = $this->faker->transactionId();
        $this->displayName = $this->faker->username();
        $this->applicationType = $this->faker->applePayApplicationType();
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
                'applicationType' => $this->applicationType,
                'applicationUrl' => $this->applicationUrl
            )
        );
    }
    
    /**
     * @return void
     */
    public function testIsSuccessfulTrue()
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
    public function testIsSuccessfulFalse()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestFailure.txt');
        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame('Not Found', $response->getReason());
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

    /**
     * @return void
     */
    public function getResponse()
    {
        $this->setMockHttpResponse('ApplePayAuthorizeRequestSuccess.txt');
        $response = $this->request->send();

        $this->assertJsonStringEqualsJsonString(
            json_encode(
                array(
                    'statusCode' => 200,
                    'reasonPhrase' => 'OK',
                    'body' => array(
                        'epochTimestamp' => 1234567890,
                        'expiresAt' => 123456789000,
                        'merchantSessionIdentifier' => 'SSH1234567890',
                        'nonce' => 'd12345',
                        'merchantIdentifier' => 'SSHDC1234567890IJK',
                        'domainName' => 'abcd.com',
                        'displayName' => 'Abcd',
                        'signature' => '1234567890',
                    )
                )
            ),
            $response->getResponse()
        );
    }
}
