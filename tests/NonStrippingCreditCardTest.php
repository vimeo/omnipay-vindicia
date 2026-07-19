<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestFramework\DataFaker;

class NonStrippingCreditCardTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp(): void
    {
        $this->card = new NonStrippingCreditCard();
        $this->faker = new DataFaker();
    }

    /**
     * @return void
     */
    public function testNumber()
    {
        $number = '424242XXXXXX4242';
        $this->assertSame($this->card, $this->card->setNumber($number));
        $this->assertSame($number, $this->card->getNumber());

        $number = '5555-5555 5555-4444';
        $this->assertSame($this->card, $this->card->setNumber($number));
        $this->assertSame($number, $this->card->getNumber());

        $number = '5555555555554444';
        $this->assertSame($this->card, $this->card->setNumber($number));
        $this->assertSame($number, $this->card->getNumber());
    }

    /**
     * @return void
     */
    public function testPaymentInstrumentName()
    {
        $paymentInstrumentName = $this->faker->paymentInstrumentName();
        $this->assertSame($this->card, $this->card->setPaymentInstrumentName($paymentInstrumentName));
        $this->assertSame($paymentInstrumentName, $this->card->getPaymentInstrumentName());
    }

    /**
     * @return void
     */
    public function testPaymentNetwork()
    {
        $paymentNetwork = $this->faker->paymentNetwork();
        $this->assertSame($this->card, $this->card->setPaymentNetwork($paymentNetwork));
        $this->assertSame($paymentNetwork, $this->card->getPaymentNetwork());
    }

    /**
     * @return void
     */
    public function testApplePayTransactionReference()
    {
        $applePayTransactionReference = $this->faker->applePayTransactionReference();
        $this->assertSame($this->card, $this->card->setApplePayTransactionReference($applePayTransactionReference));
        $this->assertSame($applePayTransactionReference, $this->card->getApplePayTransactionReference());
    }
}
