<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;

class NonStrippingCreditCardTest extends TestCase
{
    public function setUp()
    {
        $this->card = new NonStrippingCreditCard();
    }

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
}
