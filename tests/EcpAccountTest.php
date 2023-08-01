<?php

namespace Omnipay\Vindicia;

use EcpAccount;
use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class EcpAccountTest extends TestCase
{
    public function setUp(): void
    {
        $this->ecp = new EcpAccount();
        $this->faker = new DataFaker();
    }

    public function testMaskedAccountNumber(): void
    {
        $value = 'XXXXX1234';
        $this->assertSame($this->ecp, $this->ecp->setMaskedAccountNumber($value));
        $this->assertSame($value, $this->ecp->getMaskedAccountNumber());
    }

    public function testRoutingNumber(): void
    {
        $value = '123456789';
        $this->assertSame($this->ecp, $this->ecp->setRoutingNumber($value));
        $this->assertSame($value, $this->ecp->getRoutingNumber());
    }

    public function testAccountType(): void
    {
        $value = 'ConsumerChecking';
        $this->assertSame($this->ecp, $this->ecp->setAccountType($value));
        $this->assertSame($value, $this->ecp->getAccountType());
    }

    public function testPostalCode(): void
    {
        $value = $this->faker->postcode();
        $this->assertSame($this->ecp, $this->ecp->setBillingPostcode($value));
        $this->assertSame($value, $this->ecp->getBillingPostcode());
    }

    public function testCountry(): void
    {
        $value = $this->faker->region();
        $this->assertSame($this->ecp, $this->ecp->setBillingCountry($value));
        $this->assertSame($value, $this->ecp->getBillingCountry());
    }
}
