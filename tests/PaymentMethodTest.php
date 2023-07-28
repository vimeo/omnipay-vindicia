<?php

namespace Omnipay\Vindicia;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Tests\TestCase;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\EcpAccount;

class PaymentMethodTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
        $this->paymentMethod = new PaymentMethod();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->paymentMethodReference = $this->faker->paymentMethodReference();
    }

    /**
     * @return void
     */
    public function testConstructWithParams()
    {
        $paymentMethod = new PaymentMethod(array(
            'paymentMethodId' => $this->paymentMethodId,
            'paymentMethodReference' => $this->paymentMethodReference
        ));
        $this->assertSame($this->paymentMethodId, $paymentMethod->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $paymentMethod->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testInitializeWithParams()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->initialize(array(
            'paymentMethodId' => $this->paymentMethodId,
            'paymentMethodReference' => $this->paymentMethodReference
        )));
        $this->assertSame($this->paymentMethodId, $this->paymentMethod->getPaymentMethodId());
        $this->assertSame($this->paymentMethodReference, $this->paymentMethod->getPaymentMethodReference());
    }

    /**
     * @return void
     */
    public function testGetParameters()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setPaymentMethodId($this->paymentMethodId)->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame(array('paymentMethodId' => $this->paymentMethodId, 'paymentMethodReference' => $this->paymentMethodReference), $this->paymentMethod->getParameters());
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setPaymentMethodId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $this->paymentMethod->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setPaymentMethodReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $this->paymentMethod->getPaymentMethodReference());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testId()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setId($this->paymentMethodId));
        $this->assertSame($this->paymentMethodId, $this->paymentMethod->getId());
    }

    /**
     * @return void
     * @psalm-suppress DeprecatedMethod because we want to make sure it still works
     */
    public function testReference()
    {
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setReference($this->paymentMethodReference));
        $this->assertSame($this->paymentMethodReference, $this->paymentMethod->getReference());
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
    public function testType()
    {
        $type = 'PayPal';
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setType($type));
        $this->assertSame($type, $this->paymentMethod->getType());
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

    public function testEcpAccount(): void
    {
        $ecp = new EcpAccount();
        $this->assertSame($this->paymentMethod, $this->paymentMethod->setEcpAccount($ecp));
        $this->assertSame($ecp, $this->paymentMethod->getEcpAccount());
    }
}
