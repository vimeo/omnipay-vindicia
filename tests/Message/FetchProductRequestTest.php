<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\Mocker;
use Omnipay\VindiciaTest\DataFaker;
use Omnipay\Vindicia\Message\FetchProductRequest;
use Omnipay\VindiciaTest\SoapTestCase;

class FetchProductRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->productId = $this->faker->productId();

        $this->request = new FetchProductRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'productId' => $this->productId
            )
        );

        $this->productReference = $this->faker->productReference();
    }

    public function testProductId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductId($this->productId));
        $this->assertSame($this->productId, $request->getProductId());
    }

    public function testProductReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\FetchProductRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setProductReference($this->productReference));
        $this->assertSame($this->productReference, $request->getProductReference());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->productId, $data['merchantProductId']);
        $this->assertSame('fetchByMerchantProductId', $data['action']);
    }

    public function testGetDataByReference()
    {
        $this->request->setProductId(null)->setProductReference($this->productReference);

        $data = $this->request->getData();

        $this->assertSame($this->productReference, $data['vid']);
        $this->assertSame('fetchByVid', $data['action']);
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the productId or productReference parameter is required.
     */
    public function testProductIdOrReferenceRequired()
    {
        $this->request->setProductId(null);
        $this->request->setProductReference(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('FetchProductSuccess.xml', array(
            'PRODUCT_ID' => $this->productId,
            'PRODUCT_REFERENCE' => $this->productReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getProduct());
        $this->assertSame($this->productId, $response->getProductId());
        $this->assertSame($this->productReference, $response->getProductReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Product.wsdl', $this->getLastEndpoint());
    }

    public function testSendByReferenceSuccess()
    {
        $this->setMockSoapResponse('FetchProductSuccess.xml', array(
            'PRODUCT_ID' => $this->productId,
            'PRODUCT_REFERENCE' => $this->productReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getProduct());
        $this->assertSame($this->productId, $response->getProductId());
        $this->assertSame($this->productReference, $response->getProductReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Product.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        $this->setMockSoapResponse('FetchProductFailure.xml', array(
            'PRODUCT_ID' => $this->productId,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Unable to load product by sku "' . $this->productId . '":  No match', $response->getMessage());

        $this->assertNull($response->getProductId());
        $this->assertNull($response->getProductReference());
    }

    public function testSendByReferenceFailure()
    {
        $this->setMockSoapResponse('FetchProductByReferenceFailure.xml', array(
            'PRODUCT_REFERENCE' => $this->productReference,
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('404', $response->getCode());
        $this->assertSame('Unable to load product by VID "' . $this->productReference . '" -  No match', $response->getMessage());

        $this->assertNull($response->getProductId());
        $this->assertNull($response->getProductReference());
    }
}
