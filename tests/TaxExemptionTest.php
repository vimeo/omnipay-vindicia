<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;

class TaxExemptionTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->exemption = new TaxExemption();
        $this->faker = new DataFaker();
        $this->exemptionId = $this->faker->taxExemptionId();
        $this->region = $this->faker->region();
        $this->active = $this->faker->bool();
    }

    /**
     * @return void
     */
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

    /**
     * @return void
     */
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

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->exemption, $this->exemption->setExemptionId($this->exemptionId));
        $this->assertSame(array('active' => true, 'exemptionId' => $this->exemptionId), $this->exemption->getParameters());
    }

    /**
     * @return void
     */
    public function testExemptionId()
    {
        $this->assertSame($this->exemption, $this->exemption->setExemptionId($this->exemptionId));
        $this->assertSame($this->exemptionId, $this->exemption->getExemptionId());
    }

    /**
     * @return void
     */
    public function testRegion()
    {
        $this->assertSame($this->exemption, $this->exemption->setRegion($this->region));
        $this->assertSame($this->region, $this->exemption->getRegion());
    }

    /**
     * @return void
     */
    public function testActive()
    {
        $this->assertSame($this->exemption, $this->exemption->setActive($this->active));
        $this->assertSame($this->active, $this->exemption->getActive());
    }
}
