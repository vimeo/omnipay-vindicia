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
        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'validationURL' => 'https://apple-pay-gateway-cert.apple.com/'
            )
        );
    }

   /**
	* @return void
	*/
    public function testSendSuccess()
    {
        //$this->setMockHttpResponse('PurchaseSuccess.txt');
        $response = $this->request->send();

        var_dump($response);

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }
}
