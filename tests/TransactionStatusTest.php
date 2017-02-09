<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class TransactionStatusTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->transactionStatus = new TransactionStatus();
        $this->transactionStatusStatus = $this->faker->status();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $transactionStatus = new TransactionStatus(array(
            'status' => $this->transactionStatusStatus
        ));
        $this->assertSame($this->transactionStatusStatus, $transactionStatus->getStatus());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->transactionStatus, $this->transactionStatus->initialize(array(
            'status' => $this->transactionStatusStatus
        )));
        $this->assertSame($this->transactionStatusStatus, $this->transactionStatus->getStatus());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setStatus($this->transactionStatusStatus));
        $this->assertSame(array('status' => $this->transactionStatusStatus), $this->transactionStatus->getParameters());
    }

    /**
     * @return void
     */
    public function testStatus()
    {
        $status = $this->faker->status();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setStatus($status));
        $this->assertSame($status, $this->transactionStatus->getStatus());
    }

    /**
     * @return void
     */
    public function testTime()
    {
        $time = $this->faker->timestamp();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setTime($time));
        $this->assertSame($time, $this->transactionStatus->getTime());
    }

    /**
     * @return void
     */
    public function testAuthorizationCode()
    {
        $authorizationCode = $this->faker->statusCode();
        $this->assertSame($this->transactionStatus, $this->transactionStatus->setAuthorizationCode($authorizationCode));
        $this->assertSame($authorizationCode, $this->transactionStatus->getAuthorizationCode());
    }
}
