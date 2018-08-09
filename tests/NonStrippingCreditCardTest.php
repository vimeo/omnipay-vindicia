<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;

class NonStrippingCreditCardTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->card = new NonStrippingCreditCard();
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
        $paymentInstrumentName = 'AmEx 1362';
        $this->assertSame($this->card, $this->card->setPaymentInstrumentName($paymentInstrumentName));
        $this->assertSame($paymentInstrumentName, $this->card->getPaymentInstrumentName());
    }

    /**
     * @return void
     */
    public function testPaymentNetwork()
    {
        $paymentNetwork = 'AmEx';
        $this->assertSame($this->card, $this->card->setPaymentNetwork($paymentNetwork));
        $this->assertSame($paymentNetwork, $this->card->getPaymentNetwork());
    }

    /**
     * @return void
     */
    public function testTransactionIdentifier()
    {
        $transactionIdentifier = '7e0c086a15c67fc8b573e4c43cde55c85785aadacd585ee66789a675fce116b1';
        $this->assertSame($this->card, $this->card->setTransactionIdentifier($transactionIdentifier));
        $this->assertSame($transactionIdentifier, $this->card->getTransactionIdentifier());
    }

    /**
     * @return void
     */
    public function testPaymentData()
    {
        $paymentData = '{  
         "version":"EC_v1",
         "data":"evb9LioL0u7rgqgZZj5DttZwav5cPZlGyCI7Ha63PmDL3ejFzZFZCFJ96hw1HyvO71Mm5+sSvSdUwnIWH9oGYi8PcTwEOFQbebvSb/eSFf+NQLrK3lhYfGJUV7HaRBbpKJrcI0GBJQ0kXWqXZoren0iInLM/c/R9FoFfMJ76aCK68/82X+jQ5vLMgBID3NNC31qvlFSAMaSWlZ0JCnbWvSo1INwLvRLYXvLgWzbjsCLF/le9mkAaGfRe4aCPsaOxCYyqRaKkY65QjHj6QnXS8t8FUkoPrgvgD6sCpSuPAqGxiEBQM0OyOAFje4pfEzjfGVUpFXZ+DBYAShiP+5jQAgyIAu4G7Dia0gBBMU6W0n/D5Bzsus4clxkMM+ReJ3EV03+z9ISlI4+QZJk=",
         "signature":"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",
         "header":{  
            "ephemeralPublicKey":"MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAEiyRKVurFQaICFfXmbJyF6j237mRARhsFte4kwffyNx+d2u4FvkimDv9q5xG8h8jyj00kYVaiCt9QCkXOy4Zvqg==",
            "publicKeyHash":"Gcixq6XzHMREhRp9971hQaoPIfOa067lb5pbrQrl44o=",
            "transactionId":"609909581ec64792b79cfb609400015aa771a29b1430bc05cac3c05c216e2ba3"
         }';
        $this->assertSame($this->card, $this->card->setPaymentData($paymentData));
        $this->assertSame($paymentData, $this->card->getPaymentData());
    }
}
