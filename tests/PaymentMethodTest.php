<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;
use Omnipay\Common\CreditCard;

class PaymentMethodTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->paymentMethod = new PaymentMethod();
        $this->id = $this->faker->paymentMethodId();
        $this->reference = $this->faker->paymentMethodReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $paymentMethod = new PaymentMethod(array(
            'id' => $this->id,
            'reference' => $this->reference
        ));
        $this->assertSame($this->id, $paymentMethod->getId());
        $this->assertSame($this->reference, $paymentMethod->getReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->initialize(array(
            'id' => $this->id,
            'reference' => $this->reference
        )));
        $this->assertSame($this->id, $this->paymentMethod->getId());
        $this->assertSame($this->reference, $this->paymentMethod->getReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setId($this->id)->setReference($this->reference));
        $this->assertSame(array('id' => $this->id, 'reference' => $this->reference), $this->paymentMethod->getParameters());
    }

    /**
     * @return void
     */
    public function testId()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setId($this->id));
        $this->assertSame($this->id, $this->paymentMethod->getId());
    }

    /**
     * @return void
     */
    public function testReference()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setReference($this->reference));
        $this->assertSame($this->reference, $this->paymentMethod->getReference());
    }

    /**
     * @return void
     */
    public function testCard()
    {
        $card = new CreditCard();
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setCard($card));
        $this->assertSame($card, $this->paymentMethod->getCard());
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = array($this->faker->attribute());
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setAttributes($attributes));
        $this->assertSame($attributes, $this->paymentMethod->getAttributes());
    }
}
