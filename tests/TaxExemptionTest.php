<?php

namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TaxExemption;

class TaxExemptionTest extends TestCase
{
    public function setUp()
    {
        $this->exemption = new TaxExemption();
        $this->faker = new DataFaker();
        $this->exemptionId = $this->faker->taxExemptionId();
        $this->region = $this->faker->region();
        $this->active = $this->faker->bool();
    }

    public function testConstructWithParams()
    {
        $exemption = new TaxExemption(array(
            'exemptionId' => $this->exemptionId,
            'region' => $this->region,
            'active' => $this->active
        ));
        $this->assertSame($this->exemptionId, $exemption->getExemptionId());
        $this->assertSame($this->region, $exemption->getRegion());
        $this->assertSame($this->active, $exemption->getActive());
    }

    public function testInitializeWithParams()
    {
        $this->assertSame($this->exemption, $this->exemption->initialize(array(
            'exemptionId' => $this->exemptionId,
            'region' => $this->region,
            'active' => $this->active
        )));
        $this->assertSame($this->exemptionId, $this->exemption->getExemptionId());
        $this->assertSame($this->region, $this->exemption->getRegion());
        $this->assertSame($this->active, $this->exemption->getActive());
    }

    public function testGetParameters()
    {
        $this->assertSame($this->exemption, $this->exemption->setExemptionId($this->exemptionId));
        $this->assertSame(array('active' => true, 'exemptionId' => $this->exemptionId), $this->exemption->getParameters());
    }

    public function testExemptionId()
    {
        $this->assertSame($this->exemption, $this->exemption->setExemptionId($this->exemptionId));
        $this->assertSame($this->exemptionId, $this->exemption->getExemptionId());
    }

    public function testRegion()
    {
        $this->assertSame($this->exemption, $this->exemption->setRegion($this->region));
        $this->assertSame($this->region, $this->exemption->getRegion());
    }

    public function testActive()
    {
        $this->assertSame($this->exemption, $this->exemption->setActive($this->active));
        $this->assertSame($this->active, $this->exemption->getActive());
    }
}
