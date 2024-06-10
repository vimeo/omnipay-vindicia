<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class ResponseTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        date_default_timezone_set('Europe/London');
        $this->response = Mocker::mock('\Omnipay\Vindicia\Message\Response')->makePartial();
    }

    /**
     * @return void
     */
    public function testIsSuccessful()
    {
        $this->response->shouldReceive('getCode')->andReturn('200');
        $this->assertTrue($this->response->isSuccessful());

        // 202 on an authorize means that the tax service was unavailable,
        // but the request still succeeded
        $this->response = Mocker::mock('\Omnipay\Vindicia\Message\Response')->makePartial();
        $this->response->shouldReceive('getCode')->andReturn('202');
        $this->assertTrue($this->response->isSuccessful());

        $this->response = Mocker::mock('\Omnipay\Vindicia\Message\Response')->makePartial();
        $this->response->shouldReceive('getCode')->andReturn('400');
        $this->assertFalse($this->response->isSuccessful());
    }
}
