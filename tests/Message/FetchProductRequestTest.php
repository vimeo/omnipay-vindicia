<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

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
        $this->taxClassification = $this->faker->taxClassification();
        $this->planId = $this->faker->planId();
        $this->planReference = $this->faker->planReference();
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
            'PRODUCT_REFERENCE' => $this->productReference,
            'TAX_CLASSIFICATION' => $this->taxClassification,
            'PLAN_ID' => $this->planId,
            'PLAN_REFERENCE' => $this->planReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertNotNull($response->getProduct());
        $this->assertSame($this->productId, $response->getProductId());
        $this->assertSame($this->productReference, $response->getProductReference());

        $product = $response->getProduct();
        $this->assertInstanceOf('\Omnipay\Vindicia\Product', $product);
        $this->assertSame($this->productId, $response->getProductId());
        $this->assertSame($this->productReference, $response->getProductReference());
        $this->assertSame($this->productId, $product->getId());
        $this->assertSame($this->productReference, $product->getReference());
        $this->assertSame($this->taxClassification, $product->getTaxClassification());
        $plan = $product->getPlan();
        $this->assertSame($this->planId, $product->getPlanId());
        $this->assertSame($this->planId, $plan->getId());
        $this->assertSame($this->planReference, $product->getPlanReference());
        $this->assertSame($this->planReference, $plan->getReference());
        $prices = $product->getPrices();
        $this->assertSame(2, count($prices));
        foreach ($prices as $price) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Price', $price);
        }
        $attributes = $product->getAttributes();
        $this->assertSame(2, count($attributes));
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf('\Omnipay\Vindicia\Attribute', $attribute);
        }

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
