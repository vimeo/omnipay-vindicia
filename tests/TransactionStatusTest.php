<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class TransactionStatusTest extends TestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->transactionStatus = new TransactionStatus();
        $this->status = $this->faker->status();
    }

    public function testConstructWithParams()
    {
        $transactionStatus = new TransactionStatus(array(
            'status' => $this->status
        ));
        $this->assertSame($this->status, $transactionStatus->getStatus());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->transactionStatus, $this->transactionStatus->initialize(array(
            'status' => $this->status
        )));
        $this->assertSame($this->status, $this->transactionStatus->getStatus());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setStatus($this->status));
        $this->assertSame(array('status' => $this->status), $this->transactionStatus->getParameters());
    }

    public function testStatus()
    {
        $status = $this->faker->status();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setStatus($status));
        $this->assertSame($status, $this->transactionStatus->getStatus());
    }

    public function testTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setTime($time));
        $this->assertSame($time, $this->transactionStatus->getTime());
    }

    public function testAuthorizationCode()
    {
        $authorizationCode = $this->faker->statusCode();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setAuthorizationCode($authorizationCode));
        $this->assertSame($authorizationCode, $this->transactionStatus->getAuthorizationCode());
    }
}
