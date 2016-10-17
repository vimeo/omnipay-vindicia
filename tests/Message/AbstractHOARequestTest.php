<?php

namespace Omnipay\VindiciaTest\Message;

require_once(dirname(dirname(__DIR__)) . '/vendor/autoload.php');

use Omnipay\VindiciaTest\DataFaker;
use Omnipay\VindiciaTest\SoapTestCase;
use Omnipay\Vindicia\Message\AbstractHOARequest;
use Omnipay\VindiciaTest\Mocker;
use ReflectionClass;
use Omnipay\Vindicia\Attribute;

class AbstractHOARequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->request = Mocker::mockHOARequest('\Omnipay\Vindicia\Message\AbstractHOARequest')->shouldAllowMockingProtectedMethods();
        $this->request->initialize();

        $this->faker = new DataFaker();

        $this->errorUrl = $this->faker->url();
        $this->returnUrl = $this->faker->url();
        $this->ip = $this->faker->ipAddress();
        $this->HOAAttributes = $this->faker->attributes();
    }

    public function testIp()
    {
        $this->assertSame($this->request, $this->request->setIp($this->ip));
        $this->assertSame($this->ip, $this->request->getIp());
    }

    public function testReturnUrl()
    {
        $this->assertSame($this->request, $this->request->setReturnUrl($this->returnUrl));
        $this->assertSame($this->returnUrl, $this->request->getReturnUrl());
    }

    public function testErrorUrl()
    {
        $this->assertSame($this->request, $this->request->setErrorUrl($this->errorUrl));
        $this->assertSame($this->errorUrl, $this->request->getErrorUrl());
    }

    public function testHOAAttributesAsBag()
    {
        // $attributes is an AttributeBag
        $attributes = $this->faker->attributes();
        $this->assertSame($this->request, $this->request->setHOAAttributes($attributes));
        $this->assertSame($attributes, $this->request->getHOAAttributes());
    }

    public function testHOAAttributesAsArray()
    {
        // $attributes is an array
        $attributes = $this->faker->attributes(true);
        $this->assertSame($this->request, $this->request->setHOAAttributes($attributes));

        $returnedAttributes = $this->request->getHOAAttributes();
        $this->assertInstanceOf('Omnipay\Vindicia\AttributeBag', $returnedAttributes);

        $numAttributes = count($attributes);
        $this->assertSame($numAttributes, $returnedAttributes->count());

        foreach ($returnedAttributes as $i => $returnedAttribute) {
            $this->assertEquals(new Attribute($attributes[$i]), $returnedAttribute);
        }
    }

    public function testGetData()
    {
        $this->markTestSkipped('Mocking and reflection don\'t play nice together, we might not be able to have this test.');

        $regularFunctionName = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 9));
        $regularObjectName = ucfirst($this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 9)));

        $reflection = new ReflectionClass($this->request);
        $regularRequestProperty = $reflection->getProperty('regularRequest');
        $regularRequestProperty->setAccessible(true);
        $regularRequest = $regularRequestProperty->getValue($this->request);
        $regularRequest->shouldReceive('getFunction')->andReturn($regularFunctionName);
        $regularRequest->shouldReceive('getObject')->andReturn($regularObjectName);
        $regularRequestProperty->setValue($this->request, $regularRequest);

        $this->request->setIp($this->ip)
            ->setReturnUrl($this->returnUrl)
            ->setErrorUrl($this->errorUrl);

        $functionName = $this->faker->randomCharacters(DataFaker::ALPHABET_LOWER, $this->faker->intBetween(5, 9));
        $this->request->shouldReceive('getFunction')->andReturn($functionName);

        $data = $this->request->getData();

        $this->assertSame($this->returnUrl, $data['session']->returnURL);
        $this->assertSame($this->errorUrl, $data['session']->errorURL);
        $this->assertSame($this->ip, $data['session']->ipAddress);
        $this->assertSame($functionName, $data['action']);
        $this->assertSame($regularObjectName . '_' . $regularFunctionName);
        $this->assertSame(AbstractRequest::API_VERSION, $data['session']->version);

        $this->assertEquals(
            array(),
            $data['session']->privateFormValues
        );
        $this->assertEquals(
            array(),
            $data['session']->methodParamValues
        );

        $this->assertSame('initialize', $data['action']);
    }
}
